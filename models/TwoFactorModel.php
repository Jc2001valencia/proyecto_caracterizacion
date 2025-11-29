<?php
// models/TwoFactorModel.php
class TwoFactorModel {
    private $conn;
    private $table_name = "usuario_codigos_2fa";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generarCodigo2FA($organizacion_id) {
        // Eliminar códigos anteriores
        $this->eliminarCodigosExpirados($organizacion_id);

        // Generar nuevo código
        $codigo = sprintf("%06d", mt_rand(1, 999999));
        $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $query = "INSERT INTO " . $this->table_name . " 
                  (organizacion_id, codigo, fecha_expiracion) 
                  VALUES (:organizacion_id, :codigo, :fecha_expiracion)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":organizacion_id", $organizacion_id);
        $stmt->bindParam(":codigo", $codigo);
        $stmt->bindParam(":fecha_expiracion", $fecha_expiracion);

        if ($stmt->execute()) {
            return $codigo;
        }
        return false;
    }

    public function verificarCodigo($organizacion_id, $codigo) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE organizacion_id = :organizacion_id 
                  AND codigo = :codigo 
                  AND fecha_expiracion > NOW() 
                  AND utilizado = 0 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":organizacion_id", $organizacion_id);
        $stmt->bindParam(":codigo", $codigo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $this->marcarComoUsado($organizacion_id, $codigo);
            return true;
        }
        return false;
    }

    private function marcarComoUsado($organizacion_id, $codigo) {
        $query = "UPDATE " . $this->table_name . " 
                  SET utilizado = 1 
                  WHERE organizacion_id = :organizacion_id 
                  AND codigo = :codigo";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":organizacion_id", $organizacion_id);
        $stmt->bindParam(":codigo", $codigo);
        $stmt->execute();
    }

    private function eliminarCodigosExpirados($organizacion_id) {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE organizacion_id = :organizacion_id 
                  AND (fecha_expiracion <= NOW() OR utilizado = 1)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":organizacion_id", $organizacion_id);
        $stmt->execute();
    }
}
?>