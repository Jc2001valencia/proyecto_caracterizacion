<?php

require_once __DIR__ . '/../config/db.php';  // <-- OJO en la barra antes de ..


class UsuarioModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Obtener usuario por email
    public function getByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   public function create($data) {
    try {
        $stmt = $this->conn->prepare(
            "INSERT INTO usuarios (nombre, email, usuario, contrasena) 
            VALUES (:nombre, :email, :usuario, :contrasena)"
        );
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':usuario', $data['usuario']);
        $stmt->bindParam(':contrasena', $data['contrasena']);
        $ok = $stmt->execute();
        return $ok; // true si insertó correctamente
    } catch (PDOException $e) {
        // Guardar el error en el log y devolverlo
        error_log("Error SQL create usuario: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}


    // Actualizar contraseña
    public function updatePassword($email, $passwordHash) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET contrasena = :contrasena WHERE email = :email");
        $stmt->bindParam(':contrasena', $passwordHash);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }
}