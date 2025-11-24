<?php
require_once "../config/conexion.php"; // tu archivo de conexión a la BD

$sql = "SELECT id, nombre_proyecto, descripcion_proyecto, dominio_cynefin, complejidad_total FROM proyectos ORDER BY id DESC";
$result = $conn->query($sql);

$proyectos = [];

if ($result->num_rows > 0) {
    while ($fila = $result->fetch_assoc()) {
        $proyectos[] = $fila;
    }
}

header('Content-Type: application/json');
echo json_encode($proyectos);
?>