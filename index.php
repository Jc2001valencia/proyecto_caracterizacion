<?php
// index.php - VERSIÓN CORREGIDA Y FUNCIONAL
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Configuración
define('DEBUG_MODE', true); // Cambiar a false en producción

// Log básico
error_log("========== INDEX.PHP ==========");
error_log("URL: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
error_log("Método: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A'));

// ========================================
// INCLUIR CONEXIÓN BD
// ========================================
$db = null;
if (file_exists('config/db.php')) {
    require_once 'config/db.php';
    if (isset($db) && $db !== null) {
        error_log("✅ Conexión BD establecida");
    } else {
        error_log("❌ Variable \$db no está definida en config/db.php");
        die("Error: Conexión a base de datos no disponible");
    }
} else {
    error_log("❌ config/db.php no existe");
    die("Error: Archivo de configuración no encontrado");
}

// ========================================
// INCLUIR MODELOS Y CONTROLADORES
// ========================================
require_once 'models/UsuarioModel.php';
require_once 'models/TwoFactorModel.php';
require_once 'controllers/AuthController.php';

// Instanciar controlador principal
$authController = new AuthController($db);

// ========================================
// FUNCIONES DE AYUDA PARA VISTAS
// ========================================
function mostrarVista($vista, $data = []) {
    extract($data);
    
  $archivos = [
    'login' => ['views/AuthView.php', 'views/auth_view.php', 'views/login.php'],
    'register' => ['views/AuthView.php', 'views/auth_view.php', 'views/register.php'],
    '2fa' => ['views/AuthView.php', 'views/auth_view.php', 'views/2fa.php'],
    'crear-organizacion' => ['views/AuthView.php', 'views/crear_organizacion.php'],
    'home' => ['views/Home.php', 'views/home.php'],
    'lider_home' => ['views/lider_home.php', 'views/LiderHome.php'], // ✅ AGREGAR ESTO
    'landing' => ['views/home_landing.php', 'views/landing.php']
];
    
    if (isset($archivos[$vista])) {
        foreach ($archivos[$vista] as $archivo) {
            if (file_exists($archivo)) {
                error_log("✅ Cargando vista: $archivo");
                $current_page = $vista; // Variable para AuthView.php
                require_once $archivo;
                exit;
            }
        }
    }
    
    error_log("❌ Vista '$vista' no encontrada");
    echo "<h1>Error</h1><p>Vista '$vista' no disponible</p>";
    exit;
}

// ========================================
// PROTECCIÓN DE RUTAS
// ========================================
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario'])) {
        $_SESSION['error'] = "Debes iniciar sesión primero";
        header('Location: index.php?page=login');
        exit;
    }
}

function verificarSesion2FA() {
    if (!isset($_SESSION['usuario_temp'])) {
        $_SESSION['error'] = "Sesión expirada. Inicia sesión nuevamente.";
        header('Location: index.php?page=login');
        exit;
    }
}

// ========================================
// DETERMINAR ACCIÓN/PÁGINA
// ========================================
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$page = $_GET['page'] ?? null;

error_log("Action: " . ($action ?? 'null'));
error_log("Page: " . ($page ?? 'null'));

// ========================================
// ENRUTAMIENTO
// ========================================

// ============ ACCIONES POST (Formularios) ============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    
    switch($action) {
        
        // -------- LOGIN --------
        case 'login':
            error_log("🔐 Procesando LOGIN");
            try {
                $authController->login();
            } catch (Exception $e) {
                error_log("❌ Error en login: " . $e->getMessage());
                $_SESSION['error'] = "Error al iniciar sesión: " . $e->getMessage();
                header('Location: index.php?page=login');
                exit;
            }
            break;
        
        // -------- REGISTRO --------
        case 'register':
            error_log("📝 Procesando REGISTRO");
            try {
                $authController->registrar();
            } catch (Exception $e) {
                error_log("❌ Error en registro: " . $e->getMessage());
                $_SESSION['error'] = "Error al registrar: " . $e->getMessage();
                $page_redirect = isset($_POST['paso']) && $_POST['paso'] === '2' 
                    ? 'crear-organizacion' 
                    : 'register';
                header('Location: index.php?page=' . $page_redirect);
                exit;
            }
            break;
        
        // -------- VERIFICACIÓN 2FA --------
        case 'verify2fa':
            error_log("🔐 Procesando VERIFY 2FA");
            verificarSesion2FA();
            try {
                $authController->verificar2FA();
            } catch (Exception $e) {
                error_log("❌ Error en verify2fa: " . $e->getMessage());
                $_SESSION['error'] = "Error en verificación: " . $e->getMessage();
                header('Location: index.php?page=2fa');
                exit;
            }
            break;
        
        // -------- GUARDAR PROYECTO --------
        case 'guardar_proyecto':
            error_log("💾 Procesando GUARDAR PROYECTO");
            verificarAutenticacion();
            
            if (!file_exists('controllers/ProyectoController.php')) {
                $_SESSION['error'] = "Controlador de proyectos no disponible";
                header('Location: index.php?action=home');
                exit;
            }
            
            require_once 'controllers/ProyectoController.php';
            try {
                $proyectoController = new ProyectoController($db);
                $proyectoController->guardar();
            } catch (Exception $e) {
                error_log("❌ Error guardando proyecto: " . $e->getMessage());
                $_SESSION['error'] = "Error al guardar proyecto: " . $e->getMessage();
                header('Location: index.php?action=home');
                exit;
            }
            break;
        
        default:
            error_log("⚠️ Acción POST no reconocida: $action");
            $_SESSION['error'] = "Acción no válida";
            header('Location: index.php');
            exit;
    }
}

