<?php
// ========================================
// MODELS/Proyecto.php - CORREGIDO
// ========================================

class Proyecto {
    private $conn;
    private $table_name = "proyectos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear($datos) {
        try {
            // ===== VALIDACIÓN CRÍTICA: verificar que organizacion_id y usuario_id NO sean NULL =====
            if (!isset($datos['organizacion_id']) || empty($datos['organizacion_id']) || $datos['organizacion_id'] <= 0) {
                error_log("ERROR CRÍTICO en Proyecto::crear - organizacion_id es NULL o inválido");
                error_log("Datos recibidos: " . print_r($datos, true));
                return false;
            }
            
            if (!isset($datos['usuario_id']) || empty($datos['usuario_id']) || $datos['usuario_id'] <= 0) {
                error_log("ERROR CRÍTICO en Proyecto::crear - usuario_id es NULL o inválido");
                error_log("Datos recibidos: " . print_r($datos, true));
                return false;
            }

            // Log para debug
            error_log("Proyecto::crear - Creando con organizacion_id: {$datos['organizacion_id']}, usuario_id: {$datos['usuario_id']}");

            $query = "INSERT INTO " . $this->table_name . " 
                     (nombre, descripcion, horas, estado, lider_proyecto_id, 
                      organizacion_id, usuario_id, fecha_inicio, fecha_fin, created_at) 
                     VALUES 
                     (:nombre, :descripcion, :horas, :estado, :lider_proyecto_id, 
                      :organizacion_id, :usuario_id, :fecha_inicio, :fecha_fin, NOW())";

            $stmt = $this->conn->prepare($query);

            // Sanitizar datos
            $nombre = htmlspecialchars(strip_tags($datos['nombre']));
            $descripcion = htmlspecialchars(strip_tags($datos['descripcion']));
            $horas = intval($datos['horas']);
            $estado = htmlspecialchars(strip_tags($datos['estado']));
            $lider_proyecto_id = intval($datos['lider_proyecto_id']);
            $organizacion_id = intval($datos['organizacion_id']);
            $usuario_id = intval($datos['usuario_id']);
            $fecha_inicio = !empty($datos['fecha_inicio']) ? $datos['fecha_inicio'] : null;
            $fecha_fin = !empty($datos['fecha_fin']) ? $datos['fecha_fin'] : null;

            // Bind de parámetros
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':horas', $horas, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':lider_proyecto_id', $lider_proyecto_id, PDO::PARAM_INT);
            $stmt->bindParam(':organizacion_id', $organizacion_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);

            // Log del SQL antes de ejecutar
            error_log("SQL a ejecutar: " . $query);
            error_log("Parámetros: nombre=$nombre, org_id=$organizacion_id, user_id=$usuario_id");

            if ($stmt->execute()) {
                $insert_id = $this->conn->lastInsertId();
                error_log("Proyecto creado exitosamente con ID: {$insert_id}");
                return true;
            } else {
                error_log("Error al ejecutar INSERT: " . print_r($stmt->errorInfo(), true));
                return false;
            }

        } catch (PDOException $e) {
            error_log("Error en Proyecto::crear - " . $e->getMessage());
            error_log("Datos que causaron el error: " . print_r($datos, true));
            return false;
        }
    }

    public function obtenerTodos($organizacion_id = null) {
        try {
            $query = "SELECT p.*, 
                      CONCAT(u.nombre, ' ', u.apellido) as nombre_lider,
                      u.email as email_lider
                      FROM " . $this->table_name . " p
                      LEFT JOIN usuarios u ON p.lider_proyecto_id = u.id";
            
            if ($organizacion_id) {
                $query .= " WHERE p.organizacion_id = :organizacion_id";
            }
            
            $query .= " ORDER BY p.created_at DESC";

            $stmt = $this->conn->prepare($query);
            
            if ($organizacion_id) {
                $stmt->bindParam(':organizacion_id', $organizacion_id, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorId($id) {
        try {
            $query = "SELECT p.*, 
                      CONCAT(u.nombre, ' ', u.apellido) as nombre_lider,
                      u.email as email_lider
                      FROM " . $this->table_name . " p
                      LEFT JOIN usuarios u ON p.lider_proyecto_id = u.id
                      WHERE p.id = :id
                      LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    public function actualizar($id, $datos) {
        try {
            // ===== VALIDACIÓN: mantener organizacion_id y usuario_id =====
            if (!isset($datos['organizacion_id']) || $datos['organizacion_id'] <= 0) {
                error_log("ERROR en Proyecto::actualizar - organizacion_id inválido");
                return false;
            }

            $query = "UPDATE " . $this->table_name . " 
                     SET nombre = :nombre,
                         descripcion = :descripcion,
                         horas = :horas,
                         estado = :estado,
                         lider_proyecto_id = :lider_proyecto_id,
                         organizacion_id = :organizacion_id,
                         usuario_id = :usuario_id,
                         fecha_inicio = :fecha_inicio,
                         fecha_fin = :fecha_fin
                     WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Sanitizar datos
            $nombre = htmlspecialchars(strip_tags($datos['nombre']));
            $descripcion = htmlspecialchars(strip_tags($datos['descripcion']));
            $horas = intval($datos['horas']);
            $estado = htmlspecialchars(strip_tags($datos['estado']));
            $lider_proyecto_id = intval($datos['lider_proyecto_id']);
            $organizacion_id = intval($datos['organizacion_id']);
            $usuario_id = intval($datos['usuario_id']);
            $fecha_inicio = !empty($datos['fecha_inicio']) ? $datos['fecha_inicio'] : null;
            $fecha_fin = !empty($datos['fecha_fin']) ? $datos['fecha_fin'] : null;

            // Bind
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':horas', $horas, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':lider_proyecto_id', $lider_proyecto_id, PDO::PARAM_INT);
            $stmt->bindParam(':organizacion_id', $organizacion_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);

            if ($stmt->execute()) {
                error_log("Proyecto ID {$id} actualizado exitosamente");
                return true;
            } else {
                error_log("Error al actualizar proyecto: " . print_r($stmt->errorInfo(), true));
                return false;
            }

        } catch (PDOException $e) {
            error_log("Error en Proyecto::actualizar - " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                error_log("Proyecto ID {$id} eliminado exitosamente");
                return true;
            }
            
            return false;

        } catch (PDOException $e) {
            error_log("Error en Proyecto::eliminar - " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorOrganizacion($organizacion_id) {
        return $this->obtenerTodos($organizacion_id);
    }
}
?>