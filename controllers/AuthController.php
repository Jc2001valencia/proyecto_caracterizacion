<?php
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Capturar acción desde GET o POST
        $action = $_GET['action'] ?? $_POST['action'] ?? null;
        if ($action && method_exists($this, $action)) {
            $this->$action();
        }
    }

    public function mostrar($page = 'auth')
    {
        $viewPath = __DIR__ . '/../views/AuthView.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Vista no encontrada: $viewPath";
        }
    }

public function login()
{
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['contrasena'] ?? '';
    $user  = $this->usuarioModel->getByEmail($email);

    if ($user && password_verify($pass, $user['contrasena'])) {
        $_SESSION['temp_user'] = $user;
        $_SESSION['2fa_code']  = rand(100000, 999999);
        
        // Mostrar código en consola (dev)
        error_log("Código 2FA para $email: " . $_SESSION['2fa_code']);

        // También lo puedes enviar en el json para pruebas
        echo json_encode([
            'success' => true,
            'message' => 'Código 2FA enviado',
            'codigo' => $_SESSION['2fa_code'] // <--- solo para prueba
        ]);
        return;
    }

    echo json_encode(['success' => false, 'message' => 'Email o contraseña incorrectos']);
}


 public function register()
{
    $data = $_POST;

    // Validar campos obligatorios
    if(empty($data['nombre']) || empty($data['email']) || empty($data['usuario']) || empty($data['contrasena'])) {
        echo json_encode(['success'=>false,'message'=>'Todos los campos son obligatorios']);
        return;
    }

    // Verificar si el email ya existe
    $existing = $this->usuarioModel->getByEmail($data['email']);
    if($existing) {
        echo json_encode(['success'=>false,'message'=>'Email ya registrado']);
        return;
    }

    // Hashear contraseña
    $data['contrasena'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);

    // Crear usuario y manejar errores PDO
    $result = $this->usuarioModel->create($data);
    if($result === true) {
        echo json_encode(['success'=>true,'message'=>'Registro exitoso']);
    } else {
        // Si $result devuelve un array con error, mostrarlo
        $msg = is_array($result) && isset($result['error']) ? $result['error'] : 'No se pudo crear el usuario';
        echo json_encode(['success'=>false,'message'=>$msg]);
    }
}


public function verificar2FA(){
    $codigo = $_POST['codigo_2fa'] ?? '';
    if(isset($_SESSION['2fa_code']) && $_SESSION['2fa_code'] == $codigo){
        $_SESSION['user'] = $_SESSION['temp_user'];
        unset($_SESSION['temp_user'], $_SESSION['2fa_code']);
        echo json_encode(['success'=>true,'message'=>'Login exitoso','redirect'=>'views/home.php']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Código incorrecto']);
    }
}



    public function recuperar()
    {
        $email = $_POST['email'] ?? '';
        $user  = $this->usuarioModel->getByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(16));
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;

            $resetLink = "http://localhost/proyecto_caracterizacion/reset.php?token=$token";
            mail($email, 'Recuperar contraseña', "Enlace: $resetLink");
        }

        echo json_encode(['success' => true, 'message' => 'Si el email existe, se envió enlace']);
    }
}

new AuthController();