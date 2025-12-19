<?php
/**
 * Modelo de Comentarios y Ratings
 * Archivo: models/Comentarios.php
 * 
 * Maneja todas las operaciones relacionadas con comentarios y valoraciones
 * de las estrategias de caracterización de proyectos
 */

class Comentarios {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Crear tablas necesarias si no existen
     */
    public function crearTablas() {
        try {
            // Tabla de comentarios
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS estrategias_comentarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    proyecto_id INT NOT NULL,
                    estrategia_nombre VARCHAR(255) NOT NULL,
                    comentario TEXT NOT NULL,
                    rating INT DEFAULT NULL CHECK (rating >= 1 AND rating <= 5),
                    usuario_id INT DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    
                    INDEX idx_proyecto (proyecto_id),
                    INDEX idx_estrategia (estrategia_nombre),
                    INDEX idx_fecha (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
            // Tabla de ratings
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS estrategias_ratings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    proyecto_id INT NOT NULL,
                    estrategia_nombre VARCHAR(255) NOT NULL,
                    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                    usuario_id INT DEFAULT NULL,
                    ip_address VARCHAR(45) DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    
                    INDEX idx_proyecto_estrategia (proyecto_id, estrategia_nombre),
                    INDEX idx_rating (rating),
                    
                    UNIQUE KEY unique_rating (proyecto_id, estrategia_nombre, usuario_id, ip_address)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            
            return ['success' => true, 'message' => 'Tablas creadas correctamente'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al crear tablas: ' . $e->getMessage()];
        }
    }
    
    /**
     * Guardar un nuevo comentario
     */
    public function guardarComentario($proyecto_id, $estrategia_nombre, $comentario, $rating = null, $usuario_id = null) {
        try {
            // Validaciones
            if (empty($comentario)) {
                return ['success' => false, 'message' => 'El comentario no puede estar vacío'];
            }
            
            if (strlen($comentario) > 5000) {
                return ['success' => false, 'message' => 'El comentario es demasiado largo (máximo 5000 caracteres)'];
            }
            
            if ($rating !== null && ($rating < 1 || $rating > 5)) {
                return ['success' => false, 'message' => 'Rating inválido (debe ser entre 1 y 5)'];
            }
            
            // Insertar comentario
            $stmt = $this->db->prepare("
                INSERT INTO estrategias_comentarios 
                (proyecto_id, estrategia_nombre, comentario, rating, usuario_id) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $proyecto_id,
                $estrategia_nombre,
                $comentario,
                $rating,
                $usuario_id
            ]);
            
            $comentario_id = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'message' => 'Comentario guardado exitosamente',
                'data' => [
                    'id' => $comentario_id,
                    'proyecto_id' => $proyecto_id,
                    'estrategia_nombre' => $estrategia_nombre,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            
        } catch (PDOException $e) {
            error_log("Error al guardar comentario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al guardar el comentario'];
        }
    }
    
    /**
     * Guardar o actualizar un rating
     */
    public function guardarRating($proyecto_id, $estrategia_nombre, $rating, $usuario_id = null, $ip_address = null) {
        try {
            // Validaciones
            if ($rating < 1 || $rating > 5) {
                return ['success' => false, 'message' => 'Rating inválido (debe ser entre 1 y 5)'];
            }
            
            // Verificar si ya existe un rating
            $stmt = $this->db->prepare("
                SELECT id FROM estrategias_ratings 
                WHERE proyecto_id = ? 
                AND estrategia_nombre = ? 
                AND (usuario_id = ? OR ip_address = ?)
            ");
            $stmt->execute([$proyecto_id, $estrategia_nombre, $usuario_id, $ip_address]);
            
            if ($stmt->fetch()) {
                // Actualizar rating existente
                $stmt = $this->db->prepare("
                    UPDATE estrategias_ratings 
                    SET rating = ?
                    WHERE proyecto_id = ? 
                    AND estrategia_nombre = ? 
                    AND (usuario_id = ? OR ip_address = ?)
                ");
                $stmt->execute([$rating, $proyecto_id, $estrategia_nombre, $usuario_id, $ip_address]);
                $accion = 'actualizado';
            } else {
                // Insertar nuevo rating
                $stmt = $this->db->prepare("
                    INSERT INTO estrategias_ratings 
                    (proyecto_id, estrategia_nombre, rating, usuario_id, ip_address) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$proyecto_id, $estrategia_nombre, $rating, $usuario_id, $ip_address]);
                $accion = 'guardado';
            }
            
            // Obtener estadísticas actualizadas
            $estadisticas = $this->obtenerEstadisticasRating($proyecto_id, $estrategia_nombre);
            
            return [
                'success' => true,
                'message' => "Rating $accion exitosamente",
                'data' => [
                    'rating' => $rating,
                    'estadisticas' => $estadisticas
                ]
            ];
            
        } catch (PDOException $e) {
            error_log("Error al guardar rating: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al guardar la valoración'];
        }
    }
    
    /**
     * Obtener comentarios de una estrategia o proyecto
     */
    public function obtenerComentarios($proyecto_id, $estrategia_nombre = null) {
        try {
            $sql = "
                SELECT 
                    ec.id,
                    ec.proyecto_id,
                    ec.estrategia_nombre,
                    ec.comentario,
                    ec.rating,
                    ec.created_at,
                    ec.updated_at,
                    COALESCE(u.nombre, 'Anónimo') as usuario_nombre
                FROM estrategias_comentarios ec
                LEFT JOIN usuarios u ON ec.usuario_id = u.id
                WHERE ec.proyecto_id = ?
            ";
            
            $params = [$proyecto_id];
            
            if ($estrategia_nombre) {
                $sql .= " AND ec.estrategia_nombre = ?";
                $params[] = $estrategia_nombre;
            }
            
            $sql .= " ORDER BY ec.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Formatear fechas
            foreach ($comentarios as &$comentario) {
                $comentario['created_at_formatted'] = date('d/m/Y H:i', strtotime($comentario['created_at']));
            }
            
            // Obtener estadísticas si se especifica una estrategia
            $estadisticas = null;
            if ($estrategia_nombre) {
                $estadisticas = $this->obtenerEstadisticasRating($proyecto_id, $estrategia_nombre);
            }
            
            return [
                'success' => true,
                'data' => [
                    'comentarios' => $comentarios,
                    'total' => count($comentarios),
                    'estadisticas' => $estadisticas
                ]
            ];
            
        } catch (PDOException $e) {
            error_log("Error al obtener comentarios: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al obtener los comentarios'];
        }
    }
    
    /**
     * Obtener estadísticas de ratings de una estrategia
     */
    public function obtenerEstadisticasRating($proyecto_id, $estrategia_nombre) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_valoraciones,
                    AVG(rating) as rating_promedio,
                    MAX(rating) as rating_maximo,
                    MIN(rating) as rating_minimo,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as estrellas_5,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as estrellas_4,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as estrellas_3,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as estrellas_2,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as estrellas_1
                FROM estrategias_ratings
                WHERE proyecto_id = ? AND estrategia_nombre = ?
            ");
            
            $stmt->execute([$proyecto_id, $estrategia_nombre]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stats) {
                $stats['rating_promedio'] = round((float)$stats['rating_promedio'], 2);
                $stats['total_valoraciones'] = (int)$stats['total_valoraciones'];
            }
            
            return $stats;
            
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Eliminar un comentario
     */
    public function eliminarComentario($comentario_id, $usuario_id = null) {
        try {
            $sql = "DELETE FROM estrategias_comentarios WHERE id = ?";
            $params = [$comentario_id];
            
            // Si se proporciona usuario_id, verificar que sea el dueño
            if ($usuario_id !== null) {
                $sql .= " AND usuario_id = ?";
                $params[] = $usuario_id;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Comentario eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontró el comentario o no tienes permisos'];
            }
            
        } catch (PDOException $e) {
            error_log("Error al eliminar comentario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar el comentario'];
        }
    }
    
    /**
     * Obtener estadísticas generales del proyecto
     */
    public function obtenerEstadisticasProyecto($proyecto_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(DISTINCT ec.estrategia_nombre) as estrategias_comentadas,
                    COUNT(ec.id) as total_comentarios,
                    COUNT(DISTINCT er.estrategia_nombre) as estrategias_valoradas,
                    COUNT(er.id) as total_valoraciones,
                    AVG(er.rating) as rating_promedio_general
                FROM estrategias_comentarios ec
                LEFT JOIN estrategias_ratings er ON ec.proyecto_id = er.proyecto_id
                WHERE ec.proyecto_id = ?
            ");
            
            $stmt->execute([$proyecto_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stats) {
                $stats['rating_promedio_general'] = round((float)$stats['rating_promedio_general'], 2);
            }
            
            return [
                'success' => true,
                'data' => $stats
            ];
            
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas del proyecto: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al obtener estadísticas'];
        }
    }
    
    /**
     * Verificar si las tablas existen
     */
    public function verificarTablas() {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'estrategias_comentarios'");
            $comentarios_existe = $stmt->rowCount() > 0;
            
            $stmt = $this->db->query("SHOW TABLES LIKE 'estrategias_ratings'");
            $ratings_existe = $stmt->rowCount() > 0;
            
            return [
                'comentarios' => $comentarios_existe,
                'ratings' => $ratings_existe,
                'todas_existen' => $comentarios_existe && $ratings_existe
            ];
            
        } catch (PDOException $e) {
            return [
                'comentarios' => false,
                'ratings' => false,
                'todas_existen' => false
            ];
        }
    }
}