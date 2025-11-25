<?php
// config/EmailService.php

// CARGAR PHPMailer MANUALMENTE
$phpmailerPath = __DIR__ . '/../vendor/phpmailer/phpmailer/src/';

// Verificar si los archivos existen
if (!file_exists($phpmailerPath . 'PHPMailer.php')) {
    error_log("❌ ERROR: No se encuentra PHPMailer.php en: " . $phpmailerPath);
    throw new Exception("PHPMailer no está instalado correctamente");
}

// Incluir archivos manualmente
require_once $phpmailerPath . 'PHPMailer.php';
require_once $phpmailerPath . 'SMTP.php';
require_once $phpmailerPath . 'Exception.php';

// Usar las clases
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    
    private $mail;
    
    public function __construct() {
        try {
            $this->mail = new PHPMailer(true);
            $this->configurarMailer();
        } catch (Exception $e) {
            error_log("Error creando PHPMailer: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function configurarMailer() {
        try {
            // CONFIGURACIÓN SMTP
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.hostinger.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'mctdtool@transformaeducollab.com';
            $this->mail->Password = 'Atorres2025#';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = 465;
            $this->mail->Timeout = 30;
            $this->mail->SMTPDebug = 0;
            
            // Configuración de seguridad
            $this->mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Configurar remitente
            $this->mail->setFrom('mctdtool@transformaeducollab.com', 'Sistema MCTD');
            $this->mail->addReplyTo('mctdtool@transformaeducollab.com', 'Sistema MCTD');
            $this->mail->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Error configurando PHPMailer: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function enviarCodigo2FA($email, $nombre, $codigo) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $nombre);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Código de Verificación - Sistema MCTD';
            $this->mail->Body = $this->generarCodigo2FAHTML($nombre, $codigo);
            $this->mail->AltBody = $this->generarCodigo2FATexto($nombre, $codigo);
            
            $resultado = $this->mail->send();
            error_log("📧 Intento de envío 2FA a $email: " . ($resultado ? 'ÉXITO' : 'FALLO'));
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("❌ Error enviando código 2FA a $email: " . $e->getMessage());
            return false;
        }
    }
    
    // Método para generar el contenido del email 2FA
    private function generarCodigo2FAHTML($nombre, $codigo) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { background: #2c5282; color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; }
                .code { background: #2d3748; color: white; padding: 20px; font-size: 32px; font-weight: bold; text-align: center; letter-spacing: 8px; border-radius: 8px; margin: 25px 0; font-family: 'Courier New', monospace; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Código de Verificación</h1>
                </div>
                <div class='content'>
                    <h2>Hola $nombre,</h2>
                    <p>Se ha solicitado un inicio de sesión en tu cuenta del <strong>Sistema MCTD</strong>.</p>
                    <p>Usa el siguiente código para completar la verificación:</p>
                    
                    <div class='code'>$codigo</div>
                    
                    <p><strong>⚠️ Este código expirará en 10 minutos.</strong></p>
                    <p>Si no intentaste iniciar sesión, por favor ignora este mensaje.</p>
                </div>
                <div class='footer'>
                    <p>Sistema MCTD - Transformación Digital</p>
                    <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private function generarCodigo2FATexto($nombre, $codigo) {
        return "Código de Verificación - Sistema MCTD

Hola $nombre,

Se ha solicitado un inicio de sesión en tu cuenta del Sistema MCTD.

Usa el siguiente código para completar la verificación:

CÓDIGO: $codigo

⚠️ Este código expirará en 10 minutos.

Si no intentaste iniciar sesión, por favor ignora este mensaje.

--
Sistema MCTD - Transformación Digital
Este es un mensaje automático, por favor no respondas a este correo.
        ";
    }
}
?>