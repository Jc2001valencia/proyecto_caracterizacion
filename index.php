<?php
// index.php - Punto de entrada

// Definir la ruta base para MVC
define('BASE_PATH', __DIR__);

// Autoload simple de modelos y controladores
spl_autoload_register(function($class){
    if(file_exists(BASE_PATH."/controllers/$class.php")){
        require_once BASE_PATH."/controllers/$class.php";
    } elseif(file_exists(BASE_PATH."/models/$class.php")){
        require_once BASE_PATH."/models/$class.php";
    }
});

// Determinar la página a mostrar
$page = $_GET['page'] ?? 'login';

// Instanciar el controlador de autenticación
$authController = new AuthController();
$authController->mostrar($page);