<?php
// models/UsuarioModel.php
class UsuarioModel {
    private $conn;
    private $table_name = "organizaciones";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear($nombre, $descripcion, $usuario, $contrasena, $email) {
        // Verificar si el usuario o email ya existen
        if ($this->existeUsuario($usuario)) {
            throw new Exception("El nombre de usuario ya está en uso");
        }
        
        if ($this->existeEmail($email)) {
            throw new Exception("El email ya está registrado");
        }

        $query = "INSERT INTO " . $this->table_name . " 
                 (nombre, descripcion, usuario, contrasena, email) 
                  VALUES (:nombre, :descripcion, :usuario, :contrasena, :email)";

        $stmt = $this->conn->prepare($query);

        // Hash de la contraseña
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

        // Bind parameters
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":contrasena", $hashed_password);
        $stmt->bindParam(":email", $email);

        return $stmt->execute();
    }

    public function buscarPorCredencial($credencial) {
        $query = "SELECT id, nombre, descripcion, usuario, contrasena, email, created_at 
                  FROM " . $this->table_name . " 
                  WHERE usuario = :credencial OR email = :credencial 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":credencial", $credencial);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verificarLogin($credencial, $contrasena) {
        $usuario = $this->buscarPorCredencial($credencial);
        
        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            return $usuario;
        }
        return false;
    }

    private function existeUsuario($usuario) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE usuario = :usuario LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function existeEmail($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>