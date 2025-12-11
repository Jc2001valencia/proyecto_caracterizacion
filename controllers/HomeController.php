<?php
// controllers/HomeController.php
class HomeController {
    public function index() {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['usuario'])) {
            header("Location: ../index.php?action=login");
            exit();
        }
        
        // Incluir la vista home
        require_once __DIR__ . '/../views/home.php';
    }
    
    // Método para obtener proyectos en JSON (tu código original)
    public function obtenerProyectosJSON() {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['error' => 'No autenticado']);
            exit();
        }
        
        // Incluir conexión
        require_once __DIR__ . '/../config/db.php';
        
        if (!isset($db) || $db === null) {
            echo json_encode(['error' => 'Error de conexión']);
            exit();
        }
        
        $sql = "SELECT id, nombre_proyecto, descripcion_proyecto, dominio_cynefin, complejidad_total FROM proyectos ORDER BY id DESC";
        $result = $db->query($sql);
        
        $proyectos = [];
        
        if ($result) {
            while ($fila = $result->fetch(PDO::FETCH_ASSOC)) {
                $proyectos[] = $fila;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($proyectos);
        exit();
    }
}
?>