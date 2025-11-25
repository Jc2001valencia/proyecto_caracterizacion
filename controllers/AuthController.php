<?php
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../config/EmailService.php';

class AuthController
{
    private $usuarioModel;
    private $emailService;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->emailService = new EmailService();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Capturar acción desde GET o POST
        $action = $_GET['action'] ?? $_POST['action'] ?? null;
        if ($action && method_exists($this, $action)) {
            $this->$action();
        }
    }

    public function register()
    {
        try {
            $data = $_POST;

            // Validar campos obligatorios
            if(empty($data['nombre']) || empty($data['email']) || empty($data['usuario']) || empty($data['contrasena'])) {
                echo json_encode(['success'=>false,'message'=>'Todos los campos son obligatorios']);
                return;
            }

            // Validar formato de email
            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success'=>false,'message'=>'El formato del email no es válido']);
                return;
            }

            // Verificar si el email ya existe
            $existing = $this->usuarioModel->getByEmail($data['email']);
            if($existing) {
                echo json_encode(['success'=>false,'message'=>'Email ya registrado']);
                return;
            }

            // Verificar si el usuario ya existe
            $existingUser = $this->usuarioModel->getByUsername($data['usuario']);
            if($existingUser) {
                echo json_encode(['success'=>false,'message'=>'El nombre de usuario ya existe']);
                return;
            }

            // Validar fortaleza de contraseña
            if(strlen($data['contrasena']) < 8) {
                echo json_encode(['success'=>false,'message'=>'La contraseña debe tener al menos 8 caracteres']);
                return;
            }

            // Hashear contraseña
            $data['contrasena'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);

            // Crear usuario (sin verificación de email)
            $result = $this->usuarioModel->create($data);
            
            if($result === true) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Registro exitoso. Ya puedes iniciar sesión.'
                ]);
            } else {
                $msg = is_array($result) && isset($result['error']) ? $result['error'] : 'No se pudo crear el usuario';
                echo json_encode(['success'=>false,'message'=>$msg]);
            }

        } catch (Exception $e) {
            error_log("Error en registro: " . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Error en el servidor. Intente nuevamente.']);
        }
    }

    public function login()
    {
        try {
            $email = $_POST['email'] ?? '';
            $pass  = $_POST['contrasena'] ?? '';
            
            // Validar campos
            if(empty($email) || empty($pass)) {
                echo json_encode(['success'=>false,'message'=>'Email y contraseña son obligatorios']);
                return;
            }

            $user = $this->usuarioModel->getByEmail($email);

            if ($user) {
                // Verificar contraseña
                if (password_verify($pass, $user['contrasena'])) {
                    // Reiniciar intentos fallidos
                    $this->usuarioModel->resetIntentosLogin($user['id']);
                    
                    // Generar código 2FA
                    $codigo_2fa = sprintf("%06d", mt_rand(1, 999999));
                    $fecha_expiracion_2fa = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    
                    // Guardar código 2FA en base de datos
                    $this->usuarioModel->guardarCodigo2FA($user['id'], $codigo_2fa, $fecha_expiracion_2fa);
                    
                    // Enviar código 2FA por email
                    $emailEnviado = $this->emailService->enviarCodigo2FA(
                        $user['email'], 
                        $user['nombre'], 
                        $codigo_2fa
                    );

                    if ($emailEnviado) {
                        $_SESSION['user_id_2fa'] = $user['id'];
                        echo json_encode([
                            'success' => true,
                            'message' => 'Código de verificación enviado a su correo electrónico',
                            'requires2fa' => true
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Error al enviar el código de verificación. Intente nuevamente.'
                        ]);
                    }
                    
                } else {
                    // Registrar intento fallido
                    $this->usuarioModel->registrarIntentoFallido($user['id']);
                    
                    $intentos_restantes = 5 - ($user['intentos_login'] + 1);
                    if($intentos_restantes > 0) {
                        echo json_encode([
                            'success' => false, 
                            'message' => "Credenciales incorrectas. Le quedan $intentos_restantes intentos."
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Cuenta bloqueada por múltiples intentos fallidos. Intente en 30 minutos.'
                        ]);
                    }
                }
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Email o contraseña incorrectos'
                ]);
            }

        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Error en el servidor. Intente nuevamente.']);
        }
    }

    // Mantener solo verificar2FA() y reenviar2FA()
    public function verificar2FA()
    {
        try {
            $codigo = $_POST['codigo_2fa'] ?? '';
            
            if(empty($codigo)) {
                echo json_encode(['success'=>false,'message'=>'El código es obligatorio']);
                return;
            }

            if(!isset($_SESSION['user_id_2fa'])) {
                echo json_encode(['success'=>false,'message'=>'Sesión de verificación no válida']);
                return;
            }

            $user_id = $_SESSION['user_id_2fa'];
            $user = $this->usuarioModel->verificarCodigo2FA($user_id, $codigo);

            if($user) {
                // Establecer sesión de usuario
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nombre'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_usuario'] = $user['usuario'];
                $_SESSION['logged_in'] = true;
                $_SESSION['last_activity'] = time();

                // Limpiar código 2FA y actualizar último login
                $this->usuarioModel->limpiarCodigo2FA($user['id']);
                $this->usuarioModel->actualizarUltimoLogin($user['id']);

                unset($_SESSION['user_id_2fa']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Verificación exitosa',
                    'redirect' => 'views/Home.php'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Código incorrecto o expirado'
                ]);
            }

        } catch (Exception $e) {
            error_log("Error en verificación 2FA: " . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Error en el servidor. Intente nuevamente.']);
        }
    }

    public function reenviar2FA()
    {
        try {
            if(!isset($_SESSION['user_id_2fa'])) {
                echo json_encode(['success'=>false,'message'=>'Sesión no válida']);
                return;
            }

            $user_id = $_SESSION['user_id_2fa'];
            $user = $this->usuarioModel->getById($user_id);

            if(!$user) {
                echo json_encode(['success'=>false,'message'=>'Usuario no encontrado']);
                return;
            }

            // Generar nuevo código 2FA
            $nuevo_codigo = sprintf("%06d", mt_rand(1, 999999));
            $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Guardar nuevo código
            $this->usuarioModel->guardarCodigo2FA($user['id'], $nuevo_codigo, $fecha_expiracion);

            // Enviar nuevo código por email
            $emailEnviado = $this->emailService->enviarCodigo2FA(
                $user['email'], 
                $user['nombre'], 
                $nuevo_codigo
            );

            if($emailEnviado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Nuevo código de verificación enviado a su correo'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error al enviar el código. Intente nuevamente.'
                ]);
            }

        } catch (Exception $e) {
            error_log("Error al reenviar 2FA: " . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Error en el servidor. Intente nuevamente.']);
        }
    }

    // Eliminar los métodos de verificación de email que ya no necesitamos
    // public function verificarEmail() { ... }
    // private function mostrarVerificacionExito() { ... }
    // private function mostrarVerificacionError() { ... }
}

new AuthController();