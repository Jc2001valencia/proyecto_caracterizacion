<?php
// models/OrganizacionModel.php
class OrganizacionModel {
    private $conn;
    private $table_name = "organizaciones";
    private $table_usuario_organizacion = "usuario_organizacion";
    private $table_roles = "roles";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear($usuario_admin_id, $nombre, $descripcion = '', $telefono = '', 
                         $email = '', $direccion = '') {
        
        try {
            // 1. Crear la organización
            $query = "INSERT INTO " . $this->table_name . " 
                     (nombre, descripcion, telefono, email, direccion, usuario_admin_id, created_at) 
                      VALUES (:nombre, :descripcion, :telefono, :email, :direccion, :usuario_admin_id, NOW())";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":direccion", $direccion);
            $stmt->bindParam(":usuario_admin_id", $usuario_admin_id);

            if (!$stmt->execute()) {
                throw new Exception("Error al crear la organización: " . implode(", ", $stmt->errorInfo()));
            }

            $organizacion_id = $this->conn->lastInsertId();

            // 2. Obtener ID del rol 'admin_org'
            $rol_id = $this->obtenerRolId('admin_org');
            if (!$rol_id) {
                throw new Exception("Error: No se encontró el rol 'admin_org' en la base de datos");
            }

            // 3. Asignar usuario como administrador de la organización
            $query_relacion = "INSERT INTO " . $this->table_usuario_organizacion . " 
                              (usuario_id, organizacion_id, rol_id, creado_en) 
                               VALUES (:usuario_id, :organizacion_id, :rol_id, NOW())";

            $stmt_relacion = $this->conn->prepare($query_relacion);
            $stmt_relacion->bindParam(":usuario_id", $usuario_admin_id);
            $stmt_relacion->bindParam(":organizacion_id", $organizacion_id);
            $stmt_relacion->bindParam(":rol_id", $rol_id);

            if (!$stmt_relacion->execute()) {
                throw new Exception("Error al asignar rol al usuario: " . implode(", ", $stmt_relacion->errorInfo()));
            }

            return $organizacion_id;

        } catch (Exception $e) {
            error_log("Error en OrganizacionModel::crear: " . $e->getMessage());
            throw $e;
        }
    }

    private function obtenerRolId($nombre_rol) {
        $query = "SELECT id FROM " . $this->table_roles . " 
                  WHERE nombre = :nombre LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre_rol);
        
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : false;
        }
        return false;
    }

    public function obtenerPorUsuario($usuario_id) {
        $query = "SELECT o.*, r.nombre as rol, r.id as rol_id
                  FROM " . $this->table_name . " o
                  INNER JOIN " . $this->table_usuario_organizacion . " uo 
                    ON o.id = uo.organizacion_id
                  INNER JOIN " . $this->table_roles . " r 
                    ON uo.rol_id = r.id
                  WHERE uo.usuario_id = :usuario_id
                  ORDER BY o.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>