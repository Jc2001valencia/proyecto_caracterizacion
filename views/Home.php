<?php
// ========================================
// VIEWS/HOME.PHP - DASHBOARD MEJORADO
// ========================================

// 1. INICIAR SESIÓN Y VERIFICACIÓN
if (session_status() == PHP_SESSION_NONE) session_start();

// Verificar si hay sesión activa
$sesion_activa = isset($_SESSION['usuario']);
if (!$sesion_activa) {
    header('Location: ../index.php?action=login_view');
    exit;
}

// 2. CONEXIÓN BD
require_once __DIR__ . '/../config/db.php';
$database = new Database();
$db = $database->getConnection();

if (!($db instanceof PDO)) die("ERROR: No se pudo conectar a la base de datos");

// ====== CONFIGURACIÓN DE RUTAS ======
$base_web_url = 'http://localhost/proyecto_caracterizacion';
$base_path = (basename(dirname(__FILE__)) === 'views') ? '../controllers/' : 'controllers/';

// 3. MANEJO DE IDIOMA
$idioma = $_COOKIE['idioma'] ?? 'es';
$textos = [
    'es' => [
        'titulo_sistema' => 'Marco Cynefin',
        'titulo_dashboard' => 'Dashboard',
        'gestion_proyectos' => 'Gestión de proyectos',
        'sistema_caracterizacion' => 'Sistema de caracterización - Framework Cynefin',
        'bienvenido' => '¡Bienvenido administrador',
        'debes_iniciar_sesion' => 'Debes iniciar sesión primero',
        'proyectos_activos' => 'Proyectos activos',
        'proyectos_registrados' => 'proyecto(s) registrado(s)',
        'nuevo_proyecto' => 'Nuevo proyecto',
        'crear_nuevo_proyecto' => 'Crear nuevo proyecto',
        'crear_nuevo_lider' => 'Crear nuevo líder',
        'idioma' => 'Idioma',
        'espanol' => 'Español',
        'ingles' => 'Inglés',
        'sin_lideres' => 'No hay líderes disponibles',
        'crear_lider_primero' => 'Debes crear al menos un líder antes de crear proyectos',
        'ir_a_lideres' => 'Ir a líderes',
        'lideres_proyecto' => 'Líderes de proyecto',
        'lideres_activos' => 'líder(es) activo(s)',
        'informacion_organizacion' => 'Información de la organización',
        'configuracion' => 'Configuración',
        'editar_informacion' => 'Editar información',
        'detalles_proyecto' => 'Detalles del proyecto',
        'editar_proyecto' => 'Editar proyecto',
        'detalles_lider' => 'Detalles del líder',
        'editar_lider' => 'Editar líder',
        'guia_sistema' => 'Guía del sistema',
        'cerrar_sesion' => 'Cerrar sesión',
        'funcionalidades' => 'Funcionalidades',
        'nombre_proyecto' => 'Nombre del proyecto',
        'descripcion' => 'Descripción',
        'horas_estimadas' => 'Horas estimadas',
        'estado' => 'Estado',
        'asignar_lider' => 'Asignar líder',
        'fecha_inicio' => 'Fecha inicio',
        'fecha_fin_estimada' => 'Fecha fin estimada',
        'cancelar' => 'Cancelar',
        'guardar_cambios' => 'Guardar cambios',
        'crear_proyecto' => 'Crear proyecto',
        'nombre' => 'Nombre',
        'apellido' => 'Apellido',
        'email' => 'Email',
        'telefono' => 'Teléfono',
        'cambiar_contrasena' => 'Cambiar contraseña',
        'nueva_contrasena' => 'Nueva contraseña',
        'credenciales_acceso' => 'Credenciales de acceso',
        'usuario' => 'Usuario',
        'contrasena' => 'Contraseña',
        'crear_lider' => 'Crear líder',
        'direccion' => 'Dirección',
        'registro' => 'Registro',
        'ayuda_soporte' => 'Ayuda & soporte',
        'cerrar_sesion_text' => 'Cerrar sesión',
        'version_sistema' => 'v1.0.0 • © 2024 Cynefin'
    ],
    'en' => [
        'titulo_sistema' => 'Cynefin Framework',
        'titulo_dashboard' => 'Dashboard',
        'gestion_proyectos' => 'Project management',
        'sistema_caracterizacion' => 'Characterization system - Cynefin Framework',
        'bienvenido' => 'Welcome administrator',
        'debes_iniciar_sesion' => 'You must login first',
        'proyectos_activos' => 'Active projects',
        'proyectos_registrados' => 'project(s) registered',
        'nuevo_proyecto' => 'New project',
        'crear_nuevo_proyecto' => 'Create new project',
        'crear_nuevo_lider' => 'Create new leader',
        'idioma' => 'Language',
        'espanol' => 'Spanish',
        'ingles' => 'English',
        'sin_lideres' => 'No leaders available',
        'crear_lider_primero' => 'You must create at least one leader before creating projects',
        'ir_a_lideres' => 'Go to leaders',
        'lideres_proyecto' => 'Project leaders',
        'lideres_activos' => 'leader(s) active',
        'informacion_organizacion' => 'Organization information',
        'configuracion' => 'Configuration',
        'editar_informacion' => 'Edit information',
        'detalles_proyecto' => 'Project details',
        'editar_proyecto' => 'Edit project',
        'detalles_lider' => 'Leader details',
        'editar_lider' => 'Edit leader',
        'guia_sistema' => 'System guide',
        'cerrar_sesion' => 'Logout',
        'funcionalidades' => 'Features',
        'nombre_proyecto' => 'Project name',
        'descripcion' => 'Description',
        'horas_estimadas' => 'Estimated hours',
        'estado' => 'Status',
        'asignar_lider' => 'Assign leader',
        'fecha_inicio' => 'Start date',
        'fecha_fin_estimada' => 'Estimated end date',
        'cancelar' => 'Cancel',
        'guardar_cambios' => 'Save changes',
        'crear_proyecto' => 'Create project',
        'nombre' => 'First name',
        'apellido' => 'Last name',
        'email' => 'Email',
        'telefono' => 'Phone',
        'cambiar_contrasena' => 'Change password',
        'nueva_contrasena' => 'New password',
        'credenciales_acceso' => 'Access credentials',
        'usuario' => 'Username',
        'contrasena' => 'Password',
        'crear_lider' => 'Create leader',
        'direccion' => 'Address',
        'registro' => 'Registration',
        'ayuda_soporte' => 'Help & support',
        'cerrar_sesion_text' => 'Logout',
        'version_sistema' => 'v1.0.0 • © 2024 Cynefin'
    ]
];

