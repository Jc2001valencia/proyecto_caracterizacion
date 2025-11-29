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
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
            return null;
        }
        return $this->conn;
    }
}

// Crear instancia y conexión
$database = new Database();
$db = $database->getConnection();

// Verificar conexión
if (!$db) {
    die("Error: No se pudo conectar a la base de datos");
}
?>