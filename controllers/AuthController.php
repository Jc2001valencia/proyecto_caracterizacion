<?php
// controllers/AuthController.php - VERSIÓN CORREGIDA Y FUNCIONAL
// Función de logging a archivo
function auth_log($mensaje, $tipo = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $log_line = "[$timestamp] [AUTH-$tipo] $mensaje\n";
    file_put_contents('auth_debug.log', $log_line, FILE_APPEND);
    error_log($mensaje); // También al log normal
}

auth_log("=== 🔥 AUTH CONTROLLER CARGADO 🔥 ===");

// Cargar modelos con rutas ABSOLUTAS
$base_dir = dirname(__DIR__);
$ds = DIRECTORY_SEPARATOR;

error_log("📁 Base directory: $base_dir");

// 1. Cargar UsuarioModel
$usuarioModelPath = $base_dir . $ds . 'models' . $ds . 'UsuarioModel.php';
if (file_exists($usuarioModelPath)) {
    require_once $usuarioModelPath;
    error_log("✅ UsuarioModel cargado desde: $usuarioModelPath");
} else {
    error_log("❌ ERROR: No se encuentra $usuarioModelPath");
    die("ERROR: No se encuentra UsuarioModel.php");
}

// 2. Cargar TwoFactorModel (SI EXISTE)
$twoFactorPath = $base_dir . $ds . 'models' . $ds . 'TwoFactorModel.php';
if (file_exists($twoFactorPath)) {
    require_once $twoFactorPath;
    error_log("✅ TwoFactorModel cargado desde: $twoFactorPath");
} else {
    error_log("⚠️ TwoFactorModel no encontrado en: $twoFactorPath");
    // Crear clase dummy para testing
    class TwoFactorModel {
        private $conn;
        public function __construct($db) {
            $this->conn = $db;
            error_log("📱 TwoFactorModel dummy creado");
        }
        public function generarCodigo2FA($usuario_id) {
            $codigo = sprintf("%06d", rand(100000, 999999));
            error_log("🔢 Código 2FA generado (dummy): $codigo para usuario $usuario_id");
            
            // Guardar en sesión para testing
            $_SESSION['codigo_2fa_generado'] = $codigo;
            $_SESSION['usuario_2fa_id'] = $usuario_id;
            
            return $codigo;
        }
        public function verificarCodigo($usuario_id, $codigo) {
            $codigo_correcto = $_SESSION['codigo_2fa_generado'] ?? '';
            $usuario_correcto = $_SESSION['usuario_2fa_id'] ?? 0;
            
            $resultado = ($codigo === $codigo_correcto && $usuario_id == $usuario_correcto);
            error_log("🔍 Verificación 2FA: código '$codigo' vs '$codigo_correcto', usuario $usuario_id vs $usuario_correcto = " . ($resultado ? 'OK' : 'FAIL'));
            
            if ($resultado) {
                unset($_SESSION['codigo_2fa_generado']);
                unset($_SESSION['usuario_2fa_id']);
            }
            
            return $resultado;
        }
    }
    error_log("📱 Clase TwoFactorModel dummy creada");
}

// 3. Cargar EmailService (SI EXISTE)
$emailServicePath = $base_dir . $ds . 'config' . $ds . 'EmailService.php';
if (file_exists($emailServicePath)) {
    require_once $emailServicePath;
    error_log("✅ EmailService cargado desde: $emailServicePath");
} else {
    error_log("⚠️ EmailService no encontrado en: $emailServicePath");
    // Crear clase dummy para testing
    class EmailService {
        public function enviarCodigo2FA($email, $nombre, $codigo) {
            error_log("📧 EMAIL SIMULADO 2FA:");
            error_log("   Para: $nombre <$email>");
            error_log("   Código: $codigo");
            error_log("   ⚠️ En producción esto se enviaría por email real");
            
            // Guardar en sesión para mostrar en pantalla
            $_SESSION['email_2fa_simulado'] = [
                'email' => $email,
                'nombre' => $nombre,
                'codigo' => $codigo,
                'fecha' => date('Y-m-d H:i:s')
            ];
            
            return true; // Siempre éxito en testing
        }
    }
    error_log("📱 Clase EmailService dummy creada");
}

class AuthController {
    private $usuarioModel;
    private $twoFactorModel;
    private $emailService;
    private $db;

