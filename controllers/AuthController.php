<?php
// controllers/AuthController.php

// Verificar si los modelos existen antes de incluirlos
if (!class_exists('UsuarioModel')) {
    require_once 'models/UsuarioModel.php';
}

if (!class_exists('TwoFactorModel')) {
    require_once 'models/TwoFactorModel.php';
}

if (!class_exists('EmailService')) {
    require_once 'config/EmailService.php';
}

class AuthController {
    private $usuarioModel;
    private $twoFactorModel;
    private $emailService;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        
        // Inicializar modelos
        try {
            $this->usuarioModel = new UsuarioModel($db);
            $this->twoFactorModel = new TwoFactorModel($db);
            $this->emailService = new EmailService();
        } catch (Exception $e) {
            die("Error inicializando modelos: " . $e->getMessage());
        }
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Generar token CSRF solo una vez por sesión
        $this->generarTokenCSRF();
    }

    private function generarTokenCSRF() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function validarCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = "Token de seguridad inválido. Por favor, recarga la página e intenta nuevamente.";
            return false;
        }
        return true;
    }

    // Mostrar vista de autenticación
    public function index() {
        $current_page = isset($_GET['page']) ? $_GET['page'] : 'login';
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
        $success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
        
        // Limpiar mensajes después de mostrarlos
        unset($_SESSION['error']);
        unset($_SESSION['success']);

        // Incluir la vista
        if (file_exists('views/AuthView.php')) {
            include 'views/AuthView.php';
        } else {
            die("Error: No se encontró la vista de autenticación");
        }
    }

    // Procesar registro - SIN 2FA
    public function registrar() {
        if (!$this->validarCSRF()) {
            header('Location: index.php?page=register');
            exit;
        }

        try {
            // Validar campos requeridos
            $required_fields = ['nombre', 'usuario', 'contrasena', 'email', 'confirmar_contrasena'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("El campo " . $field . " es requerido");
                }
            }

            // Validar que las contraseñas coincidan
            if ($_POST['contrasena'] !== $_POST['confirmar_contrasena']) {
                throw new Exception("Las contraseñas no coinciden");
            }

            $registrado = $this->usuarioModel->crear(
                trim($_POST['nombre']),
                isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '',
                trim($_POST['usuario']),
                $_POST['contrasena'],
                trim($_POST['email'])
            );

            if ($registrado) {
                $_SESSION['success'] = "¡Registro exitoso! Ya puedes iniciar sesión.";
                header('Location: index.php?page=login');
                exit;
            } else {
                throw new Exception("Error al registrar el usuario");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            
            // Mantener los datos del formulario para no perderlos
            $_SESSION['form_data'] = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'email' => $_POST['email'],
                'usuario' => $_POST['usuario']
            ];
            
            header('Location: index.php?page=register');
            exit;
        }
    }

    // Procesar login - CON 2FA
    public function login() {
        if (!$this->validarCSRF()) {
            header('Location: index.php?page=login');
            exit;
        }

        // Validar campos
        if (empty($_POST['usuario']) || empty($_POST['password'])) {
            $_SESSION['error'] = "Usuario y contraseña son requeridos";
            header('Location: index.php?page=login');
            exit;
        }

        $usuario = $this->usuarioModel->verificarLogin($_POST['usuario'], $_POST['password']);

        if ($usuario) {
            // Generar y enviar código 2FA solo para login
            $codigo2FA = $this->twoFactorModel->generarCodigo2FA($usuario['id']);
            
            if ($codigo2FA) {
              $emailEnviado = $this->emailService->enviarCodigo2FADev($usuario['email'], $usuario['nombre'], $codigo2FA);
                
                if ($emailEnviado) {
                    $_SESSION['usuario_temp'] = $usuario;
                    $_SESSION['success'] = "Se ha enviado un código de verificación a tu email.";
                    header('Location: index.php?page=2fa');
                    exit;
                } else {
                    $_SESSION['error'] = "Error al enviar el código de verificación. Por favor, intenta más tarde.";
                    header('Location: index.php?page=login');
                    exit;
                }
            } else {
                $_SESSION['error'] = "Error al generar el código de verificación";
                header('Location: index.php?page=login');
                exit;
            }
        } else {
            $_SESSION['error'] = "Credenciales incorrectas";
            header('Location: index.php?page=login');
            exit;
        }
    }

    // Verificar código 2FA - Solo para login
public function verificar2FA() {
    if (!$this->validarCSRF()) {
        header('Location: index.php?page=2fa');
        exit;
    }

    if (!isset($_SESSION['usuario_temp'])) {
        $_SESSION['error'] = "Sesión expirada. Por favor, inicia sesión nuevamente.";
        header('Location: index.php?page=login');
        exit;
    }

    if (empty($_POST['codigo'])) {
        $_SESSION['error'] = "Por favor, ingresa el código de verificación";
        header('Location: index.php?page=2fa');
        exit;
    }

    $usuario = $_SESSION['usuario_temp'];
    $codigo = trim($_POST['codigo']);

    // Validar formato del código (6 dígitos)
    if (!preg_match('/^\d{6}$/', $codigo)) {
        $_SESSION['error'] = "El código debe contener exactamente 6 dígitos";
        header('Location: index.php?page=2fa');
        exit;
    }

    if ($this->twoFactorModel->verificarCodigo($usuario['id'], $codigo)) {
        // Autenticación exitosa
        $_SESSION['usuario'] = $usuario;
        unset($_SESSION['usuario_temp']);
        $_SESSION['success'] = "¡Bienvenido, " . $usuario['nombre'] . "!";
        
        // ✅ ACTUALIZADO: Incluir directamente la vista del Home
        require_once 'views/Home.php';
        exit;
    } else {
        $_SESSION['error'] = "Código de verificación incorrecto o expirado";
        header('Location: index.php?page=2fa');
        exit;
    }
}
    // Reenviar código 2FA
    public function reenviarCodigo2FA() {
        if (!isset($_SESSION['usuario_temp'])) {
            $_SESSION['error'] = "Sesión expirada";
            header('Location: index.php?page=login');
            exit;
        }

        $usuario = $_SESSION['usuario_temp'];
        $codigo2FA = $this->twoFactorModel->generarCodigo2FA($usuario['id']);
        
        if ($codigo2FA) {
            $emailEnviado = $this->emailService->enviarCodigo2FA($usuario['email'], $usuario['nombre'], $codigo2FA);
            
            if ($emailEnviado) {
                $_SESSION['success'] = "Se ha enviado un nuevo código de verificación a tu email";
            } else {
                $_SESSION['error'] = "Error al reenviar el código de verificación. Por favor, intenta más tarde.";
            }
        } else {
            $_SESSION['error'] = "Error al generar el código de verificación";
        }
        
        header('Location: index.php?page=2fa');
        exit;
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
?>