// ============ ACCIONES GET ============
elseif ($action) {
    
    switch($action) {
        
        // -------- REENVIAR 2FA --------
        case 'reenviar2fa':
            error_log("🔄 Procesando REENVIAR 2FA");
            verificarSesion2FA();
            try {
                $authController->reenviarCodigo2FA();
            } catch (Exception $e) {
                error_log("❌ Error reenviando código: " . $e->getMessage());
                $_SESSION['error'] = "Error al reenviar código";
                header('Location: index.php?page=2fa');
                exit;
            }
            break;

            // -------- HOME LÍDER --------
case 'lider_home':
    error_log("🏠 Mostrando LIDER HOME");
    verificarAutenticacion();
    
    // Verificar que sea líder (rol_id = 2)
    if (($_SESSION['usuario']['rol_id'] ?? 0) != 2) {
        $_SESSION['error'] = "Acceso denegado. Esta área es solo para líderes.";
        header('Location: index.php?action=home');
        exit;
    }
    
    // Cargar vista de líder
    if (file_exists('views/lider_home.php')) {
        require_once 'views/lider_home.php';
        exit;
    } else {
        error_log("❌ views/lider_home.php no existe");
        $_SESSION['error'] = "Vista de líder no disponible";
        header('Location: index.php?action=home');
        exit;
    }
    break;
        
        // -------- LOGOUT --------
        case 'logout':
            error_log("👋 Procesando LOGOUT");
            try {
                $authController->logout();
            } catch (Exception $e) {
                error_log("⚠️ Error en logout: " . $e->getMessage());
                session_unset();
                session_destroy();
                header('Location: index.php?page=login');
                exit;
            }
            break;
        
        // -------- HOME --------
       case 'home':
    error_log("🏠 Mostrando HOME");
    verificarAutenticacion();
    
    // Si es líder, redirigir a su dashboard
    if (($_SESSION['usuario']['rol_id'] ?? 0) == 2) {
        header('Location: index.php?action=lider_home');
        exit;
    }
    
    mostrarVista('home');
    break;
        
        // -------- ELIMINAR PROYECTO --------
        case 'eliminar_proyecto':
            error_log("🗑️ Procesando ELIMINAR PROYECTO");
            verificarAutenticacion();
            
            if (!isset($_GET['id'])) {
                $_SESSION['error'] = "ID de proyecto no proporcionado";
                header('Location: index.php?action=home');
                exit;
            }
            
            if (!file_exists('controllers/ProyectoController.php')) {
                $_SESSION['error'] = "Controlador de proyectos no disponible";
                header('Location: index.php?action=home');
                exit;
            }
            
            require_once 'controllers/ProyectoController.php';
            try {
                $proyectoController = new ProyectoController($db);
                $proyectoController->eliminar($_GET['id']);
            } catch (Exception $e) {
                error_log("❌ Error eliminando proyecto: " . $e->getMessage());
                $_SESSION['error'] = "Error al eliminar proyecto: " . $e->getMessage();
                header('Location: index.php?action=home');
                exit;
            }
            break;
        
        // -------- VER CARACTERIZACIÓN --------
        case 'ver_caracterizacion':
            error_log("👁️ Mostrando CARACTERIZACIÓN");
            verificarAutenticacion();
            
            if (!isset($_GET['id'])) {
                $_SESSION['error'] = "ID de proyecto no proporcionado";
                header('Location: index.php?action=home');
                exit;
            }
            
            $proyecto_id = (int)$_GET['id'];
            
            if (file_exists('views/VerCaracterizacion.php')) {
                require_once 'views/VerCaracterizacion.php';
                exit;
            } else {
                $_SESSION['error'] = "Vista de caracterización no disponible";
                header('Location: index.php?action=home');
                exit;
            }
            break;
        
        default:
            error_log("⚠️ Acción GET no reconocida: $action");
            header('Location: index.php');
            exit;
    }
}

// ============ PÁGINAS (Vistas) ============
elseif ($page) {
    
    switch($page) {
        
        case 'login':
            error_log("📄 Mostrando LOGIN");
            // Si ya tiene sesión, redirigir a home
            if (isset($_SESSION['usuario'])) {
                header('Location: index.php?action=home');
                exit;
            }
            mostrarVista('login');
            break;
        
        case 'register':
            error_log("📄 Mostrando REGISTER");
            mostrarVista('register');
            break;
        
        case '2fa':
            error_log("📄 Mostrando 2FA");
            verificarSesion2FA();
            mostrarVista('2fa');
            break;
        
        case 'crear-organizacion':
            error_log("📄 Mostrando CREAR ORGANIZACIÓN");
            
            if (!isset($_SESSION['usuario_registrado'])) {
                $_SESSION['error'] = "Debes registrar un usuario primero";
                header('Location: index.php?page=register');
                exit;
            }
            
            mostrarVista('crear-organizacion');
            break;
        
        case 'home':
            error_log("📄 Mostrando HOME (via page)");
            verificarAutenticacion();
            mostrarVista('home');
            break;
        
        default:
            error_log("⚠️ Página no reconocida: $page");
            header('Location: index.php');
            exit;
    }
}

// ============ DEFAULT (Landing o Home según sesión) ============
else {
    error_log("📄 Mostrando página por defecto");
    
    if (isset($_SESSION['usuario'])) {
        // Usuario autenticado → mostrar home
        header('Location: index.php?action=home');
        exit;
    } elseif (isset($_SESSION['usuario_temp'])) {
        // En proceso de 2FA → redirigir a 2FA
        header('Location: index.php?page=2fa');
        exit;
    } else {
        // Sin sesión → mostrar landing
        mostrarVista('landing');
    }
}

error_log("========== FIN INDEX.PHP ==========");
?>