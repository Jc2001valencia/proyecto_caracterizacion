<?php
// controllers/OrganizacionController.php
require_once __DIR__ . '/../models/OrganizacionModel.php';

class OrganizacionController {
    private $organizacionModel;
    
    public function __construct($db) {
        $this->organizacionModel = new OrganizacionModel($db);
    }
    
    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario'])) {
            header("Location: ../index.php?action=login");
            exit();
        }
        
        $organizacion = $this->organizacionModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        require_once __DIR__ . '/../views/organizacion/index.php';
    }
    
    public function editar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario'])) {
            header("Location: ../index.php?action=login");
            exit();
        }
        
        $organizacion = $this->organizacionModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        require_once __DIR__ . '/../views/organizacion/editar.php';
    }
    
    public function actualizar() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario'])) {
            header("Location: ../index.php?action=login");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id' => $_POST['id'],
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'],
                'pais' => $_POST['pais'],
                'ciudad' => $_POST['ciudad'],
                'direccion' => $_POST['direccion'],
                'descripcion' => $_POST['descripcion']
            ];
            
            $resultado = $this->organizacionModel->actualizar($datos);
            
            if ($resultado) {
                $_SESSION['mensaje'] = "Organización actualizada exitosamente";
            } else {
                $_SESSION['error'] = "Error al actualizar la organización";
            }
            
            header("Location: index.php?controller=organizacion&action=index");
            exit();
        }
    }
}
?>