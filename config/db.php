<?php
// config/db.php
class Database {
    private $host = 'localhost';
    private $db_name = 'caracterizacion';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username, 
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch(PDOException $exception) {
            error_log("Error de conexión BD: " . $exception->getMessage());
            // En desarrollo, puedes descomentar esto para ver el error:
            // die("Error de conexión: " . $exception->getMessage());
            return null;
        }
        return $this->conn;
    }
}

// Crear instancia y conexión GLOBAL
$database = new Database();
$db = $database->getConnection();

// Verificación silenciosa
if ($db === null) {
    error_log("CRITICAL: No se pudo establecer conexión con la BD");
}
?>