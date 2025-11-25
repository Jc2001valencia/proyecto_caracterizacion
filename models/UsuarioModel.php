<?php
// models/UsuarioModel.php

require_once __DIR__ . '/../config/db.php';

class UsuarioModel {
    private $db;
    private $table = "usuarios";

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Tus métodos de usuario aquí
    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function createUser($data) {
        $query = "INSERT INTO " . $this->table . " (nombre, email, password) VALUES (:nombre, :email, :password)";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute($data);
    }

    // ... otros métodos de UsuarioModel
}
?>