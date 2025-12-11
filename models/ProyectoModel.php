<?php
require_once __DIR__ . '/../config/db.php';

class ProyectoModel {
    private $conn;

    public function __construct() {
        global $db; // Acceder a la variable global $db
        
        if ($db === null) {
            throw new Exception("No se pudo establecer conexión con la base de datos");
        }
        
        $this->conn = $db; // Asignar la conexión global
    }

    public function guardarProyecto($data) {
        $sql = "INSERT INTO proyectos 
                (nombre, pais_cliente, tamano_estimado, dominio_problema, descripcion, equipo, factores, complejidad_total)
                VALUES 
                (:nombre, :pais_cliente, :tamano_estimado, :dominio_problema, :descripcion, :equipo, :factores, :complejidad_total)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':pais_cliente' => $data['pais_cliente'],
            ':tamano_estimado' => $data['tamano_estimado'],
            ':dominio_problema' => $data['dominio_problema'],
            ':descripcion' => $data['descripcion'],
            ':equipo' => json_encode($data['equipo'], JSON_UNESCAPED_UNICODE),
            ':factores' => json_encode($data['factores'], JSON_UNESCAPED_UNICODE),
            ':complejidad_total' => $data['complejidad_total']
        ]);

        return $this->conn->lastInsertId();
    }

    public function obtenerProyectoPorId($id) {
        $sql = "SELECT * FROM proyectos WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($proyecto) {
            $proyecto['equipo'] = json_decode($proyecto['equipo'], true);
            $proyecto['factores'] = json_decode($proyecto['factores'], true);
        }

        return $proyecto;
    }
}
?>