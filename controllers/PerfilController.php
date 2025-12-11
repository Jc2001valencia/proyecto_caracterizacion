<?php
// controllers/PerfilController.php
require_once __DIR__ . '/../models/PerfilModel.php';

class PerfilController {
    private $perfilModel;
    
    public function __construct($db) {
        $this->perfilModel = new PerfilModel($db);
    }
    
    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario'])) {
            header("Location: ../index.php?action=login");
            exit();
        }
        
        $perfiles = $this->perfilModel->obtenerTodos();
        require_once __DIR__ . '/../views/perfiles/index.php';
    }
    
    // ... otros métodos para CRUD de perfiles
}
?>