<?php
// config/EmailService.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

class EmailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configurarMailer();
    }

    private function configurarMailer() {
        try {
            // Configuración del servidor SMTP con tus credenciales
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.hostinger.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'mctdtool@transformaeducollab.com';
            $this->mail->Password = 'Atorres2025#';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = 465;
            $this->mail->Timeout = 30;
            $this->mail->SMTPDebug = 2; // Cambiar a 2 para ver detalles del envío
            $this->mail->Debugoutput = 'error_log'; // Enviar debug a error_log
            
            // Configuración adicional de seguridad
            $this->mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Configuración del remitente
            $this->mail->setFrom('mctdtool@transformaeducollab.com', 'Sistema CARACTERIZACION');
            $this->mail->isHTML(true);
            
        } catch (Exception $e) {
            error_log("Error configurando PHPMailer: " . $e->getMessage());
        }
    }

    // Enviar código 2FA
    public function enviarCodigo2FA($email, $nombre, $codigo) {
        try {
            // Limpiar destinatarios anteriores
            $this->mail->clearAddresses();
            
            $this->mail->addAddress($email, $nombre);
            $this->mail->Subject = 'Código de Verificación - Sistema CARACTERIZACION';
            
            $mensaje = $this->crearMensaje2FA($nombre, $codigo);
            
            $this->mail->Body = $mensaje;
            $this->mail->AltBody = "Tu código de verificación es: {$codigo}. Este código expira en 15 minutos.";
            
            $enviado = $this->mail->send();
            
            if ($enviado) {
                error_log("✅ Email enviado exitosamente a: " . $email);
                return true;
            } else {
                error_log("❌ Error al enviar email: " . $this->mail->ErrorInfo);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("❌ Excepción al enviar email: " . $e->getMessage());
            return false;
        }
    }

    private function crearMensaje2FA($nombre, $codigo) {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 20px;
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 0 auto; 
                        background: white;
                        border-radius: 10px;
                        overflow: hidden;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    }
                    .header { 
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                        color: white; 
                        padding: 30px 20px; 
                        text-align: center; 
                    }
                    .content { 
                        padding: 30px; 
                        background: #f9f9f9; 
                    }
                    .code { 
                        font-size: 42px; 
                        font-weight: bold; 
                        text-align: center; 
                        color: #667eea; 
                        margin: 30px 0;
                        letter-spacing: 8px;
                        background: white;
                        padding: 20px;
                        border-radius: 8px;
                        border: 2px dashed #667eea;
                    }
                    .footer { 
                        text-align: center; 
                        padding: 20px; 
                        font-size: 12px; 
                        color: #666; 
                        background: white;
                    }
                    .warning {
                        background: #fff3cd;
                        border: 1px solid #ffeaa7;
                        border-radius: 5px;
                        padding: 15px;
                        margin: 20px 0;
                        color: #856404;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>CARACTERIZACION</h1>
                        <p>Sistema de Gestión de Proyectos</p>
                    </div>
                    <div class='content'>
                        <h2>Hola {$nombre},</h2>
                        <p>Has solicitado un código de verificación para acceder al sistema.</p>
                        
                        <div class='code'>{$codigo}</div>
                        
                        <div class='warning'>
                            <strong>⚠️ Importante:</strong> 
                            <p>Este código es de un solo uso y expirará en 15 minutos.</p>
                            <p>No compartas este código con nadie.</p>
                        </div>
                        
                        <p>Si no has solicitado este código, por favor ignora este mensaje.</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; 2025 Sistema CARACTERIZACION. Todos los derechos reservados.</p>
                        <p>Este es un mensaje automático, por favor no respondas a este email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    // Método para testing - mostrar código en pantalla en desarrollo
    public function enviarCodigo2FADev($email, $nombre, $codigo) {
        // Guardar en sesión para mostrar en pantalla
        session_start();
        $_SESSION['codigo_2fa_debug'] = $codigo;
        $_SESSION['email_destino'] = $email;
        $_SESSION['nombre_destino'] = $nombre;
        
        // También intentar enviar por email real
        $emailEnviado = $this->enviarCodigo2FA($email, $nombre, $codigo);
        
        // Para desarrollo, siempre retornar true
        return true;
    }
}
?>