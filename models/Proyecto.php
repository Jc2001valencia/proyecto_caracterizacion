<?php
// ========================================
// MODELS/Proyecto.php
// ========================================

class Proyecto {
    private $conn;
    private $table = 'proyectos';

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREAR PROYECTO
    public function crear($datos) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (nombre, descripcion, horas, estado, lider_proyecto_id, fecha_inicio, fecha_fin, created_at) 
                     VALUES (:nombre, :descripcion, :horas, :estado, :lider_id, :fecha_inicio, :fecha_fin, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':horas', $datos['horas']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':lider_id', $datos['lider_proyecto_id']);
            $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error crear proyecto: " . $e->getMessage());
            return false;
        }
    }

    // OBTENER PROYECTO POR ID
    public function obtenerPorId($id) {
        try {
            $query = "SELECT p.*, 
                      CONCAT(u.nombre, ' ', u.apellido) as nombre_lider,
                      u.email as email_lider
                      FROM " . $this->table . " p
                      LEFT JOIN usuarios u ON p.lider_proyecto_id = u.id
                      WHERE p.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obtener proyecto: " . $e->getMessage());
            return false;
        }
    }

    // OBTENER TODOS LOS PROYECTOS
    public function obtenerTodos() {
        try {
            $query = "SELECT p.id, p.nombre AS nombre_proyecto, p.descripcion AS descripcion_proyecto,
                      COALESCE(p.horas, 0) as horas, COALESCE(p.estado, 'pendiente') as estado,
                      p.fecha_inicio, p.fecha_fin, p.lider_proyecto_id, p.created_at,
                      CONCAT(u.nombre, ' ', u.apellido) AS nombre_lider
                      FROM " . $this->table . " p
                      LEFT JOIN usuarios u ON p.lider_proyecto_id = u.id
                      ORDER BY p.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obtener proyectos: " . $e->getMessage());
            return [];
        }
    }

    // ACTUALIZAR PROYECTO
    public function actualizar($id, $datos) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET nombre = :nombre,
                         descripcion = :descripcion,
                         horas = :horas,
                         estado = :estado,
                         lider_proyecto_id = :lider_id,
                         fecha_inicio = :fecha_inicio,
                         fecha_fin = :fecha_fin
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':horas', $datos['horas']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':lider_id', $datos['lider_proyecto_id']);
            $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizar proyecto: " . $e->getMessage());
            return false;
        }
    }

    // ELIMINAR PROYECTO
    public function eliminar($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error eliminar proyecto: " . $e->getMessage());
            return false;
        }
    }

    // CONTAR PROYECTOS POR ESTADO
    public function contarPorEstado() {
        try {
            $query = "SELECT estado, COUNT(*) as total 
                     FROM " . $this->table . " 
                     GROUP BY estado";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $resultado = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $resultado[$row['estado']] = $row['total'];
            }
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error contar proyectos: " . $e->getMessage());
            return [];
        }
    }
}
?>