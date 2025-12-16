<?php
// ========================================
// CONTROLLERS/LiderController.php - ACTUALIZADO
// CRUD COMPLETO DE LÍDERES CON ORGANIZACION_ID
// ========================================

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php?action=login_view');
    exit;
}

require_once __DIR__ . '/../config/db.php';

class LiderController {
    private $db;
    private $smtp_config = [
        'host' => 'smtp.hostinger.com',
        'username' => 'mctdtool@transformaeducollab.com',
        'password' => 'Atorres2025#',
        'from_email' => 'mctdtool@transformaeducollab.com',
        'from_name' => 'Sistema de Caracterización'
    ];
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // ===== NUEVO: OBTENER ORGANIZACIÓN DEL ADMIN ACTUAL =====
    private function obtenerOrganizacionAdmin() {
        try {
            if (!isset($_SESSION['usuario_id'])) {
                return null;
            }
            
            $admin_id = $_SESSION['usuario_id'];
            
            // Buscar la organización donde este usuario es admin
            $stmt = $this->db->prepare("
                SELECT id, nombre 
                FROM organizaciones 
                WHERE usuario_admin_id = ?
                LIMIT 1
            ");
            $stmt->execute([$admin_id]);
            $organizacion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $organizacion;
            
        } catch (Exception $e) {
            error_log("Error al obtener organización: " . $e->getMessage());
            return null;
        }
    }
    
    // ===== ENVIAR EMAIL CON CREDENCIALES =====
    private function enviarCredencialesPorEmail($email, $nombre, $apellido, $usuario, $contrasena_texto) {
        // Obtener información de la organización para el email
        $organizacion = $this->obtenerOrganizacionAdmin();
        $org_nombre = $organizacion['nombre'] ?? 'Sistema de Caracterización';
        
        $nombre_completo = $nombre . ' ' . $apellido;
        $asunto = "🔐 Credenciales de Acceso - " . htmlspecialchars($org_nombre);
        
        $mensaje = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .credentials { background: white; padding: 20px; border-left: 4px solid #667eea; margin: 20px 0; }
                .credential-label { font-weight: bold; color: #667eea; margin-bottom: 5px; }
                .credential-value { background: #e8eaf6; padding: 10px 15px; border-radius: 5px; font-family: monospace; font-size: 16px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; background: white; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0; color: #856404; }
                .org-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 ¡Bienvenido a " . htmlspecialchars($org_nombre) . "!</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>{$nombre_completo}</strong>,</p>
                    <p>Has sido registrado como <strong>Líder de Proyecto</strong> en <strong>" . htmlspecialchars($org_nombre) . "</strong>.</p>
                    
                    <div class='org-info'>
                        <p><strong>🏢 Organización:</strong> " . htmlspecialchars($org_nombre) . "</p>
                        <p><strong>👤 Rol:</strong> Líder de Proyecto</p>
                    </div>
                    
                    <div class='credentials'>
                        <p><span class='credential-label'>👤 Usuario:</span><br><span class='credential-value'>{$usuario}</span></p>
                        <p><span class='credential-label'>🔑 Contraseña:</span><br><span class='credential-value'>{$contrasena_texto}</span></p>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ IMPORTANTE:</strong>
                        <ul>
                            <li>Guarda estas credenciales en un lugar seguro</li>
                            <li>Te recomendamos cambiar tu contraseña al iniciar sesión</li>
                            <li>No compartas tus credenciales con nadie</li>
                        </ul>
                    </div>
                    
                    <p>Accede al sistema en: http://localhost/proyecto_caracterizacion/</p>
                </div>
                <div class='footer'>
                    <p>Sistema de Caracterización - Framework Cynefin</p>
                    <p>&copy; " . date('Y') . " Transforma EducaCollab</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Headers para email HTML
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $this->smtp_config['from_name'] . " <" . $this->smtp_config['from_email'] . ">\r\n";
        $headers .= "Reply-To: " . $this->smtp_config['from_email'] . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "X-Priority: 1\r\n";
        
        // Log para depuración
        error_log("Intentando enviar credenciales a: {$email}");
        
        // Intentar enviar el email
        $enviado = mail($email, $asunto, $mensaje, $headers);
        
        // Log del resultado
        error_log("Resultado envio credenciales a {$email}: " . ($enviado ? "ÉXITO" : "FALLO"));
        
        return $enviado;
    }
    
    // ===== CREAR LÍDER (ACTUALIZADO CON ORGANIZACION_ID) =====
    public function crear() {
        try {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $usuario = trim($_POST['usuario'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            
            // Validaciones
            if (empty($nombre)) {
                throw new Exception('El nombre es obligatorio');
            }
            
            if (empty($apellido)) {
                throw new Exception('El apellido es obligatorio');
            }
            
            if (empty($email)) {
                throw new Exception('El email es obligatorio');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El email no es válido');
            }
            
            if (empty($usuario)) {
                throw new Exception('El nombre de usuario es obligatorio');
            }
            
            if (empty($contrasena)) {
                throw new Exception('La contraseña es obligatoria');
            }
            
            if (strlen($contrasena) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }
            
            // 🔴 NUEVO: Obtener organización del admin que está creando
            $organizacion = $this->obtenerOrganizacionAdmin();
            
            if (!$organizacion || !isset($organizacion['id'])) {
                throw new Exception('No se pudo identificar la organización. Debes ser administrador de una organización para crear líderes.');
            }
            
            $organizacion_id = $organizacion['id'];
            $organizacion_nombre = $organizacion['nombre'];
            
            // Verificar que el email no exista
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('El email ya está registrado');
            }
            
            // Verificar que el usuario no exista
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE usuario = ? LIMIT 1");
            $stmt->execute([$usuario]);
            if ($stmt->fetch()) {
                throw new Exception('El nombre de usuario ya está registrado');
            }
            
            // Guardar contraseña en texto plano para el email
            $contrasena_texto = $contrasena;
            
            // Encriptar contraseña para la base de datos
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            
            // 🔴 MODIFICADO: Insertar usuario con organizacion_id
            $sql = "INSERT INTO usuarios (nombre, apellido, email, telefono, usuario, contrasena, rol_id, organizacion_id, creado_en) 
                    VALUES (?, ?, ?, ?, ?, ?, 2, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $nombre,
                $apellido,
                $email,
                $telefono,
                $usuario,
                $contrasena_hash,
                $organizacion_id  // 🔴 NUEVO PARÁMETRO
            ]);
            
            if ($result) {
                $lider_id = $this->db->lastInsertId();
                
                // ENVIAR CREDENCIALES POR EMAIL
                $email_enviado = $this->enviarCredencialesPorEmail($email, $nombre, $apellido, $usuario, $contrasena_texto);
                
                if ($email_enviado) {
                    $_SESSION['success'] = "✅ Líder '{$nombre} {$apellido}' creado exitosamente en {$organizacion_nombre}<br>📧 Credenciales enviadas a: {$email}";
                } else {
                    // Guardar credenciales en sesión
                    $_SESSION['credenciales_lider'] = [
                        'id' => $lider_id,
                        'nombre' => $nombre . ' ' . $apellido,
                        'email' => $email,
                        'usuario' => $usuario,
                        'contrasena' => $contrasena_texto,
                        'organizacion' => $organizacion_nombre,
                        'fecha' => date('d/m/Y H:i:s')
                    ];
                    
                    $_SESSION['success'] = "✅ Líder '{$nombre} {$apellido}' creado exitosamente en {$organizacion_nombre}";
                }
                
                // Log
                error_log("NUEVO LÍDER: {$nombre} {$apellido} creado en organización {$organizacion_nombre} (ID: {$organizacion_id})");
                
            } else {
                throw new Exception('Error al crear el líder en la base de datos');
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "❌ Error: " . $e->getMessage();
        }
        
        header('Location: ../views/home.php?seccion=lideres');
        exit;
    }
    
    // ===== VER LÍDER =====
    public function ver() {
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // 🔴 MODIFICADO: Incluir información de la organización
            $stmt = $this->db->prepare("
                SELECT u.id, u.nombre, u.apellido, u.email, u.usuario, u.telefono, 
                       u.creado_en, u.activo, u.organizacion_id,
                       o.nombre as organizacion_nombre,
                       COUNT(p.id) as proyectos_count
                FROM usuarios u 
                LEFT JOIN organizaciones o ON u.organizacion_id = o.id
                LEFT JOIN proyectos p ON u.id = p.lider_proyecto_id
                WHERE u.id = ? AND u.rol_id = 2
                GROUP BY u.id
            ");
            $stmt->execute([$id]);
            $lider = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($lider) {
                echo '
                <div class="space-y-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-3xl text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">' . htmlspecialchars($lider['nombre'] . ' ' . $lider['apellido']) . '</h3>
                            <p class="text-gray-600">Líder de Proyecto</p>';
                
                // 🔴 NUEVO: Mostrar organización
                if (!empty($lider['organizacion_nombre'])) {
                    echo '<p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-building mr-1"></i>
                            ' . htmlspecialchars($lider['organizacion_nombre']) . '
                          </p>';
                }
                
                echo '</div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 font-medium">Email</p>
                            <p class="text-gray-800 font-semibold">' . htmlspecialchars($lider['email']) . '</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 font-medium">Teléfono</p>
                            <p class="text-gray-800 font-semibold">' . ($lider['telefono'] ? htmlspecialchars($lider['telefono']) : 'No disponible') . '</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 font-medium">Usuario</p>
                            <p class="text-gray-800 font-semibold">' . htmlspecialchars($lider['usuario']) . '</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 font-medium">Estado</p>
                            <p class="text-gray-800 font-semibold">
                                <span class="px-3 py-1 rounded-full text-sm ' . ($lider['activo'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . '">
                                    ' . ($lider['activo'] == 1 ? 'Activo' : 'Inactivo') . '
                                </span>
                            </p>
                        </div>
                    </div>';
                
                // 🔴 NUEVO: Mostrar organización si existe
                if (!empty($lider['organizacion_nombre'])) {
                    echo '<div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-500 font-medium mb-2">Organización</p>
                            <p class="text-gray-800 font-semibold">' . htmlspecialchars($lider['organizacion_nombre']) . '</p>
                          </div>';
                }
                
                echo '<div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 font-medium mb-2">Proyectos Asignados</p>
                        <p class="text-gray-800">' . $lider['proyectos_count'] . ' proyecto(s)</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button onclick="editarLider(' . $lider['id'] . ')" class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 font-medium transition">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </button>
                        <button onclick="closeModal(\'modalVerLider\')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                            <i class="fas fa-times mr-2"></i>Cerrar
                        </button>
                    </div>
                </div>';
            } else {
                echo '<div class="text-center py-12">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                        <p class="text-gray-600 font-medium">Líder no encontrado</p>
                    </div>';
            }
        }
    }
    
    // ===== EDITAR LÍDER =====
    public function editar() {
        try {
            $id = (int)($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $contrasena_nueva = trim($_POST['contrasena'] ?? '');
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de líder inválido');
            }
            
            if (empty($nombre)) {
                throw new Exception('El nombre es obligatorio');
            }
            
            if (empty($apellido)) {
                throw new Exception('El apellido es obligatorio');
            }
            
            if (empty($email)) {
                throw new Exception('El email es obligatorio');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El email no es válido');
            }
            
            // Obtener usuario actual
            $stmt = $this->db->prepare("SELECT usuario, organizacion_id FROM usuarios WHERE id = ? AND rol_id = 2 LIMIT 1");
            $stmt->execute([$id]);
            $lider_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$lider_existente) {
                throw new Exception('Líder no encontrado');
            }
            
            $usuario = $lider_existente['usuario'];
            $organizacion_id = $lider_existente['organizacion_id'];
            
            // Verificar que el email no esté usado por otro usuario
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ? LIMIT 1");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                throw new Exception('El email ya está registrado por otro usuario');
            }
            
            // Actualizar líder
            if (!empty($contrasena_nueva)) {
                if (strlen($contrasena_nueva) < 6) {
                    throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
                }
                
                $contrasena_hash = password_hash($contrasena_nueva, PASSWORD_DEFAULT);
                
                $sql = "UPDATE usuarios 
                        SET nombre = ?, apellido = ?, email = ?, telefono = ?, contrasena = ?
                        WHERE id = ? AND rol_id = 2";
                
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([
                    $nombre,
                    $apellido,
                    $email,
                    $telefono,
                    $contrasena_hash,
                    $id
                ]);
                
                // Enviar email con nueva contraseña
                $email_enviado = $this->enviarCredencialesPorEmail($email, $nombre, $apellido, $usuario, $contrasena_nueva);
                
                if (!$email_enviado) {
                    // Si falla el email, guardar en sesión
                    $_SESSION['credenciales_lider'] = [
                        'nombre' => $nombre . ' ' . $apellido,
                        'email' => $email,
                        'usuario' => $usuario,
                        'contrasena' => $contrasena_nueva,
                        'fecha' => date('d/m/Y H:i:s'),
                        'actualizacion' => true
                    ];
                }
                
            } else {
                $sql = "UPDATE usuarios 
                        SET nombre = ?, apellido = ?, email = ?, telefono = ?
                        WHERE id = ? AND rol_id = 2";
                
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([
                    $nombre,
                    $apellido,
                    $email,
                    $telefono,
                    $id
                ]);
            }
            
            if ($result) {
                $_SESSION['success'] = "✅ Líder '{$nombre} {$apellido}' actualizado exitosamente";
            } else {
                throw new Exception('Error al actualizar el líder');
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "❌ Error: " . $e->getMessage();
        }
        
        header('Location: ../views/home.php?seccion=lideres');
        exit;
    }
    
    // ===== ELIMINAR LÍDER =====
    public function eliminar() {
        try {
            $id = (int)($_GET['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('ID de líder inválido');
            }
            
            // Obtener datos antes de eliminar
            $stmt = $this->db->prepare("SELECT nombre, apellido FROM usuarios WHERE id = ? AND rol_id = 2 LIMIT 1");
            $stmt->execute([$id]);
            $lider = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$lider) {
                throw new Exception('Líder no encontrado');
            }
            
            $nombre_completo = $lider['nombre'] . ' ' . $lider['apellido'];
            
            // Verificar si tiene proyectos asignados
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM proyectos WHERE lider_proyecto_id = ?");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                throw new Exception("No se puede eliminar. El líder tiene {$resultado['total']} proyecto(s) asignado(s)");
            }
            
            // Eliminar líder
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ? AND rol_id = 2");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                $_SESSION['success'] = "✅ Líder '{$nombre_completo}' eliminado exitosamente";
            } else {
                throw new Exception('Error al eliminar el líder');
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "❌ Error: " . $e->getMessage();
        }
        
        header('Location: ../views/home.php?seccion=lideres');
        exit;
    }
    
    // ===== LISTAR LÍDERES =====
    public function listar() {
        try {
            // Verificar si existe columna esta_borrado
            $stmt = $this->db->query("SHOW COLUMNS FROM usuarios LIKE 'esta_borrado'");
            $tiene_esta_borrado = $stmt->rowCount() > 0;
            
            $sql = "SELECT id, nombre, apellido, email, usuario, telefono, creado_en 
                    FROM usuarios 
                    WHERE rol_id = 2";
            
            if ($tiene_esta_borrado) {
                $sql .= " AND esta_borrado = 0";
            }
            
            $sql .= " ORDER BY nombre";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "❌ Error al listar líderes: " . $e->getMessage();
            return [];
        }
    }
    
    // ===== OBTENER UN LÍDER =====
    public function obtener($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, nombre, apellido, email, usuario, telefono, creado_en
                FROM usuarios
                WHERE id = ? AND rol_id = 2
                LIMIT 1
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $_SESSION['error'] = "❌ Error al obtener líder: " . $e->getMessage();
            return null;
        }
    }
    
    // ===== LIMPIAR CREDENCIALES DE SESIÓN =====
    public function limpiar_credenciales() {
        unset($_SESSION['credenciales_lider']);
        echo 'OK';
    }
}

// ===== ENRUTADOR =====
$action = $_GET['action'] ?? $_POST['action'] ?? 'listar';
$controller = new LiderController();

switch ($action) {
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->crear();
        }
        break;
        
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->editar();
        }
        break;
        
    case 'eliminar':
        $controller->eliminar();
        break;
        
    case 'ver':
        $controller->ver();
        break;
        
    case 'limpiar_credenciales':
        $controller->limpiar_credenciales();
        break;
        
    case 'listar':
    default:
        header('Location: ../views/home.php?seccion=lideres');
        exit;
}