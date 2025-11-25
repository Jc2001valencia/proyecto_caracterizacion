<?php
// autoload.php

spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/';
    
    // Mapeo de directorios
    $directories = [
        'models/',
        'controllers/', 
        'core/',
        'config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $baseDir . $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});