    public function __construct($db) {
        error_log("🔄 === AuthController::__construct() ===");
        
        // Iniciar sesión SIEMPRE
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        error_log("🔓 Sesión iniciada, ID: " . session_id());
        
        $this->db = $db;
        
        try {
            if (!$db) {
                throw new Exception("La conexión a BD es NULL");
            }
            
            // Inicializar UsuarioModel
            $this->usuarioModel = new UsuarioModel($db);
            error_log("✅ UsuarioModel inicializado");
            
            // Inicializar TwoFactorModel (dummy o real)
            $this->twoFactorModel = new TwoFactorModel($db);
            error_log("✅ TwoFactorModel inicializado (" . get_class($this->twoFactorModel) . ")");
            
            // Inicializar EmailService (dummy o real)
            $this->emailService = new EmailService();
            error_log("✅ EmailService inicializado (" . get_class($this->emailService) . ")");
            
        } catch (Exception $e) {
            error_log("❌ ERROR inicializando AuthController: " . $e->getMessage());
            throw $e;
        }
        
        // Generar token CSRF si no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            error_log("🔐 CSRF token generado: " . substr($_SESSION['csrf_token'], 0, 10) . "...");
        }
        
        error_log("✅✅ AuthController construido exitosamente");
    }

    private function validarCSRF() {
        $token_post = $_POST['csrf_token'] ?? '';
        $token_session = $_SESSION['csrf_token'] ?? '';
        
        if (empty($token_post) || empty($token_session) || $token_post !== $token_session) {
            error_log("❌ CSRF FAIL: POST='$token_post', SESSION='$token_session'");
            $_SESSION['error'] = "Token de seguridad inválido o expirado";
            return false;
        }
        
        error_log("✅ CSRF OK");
        return true;
    }
    
    private function redirect($url) {
        error_log("🔀 REDIRIGIENDO A: $url");
        
        // Limpiar cualquier buffer de salida
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        
        header("Location: $url");
        exit(); // 🔥 ESTO ES CRÍTICO - DETIENE LA EJECUCIÓN
    }

    public function registrar() {
        error_log("=== AuthController::registrar() ===");
        
        if (!$this->validarCSRF()) {
            $this->redirect('index.php?page=register');
        }

        try {
            if (isset($_POST['paso']) && $_POST['paso'] === '2') {
                error_log("📌 PASO 2 - Crear organización");
                return $this->registrarPaso2();
            }
            
            error_log("📌 PASO 1 - Registrar usuario");
            return $this->registrarPaso1();
            
        } catch (Exception $e) {
            error_log("❌ EXCEPCIÓN en registrar: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            
            $page = isset($_POST['paso']) && $_POST['paso'] === '2' ? 'crear-organizacion' : 'register';
            $this->redirect("index.php?page=$page");
        }
    }

    private function registrarPaso1() {
        error_log("=== registrarPaso1() ===");
        
        $required_fields = ['nombre', 'apellido', 'usuario', 'contrasena', 'email', 'confirmar_contrasena'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("El campo '$field' es requerido");
            }
        }

        if ($_POST['contrasena'] !== $_POST['confirmar_contrasena']) {
            throw new Exception("Las contraseñas no coinciden");
        }

        $rol_id = 1;
        error_log("📌 rol_id: $rol_id");

        $usuario_id = $this->usuarioModel->crear(
            trim($_POST['nombre']),
            trim($_POST['apellido']),
            trim($_POST['email']),
            trim($_POST['usuario']),
            $_POST['contrasena'],
            isset($_POST['telefono']) ? trim($_POST['telefono']) : null,
            $rol_id
        );

        if (!$usuario_id) {
            throw new Exception("Error al registrar el usuario en la base de datos");
        }

        error_log("✅✅ Usuario creado con ID: $usuario_id");

        $_SESSION['usuario_registrado'] = [
            'id' => $usuario_id,
            'nombre' => trim($_POST['nombre']),
            'apellido' => trim($_POST['apellido']),
            'email' => trim($_POST['email']),
            'usuario' => trim($_POST['usuario']),
            'rol_id' => $rol_id
        ];

        $_SESSION['success'] = "¡Usuario registrado exitosamente! Ahora crea tu organización.";
        $this->redirect('index.php?page=crear-organizacion');
    }

    private function registrarPaso2() {
        error_log("=== registrarPaso2() ===");
        
        if (!isset($_SESSION['usuario_registrado'])) {
            throw new Exception("Debes registrar un usuario primero");
        }

        if (empty($_POST['nombre_organizacion'])) {
            throw new Exception("El nombre de la organización es requerido");
        }

        $usuario_id = $_SESSION['usuario_registrado']['id'];
        error_log("👤 Usuario ID: $usuario_id");
        
        $this->db->beginTransaction();
        
        try {
            // 1. Obtener rol AdminOrg
            $query_rol = "SELECT id FROM roles WHERE nombre = 'AdminOrg' LIMIT 1";
            $stmt_rol = $this->db->prepare($query_rol);
            $stmt_rol->execute();
            $rol = $stmt_rol->fetch(PDO::FETCH_ASSOC);

            if (!$rol) {
                throw new Exception("No se encontró el rol 'AdminOrg'");
            }

            $rol_id = $rol['id'];
            error_log("✅ Rol AdminOrg ID: $rol_id");

            // 2. Actualizar usuario con rol
            $result_update = $this->usuarioModel->actualizarRol($usuario_id, $rol_id);
            if (!$result_update) {
                throw new Exception("Error al asignar rol AdminOrg");
            }

            // 3. Crear organización
            $query_org = "INSERT INTO organizaciones 
                         (nombre, descripcion, telefono, email, direccion, usuario_admin_id, created_at) 
                          VALUES (:nombre, :descripcion, :telefono, :email, :direccion, :usuario_admin_id, NOW())";

            $stmt_org = $this->db->prepare($query_org);
            
            $stmt_org->bindParam(":nombre", trim($_POST['nombre_organizacion']));
            $stmt_org->bindParam(":descripcion", trim($_POST['descripcion_organizacion'] ?? ''));
            $stmt_org->bindParam(":telefono", trim($_POST['telefono_organizacion'] ?? ''));
            $stmt_org->bindParam(":email", trim($_POST['email_organizacion'] ?? ''));
            $stmt_org->bindParam(":direccion", trim($_POST['direccion_organizacion'] ?? ''));
            $stmt_org->bindParam(":usuario_admin_id", $usuario_id, PDO::PARAM_INT);

            if (!$stmt_org->execute()) {
                $error = $stmt_org->errorInfo();
                throw new Exception("Error al crear organización: " . $error[2]);
            }

            $this->db->commit();
            error_log("🎉 REGISTRO COMPLETO");

            unset($_SESSION['usuario_registrado']);
            $_SESSION['success'] = "¡Registro completo! Inicia sesión con tus credenciales.";
            $this->redirect('index.php?page=login');

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
public function login() {
    // Validaciones básicas
    if (empty($_POST['usuario']) || empty($_POST['password'])) {
        $_SESSION['error'] = "Completa todos los campos";
        header('Location: index.php?page=login');
        exit;
    }
    
    $input = trim($_POST['usuario']);
    $password = $_POST['password'];
    
    try {
        $query = "SELECT id, nombre, apellido, email, usuario, contrasena, rol_id 
                 FROM usuarios 
                 WHERE (email = :input OR usuario = :input) 
                 AND esta_borrado = 0 
                 LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":input", $input);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $usuario_db['contrasena'])) {
                // 🔴 CORRECCIÓN: Guardar en sesión correctamente
                $usuario = [
                    'id' => $usuario_db['id'],
                    'nombre' => $usuario_db['nombre'],
                    'apellido' => $usuario_db['apellido'] ?? '',
                    'email' => $usuario_db['email'],
                    'usuario' => $usuario_db['usuario'] ?? '',
                    'rol_id' => $usuario_db['rol_id'] ?? 1
                ];
                
                // 🔴 CORRECCIÓN 1: Guardar usuario_id separadamente
                $_SESSION['usuario_id'] = $usuario_db['id'];
                
                // 🔴 CORRECCIÓN 2: Guardar usuario completo en temporal
                $_SESSION['usuario_temp'] = $usuario;
                
                // 🔴 CORRECCIÓN 3: También guardar en usuario (para compatibilidad)
                $_SESSION['usuario'] = $usuario;
                
                $codigo2FA = rand(100000, 999999);
                $_SESSION['codigo_generado'] = $codigo2FA;
                
                // Enviar email...
                if (!empty($usuario['email'])) {
                    try {
                        $this->emailService->enviarCodigo2FA(
                            $usuario['email'],
                            $usuario['nombre'] . ' ' . $usuario['apellido'],
                            $codigo2FA
                        );
                        $_SESSION['success'] = "Código enviado a tu email";
                    } catch (Exception $e) {
                        $_SESSION['warning'] = "Usa este código: $codigo2FA";
                    }
                } else {
                    $_SESSION['warning'] = "Usa este código: $codigo2FA";
                }
                
                header('Location: index.php?page=2fa');
                exit;
                
            } else {
                $_SESSION['error'] = "Contraseña incorrecta";
                header('Location: index.php?page=login');
                exit;
            }
        } else {
            $_SESSION['error'] = "Usuario no encontrado";
            header('Location: index.php?page=login');
            exit;
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error del sistema";
        header('Location: index.php?page=login');
        exit;
    }
}

