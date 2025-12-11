<?php
// models/UsuarioModel.php - VERSIÓN CORREGIDA
class UsuarioModel {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
        error_log("UsuarioModel inicializado con conexión: " . gettype($this->conn));
    }

   public function crear($nombre, $apellido, $email, $usuario, $contrasena, $telefono = null, $rol_id = 1) {
    error_log("UsuarioModel::crear() INICIO");
    error_log("Parámetros recibidos:");
    error_log("- nombre: $nombre");
    error_log("- apellido: $apellido");
    error_log("- email: $email");
    error_log("- usuario: $usuario");
    error_log("- telefono: " . ($telefono ?: 'NULL'));
    error_log("- rol_id: " . ($rol_id ?: 'NULL (¡PROBLEMA!)'));

    // 🔥 FORZAR rol_id = 1 SI ESTÁ VACÍO O NULL
    if (empty($rol_id) || $rol_id === null) {
        error_log("⚠️ rol_id está vacío/null, forzando a 1");
        $rol_id = 1;
    }
    
    error_log("✅ rol_id definitivo: $rol_id");

    // Verificar si el usuario o email ya existen
    if ($this->existeUsuario($usuario)) {
        throw new Exception("El nombre de usuario ya está en uso");
    }
    
    if ($this->existeEmail($email)) {
        throw new Exception("El email ya está registrado");
    }

    $query = "INSERT INTO usuarios 
             (nombre, apellido, email, usuario, contrasena, telefono, rol_id, creado_en) 
              VALUES (:nombre, :apellido, :email, :usuario, :contrasena, :telefono, :rol_id, NOW())";

    error_log("Query SQL: " . $query);

    $stmt = $this->conn->prepare($query);

    // Hash de la contraseña
    $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
    error_log("Contraseña hasheada generada");

    // Bind parameters
    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":apellido", $apellido);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":usuario", $usuario);
    $stmt->bindParam(":contrasena", $hashed_password);
    $stmt->bindParam(":telefono", $telefono);
    
    // 🔥 Asegurar que rol_id sea un entero
    $rol_id_int = (int)$rol_id;
    $stmt->bindParam(":rol_id", $rol_id_int, PDO::PARAM_INT);
    error_log("Binding rol_id como INT: $rol_id_int");

    try {
        $resultado = $stmt->execute();
        error_log("Resultado execute(): " . ($resultado ? 'TRUE' : 'FALSE'));
        
        if ($resultado) {
            $last_id = $this->conn->lastInsertId();
            error_log("✅✅✅ ÉXITO: Usuario creado con ID: $last_id, rol_id: $rol_id_int");
            return $last_id;
        } else {
            $error_info = $stmt->errorInfo();
            error_log("❌ Error en execute(): " . print_r($error_info, true));
            return false;
        }
    } catch (PDOException $e) {
        error_log("❌❌❌ PDO Exception CRÍTICA: " . $e->getMessage());
        error_log("Error info completo: " . print_r($stmt->errorInfo(), true));
        throw new Exception("Error de base de datos: " . $e->getMessage());
    }
}

    public function buscarPorEmail($email) {
    error_log("UsuarioModel::buscarPorEmail('$email')");
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("❌ Email inválido: $email");
        return false;
    }
    
    $query = "SELECT id, nombre, apellido, email, usuario, contrasena, telefono, rol_id,
                     esta_borrado, creado_en 
              FROM " . $this->table_name . " 
              WHERE email = :email AND esta_borrado = 0 
              LIMIT 1";

    error_log("Query: $query");

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":email", $email);
    
    if ($stmt->execute()) {
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Resultado búsqueda: " . ($resultado ? 'ENCONTRADO' : 'NO ENCONTRADO'));
        return $resultado;
    }
    
    error_log("Error en execute buscarPorEmail");
    return false;
}

public function verificarLogin($email, $contrasena) {
    error_log("🔍 UsuarioModel::verificarLogin() - Email: $email");
    
    // Validar que sea un email (no username)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("❌ No es un email válido: $email");
        return false;
    }
    
    try {
        // Buscar SOLO por email (no por username)
        $query = "SELECT id, nombre, apellido, email, usuario, contrasena, telefono, rol_id,
                         esta_borrado, creado_en 
                  FROM " . $this->table_name . " 
                  WHERE email = :email AND esta_borrado = 0 
                  LIMIT 1";

        error_log("Query (solo email): $query");

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        
        if ($stmt->execute()) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                error_log("✅ Usuario encontrado - ID: " . $usuario['id']);
                
                // Verificar contraseña
                if (password_verify($contrasena, $usuario['contrasena'])) {
                    error_log("✅✅ Contraseña correcta");
                    
                    // Devolver datos básicos (sin contraseña)
                    unset($usuario['contrasena']);
                    return $usuario;
                } else {
                    error_log("❌ Contraseña incorrecta");
                }
            } else {
                error_log("❌ No existe usuario con email: $email");
            }
        }
    } catch (Exception $e) {
        error_log("❌ Error en verificarLogin: " . $e->getMessage());
    }
    
    return false;
}
    private function existeUsuario($usuario) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE usuario = :usuario AND esta_borrado = 0 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();
        $count = $stmt->rowCount();
        error_log("existeUsuario('$usuario'): " . ($count > 0 ? 'SI' : 'NO'));
        return $count > 0;
    }

    private function existeEmail($email) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE email = :email AND esta_borrado = 0 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $count = $stmt->rowCount();
        error_log("existeEmail('$email'): " . ($count > 0 ? 'SI' : 'NO'));
        return $count > 0;
    }

    public function actualizarRol($usuario_id, $rol_id) {
        error_log("UsuarioModel::actualizarRol($usuario_id, $rol_id)");
        
        $query = "UPDATE " . $this->table_name . " 
                  SET rol_id = :rol_id 
                  WHERE id = :usuario_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rol_id", $rol_id);
        $stmt->bindParam(":usuario_id", $usuario_id);

        $result = $stmt->execute();
        error_log("Actualizar rol result: " . ($result ? 'OK' : 'FAIL'));
        
        if (!$result) {
            error_log("Error: " . print_r($stmt->errorInfo(), true));
        }
        
        return $result;
    }
}
?>