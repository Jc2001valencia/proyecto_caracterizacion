<?php
// verificar_tablas_completo.php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/proyecto_caracterizacion/config/db.php');

echo "<h3>🔍 ANÁLISIS COMPLETO DE LA BASE DE DATOS</h3>";

if (!isset($db) || $db === null) {
    die("❌ Error de conexión a la BD");
}

try {
    // 1. Verificar TODAS las tablas existentes
    $stmt = $db->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h4>📊 TODAS las tablas en la base de datos:</h4>";
    echo "<ul>";
    foreach ($tablas as $tabla) {
        echo "<li><strong>" . htmlspecialchars($tabla) . "</strong></li>";
    }
    echo "</ul>";
    
    // 2. Verificar estructura de CADA tabla
    echo "<h4>🔎 ESTRUCTURA DE CADA TABLA:</h4>";
    
    foreach ($tablas as $tabla) {
        echo "<div style='border: 2px solid #3b82f6; margin: 10px 0; padding: 15px; border-radius: 8px;'>";
        echo "<h5 style='color: #3b82f6;'>📋 Tabla: <strong>" . htmlspecialchars($tabla) . "</strong></h5>";
        
        // Describir la tabla
        $stmt_desc = $db->query("DESCRIBE " . $tabla);
        $columnas = $stmt_desc->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f3f4f6;'>";
        echo "<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
        echo "</tr>";
        
        foreach ($columnas as $columna) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($columna['Field']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($columna['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($columna['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($columna['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($columna['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($columna['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Mostrar algunos datos de ejemplo
        echo "<h6>📝 Datos de ejemplo (primeros 3 registros):</h6>";
        $stmt_data = $db->query("SELECT * FROM " . $tabla . " LIMIT 3");
        $datos = $stmt_data->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($datos) > 0) {
            echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%; background-color: #f9fafb;'>";
            echo "<tr style='background-color: #e5e7eb;'>";
            foreach (array_keys($datos[0]) as $columna) {
                echo "<th>" . htmlspecialchars($columna) . "</th>";
            }
            echo "</tr>";
            
            foreach ($datos as $fila) {
                echo "<tr>";
                foreach ($fila as $valor) {
                    echo "<td>" . htmlspecialchars($valor ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: #6b7280;'>❌ No hay datos en esta tabla</p>";
        }
        
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #fef2f2; color: #dc2626; padding: 15px; border: 1px solid #fecaca; border-radius: 8px;'>";
    echo "<h4>❌ Error en la consulta:</h4>";
    echo "<p><strong>" . $e->getMessage() . "</strong></p>";
    echo "</div>";
}
?>