public function verificar2FA() {
    // 1. VALIDAR CSRF
    if (!$this->validarCSRF()) {
        $_SESSION['error'] = "Token de seguridad inválido";
        header('Location: index.php?page=2fa');
        exit;
    }

    // 2. VALIDAR QUE HAY DATOS
    if (empty($_POST['codigo'])) {
        $_SESSION['error'] = "Ingresa el código de verificación";
        header('Location: index.php?page=2fa');
        exit;
    }

    // 3. VALIDAR QUE EXISTE SESIÓN TEMPORAL
    if (!isset($_SESSION['usuario_temp'])) {
        $_SESSION['error'] = "La sesión ha expirado. Por favor, inicia sesión nuevamente.";
        header('Location: index.php?page=login');
        exit;
    }

    // 4. VALIDAR QUE EXISTE CÓDIGO EN SESIÓN
    if (!isset($_SESSION['codigo_generado'])) {
        $_SESSION['error'] = "No se encontró código de verificación. Por favor, solicita uno nuevo.";
        header('Location: index.php?page=login');
        exit;
    }

    // 5. OBTENER Y LIMPIAR CÓDIGOS
    $codigo_ingresado = trim($_POST['codigo']);
    $codigo_correcto = $_SESSION['codigo_generado'];
    
    // Remover espacios y caracteres no numéricos
    $codigo_ingresado_limpio = preg_replace('/[^0-9]/', '', $codigo_ingresado);
    $codigo_correcto_limpio = preg_replace('/[^0-9]/', '', (string)$codigo_correcto);
    
    // Validar formato (6 dígitos exactos)
    if (!preg_match('/^\d{6}$/', $codigo_ingresado_limpio)) {
        $_SESSION['error'] = "El código debe contener exactamente 6 dígitos numéricos";
        header('Location: index.php?page=2fa');
        exit;
    }

    // 6. 🔥 COMPARACIÓN MULTICAPA (MUY ROBUSTA)
    $codigo_valido = false;
    
    // Método 1: Comparación estricta como strings limpios
    if ($codigo_ingresado_limpio === $codigo_correcto_limpio) {
        $codigo_valido = true;
    }
    // Método 2: Comparación como enteros (fallback)
    elseif ((int)$codigo_ingresado_limpio === (int)$codigo_correcto_limpio) {
        $codigo_valido = true;
    }
    // Método 3: Comparación numérica estricta
    elseif (strcmp($codigo_ingresado_limpio, $codigo_correcto_limpio) === 0) {
        $codigo_valido = true;
    }
    // Método 4: Verificación adicional (por si acaso)
    elseif ($codigo_ingresado_limpio == $codigo_correcto_limpio) {
        $codigo_valido = true;
    }

    // 7. SI EL CÓDIGO ES VÁLIDO
    if ($codigo_valido) {
        // ✅ VERIFICACIÓN EXITOSA
        
        // Establecer usuario en sesión principal
        $_SESSION['usuario'] = $_SESSION['usuario_temp'];
        
        // Guardar timestamp de inicio de sesión
        $_SESSION['usuario']['ultimo_login'] = date('Y-m-d H:i:s');
        
        // Obtener rol para redirección
        $rol_id = (int)($_SESSION['usuario']['rol_id'] ?? 0);
        $nombre_usuario = htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Usuario');
        
        // Limpiar datos temporales
        unset($_SESSION['usuario_temp']);
        unset($_SESSION['codigo_generado']);
        
       // 8. REDIRECCIÓN SEGÚN ROL
switch ($rol_id) {
    case 2: // Líder de Proyecto
        $_SESSION['success'] = "¡Bienvenido Líder $nombre_usuario!";
        header('Location: index.php?action=lider_home');
        exit;
        
    case 1: // AdminOrg (por defecto)
    default:
        $_SESSION['success'] = "¡Bienvenido Administrador $nombre_usuario!";
        header('Location: index.php?action=home');
        exit;
}
        
        // 9. REDIRIGIR (con múltiples métodos)
        
        // Método 1: Header HTTP
        header("Location: $url_destino");
        
        // Método 2: JavaScript (fallback)
        echo "<script>window.location.href = '$url_destino';</script>";
        
        // Método 3: Meta refresh (segundo fallback)
        echo "<meta http-equiv='refresh' content='0;url=$url_destino'>";
        
        // Método 4: Enlace visible (último recurso)
        echo "<p>Redireccionando... <a href='$url_destino'>Haz clic aquí si no redirige automáticamente</a></p>";
        
        exit;
        
    } else {
        // ❌ CÓDIGO INCORRECTO
        
        // Incrementar intentos fallidos
        $_SESSION['intentos_fallidos_2fa'] = ($_SESSION['intentos_fallidos_2fa'] ?? 0) + 1;
        
        // Bloquear después de 3 intentos
        if ($_SESSION['intentos_fallidos_2fa'] >= 3) {
            session_unset();
            session_destroy();
            $_SESSION['error'] = "Demasiados intentos fallidos. Sesión bloqueada. Por favor, inicia sesión nuevamente.";
            header('Location: index.php?page=login');
            exit;
        }
        
        // Mensaje de error con intentos restantes
        $intentos_restantes = 3 - $_SESSION['intentos_fallidos_2fa'];
        $_SESSION['error'] = "Código incorrecto. Te quedan $intentos_restantes intento(s).";
        
        header('Location: index.php?page=2fa');
        exit;
    }
}

    public function reenviarCodigo2FA() {
    if (!isset($_SESSION['usuario_temp'])) {
        $_SESSION['error'] = "Sesión expirada";
        header('Location: index.php?page=login');
        exit;
    }

    $usuario = $_SESSION['usuario_temp'];
    $codigo2FA = $this->twoFactorModel->generarCodigo2FA($usuario['id']);
    
    if ($codigo2FA) {
        // Actualizar código en sesión
        $_SESSION['codigo_generado'] = $codigo2FA;
        
        // Intentar enviar email
        try {
            $emailEnviado = $this->emailService->enviarCodigo2FA(
                $usuario['email'], 
                $usuario['nombre'], 
                $codigo2FA
            );
            
            if ($emailEnviado) {
                $_SESSION['success'] = "Se ha enviado un nuevo código de verificación";
            } else {
                $_SESSION['warning'] = "Email no enviado. Nuevo código: <strong>$codigo2FA</strong>";
            }
            
        } catch (Exception $e) {
            $_SESSION['warning'] = "Nuevo código: <strong>$codigo2FA</strong>";
        }
        
    } else {
        $_SESSION['error'] = "Error al generar el código de verificación";
    }
    
    header('Location: index.php?page=2fa');
    exit;
}
    private function obtenerUsuarioCompleto($usuario_id) {
        error_log("🔍 Obteniendo usuario completo ID: $usuario_id");
        
        $query = "SELECT u.*, r.nombre as rol_nombre, r.descripcion as rol_descripcion
                  FROM usuarios u
                  LEFT JOIN roles r ON u.rol_id = r.id
                  WHERE u.id = :usuario_id AND u.esta_borrado = 0 
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                error_log("✅ Usuario obtenido: " . $usuario['nombre'] . " (" . $usuario['rol_nombre'] . ")");
                return $usuario;
            }
        }
        
        error_log("❌ Usuario NO encontrado o error en query");
        return null;
    }

    private function redirigirSegunRol() {
        error_log("🔀 Redirigiendo según rol...");
        
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['rol_nombre'])) {
            error_log("❌ No hay usuario o rol en sesión");
            $this->redirect('index.php?page=login');
            return;
        }

        $rol = $_SESSION['usuario']['rol_nombre'];
        error_log("👤 Rol del usuario: $rol");
        
        switch ($rol) {
            case 'AdminSistema':
            case 'AdminOrg':
                error_log("🔀 → home (Admin)");
                $this->redirect('index.php?action=home');
                break;
            case 'LiderProyecto':
                error_log("🔀 → lider_home");
                $this->redirect('index.php?page=lider_home');
                break;
            default:
                error_log("🔀 → miembro_home");
                $this->redirect('index.php?page=miembro_home');
                break;
        }
    }

    public function logout() {
        error_log("=== logout() ===");
        session_unset();
        session_destroy();
        $this->redirect('index.php?page=login');
    }
}
?>