$t = $textos[$idioma] ?? $textos['es'];

// 4. VARIABLES
$proyectos = $lideres = [];
$mi_organizacion = null;
$organizacion_id_real = null;
$error_message = $_SESSION['error'] ?? null;
$success_message = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

$seccion_activa = $_GET['seccion'] ?? 'proyectos';
$puede_crear_proyectos = false;

// 5. OBTENER ORGANIZACIÓN DEL USUARIO
try {
    $usuario_id = $_SESSION['usuario']['id'] ?? 0;
    
    if ($usuario_id > 0) {
        // Obtener organización del usuario
        $stmt = $db->prepare("SELECT organizacion_id FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->execute([$usuario_id]);
        $usuario_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario_data && isset($usuario_data['organizacion_id'])) {
            $organizacion_id_real = $usuario_data['organizacion_id'];
            
            // Cargar datos de la organización
            $stmt = $db->prepare("SELECT * FROM organizaciones WHERE id = ? LIMIT 1");
            $stmt->execute([$organizacion_id_real]);
            $mi_organizacion = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    // Solo continuar si tenemos organizacion_id
    if ($organizacion_id_real) {
        
        // Cargar proyectos de la organización
        $sql_proyectos = "
            SELECT p.id, p.nombre AS nombre_proyecto, p.descripcion AS descripcion_proyecto,
                   COALESCE(p.horas, 0) as horas, COALESCE(p.estado, 'pendiente') as estado,
                   p.fecha_inicio, p.fecha_fin, p.lider_proyecto_id, p.created_at,
                   CONCAT(u.nombre, ' ', u.apellido) AS nombre_lider
            FROM proyectos p
            LEFT JOIN usuarios u ON p.lider_proyecto_id = u.id
            WHERE p.organizacion_id = :org_id
            ORDER BY p.created_at DESC";
        
        $stmt = $db->prepare($sql_proyectos);
        $stmt->bindParam(':org_id', $organizacion_id_real, PDO::PARAM_INT);
        $stmt->execute();
        $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Cargar líderes de la organización
        $sql_lideres = "SELECT id, nombre, apellido, email, usuario, telefono, organizacion_id, rol_id, creado_en 
                        FROM usuarios 
                        WHERE rol_id = 2 AND organizacion_id = :org_id";
        
        $stmt = $db->prepare($sql_lideres);
        $stmt->bindParam(':org_id', $organizacion_id_real, PDO::PARAM_INT);
        $stmt->execute();
        $lideres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Verificar si puede crear proyectos
        $puede_crear_proyectos = count($lideres) > 0;
    }
    
} catch (PDOException $e) {
    error_log("Error al cargar datos: " . $e->getMessage());
    $error_message = "Error al cargar datos del sistema: " . $e->getMessage();
}

// Si no hay organización, crear una estructura temporal
if (!$mi_organizacion && $organizacion_id_real) {
    $mi_organizacion = [
        'id' => $organizacion_id_real,
        'nombre' => 'Mi organización',
        'descripcion' => 'Completar perfil',
        'direccion' => 'No especificada',
        'telefono' => 'No disponible',
        'email' => $_SESSION['usuario']['email'] ?? 'admin@organizacion.com',
        'activo' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ];
}

$nombre_usuario = $_SESSION['usuario']['nombre'] ?? 'Usuario';
$email_usuario = $_SESSION['usuario']['email'] ?? '';
$inicial_usuario = strtoupper(substr($nombre_usuario, 0, 1));
?>

<!DOCTYPE html>
<html lang="<?= $idioma ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de caracterización</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modal-overlay.show {
        opacity: 1;
    }

    .modal-overlay.hidden {
        display: none !important;
    }

    .modal-content {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.9) translateY(-20px);
        transition: transform 0.3s ease;
    }

    .modal-overlay.show .modal-content {
        transform: scale(1) translateY(0);
    }

    .sidebar-mobile {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-mobile.active {
        transform: translateX(0);
    }

    .seccion-content {
        display: none;
        animation: fadeIn 0.4s ease;
    }

    .seccion-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .loading-spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .btn-disabled {
        opacity: 0.5;
        cursor: not-allowed !important;
    }

    .btn-disabled:hover {
        transform: none !important;
        box-shadow: none !important;
    }
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar Desktop -->
        <aside class="hidden lg:flex lg:w-64 bg-gray-800 text-white flex-col fixed h-screen shadow-2xl">
            <!-- Header -->
            <div class="p-6 border-b border-gray-700 bg-gray-900">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3 shadow-lg">
                        <i class="fas fa-chart-network text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold"><?= $t['titulo_dashboard'] ?></h1>
                        <p class="text-xs text-gray-400 mt-1"><?= $t['titulo_sistema'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Perfil de usuario -->
            <div class="p-4 border-b border-gray-700">
                <?php if ($sesion_activa): ?>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center font-bold text-lg shadow-lg ring-2 ring-blue-400 ring-offset-2 ring-offset-gray-800">
                            <?= $inicial_usuario ?>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate"><?= htmlspecialchars($nombre_usuario) ?></p>
                        <p class="text-xs text-gray-300 truncate">Administrador</p>
                        <p class="text-xs text-gray-400 truncate mt-1"><?= htmlspecialchars($email_usuario) ?></p>
                    </div>
                </div>
                <div class="mt-3 bg-green-900/20 border border-green-800/50 rounded-lg p-2">
                    <p class="text-xs text-green-300 flex items-center">
                        <i class="fas fa-check-circle mr-2 text-green-400"></i>
                        <?= $t['bienvenido'] ?> <?= htmlspecialchars(explode(' ', $nombre_usuario)[0]) ?>!
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Selector de idioma -->
            <div class="p-4 border-b border-gray-700">
                <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider"><?= $t['idioma'] ?></p>
                <div class="flex space-x-2">
                    <form method="POST" action="<?= $base_web_url ?>/controllers/IdiomaController.php" class="flex-1">
                        <input type="hidden" name="idioma" value="es">
                        <button type="submit" class="w-full px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center
                            <?= ($idioma ?? 'es') == 'es' 
                                ? 'bg-blue-600 text-white shadow-lg ring-2 ring-blue-400' 
                                : 'bg-gray-700 text-gray-300 hover:bg-gray-600' ?>">
                            <i class="fas fa-flag mr-2"></i> ES
                        </button>
                    </form>
                    <form method="POST" action="<?= $base_web_url ?>/controllers/IdiomaController.php" class="flex-1">
                        <input type="hidden" name="idioma" value="en">
                        <button type="submit" class="w-full px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center
                            <?= ($idioma ?? 'es') == 'en' 
                                ? 'bg-blue-600 text-white shadow-lg ring-2 ring-blue-400' 
                                : 'bg-gray-700 text-gray-300 hover:bg-gray-600' ?>">
                            <i class="fas fa-flag-usa mr-2"></i> EN
                        </button>
                    </form>
                </div>
            </div>

            <!-- Navegación principal -->
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <p class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold tracking-wider mb-3">
                    <i class="fas fa-sitemap mr-2"></i> Gestión principal
                </p>

                <!-- Proyectos -->
                <a href="#" onclick="cambiarSeccion('proyectos'); return false;" class="nav-link group flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200
                    <?= $seccion_activa === 'proyectos' 
                        ? 'bg-blue-900/40 text-white border-l-4 border-blue-400' 
                        : 'hover:bg-gray-700/50 text-gray-300' ?>">
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 rounded-lg bg-blue-900/50 flex items-center justify-center mr-3 group-hover:bg-blue-800">
                            <i class="fas fa-project-diagram text-blue-300"></i>
                        </div>
                        <span><?= $t['gestion_proyectos'] ?></span>
                    </div>
                    <span class="badge bg-blue-500 px-2 py-1 rounded-full text-xs font-bold min-w-[24px] text-center">
                        <?= count($proyectos) ?>
                    </span>
                </a>

                <!-- Líderes -->
                <a href="#" onclick="cambiarSeccion('lideres'); return false;" class="nav-link group flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200
                    <?= $seccion_activa === 'lideres' 
                        ? 'bg-green-900/40 text-white border-l-4 border-green-400' 
                        : 'hover:bg-gray-700/50 text-gray-300' ?>">
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 rounded-lg bg-green-900/50 flex items-center justify-center mr-3 group-hover:bg-green-800">
                            <i class="fas fa-user-tie text-green-300"></i>
                        </div>
                        <span>Líderes</span>
                    </div>
                    <span class="badge bg-green-500 px-2 py-1 rounded-full text-xs font-bold min-w-[24px] text-center">
                        <?= count($lideres) ?>
                    </span>
                </a>

                <!-- Organización -->
                <a href="#" onclick="cambiarSeccion('organizacion'); return false;" class="nav-link group flex items-center justify-between px-4 py-3 rounded-lg transition-all duration-200
                    <?= $seccion_activa === 'organizacion' 
                        ? 'bg-purple-900/40 text-white border-l-4 border-purple-400' 
                        : 'hover:bg-gray-700/50 text-gray-300' ?>">
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 rounded-lg bg-purple-900/50 flex items-center justify-center mr-3 group-hover:bg-purple-800">
                            <i class="fas fa-building text-purple-300"></i>
                        </div>
                        <span>Mi organización</span>
                    </div>
                    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                </a>
            </nav>

            <!-- Footer del sidebar -->
            <div class="p-4 border-t border-gray-700 space-y-3 bg-gray-900/50">
                <!-- Botón Ayuda -->
                <button onclick="openModal('modalAyuda')"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-question-circle mr-2 text-lg"></i>
                    <span><?= $t['ayuda_soporte'] ?></span>
                </button>

                <!-- Botón Cerrar Sesión -->
                <button onclick="openModal('modalLogout')"
                    class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                    <i class="fas fa-sign-out-alt mr-2 text-lg"></i>
                    <span><?= $t['cerrar_sesion_text'] ?></span>
                </button>

                <!-- Versión del sistema -->
                <div class="pt-2 text-center">
                    <p class="text-xs text-gray-500"><?= $t['version_sistema'] ?></p>
                </div>
            </div>
        </aside>

        <!-- Sidebar Mobile -->
        <div id="mobileOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"
            onclick="toggleMobileSidebar()"></div>

        <aside id="mobileSidebar"
            class="lg:hidden w-72 bg-gray-800 text-white fixed h-screen z-40 shadow-2xl transform -translate-x-full transition-transform duration-300">
            <!-- Header Mobile -->
            <div class="p-5 border-b border-gray-700 bg-gradient-to-r from-gray-900 to-gray-800">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3 shadow-lg">
                            <i class="fas fa-chart-network text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold"><?= $t['titulo_dashboard'] ?></h1>
                            <p class="text-xs text-gray-400">Sistema Cynefin</p>
                        </div>
                    </div>
                    <button onclick="toggleMobileSidebar()"
                        class="w-10 h-10 rounded-full bg-gray-700 hover:bg-gray-600 flex items-center justify-center text-xl transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Contenido Mobile -->
            <div class="overflow-y-auto" style="max-height: calc(100vh - 140px);">
                <!-- Perfil Mobile -->
                <div class="p-4 border-b border-gray-700">
                    <?php if ($sesion_activa): ?>
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="relative">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center font-bold text-xl shadow-lg ring-2 ring-blue-400">
                                <?= $inicial_usuario ?>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-lg"><?= htmlspecialchars($nombre_usuario) ?></p>
                            <p class="text-sm text-gray-300">Administrador</p>
                            <p class="text-xs text-gray-400 mt-1"><?= htmlspecialchars($email_usuario) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Navegación Mobile -->
                <nav class="p-4 space-y-1">
                    <!-- Proyectos Mobile -->
                    <a href="#" onclick="cambiarSeccion('proyectos'); toggleMobileSidebar(); return false;"
                        class="flex items-center justify-between px-4 py-3 rounded-lg bg-gray-700/50">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-blue-900/50 flex items-center justify-center mr-3">
                                <i class="fas fa-project-diagram text-blue-300"></i>
                            </div>
                            <span>Proyectos</span>
                        </div>
                        <span class="badge bg-blue-500 px-2 py-1 rounded-full text-xs font-bold">
                            <?= count($proyectos) ?>
                        </span>
                    </a>

                    <!-- Líderes Mobile -->
                    <a href="#" onclick="cambiarSeccion('lideres'); toggleMobileSidebar(); return false;"
                        class="flex items-center justify-between px-4 py-3 rounded-lg bg-gray-700/50">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-green-900/50 flex items-center justify-center mr-3">
                                <i class="fas fa-user-tie text-green-300"></i>
                            </div>
                            <span>Líderes</span>
                        </div>
                        <span class="badge bg-green-500 px-2 py-1 rounded-full text-xs font-bold">
                            <?= count($lideres) ?>
                        </span>
                    </a>

                    <!-- Organización Mobile -->
                    <a href="#" onclick="cambiarSeccion('organizacion'); toggleMobileSidebar(); return false;"
                        class="flex items-center justify-between px-4 py-3 rounded-lg bg-gray-700/50">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-purple-900/50 flex items-center justify-center mr-3">
                                <i class="fas fa-building text-purple-300"></i>
                            </div>
                            <span>Mi organización</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </nav>
            </div>

            <!-- Footer Mobile -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700 bg-gray-900/80">
                <div class="flex space-x-2">
                    <button onclick="openModal('modalAyuda'); toggleMobileSidebar();"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2.5 rounded-lg font-medium flex items-center justify-center">
                        <i class="fas fa-question-circle mr-2"></i>
                        <span class="text-sm">Ayuda</span>
                    </button>
                    <button onclick="openModal('modalLogout'); toggleMobileSidebar();"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg font-medium flex items-center justify-center">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <span class="text-sm">Salir</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 lg:ml-64 p-4 md:p-8">
            <!-- Header Mobile -->
            <div class="lg:hidden flex items-center justify-between mb-6 bg-white rounded-lg shadow-md p-4">
                <button onclick="toggleMobileSidebar()"
                    class="p-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-lg font-bold text-gray-800" id="tituloMobile">
                    <i class="fas fa-project-diagram mr-2 text-blue-600"></i><?= $t['gestion_proyectos'] ?>
                </h1>
                <div class="w-10"></div>
            </div>

            <!-- Título Desktop -->
            <div class="hidden lg:block mb-8">
                <h1 class="text-3xl font-bold text-gray-800" id="tituloDesktop">
                    <i class="fas fa-project-diagram mr-3 text-blue-600"></i><?= $t['gestion_proyectos'] ?>
                </h1>
                <p class="text-gray-600 mt-2"><?= $t['sistema_caracterizacion'] ?></p>

                <!-- Mensaje de sesión -->
                <?php if ($sesion_activa): ?>
                <div class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-lg mt-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span><?= $t['bienvenido'] ?> <?= htmlspecialchars($nombre_usuario) ?>!</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Mensajes -->
            <?php if ($error_message): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow card-hover">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                    <p class="text-red-700 font-medium"><?= $error_message ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow card-hover">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
                    <p class="text-green-700 font-medium"><?= $success_message ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- CONTENIDO DINÁMICO -->

            <!-- SECCIÓN: PROYECTOS -->
            <div id="seccion-proyectos" class="seccion-content <?= $seccion_activa === 'proyectos' ? 'active' : '' ?>">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800"><?= $t['proyectos_activos'] ?></h2>
                        <p class="text-gray-600 mt-1 flex items-center">
                            <i class="fas fa-folder mr-2 text-blue-500"></i>
                            <?= count($proyectos) ?> <?= $t['proyectos_registrados'] ?>
                        </p>

                        <!-- Alerta si no hay líderes -->
                        <?php if (!$puede_crear_proyectos): ?>
                        <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800"><?= $t['crear_lider_primero'] ?>
                                    </h3>
                                    <div class="mt-2">
                                        <button onclick="cambiarSeccion('lideres')"
                                            class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition">
                                            <i class="fas fa-user-plus mr-1.5"></i><?= $t['ir_a_lideres'] ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botón para nuevo proyecto -->
                    <?php if ($puede_crear_proyectos): ?>
                    <button onclick="openModal('modalNuevoProyecto')"
                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i><?= $t['nuevo_proyecto'] ?>
                    </button>
                    <?php else: ?>
                    <button onclick="mostrarAlertaSinLideres()"
                        class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition btn-disabled"
                        disabled>
                        <i class="fas fa-plus mr-2"></i><?= $t['nuevo_proyecto'] ?>
                    </button>
                    <?php endif; ?>
                </div>

                <!-- Cards de Proyectos -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php if (count($proyectos) > 0): foreach ($proyectos as $p): 
                        $estado_config = [
                            'activo' => ['color' => 'bg-green-100 text-green-800 border-green-300', 'icon' => 'fa-check-circle'],
                            'pendiente' => ['color' => 'bg-yellow-100 text-yellow-800 border-yellow-300', 'icon' => 'fa-clock'],
                            'finalizado' => ['color' => 'bg-blue-100 text-blue-800 border-blue-300', 'icon' => 'fa-flag-checkered']
                        ];
                        $config = $estado_config[$p['estado']] ?? $estado_config['pendiente'];
                    ?>
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden card-hover border-t-4 border-blue-500">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-gray-800 flex-1 line-clamp-2">
                                    <?= htmlspecialchars($p['nombre_proyecto']) ?>
                                </h3>
                                <span class="ml-2 text-xs text-gray-500 font-semibold">#<?= $p['id'] ?></span>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                <?= htmlspecialchars($p['descripcion_proyecto'] ?? 'Sin descripción') ?>
                            </p>

                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-clock mr-1 text-blue-500"></i>
                                        <span class="font-semibold"><?= $p['horas'] ?>h</span>
                                    </span>
                                    <span
                                        class="<?= $config['color'] ?> px-3 py-1 text-xs rounded-full border flex items-center">
                                        <i class="fas <?= $config['icon'] ?> mr-1"></i>
                                        <?= ucfirst($p['estado']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center text-sm text-gray-600 mb-4">
                                <i class="fas fa-user-tie mr-2 text-green-500"></i>
                                <span
                                    class="truncate"><?= htmlspecialchars($p['nombre_lider'] ?? 'Sin asignar') ?></span>
                            </div>

                            <div class="flex gap-2 mt-4 pt-4 border-t">
                                <button onclick="verProyecto(<?= $p['id'] ?>)"
                                    class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>Ver
                                </button>
                                <button onclick="editarProyecto(<?= $p['id'] ?>)"
                                    class="flex-1 px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </button>
                                <button
                                    onclick="eliminar('proyecto', <?= $p['id'] ?>, '<?= addslashes($p['nombre_proyecto']) ?>')"
                                    class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="col-span-full">
                        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                            <div
                                class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-5xl text-gray-300"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">No hay proyectos</h3>

                            <?php if ($puede_crear_proyectos): ?>
                            <p class="text-gray-600 mb-6">Comienza creando tu primer proyecto</p>
                            <button onclick="openModal('modalNuevoProyecto')"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium shadow-lg transition transform hover:scale-105">
                                <i class="fas fa-plus mr-2"></i>Crear primer proyecto
                            </button>
                            <?php else: ?>
                            <div class="max-w-md mx-auto">
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-6 text-left">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700"><?= $t['crear_lider_primero'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <button onclick="cambiarSeccion('lideres')"
                                    class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-medium shadow-lg transition transform hover:scale-105">
                                    <i class="fas fa-user-plus mr-2"></i>Crear primer líder
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECCIÓN: LÍDERES -->
            <div id="seccion-lideres" class="seccion-content <?= $seccion_activa === 'lideres' ? 'active' : '' ?>">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800"><?= $t['lideres_proyecto'] ?></h2>
                        <p class="text-gray-600 mt-1 flex items-center">
                            <i class="fas fa-users mr-2 text-green-500"></i>
                            <?= count($lideres) ?> <?= $t['lideres_activos'] ?>
                        </p>
                    </div>
                    <button onclick="openModal('modalNuevoLider')"
                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i>Nuevo líder
                    </button>
                </div>

                <!-- Cards de Líderes -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php if (count($lideres) > 0): foreach ($lideres as $l): ?>
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden card-hover border-t-4 border-green-500">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                    <?= strtoupper(substr($l['nombre'], 0, 1) . substr($l['apellido'], 0, 1)) ?>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-bold text-gray-800">
                                        <?= htmlspecialchars($l['nombre'] . ' ' . $l['apellido']) ?>
                                    </h3>
                                    <p class="text-xs text-gray-500 font-semibold">ID: #<?= $l['id'] ?></p>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-envelope mr-2 text-blue-500 w-4"></i>
                                    <span class="truncate"><?= htmlspecialchars($l['email']) ?></span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-phone mr-2 text-green-500 w-4"></i>
                                    <span><?= htmlspecialchars($l['telefono'] ?? 'No disponible') ?></span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-user mr-2 text-purple-500 w-4"></i>
                                    <span><?= htmlspecialchars($l['usuario']) ?></span>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-4 pt-4 border-t">
                                <button onclick="verLider(<?= $l['id'] ?>)"
                                    class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>Ver
                                </button>
                                <button onclick="editarLider(<?= $l['id'] ?>)"
                                    class="flex-1 px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </button>
                                <button onclick="eliminar('lider', <?= $l['id'] ?>, '<?= addslashes($l['nombre']) ?>')"
                                    class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="col-span-full">
                        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                            <div
                                class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user-tie text-5xl text-gray-300"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">No hay líderes</h3>
                            <p class="text-gray-600 mb-6">Debes crear al menos un líder para poder crear proyectos</p>
                            <button onclick="openModal('modalNuevoLider')"
                                class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-medium shadow-lg transition transform hover:scale-105">
                                <i class="fas fa-user-plus mr-2"></i>Agregar primer líder
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECCIÓN: ORGANIZACIÓN -->
            <div id="seccion-organizacion"
                class="seccion-content <?= $seccion_activa === 'organizacion' ? 'active' : '' ?>">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6"><?= $t['informacion_organizacion'] ?></h2>

                    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 card-hover">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-8 text-white">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-building text-4xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold"><?= htmlspecialchars($mi_organizacion['nombre']) ?>
                                    </h3>
                                    <p class="text-blue-200 mt-1 flex items-center">
                                        <i class="fas fa-id-card mr-2"></i>ID: #<?= $mi_organizacion['id'] ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="flex items-start space-x-3">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-envelope text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500 font-medium"><?= $t['email'] ?></p>
                                        <p class="text-gray-800 font-semibold">
                                            <?= htmlspecialchars($mi_organizacion['email']) ?></p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div
                                        class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-phone text-green-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500 font-medium"><?= $t['telefono'] ?></p>
                                        <p class="text-gray-800 font-semibold">
                                            <?= htmlspecialchars($mi_organizacion['telefono'] ?? 'No disponible') ?></p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div
                                        class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-purple-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500 font-medium"><?= $t['direccion'] ?></p>
                                        <p class="text-gray-800 font-semibold">
                                            <?= htmlspecialchars($mi_organizacion['direccion'] ?? 'No especificada') ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div
                                        class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calendar text-orange-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500 font-medium"><?= $t['registro'] ?></p>
                                        <p class="text-gray-800 font-semibold">
                                            <?= isset($mi_organizacion['created_at']) && !empty($mi_organizacion['created_at']) 
                                                ? date('d/m/Y', strtotime($mi_organizacion['created_at'])) 
                                                : 'No registrada' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 pt-4 border-t">
                                <div
                                    class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-info-circle text-gray-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500 font-medium"><?= $t['descripcion'] ?></p>
                                    <p class="text-gray-800">
                                        <?= htmlspecialchars($mi_organizacion['descripcion'] ?? 'Sin descripción disponible') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-cog mr-3 text-blue-600"></i><?= $t['configuracion'] ?>
                        </h3>
                        <button onclick="openModal('modalEditarOrganizacion')"
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition transform hover:scale-105">
                            <i class="fas fa-edit mr-2"></i><?= $t['editar_informacion'] ?>
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- MODALES -->

    <!-- Modal: Nuevo Proyecto -->
    <div id="modalNuevoProyecto" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl">
            <div
                class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 text-white rounded-t-xl z-10 flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-clipboard-check mr-3"></i><?= $t['crear_nuevo_proyecto'] ?>
                </h2>
                <button onclick="closeModal('modalNuevoProyecto')"
                    class="text-white hover:text-gray-200 text-3xl font-bold transition">&times;</button>
            </div>

            <form action="<?= $base_web_url ?>/controllers/ProyectoController.php?action=crear" method="POST"
                class="p-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['nombre_proyecto'] ?>
                            *</label>
                        <input type="text" name="nombre" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Ej: Sistema de gestión académica">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['descripcion'] ?></label>
                        <textarea name="descripcion" rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Describe brevemente el proyecto..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['horas_estimadas'] ?>
                                *</label>
                            <input type="number" name="horas" required min="1"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['estado'] ?></label>
                            <select name="estado"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="pendiente">Pendiente</option>
                                <option value="activo">Activo</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['asignar_lider'] ?>
                            *</label>
                        <div class="flex space-x-2">
                            <select name="lider_proyecto_id" required
                                class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">Seleccionar líder...</option>
                                <?php foreach ($lideres as $lider): ?>
                                <option value="<?= $lider['id'] ?>">
                                    <?= htmlspecialchars($lider['nombre'] . ' ' . $lider['apellido']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button"
                                onclick="openModal('modalNuevoLider'); closeModal('modalNuevoProyecto')"
                                class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                <i class="fas fa-user-plus"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Si no existe el líder, puedes crear uno nuevo</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['fecha_inicio'] ?></label>
                            <input type="date" name="fecha_inicio"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['fecha_fin_estimada'] ?></label>
                            <input type="date" name="fecha_fin"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalNuevoProyecto')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        <?= $t['cancelar'] ?>
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i><?= $t['crear_proyecto'] ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Ver Proyecto -->
    <div id="modalVerProyecto" class="modal-overlay hidden">
        <div class="modal-content max-w-3xl">
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 text-white rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-eye mr-3"></i><?= $t['detalles_proyecto'] ?>
                </h2>
                <button onclick="closeModal('modalVerProyecto')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>
            <div class="p-6" id="detallesProyecto">
                <div class="text-center py-12">
                    <div class="loading-spinner mx-auto mb-4"></div>
                    <p class="text-gray-600 font-medium">Cargando detalles del proyecto...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Proyecto -->
    <div id="modalEditarProyecto" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl">
            <div
                class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-5 text-white rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-edit mr-3"></i><?= $t['editar_proyecto'] ?>
                </h2>
                <button onclick="closeModal('modalEditarProyecto')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>

            <form action="<?= $base_path ?>ProyectoController.php?action=editar" method="POST" class="p-6"
                id="formEditarProyecto">
                <input type="hidden" name="id" id="edit_proyecto_id">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['nombre_proyecto'] ?>
                            *</label>
                        <input type="text" name="nombre" id="edit_proyecto_nombre" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['descripcion'] ?></label>
                        <textarea name="descripcion" id="edit_proyecto_descripcion" rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['horas_estimadas'] ?>
                                *</label>
                            <input type="number" name="horas" id="edit_proyecto_horas" required min="1"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['estado'] ?></label>
                            <select name="estado" id="edit_proyecto_estado"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                <option value="pendiente">Pendiente</option>
                                <option value="activo">Activo</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['asignar_lider'] ?>
                            *</label>
                        <div class="flex space-x-2">
                            <select name="lider_proyecto_id" id="edit_proyecto_lider" required
                                class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                <option value="">Seleccionar líder...</option>
                                <?php foreach ($lideres as $lider): ?>
                                <option value="<?= $lider['id'] ?>">
                                    <?= htmlspecialchars($lider['nombre'] . ' ' . $lider['apellido']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button"
                                onclick="openModal('modalNuevoLider'); closeModal('modalEditarProyecto')"
                                class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                                <i class="fas fa-user-plus"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Si no existe el líder, puedes crear uno nuevo</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['fecha_inicio'] ?></label>
                            <input type="date" name="fecha_inicio" id="edit_proyecto_fecha_inicio"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['fecha_fin_estimada'] ?></label>
                            <input type="date" name="fecha_fin" id="edit_proyecto_fecha_fin"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalEditarProyecto')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        <?= $t['cancelar'] ?>
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white rounded-lg hover:from-yellow-700 hover:to-yellow-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i><?= $t['guardar_cambios'] ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Ver Líder -->
    <div id="modalVerLider" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl">
            <div
                class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5 text-white rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-eye mr-3"></i><?= $t['detalles_lider'] ?>
                </h2>
                <button onclick="closeModal('modalVerLider')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>
            <div class="p-6" id="detallesLider">
                <div class="text-center py-12">
                    <div class="loading-spinner mx-auto mb-4"></div>
                    <p class="text-gray-600 font-medium">Cargando detalles del líder...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Líder -->
    <div id="modalEditarLider" class="modal-overlay hidden">
        <div class="modal-content max-w-lg">
            <div
                class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-5 text-white rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-edit mr-3"></i><?= $t['editar_lider'] ?>
                </h2>
                <button onclick="closeModal('modalEditarLider')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>

            <form action="<?= $base_web_url ?>/controllers/LiderController.php?action=editar" method="POST" class="p-6">
                <input type="hidden" name="id" id="edit_lider_id">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['nombre'] ?> *</label>
                            <input type="text" name="nombre" id="edit_lider_nombre" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['apellido'] ?>
                                *</label>
                            <input type="text" name="apellido" id="edit_lider_apellido" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['email'] ?> *</label>
                        <input type="email" name="email" id="edit_lider_email" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['telefono'] ?></label>
                        <input type="tel" name="telefono" id="edit_lider_telefono"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                    </div>

                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-key mr-2 text-yellow-600"></i><?= $t['cambiar_contrasena'] ?> (opcional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-3">Dejar vacío para mantener la contraseña actual</p>
                        <input type="password" name="contrasena"
                            placeholder="<?= $t['nueva_contrasena'] ?> (mínimo 6 caracteres)"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalEditarLider')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        <?= $t['cancelar'] ?>
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white rounded-lg hover:from-yellow-700 hover:to-yellow-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i><?= $t['guardar_cambios'] ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Nuevo Líder -->
    <div id="modalNuevoLider" class="modal-overlay hidden">
        <div class="modal-content max-w-lg">
            <div
                class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5 text-white rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-user-plus mr-3"></i><?= $t['crear_nuevo_lider'] ?>
                </h2>
                <button onclick="closeModal('modalNuevoLider')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>

            <form action="<?= $base_web_url ?>/controllers/LiderController.php?action=crear" method="POST" class="p-6">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['nombre'] ?> *</label>
                            <input type="text" name="nombre" id="liderNombre" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['apellido'] ?>
                                *</label>
                            <input type="text" name="apellido" id="liderApellido" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['email'] ?> *</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['telefono'] ?></label>
                        <input type="tel" name="telefono"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-lg border-2 border-green-200">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-key mr-2 text-green-600"></i><?= $t['credenciales_acceso'] ?>
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['usuario'] ?>
                                    *</label>
                                <div class="flex">
                                    <input type="text" name="usuario" id="usuarioInput" required
                                        class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-l-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                    <button type="button" onclick="genUsuario()"
                                        class="px-4 bg-green-100 border-2 border-l-0 border-gray-300 rounded-r-lg hover:bg-green-200 transition">
                                        <i class="fas fa-redo text-green-600"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['contrasena'] ?>
                                    *</label>
                                <div class="flex">
                                    <input type="password" name="contrasena" id="passwordInput" required
                                        class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-l-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                    <button type="button" onclick="togglePass()"
                                        class="px-4 bg-gray-100 border-2 border-l-0 border-r-0 border-gray-300 hover:bg-gray-200 transition">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                    <button type="button" onclick="genPass()"
                                        class="px-4 bg-green-100 border-2 border-l-0 border-gray-300 rounded-r-lg hover:bg-green-200 transition">
                                        <i class="fas fa-redo text-green-600"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalNuevoLider')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        <?= $t['cancelar'] ?>
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i><?= $t['crear_lider'] ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Editar Organización -->
    <div id="modalEditarOrganizacion" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl">
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 text-white rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-edit mr-3"></i>Editar organización
                </h2>
                <button onclick="closeModal('modalEditarOrganizacion')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>

            <form action="<?= $base_web_url ?>/controllers/OrganizacionController.php?action=editar" method="POST"
                class="p-6">
                <input type="hidden" name="id" value="<?= $mi_organizacion['id'] ?>">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['nombre'] ?> *</label>
                        <input type="text" name="nombre" required
                            value="<?= htmlspecialchars($mi_organizacion['nombre']) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['email'] ?> *</label>
                        <input type="email" name="email" required
                            value="<?= htmlspecialchars($mi_organizacion['email']) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['telefono'] ?></label>
                            <input type="tel" name="telefono"
                                value="<?= htmlspecialchars($mi_organizacion['telefono'] ?? '') ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['direccion'] ?></label>
                            <input type="text" name="direccion"
                                value="<?= htmlspecialchars($mi_organizacion['direccion'] ?? '') ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $t['descripcion'] ?></label>
                        <textarea name="descripcion" rows="4"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?= htmlspecialchars($mi_organizacion['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalEditarOrganizacion')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        <?= $t['cancelar'] ?>
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i><?= $t['guardar_cambios'] ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Ayuda -->
    <div id="modalAyuda" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl">
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 text-white rounded-t-xl flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-question-circle mr-3"></i><?= $t['guia_sistema'] ?>
                </h2>
                <button onclick="closeModal('modalAyuda')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>
            <div class="p-6 space-y-5">
                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-lg">
                    <p class="text-gray-700">Sistema de caracterización de proyectos basado en el framework Cynefin.</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-project-diagram text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">1. Proyectos</h3>
                            <p class="text-gray-600 text-sm">Crea y gestiona proyectos asignando un líder responsable.
                                Cada proyecto puede ser caracterizado según el framework Cynefin.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-tie text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">2. Líderes</h3>
                            <p class="text-gray-600 text-sm">Administra líderes de proyecto. Al crear un líder, se
                                generan automáticamente sus credenciales de acceso al sistema.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-building text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-1">3. Mi organización</h3>
                            <p class="text-gray-600 text-sm">Configura y actualiza la información de tu organización,
                                incluyendo datos de contacto y descripción.</p>
                        </div>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg mt-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-1"><?= $t['funcionalidades'] ?></h4>
                                <p class="text-gray-600 text-sm">Todas las secciones permiten operaciones CRUD
                                    completas: Crear, Ver, Editar y Eliminar registros de forma segura.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Logout -->
    <div id="modalLogout" class="modal-overlay hidden">
        <div class="modal-content max-w-md">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-out-alt text-4xl text-red-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2"><?= $t['cerrar_sesion'] ?></h3>
                <p class="text-gray-600 mb-6">¿Estás seguro de que deseas salir del sistema?</p>
                <div class="flex gap-3">
                    <button onclick="closeModal('modalLogout')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        <?= $t['cancelar'] ?>
                    </button>
                    <a href="<?= $base_web_url ?>/index.php?action=logout"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 font-medium text-center shadow-lg transition">
                        Sí, salir
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // CONFIGURACIÓN DE RUTAS
    const BASE_WEB_URL = '<?= $base_web_url ?>';
    const BASE_PATH = '<?= $base_path ?>';
    const PUEDE_CREAR_PROYECTOS = <?= $puede_crear_proyectos ? 'true' : 'false' ?>;

    // GESTIÓN DE MODALES
    function openModal(id) {
        if (id === 'modalNuevoProyecto' && !PUEDE_CREAR_PROYECTOS) {
            mostrarAlertaSinLideres();
            return;
        }

        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('show'), 10);
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('show');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // GESTIÓN DE SIDEBAR MOBILE
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('mobileOverlay');
        const isActive = sidebar.classList.toggle('active');
        overlay.classList.toggle('hidden');
        document.body.style.overflow = isActive ? 'hidden' : 'auto';
    }

    // ALERTA CUANDO NO HAY LÍDERES
    function mostrarAlertaSinLideres() {
        const alerta = document.createElement('div');
        alerta.className =
            'fixed top-4 right-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-lg z-50 max-w-md';
        alerta.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        <?= $t['crear_lider_primero'] ?>
                    </h3>
                    <div class="mt-2 flex space-x-2">
                        <button onclick="cambiarSeccion('lideres'); this.parentElement.parentElement.parentElement.remove()" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-xs font-medium rounded-md hover:bg-yellow-700 transition">
                            <i class="fas fa-user-plus mr-1"></i>
                            <?= $t['ir_a_lideres'] ?>
                        </button>
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex items-center px-3 py-1.5 bg-gray-200 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-300 transition">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(alerta);

        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.remove();
            }
        }, 10000);
    }

    // CAMBIAR SECCIÓN
    function cambiarSeccion(seccion) {
        document.querySelectorAll('.seccion-content').forEach(s => s.classList.remove('active'));
        const seccionElement = document.getElementById(`seccion-${seccion}`);
        if (seccionElement) {
            seccionElement.classList.add('active');
        }

        const titulos = {
            'proyectos': {
                mobile: '<i class="fas fa-project-diagram mr-2 text-blue-600"></i><?= $t["gestion_proyectos"] ?>',
                desktop: '<i class="fas fa-project-diagram mr-3 text-blue-600"></i><?= $t["gestion_proyectos"] ?>'
            },
            'lideres': {
                mobile: '<i class="fas fa-user-tie mr-2 text-green-600"></i>Líderes',
                desktop: '<i class="fas fa-user-tie mr-3 text-green-600"></i>Gestión de líderes'
            },
            'organizacion': {
                mobile: '<i class="fas fa-building mr-2 text-blue-600"></i>Mi organización',
                desktop: '<i class="fas fa-building mr-3 text-blue-600"></i>Mi organización'
            }
        };

        if (titulos[seccion]) {
            const tituloMobile = document.getElementById('tituloMobile');
            const tituloDesktop = document.getElementById('tituloDesktop');
            if (tituloMobile) tituloMobile.innerHTML = titulos[seccion].mobile;
            if (tituloDesktop) tituloDesktop.innerHTML = titulos[seccion].desktop;
        }

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // ELIMINAR
    function eliminar(tipo, id, nombre) {
        if (confirm(`¿Estás seguro de eliminar el ${tipo} "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
            const urls = {
                proyecto: 'ProyectoController',
                lider: 'LiderController'
            };
            if (urls[tipo]) {
                window.location.href = `${BASE_PATH}${urls[tipo]}.php?action=eliminar&id=${id}`;
            }
        }
    }

    // VER PROYECTO (AJAX)
    function verProyecto(id) {
        openModal('modalVerProyecto');
        fetch(`${BASE_PATH}ProyectoController.php?action=ver&id=${id}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('detallesProyecto').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('detallesProyecto').innerHTML = `
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-circle text-4xl text-red-600"></i>
                        </div>
                        <p class="text-red-600 font-medium">Error al cargar los detalles del proyecto</p>
                    </div>
                `;
            });
    }

    // VER LÍDER
    function verLider(id) {
        openModal('modalVerLider');
        fetch(`${BASE_PATH}LiderController.php?action=ver&id=${id}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('detallesLider').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('detallesLider').innerHTML = `
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-circle text-4xl text-red-600"></i>
                        </div>
                        <p class="text-red-600 font-medium">Error al cargar los detalles del líder</p>
                    </div>
                `;
            });
    }

    // EDITAR PROYECTO
    function editarProyecto(id) {
        if (!PUEDE_CREAR_PROYECTOS) {
            mostrarAlertaSinLideres();
            return;
        }

        fetch(`${BASE_PATH}ProyectoController.php?action=datos&id=${id}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(proyecto => {
                document.getElementById('edit_proyecto_id').value = proyecto.id;
                document.getElementById('edit_proyecto_nombre').value = proyecto.nombre;
                document.getElementById('edit_proyecto_descripcion').value = proyecto.descripcion || '';
                document.getElementById('edit_proyecto_horas').value = proyecto.horas;
                document.getElementById('edit_proyecto_estado').value = proyecto.estado;
                document.getElementById('edit_proyecto_lider').value = proyecto.lider_proyecto_id;
                document.getElementById('edit_proyecto_fecha_inicio').value = proyecto.fecha_inicio || '';
                document.getElementById('edit_proyecto_fecha_fin').value = proyecto.fecha_fin || '';

                openModal('modalEditarProyecto');
            })
            .catch(error => {
                console.log('Usando fallback:', error);
                openModal('modalEditarProyecto');
            });
    }

    // EDITAR LÍDER
    function editarLider(id) {
        fetch(`${BASE_PATH}LiderController.php?action=datos&id=${id}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(lider => {
                document.getElementById('edit_lider_id').value = lider.id;
                document.getElementById('edit_lider_nombre').value = lider.nombre;
                document.getElementById('edit_lider_apellido').value = lider.apellido;
                document.getElementById('edit_lider_email').value = lider.email;
                document.getElementById('edit_lider_telefono').value = lider.telefono || '';

                openModal('modalEditarLider');
            })
            .catch(error => {
                openModal('modalEditarLider');
            });
    }

    // GENERAR USUARIO AUTOMÁTICO
    function genUsuario() {
        const nombre = document.getElementById('liderNombre')?.value.trim() || '';
        const apellido = document.getElementById('liderApellido')?.value.trim() || '';
        if (nombre && apellido) {
            const usuario = (nombre.toLowerCase() + '.' + apellido.toLowerCase())
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z.]/g, '');
            const usuarioInput = document.getElementById('usuarioInput');
            if (usuarioInput) usuarioInput.value = usuario;
        }
    }

    // TOGGLE PASSWORD VISIBILITY
    let passVisible = false;

    function togglePass() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (input && icon) {
            passVisible = !passVisible;
            input.type = passVisible ? 'text' : 'password';
            icon.className = passVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
        }
    }

    // GENERAR PASSWORD ALEATORIO
    function genPass() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&*';
        let pass = '';
        for (let i = 0; i < 12; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const passwordInput = document.getElementById('passwordInput');
        if (passwordInput) passwordInput.value = pass;
    }

    // INICIALIZACIÓN
    document.addEventListener('DOMContentLoaded', () => {
        const nombreInput = document.getElementById('liderNombre');
        const apellidoInput = document.getElementById('liderApellido');
        if (nombreInput && apellidoInput) {
            nombreInput.addEventListener('input', genUsuario);
            apellidoInput.addEventListener('input', genUsuario);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modales = ['modalNuevoProyecto', 'modalVerProyecto', 'modalNuevoLider',
                    'modalEditarOrganizacion', 'modalAyuda', 'modalLogout'
                ];
                modales.forEach(id => {
                    const modal = document.getElementById(id);
                    if (modal && !modal.classList.contains('hidden')) {
                        closeModal(id);
                    }
                });
            }
        });

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal(modal.id);
                }
            });
        });

        const seccionActiva = '<?= $seccion_activa ?>';
        if (seccionActiva) {
            cambiarSeccion(seccionActiva);
        }
    });
    </script>
</body>

</html>