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

    public function mostrar($page = 'home')
    {
        switch($page) {
            case 'login':
            case 'register':
                $viewPath = __DIR__ . '/../views/AuthView.php';
                break;
            case 'home':
            default:
                $viewPath = __DIR__ . '/../views/home_landing.php';
                break;
        }
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Vista no encontrada: $viewPath";
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

            // Generar token de verificación
            $token_verificacion = bin2hex(random_bytes(32));
            $data['token_verificacion'] = $token_verificacion;
            $data['fecha_expiracion_token'] = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Crear usuario
            $result = $this->usuarioModel->create($data);
            
            if($result === true) {
                // Enviar email de verificación
                $emailEnviado = $this->emailService->enviarEmailVerificacion(
                    $data['email'], 
                    $data['nombre'], 
                    $token_verificacion
                );

                if($emailEnviado) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Registro exitoso. Se ha enviado un enlace de verificación a su correo electrónico.'
                    ]);
                } else {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Registro exitoso, pero no se pudo enviar el email de verificación. Contacte al administrador.'
                    ]);
                }
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
                // Verificar si la cuenta está verificada
                if(!$user['verificado']) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Por favor verifique su email antes de iniciar sesión. Revise su bandeja de entrada.'
                    ]);
                    return;
                }

                // Verificar si la cuenta está bloqueada
                if($user['bloqueado_hasta'] && strtotime($user['bloqueado_hasta']) > time()) {
                    $tiempo_restante = strtotime($user['bloqueado_hasta']) - time();
                    $minutos = ceil($tiempo_restante / 60);
                    echo json_encode([
                        'success' => false, 
                        'message' => "Cuenta bloqueada. Intente nuevamente en $minutos minutos."
                    ]);
                    return;
                }

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
                        $_SESSION['organizacion_id_2fa'] = $user['id'];
                        echo json_encode([
                            'success' => true,
                            'message' => 'Código de verificación enviado a su correo electrónico'
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

    public function verificar2FA()
    {
        try {
            $codigo = $_POST['codigo_2fa'] ?? '';
            
            if(empty($codigo)) {
                echo json_encode(['success'=>false,'message'=>'El código es obligatorio']);
                return;
            }

            if(!isset($_SESSION['organizacion_id_2fa'])) {
                echo json_encode(['success'=>false,'message'=>'Sesión de verificación no válida']);
                return;
            }

            $organizacion_id = $_SESSION['organizacion_id_2fa'];
            $user = $this->usuarioModel->verificarCodigo2FA($organizacion_id, $codigo);

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

                unset($_SESSION['organizacion_id_2fa']);

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
            if(!isset($_SESSION['organizacion_id_2fa'])) {
                echo json_encode(['success'=>false,'message'=>'Sesión no válida']);
                return;
            }

            $organizacion_id = $_SESSION['organizacion_id_2fa'];
            $user = $this->usuarioModel->getById($organizacion_id);

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

    public function verificarEmail()
    {
        try {
            $token = $_GET['token'] ?? '';
            
            if(empty($token)) {
                die("Token no proporcionado");
            }

            $user = $this->usuarioModel->verificarTokenEmail($token);

            if($user) {
                // Marcar como verificado
                $this->usuarioModel->marcarComoVerificado($user['id']);
                
                // Mostrar página de éxito
                $this->mostrarVerificacionExito($user['nombre']);
            } else {
                // Mostrar página de error
                $this->mostrarVerificacionError("Token de verificación no válido o expirado");
            }

        } catch (Exception $e) {
            error_log("Error en verificación de email: " . $e->getMessage());
            $this->mostrarVerificacionError("Error en el servidor");
        }
    }

    public function recuperar()
    {
        try {
            $email = $_POST['email_recuperar'] ?? '';
            
            if(empty($email)) {
                echo json_encode(['success'=>false,'message'=>'El email es obligatorio']);
                return;
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success'=>false,'message'=>'El formato del email no es válido']);
                return;
            }

            $user = $this->usuarioModel->getByEmail($email);

            if ($user && $user['verificado']) {
                // Generar token de recuperación
                $token_recuperacion = bin2hex(random_bytes(32));
                $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Guardar token
                $this->usuarioModel->guardarTokenRecuperacion($user['id'], $token_recuperacion, $fecha_expiracion);

                // Enviar email de recuperación
                $emailEnviado = $this->emailService->enviarEmailRecuperacion(
                    $user['email'], 
                    $user['nombre'], 
                    $token_recuperacion
                );

                if($emailEnviado) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Se ha enviado un enlace de recuperación a su correo electrónico'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Error al enviar el email de recuperación'
                    ]);
                }
            } else {
                // Por seguridad, no revelar si el email existe o no
                echo json_encode([
                    'success' => true, 
                    'message' => 'Si el email existe y está verificado, recibirá un enlace de recuperación'
                ]);
            }

        } catch (Exception $e) {
            error_log("Error en recuperación: " . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Error en el servidor. Intente nuevamente.']);
        }
    }

    private function mostrarVerificacionExito($nombre)
    {
        ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verificado - Sistema de Gestión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-green-400 to-blue-600 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-green-600 text-4xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-4">¡Email Verificado!</h1>
        <p class="text-gray-600 mb-6">
            Tu cuenta ha sido verificada exitosamente. Ahora puedes iniciar sesión en el sistema.
        </p>
        <p class="text-green-600 font-semibold mb-6">
            Bienvenido/a, <?php echo htmlspecialchars($nombre); ?>!
        </p>
        <a href="../index.php?page=login"
            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
            <i class="fas fa-sign-in-alt mr-2"></i>
            Iniciar Sesión
        </a>
    </div>
</body>

</html>
<?php
        exit;
    }

    private function mostrarVerificacionError($mensaje)
    {
        ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Verificación - Sistema de Gestión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-red-400 to-orange-600 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-exclamation-triangle text-red-600 text-4xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Error en Verificación</h1>
        <p class="text-gray-600 mb-6">
            <?php echo htmlspecialchars($mensaje); ?>
        </p>
        <a href="../index.php"
            class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver al Inicio
        </a>
    </div>
</body>

</html>
<?php
        exit;
    }
}

new AuthController();