<?php
// config/routes.php

$routes = [
    // Páginas principales
    'GET' => [
        '/' => 'AuthController@mostrarLogin',
        '/login' => 'AuthController@mostrarLogin',
        '/register' => 'AuthController@mostrarRegister',
        '/home' => 'HomeController@index',
        '/logout' => 'AuthController@logout'
    ],
    
    // Acciones POST (API)
    'POST' => [
        '/auth/login' => 'AuthController@login',
        '/auth/register' => 'AuthController@register',
        '/auth/verify-2fa' => 'AuthController@verify2fa',
        '/auth/reenviarCodigo2FA' => 'AuthController@reenviarCodigo2FA',
        '/auth/solicitarRecuperacion' => 'AuthController@solicitarRecuperacion'
    ]
];

function findRoute($method, $path) {
    global $routes;
    
    if (isset($routes[$method][$path])) {
        return $routes[$method][$path];
    }
    
    return null;
}
?>