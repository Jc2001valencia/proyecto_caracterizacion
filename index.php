<?php
// index.php
session_start();

// Incluir configuración de base de datos
require_once 'config/db.php';

// Verificar si la conexión a la base de datos fue exitosa
if (!isset($db) || $db === null) {
    die("Error: No se pudo establecer conexión con la base de datos");
}

// Incluir controlador
require_once 'controllers/AuthController.php';

// Crear controlador de autenticación
try {
    $authController = new AuthController($db);
} catch (Exception $e) {
    die("Error al inicializar el controlador: " . $e->getMessage());
}

// Determinar la acción a realizar
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : 'index');

// Manejar las acciones
switch ($action) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->registrar();
        } else {
            header('Location: index.php?page=register');
        }
        break;
        
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            header('Location: index.php?page=login');
        }
        break;
        
    case 'verify2fa':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->verificar2FA();
        } else {
            header('Location: index.php?page=2fa');
        }
        break;
        
    case 'reenviar2fa':
        $authController->reenviarCodigo2FA();
        break;
        
    case 'logout':
        $authController->logout();
        break;
        
    default:
        $authController->index();
        break;
}
?>