<?php
// test_verificacion_debug.php
session_start();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Verificación Debug</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .step { background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .debug { background: #ffffcc; padding: 10px; border: 1px solid #ccc; }
        code { background: #333; color: #fff; padding: 2px 5px; }
    </style>
</head>
<body>
    <h1>🔍 Test Verificación DEBUG</h1>";

// ============================================
// 1. GENERAR CÓDIGO SI NO EXISTE
// ============================================
if (!isset($_SESSION['codigo_generado'])) {
    $codigo_prueba = rand(100000, 999999);
    $_SESSION['codigo_generado'] = (string)$codigo_prueba; // Forzar string
    
    echo "<div class='step'>
        <h2>✅ Código Generado</h2>
        <p>Código: <strong>" . $_SESSION['codigo_generado'] . "</strong></p>
        <p>Tipo: " . gettype($_SESSION['codigo_generado']) . "</p>
    </div>";
}

// ============================================
// 2. FORMULARIO
// ============================================
echo "<div class='step'>
    <h2>🔐 Formulario de Verificación</h2>
    <p><strong>Usa este código:</strong> " . $_SESSION['codigo_generado'] . "</p>
    <form method='POST'>
        <input type='text' name='codigo' maxlength='6' required 
               placeholder='000000' style='font-size: 20px; padding: 10px;'>
        <button type='submit'>Verificar</button>
    </form>
</div>";

// ============================================
// 3. PROCESAR CON DEBUG DETALLADO
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo_ingresado = $_POST['codigo'];
    $codigo_correcto = $_SESSION['codigo_generado'];
    
    echo "<div class='step'>
        <h2>📊 DEBUG DETALLADO</h2>
        
        <div class='debug'>
            <h3>Valores crudos:</h3>
            <p>Código ingresado: '<strong>" . htmlspecialchars($codigo_ingresado) . "</strong>'</p>
            <p>Código correcto: '<strong>" . htmlspecialchars($codigo_correcto) . "</strong>'</p>
        </div>
        
        <div class='debug'>
            <h3>Tipos de datos:</h3>
            <p>Tipo ingresado: " . gettype($codigo_ingresado) . "</p>
            <p>Tipo correcto: " . gettype($codigo_correcto) . "</p>
        </div>
        
        <div class='debug'>
            <h3>Longitudes:</h3>
            <p>Longitud ingresado: " . strlen($codigo_ingresado) . " caracteres</p>
            <p>Longitud correcto: " . strlen($codigo_correcto) . " caracteres</p>
        </div>
        
        <div class='debug'>
            <h3>Representación hexadecimal:</h3>
            <p>Ingresado (hex): <code>" . bin2hex($codigo_ingresado) . "</code></p>
            <p>Correcto (hex): <code>" . bin2hex($codigo_correcto) . "</code></p>
        </div>
        
        <div class='debug'>
            <h3>Comparaciones:</h3>";
    
    // Todas las comparaciones posibles
    $comparaciones = [
        '== (igualdad)' => $codigo_ingresado == $codigo_correcto,
        '=== (identidad)' => $codigo_ingresado === $codigo_correcto,
        'trim() == trim()' => trim($codigo_ingresado) == trim($codigo_correcto),
        'trim() === trim()' => trim($codigo_ingresado) === trim($codigo_correcto),
        '(int) == (int)' => (int)$codigo_ingresado == (int)$codigo_correcto,
        '(int) === (int)' => (int)$codigo_ingresado === (int)$codigo_correcto,
        'strcmp() === 0' => strcmp($codigo_ingresado, $codigo_correcto) === 0,
        'strcmp(trim()) === 0' => strcmp(trim($codigo_ingresado), trim($codigo_correcto)) === 0,
    ];
    
    foreach ($comparaciones as $nombre => $resultado) {
        echo "<p>" . $nombre . ": " . ($resultado ? "✅ VERDADERO" : "❌ FALSO") . "</p>";
    }
    
    echo "</div>";
    
    // ============================================
    // 4. VERIFICACIÓN FINAL
    // ============================================
    echo "<h3>🎯 Resultado Final:</h3>";
    
    // Usar la comparación más robusta
    if (trim($codigo_ingresado) === trim($codigo_correcto)) {
        echo "<p class='success'>✅✅✅ VERIFICACIÓN EXITOSA (trim + identidad)</p>";
        
        // Simular éxito
        $_SESSION['usuario'] = ['nombre' => 'Test', 'rol_id' => 1];
        unset($_SESSION['codigo_generado']);
        
    } elseif ((int)$codigo_ingresado === (int)$codigo_correcto) {
        echo "<p class='success'>✅✅✅ VERIFICACIÓN EXITOSA (como enteros)</p>";
        
        $_SESSION['usuario'] = ['nombre' => 'Test', 'rol_id' => 1];
        unset($_SESSION['codigo_generado']);
        
    } else {
        echo "<p class='error'>❌❌❌ VERIFICACIÓN FALLIDA</p>";
        echo "<p>Posible problema: espacios invisibles, encoding, o tipos diferentes.</p>";
    }
    
    echo "</div>";
}

// ============================================
// 5. SOLUCIÓN PARA AuthController
// ============================================
echo "<div class='step'>
    <h2>🔧 Solución para AuthController</h2>
    <p>En tu <code>AuthController->verificar2FA()</code>, usa esta comparación:</p>
    <pre>
if ((int)trim(\$codigo_ingresado) === (int)trim(\$codigo_correcto)) {
    // ✅ Código correcto
} else {
    // ❌ Código incorrecto
}
    </pre>
    <p>O mejor aún:</p>
    <pre>
// Convertir a enteros y comparar
if ((int)\$codigo_ingresado === (int)\$_SESSION['codigo_generado']) {
    // ✅ Correcto
}
    </pre>
</div>";

// ============================================
// 6. REINICIAR
// ============================================
echo "<div class='step'>
    <p><a href='test_verificacion_debug.php?new=1'>🔄 Generar nuevo código</a></p>
    <p><a href='test_verificacion_debug.php?clear=1'>🧹 Limpiar sesión</a></p>
</div>";

// Manejar reinicios
if (isset($_GET['new'])) {
    $_SESSION['codigo_generado'] = (string)rand(100000, 999999);
    header('Location: test_verificacion_debug.php');
    exit;
}

if (isset($_GET['clear'])) {
    session_unset();
    header('Location: test_verificacion_debug.php');
    exit;
}

echo "</body></html>";
?>