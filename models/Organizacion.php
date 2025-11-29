<?php
require_once __DIR__ . '/../config/db.php';

class Organizacion {
    private $conn;
    private $table_name = "organizaciones";

    public $id;
    public $nombre;
    public $descripcion;
    public $usuario;
    public $contrasena;
    public $email;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function login($usuario, $password) {
        echo "🔍 [LOGIN] INICIANDO<br>";
        echo "🔍 [LOGIN] Parámetros recibidos:<br>";
        echo "🔍 [LOGIN] - usuario: '" . $usuario . "'<br>";
        echo "🔍 [LOGIN] - password: '" . $password . "'<br>";
        
        try {
            // 🔥 QUERY CORREGIDA - usar parámetros diferentes
            $query = "SELECT id, nombre, descripcion, usuario, contrasena, email, created_at 
                      FROM " . $this->table_name . " 
                      WHERE usuario = :usuario_param OR email = :email_param 
                      LIMIT 1";
            
            echo "🔍 [QUERY] Preparando: " . $query . "<br>";
            echo "🔍 [QUERY] Parámetros: :usuario_param = '" . $usuario . "', :email_param = '" . $usuario . "'<br>";
            
            $stmt = $this->conn->prepare($query);
            
            // 🔥 BINDEAR PARÁMETROS DIFERENTES
            $stmt->bindParam(':usuario_param', $usuario);
            $stmt->bindParam(':email_param', $usuario);
            
            echo "🔍 [QUERY] Ejecutando...<br>";
            $stmt->execute();
            
            $rowCount = $stmt->rowCount();
            echo "🔍 [QUERY] Resultado: " . $rowCount . " filas encontradas<br>";
            
            if ($rowCount == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo "🔍 [BD] Datos encontrados:<br>";
                echo "🔍 [BD] - ID: " . $row['id'] . "<br>";
                echo "🔍 [BD] - Usuario: '" . $row['usuario'] . "'<br>";
                echo "🔍 [BD] - Email: '" . $row['email'] . "'<br>";
                echo "🔍 [BD] - Contraseña: '" . $row['contrasena'] . "'<br>";
                
                // COMPARACIÓN
                echo "🔍 [COMPARACIÓN] Iniciando...<br>";
                $password_md5 = md5($password);
                echo "🔍 [COMPARACIÓN] MD5 del password: '" . $password_md5 . "'<br>";
                echo "🔍 [COMPARACIÓN] Contraseña BD: '" . $row['contrasena'] . "'<br>";
                
                $coincide = ($password_md5 === $row['contrasena']);
                echo "🔍 [COMPARACIÓN] ¿Coinciden? " . ($coincide ? '✅ SÍ' : '❌ NO') . "<br>";
                
                if ($coincide) {
                    echo "🔍 [LOGIN] Asignando propiedades...<br>";
                    $this->id = $row['id'];
                    $this->nombre = $row['nombre'];
                    $this->descripcion = $row['descripcion'];
                    $this->usuario = $row['usuario'];
                    $this->email = $row['email'];
                    $this->contrasena = $row['contrasena'];
                    $this->created_at = $row['created_at'];
                    
                    echo "🔍 [LOGIN] 🎉 Retornando TRUE<br>";
                    return true;
                } else {
                    echo "🔍 [LOGIN] ❌ Contraseñas no coinciden<br>";
                }
            } else {
                echo "🔍 [LOGIN] ❌ Usuario/email no encontrado<br>";
            }
            
            return false;
            
        } catch (PDOException $e) {
            echo "🔍 [ERROR] Excepción: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    public function registrar($datos) {
        echo "🔍 [REGISTRO] INICIANDO<br>";
        
        try {
            $query = "INSERT INTO organizaciones 
                     (nombre, descripcion, usuario, contrasena, email) 
                     VALUES (:nombre, :descripcion, :usuario, :contrasena, :email)";

            echo "🔍 [QUERY] Preparando: " . $query . "<br>";
            
            $stmt = $this->conn->prepare($query);
            
            $hashed_password = md5($datos['contrasena']);
            echo "🔍 [REGISTRO] MD5 generado: '" . $hashed_password . "'<br>";
            
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':usuario', $datos['usuario']);
            $stmt->bindParam(':contrasena', $hashed_password);
            $stmt->bindParam(':email', $datos['email']);

            echo "🔍 [QUERY] Ejecutando INSERT...<br>";
            $resultado = $stmt->execute();
            
            if ($resultado) {
                $this->id = $this->conn->lastInsertId();
                echo "🔍 [REGISTRO] ✅ EXITOSO - ID: " . $this->id . "<br>";
            } else {
                echo "🔍 [REGISTRO] ❌ FALLIDO<br>";
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            echo "🔍 [ERROR] Excepción: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    // ... otros métodos igual
}
?>