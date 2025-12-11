<?php
// models/TwoFactorModel.php - VERSIÓN LIMPIA Y FUNCIONAL
class TwoFactorModel {
    private $conn;
    private $table_name = "usuario_codigos_2fa";

    public function __construct($db) {
        $this->conn = $db;
        error_log("✅ TwoFactorModel inicializado");
    }

    /**
     * Guarda código 2FA en la base de datos
     * @param int $usuario_id - ID del usuario
     * @return string - Código generado de 6 dígitos
     */
    public function generarYGuardarCodigo($usuario_id) {
        error_log("🔥 TwoFactorModel::generarYGuardarCodigo($usuario_id)");
        
        // Validar usuario_id
        $usuario_id = (int)$usuario_id;
        if ($usuario_id <= 0) {
            error_log("❌ usuario_id inválido: $usuario_id");
            throw new Exception("ID de usuario inválido");
        }
        
        try {
            // 1. Invalidar códigos anteriores del usuario
            $this->invalidarCodigosAnteriores($usuario_id);
            
            // 2. Generar nuevo código
            $codigo = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            // 3. Insertar en BD
            $query = "INSERT INTO " . $this->table_name . " 
                     (usuario_id, codigo, fecha_expiracion, utilizado, created_at) 
                     VALUES (:usuario_id, :codigo, :fecha_expiracion, 0, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(":codigo", $codigo, PDO::PARAM_STR);
            $stmt->bindValue(":fecha_expiracion", $fecha_expiracion, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $registro_id = $this->conn->lastInsertId();
                error_log("✅ Código guardado: ID=$registro_id, Usuario=$usuario_id, Código=$codigo");
                return $codigo;
            } else {
                error_log("❌ Error al insertar código: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Error al generar código 2FA");
            }
            
        } catch (PDOException $e) {
            error_log("❌ PDOException: " . $e->getMessage());
            throw new Exception("Error de base de datos al generar código 2FA");
        }
    }

    /**
     * Invalida códigos anteriores no utilizados del usuario
     */
    private function invalidarCodigosAnteriores($usuario_id) {
        $query = "UPDATE " . $this->table_name . " 
                 SET utilizado = 1 
                 WHERE usuario_id = :usuario_id 
                 AND utilizado = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $actualizados = $stmt->rowCount();
        if ($actualizados > 0) {
            error_log("🗑️ Invalidados $actualizados código(s) anterior(es)");
        }
    }

    /**
     * Verifica si el código ingresado es válido
     * @param int $usuario_id - ID del usuario
     * @param string $codigo_ingresado - Código de 6 dígitos
     * @return bool - true si es válido, false si no
     */
    public function verificarCodigo($usuario_id, $codigo_ingresado) {
        error_log("🔍 TwoFactorModel::verificarCodigo($usuario_id, $codigo_ingresado)");
        
        // Validar parámetros
        $usuario_id = (int)$usuario_id;
        $codigo_limpio = preg_replace('/[^0-9]/', '', $codigo_ingresado);
        
        if (strlen($codigo_limpio) !== 6) {
            error_log("❌ Código inválido (no tiene 6 dígitos)");
            return false;
        }
        
        try {
            // Buscar código válido (no usado y no expirado)
            $query = "SELECT id, codigo, fecha_expiracion 
                     FROM " . $this->table_name . " 
                     WHERE usuario_id = :usuario_id 
                     AND codigo = :codigo 
                     AND utilizado = 0 
                     AND fecha_expiracion > NOW() 
                     ORDER BY created_at DESC 
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":usuario_id", $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(":codigo", $codigo_limpio, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Marcar como usado
                $this->marcarComoUsado($resultado['id']);
                
                error_log("✅ Código VÁLIDO - Usuario: $usuario_id");
                return true;
            } else {
                error_log("❌ Código NO válido o expirado");
                
                // Verificar si existe pero está expirado
                $this->verificarSiExpirado($usuario_id, $codigo_limpio);
                
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("❌ Error verificando código: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si el código existe pero está expirado (para mejor mensaje de error)
     */
    private function verificarSiExpirado($usuario_id, $codigo) {
        $query = "SELECT fecha_expiracion 
                 FROM " . $this->table_name . " 
                 WHERE usuario_id = :usuario_id 
                 AND codigo = :codigo 
                 AND utilizado = 0 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(":codigo", $codigo, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            error_log("⚠️ El código existe pero está EXPIRADO");
        }
    }

    /**
     * Marca un código como usado
     */
    private function marcarComoUsado($registro_id) {
        $query = "UPDATE " . $this->table_name . " 
                 SET utilizado = 1 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $registro_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            error_log("✅ Código marcado como usado (ID: $registro_id)");
            return true;
        }
        return false;
    }

    /**
     * Limpia códigos expirados o usados (mantenimiento)
     * Puede llamarse con un cron job o al inicio de sesión
     */
    public function limpiarCodigosViejos() {
        error_log("🧹 Limpiando códigos viejos...");
        
        $query = "DELETE FROM " . $this->table_name . " 
                 WHERE fecha_expiracion < NOW() 
                 OR utilizado = 1";
        
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute()) {
            $eliminados = $stmt->rowCount();
            error_log("✅ Eliminados $eliminados código(s) viejo(s)");
            return $eliminados;
        }
        
        return 0;
    }

    /**
     * Obtiene estadísticas de la tabla (útil para debugging)
     */
    public function obtenerEstadisticas() {
        $query = "SELECT 
                     COUNT(*) as total,
                     COUNT(CASE WHEN utilizado = 1 THEN 1 END) as usados,
                     COUNT(CASE WHEN fecha_expiracion < NOW() THEN 1 END) as expirados,
                     COUNT(CASE WHEN utilizado = 0 AND fecha_expiracion > NOW() THEN 1 END) as validos
                 FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("📊 Estadísticas 2FA: " . json_encode($stats));
        
        return $stats;
    }

    /**
     * Obtiene el último código generado para un usuario (solo para debugging)
     */
    public function obtenerUltimoCodigoDebug($usuario_id) {
        if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
            return "Debug deshabilitado";
        }
        
        $query = "SELECT codigo, fecha_expiracion, utilizado 
                 FROM " . $this->table_name . " 
                 WHERE usuario_id = :usuario_id 
                 ORDER BY created_at DESC 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>