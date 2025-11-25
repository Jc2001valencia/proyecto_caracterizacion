<?php
class EmailService {
    
    public function enviarEmailVerificacion($email, $nombre, $token) {
        $asunto = "Verifica tu cuenta - Sistema de Gestión";
        $enlace_verificacion = "http://" . $_SERVER['HTTP_HOST'] . "/proyecto_caracterizacion/index.php?action=verificarEmail&token=" . $token;
        
        $mensaje = $this->crearTemplateEmailVerificacion($nombre, $enlace_verificacion);
        return $this->enviarEmail($email, $asunto, $mensaje);
    }
    
    public function enviarCodigo2FA($email, $nombre, $codigo) {
        $asunto = "Código de verificación - Sistema de Gestión";
        $mensaje = $this->crearTemplate2FA($nombre, $codigo);
        return $this->enviarEmail($email, $asunto, $mensaje);
    }
    
    public function enviarEmailRecuperacion($email, $nombre, $token) {
        $asunto = "Recupera tu contraseña - Sistema de Gestión";
        $enlace_recuperacion = "http://" . $_SERVER['HTTP_HOST'] . "/proyecto_caracterizacion/views/restablecer_contrasena.php?token=" . $token;
        
        $mensaje = $this->crearTemplateRecuperacion($nombre, $enlace_recuperacion);
        return $this->enviarEmail($email, $asunto, $mensaje);
    }
    
    private function crearTemplateEmailVerificacion($nombre, $enlace) {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Verifica tu cuenta</h1>
                </div>
                <div class='content'>
                    <h2>Hola $nombre,</h2>
                    <p>Gracias por registrarte en nuestro Sistema de Gestión. Para activar tu cuenta, por favor haz clic en el siguiente enlace:</p>
                    <p style='text-align: center;'>
                        <a href='$enlace' class='button'>Verificar Mi Cuenta</a>
                    </p>
                    <p><strong>Este enlace expirará en 24 horas.</strong></p>
                    <p>Si no puedes hacer clic en el botón, copia y pega esta URL en tu navegador:</p>
                    <p style='word-break: break-all;'>$enlace</p>
                </div>
                <div class='footer'>
                    <p>Si no te registraste en nuestro sistema, por favor ignora este email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private function crearTemplate2FA($nombre, $codigo) {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .code { font-size: 32px; font-weight: bold; text-align: center; letter-spacing: 8px; color: #059669; padding: 20px; background: white; border: 2px dashed #10b981; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Verificación en Dos Pasos</h1>
                </div>
                <div class='content'>
                    <h2>Hola $nombre,</h2>
                    <p>Se ha solicitado iniciar sesión en tu cuenta. Usa el siguiente código para completar la verificación:</p>
                    <div class='code'>$codigo</div>
                    <p><strong>Este código expirará en 10 minutos.</strong></p>
                    <p>Si no reconoces este intento de inicio de sesión, por favor cambia tu contraseña inmediatamente.</p>
                </div>
                <div class='footer'>
                    <p>Por seguridad, no compartas este código con nadie.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private function crearTemplateRecuperacion($nombre, $enlace) {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { display: inline-block; padding: 12px 24px; background: #ef4444; color: white; text-decoration: none; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Recupera tu Contraseña</h1>
                </div>
                <div class='content'>
                    <h2>Hola $nombre,</h2>
                    <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
                    <p style='text-align: center;'>
                        <a href='$enlace' class='button'>Restablecer Contraseña</a>
                    </p>
                    <p><strong>Este enlace expirará en 1 hora.</strong></p>
                    <p>Si no solicitaste restablecer tu contraseña, puedes ignorar este email.</p>
                </div>
                <div class='footer'>
                    <p>Por seguridad, no compartas este enlace con nadie.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private function enviarEmail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Sistema de Gestión <no-reply@" . $_SERVER['HTTP_HOST'] . ">" . "\r\n";
        
        // En entorno de desarrollo, simular envío
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
            error_log("Email simulado enviado a: $to - Asunto: $subject");
            return true;
        }
        
        return mail($to, $subject, $message, $headers);
    }
}
?>