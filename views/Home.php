<?php
// ========================================
// VIEWS/HOME.PHP - DASHBOARD INTERACTIVO MEJORADO
// ========================================

// 1. INICIAR SESIÓN
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php?action=login_view');
    exit;
}

// 2. CONEXIÓN BD
require_once __DIR__ . '/../config/db.php';
$database = new Database();
$db = $database->getConnection();

if (!($db instanceof PDO)) die("ERROR: No se pudo conectar a la base de datos");

// Detectar ruta base para los controladores
$base_path = (basename(dirname(__FILE__)) === 'views') ? '../controllers/' : 'controllers/';

// 3. VARIABLES
$proyectos = $dominios = $perfiles = $caracteristicas = $paises = $lideres = [];
$mi_organizacion = null;
$error_message = $_SESSION['error'] ?? null;
$success_message = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

$seccion_activa = $_GET['seccion'] ?? 'proyectos';

// 4. CARGAR DATOS
try {
    // PROYECTOS
    $stmt = $db->prepare("
        SELECT p.id, p.nombre AS nombre_proyecto, p.descripcion AS descripcion_proyecto,
               COALESCE(p.horas, 0) as horas, COALESCE(p.estado, 'pendiente') as estado,
               p.fecha_inicio, p.fecha_fin, p.lider_proyecto_id, p.created_at,
               CONCAT(u.nombre, ' ', u.apellido) AS nombre_lider
        FROM proyectos p
        LEFT JOIN usuarios u ON p.lider_proyecto_id = u.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // LÍDERES
    $sql = "SELECT id, nombre, apellido, email, usuario, telefono, creado_en FROM usuarios WHERE rol_id = 2";
    $stmt = $db->query("SHOW COLUMNS FROM usuarios LIKE 'esta_borrado'");
    if ($stmt->rowCount() > 0) $sql .= " AND esta_borrado = 0";
    $sql .= " ORDER BY nombre";
    $stmt = $db->query($sql);
    $lideres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ORGANIZACIÓN
    $usuario_id = $_SESSION['usuario']['id'] ?? 0;
    if ($usuario_id > 0) {
        $stmt = $db->prepare("SELECT * FROM organizaciones WHERE id = 1 OR usuario_admin_id = ? LIMIT 1");
        $stmt->execute([$usuario_id]);
        $mi_organizacion = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$mi_organizacion) {
        $mi_organizacion = [
            'id' => 0, 'nombre' => 'Mi Organización', 'descripcion' => 'Completar perfil',
            'direccion' => 'No especificada', 'telefono' => 'No disponible',
            'email' => $_SESSION['usuario']['email'] ?? 'admin@organizacion.com',
            'activo' => 1, 'fecha_registro' => date('Y-m-d H:i:s')
        ];
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $error_message = "Error al cargar datos del sistema";
}

$nombre_usuario = $_SESSION['usuario']['nombre'] ?? 'Usuario';
$email_usuario = $_SESSION['usuario']['email'] ?? '';
$inicial_usuario = strtoupper(substr($nombre_usuario, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Caracterización</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Reset de estilos para modales */
    .modal-overlay * {
        box-sizing: border-box;
    }

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

    /* Estilos específicos para inputs en modales */
    .modal-content input[type="text"],
    .modal-content input[type="email"],
    .modal-content input[type="tel"],
    .modal-content input[type="number"],
    .modal-content input[type="date"],
    .modal-content input[type="password"],
    .modal-content select,
    .modal-content textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        transition: all 0.2s;
        background: white;
    }

    .modal-content input:focus,
    .modal-content select:focus,
    .modal-content textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .modal-content label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .modal-content button[type="submit"] {
        background: linear-gradient(to right, #3b82f6, #2563eb);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal-content button[type="submit"]:hover {
        background: linear-gradient(to right, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .modal-content button[type="button"] {
        background: #e5e7eb;
        color: #374151;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .modal-content button[type="button"]:hover {
        background: #d1d5db;
    }

    .sidebar-mobile {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-mobile.active {
        transform: translateX(0);
    }

    .nav-link {
        transition: all 0.2s ease;
    }

    .nav-link.active {
        background-color: #374151;
        border-left: 4px solid #3b82f6;
    }

    .nav-link:hover {
        transform: translateX(4px);
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
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.5rem;
        height: 1.5rem;
        padding: 0 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .modal-content {
            max-height: 95vh;
            border-radius: 0.5rem;
        }
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
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar Desktop -->
        <aside class="hidden lg:flex lg:w-64 bg-gray-800 text-white flex-col fixed h-screen shadow-2xl">
            <div class="p-6 border-b border-gray-700 bg-gray-900">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-chart-line mr-2 text-blue-400"></i>
                    Dashboard
                </h1>
                <p class="text-xs text-gray-400 mt-1">Sistema Cynefin</p>
            </div>

            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-11 h-11 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center font-bold text-lg shadow-lg">
                        <?= $inicial_usuario ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate"><?= htmlspecialchars($nombre_usuario) ?></p>
                        <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars($email_usuario) ?></p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <p class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold tracking-wider">Gestión Principal</p>

                <a href="#" onclick="cambiarSeccion('proyectos'); return false;"
                    class="nav-link block px-4 py-3 rounded-lg <?= $seccion_activa === 'proyectos' ? 'active' : '' ?>"
                    data-seccion="proyectos">
                    <i class="fas fa-project-diagram mr-3"></i>Proyectos
                    <span class="badge bg-blue-500 float-right"><?= count($proyectos) ?></span>
                </a>

                <a href="#" onclick="cambiarSeccion('lideres'); return false;"
                    class="nav-link block px-4 py-3 rounded-lg <?= $seccion_activa === 'lideres' ? 'active' : '' ?>"
                    data-seccion="lideres">
                    <i class="fas fa-user-tie mr-3"></i>Líderes
                    <span class="badge bg-green-500 float-right"><?= count($lideres) ?></span>
                </a>

                <a href="#" onclick="cambiarSeccion('organizacion'); return false;"
                    class="nav-link block px-4 py-3 rounded-lg <?= $seccion_activa === 'organizacion' ? 'active' : '' ?>"
                    data-seccion="organizacion">
                    <i class="fas fa-building mr-3"></i>Mi Organización
                </a>
            </nav>

            <div class="p-4 border-t border-gray-700 space-y-2">
                <button onclick="openModal('modalAyuda')"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-question-circle mr-2"></i>Ayuda
                </button>
                <button onclick="openModal('modalLogout')"
                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </button>
            </div>
        </aside>

        <!-- Sidebar Mobile -->
        <div id="mobileOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"
            onclick="toggleMobileSidebar()"></div>

        <aside id="mobileSidebar"
            class="sidebar-mobile lg:hidden w-64 bg-gray-800 text-white fixed h-screen z-40 shadow-2xl">
            <div class="p-4 border-b border-gray-700 flex justify-between items-center bg-gray-900">
                <div>
                    <h1 class="text-xl font-bold flex items-center">
                        <i class="fas fa-chart-line mr-2 text-blue-400"></i>Dashboard
                    </h1>
                    <p class="text-xs text-gray-400">Sistema Cynefin</p>
                </div>
                <button onclick="toggleMobileSidebar()" class="text-white text-2xl hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center font-bold shadow-lg">
                        <?= $inicial_usuario ?>
                    </div>
                    <div>
                        <p class="font-medium"><?= htmlspecialchars($nombre_usuario) ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($email_usuario) ?></p>
                    </div>
                </div>
            </div>

            <nav class="p-4 space-y-2 overflow-y-auto" style="max-height: calc(100vh - 280px);">
                <p class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold">Gestión Principal</p>

                <a href="#" onclick="cambiarSeccion('proyectos'); toggleMobileSidebar(); return false;"
                    class="nav-link block px-4 py-3 rounded-lg" data-seccion="proyectos">
                    <i class="fas fa-project-diagram mr-3"></i>Proyectos
                    <span class="badge bg-blue-500 float-right"><?= count($proyectos) ?></span>
                </a>

                <a href="#" onclick="cambiarSeccion('lideres'); toggleMobileSidebar(); return false;"
                    class="nav-link block px-4 py-3 rounded-lg" data-seccion="lideres">
                    <i class="fas fa-user-tie mr-3"></i>Líderes
                    <span class="badge bg-green-500 float-right"><?= count($lideres) ?></span>
                </a>

                <a href="#" onclick="cambiarSeccion('organizacion'); toggleMobileSidebar(); return false;"
                    class="nav-link block px-4 py-3 rounded-lg" data-seccion="organizacion">
                    <i class="fas fa-building mr-3"></i>Mi Organización
                </a>

                <div class="border-t border-gray-700 my-3"></div>

                <p class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold">Configuración</p>

                <a href="#" class="nav-link block px-4 py-3 rounded-lg opacity-50 cursor-not-allowed">
                    <i class="fas fa-sitemap mr-3"></i>Dominios
                    <span class="badge bg-gray-500 float-right text-xs">Próx.</span>
                </a>

                <a href="#" class="nav-link block px-4 py-3 rounded-lg opacity-50 cursor-not-allowed">
                    <i class="fas fa-users-cog mr-3"></i>Perfiles
                    <span class="badge bg-gray-500 float-right text-xs">Próx.</span>
                </a>

                <a href="#" class="nav-link block px-4 py-3 rounded-lg opacity-50 cursor-not-allowed">
                    <i class="fas fa-layer-group mr-3"></i>Características
                    <span class="badge bg-gray-500 float-right text-xs">Próx.</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-700 space-y-2 absolute bottom-0 left-0 right-0 bg-gray-800">
                <button onclick="openModal('modalAyuda'); toggleMobileSidebar();"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-question-circle mr-2"></i>Ayuda
                </button>
                <button onclick="openModal('modalLogout'); toggleMobileSidebar();"
                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </button>
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
                    <i class="fas fa-project-diagram mr-2 text-blue-600"></i>Proyectos
                </h1>
                <div class="w-10"></div>
            </div>

            <!-- Título Desktop -->
            <div class="hidden lg:block mb-8">
                <h1 class="text-3xl font-bold text-gray-800" id="tituloDesktop">
                    <i class="fas fa-project-diagram mr-3 text-blue-600"></i>Gestión de Proyectos
                </h1>
                <p class="text-gray-600 mt-2">Sistema de Caracterización - Framework Cynefin</p>
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
                        <h2 class="text-2xl font-semibold text-gray-800">Proyectos Activos</h2>
                        <p class="text-gray-600 mt-1 flex items-center">
                            <i class="fas fa-folder mr-2 text-blue-500"></i>
                            <?= count($proyectos) ?> proyecto(s) registrado(s)
                        </p>
                    </div>
                    <button onclick="openModal('modalNuevoProyecto')"
                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>Nuevo Proyecto
                    </button>
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
                            <p class="text-gray-600 mb-6">Comienza creando tu primer proyecto</p>
                            <button onclick="openModal('modalNuevoProyecto')"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium shadow-lg transition transform hover:scale-105">
                                <i class="fas fa-plus mr-2"></i>Crear Primer Proyecto
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECCIÓN: LÍDERES -->
            <div id="seccion-lideres" class="seccion-content <?= $seccion_activa === 'lideres' ? 'active' : '' ?>">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Líderes de Proyecto</h2>
                        <p class="text-gray-600 mt-1 flex items-center">
                            <i class="fas fa-users mr-2 text-green-500"></i>
                            <?= count($lideres) ?> líder(es) activo(s)
                        </p>
                    </div>
                    <button onclick="openModal('modalNuevoLider')"
                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i>Nuevo Líder
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
                            <p class="text-gray-600 mb-6">Agrega el primer líder de proyecto</p>
                            <button onclick="openModal('modalNuevoLider')"
                                class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-medium shadow-lg transition transform hover:scale-105">
                                <i class="fas fa-user-plus mr-2"></i>Agregar Primer Líder
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
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Información de la Organización</h2>

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
                                        <p class="text-sm text-gray-500 font-medium">Email</p>
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
                                        <p class="text-sm text-gray-500 font-medium">Teléfono</p>
                                        <p class="text-gray-800 font-semibold">
                                            <?= htmlspecialchars($mi_organizacion['telefono'] ?? 'No disponible') ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div
                                        class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-purple-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500 font-medium">Dirección</p>
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
                                        <p class="text-sm text-gray-500 font-medium">Registro</p>
                                        <p class="text-gray-800 font-semibold">
                                            <?= isset($mi_organizacion['created_at']) && !empty($mi_organizacion['created_at']) 
                                                ? date('d/m/Y', strtotime($mi_organizacion['created_at'])) 
                                                : (isset($mi_organizacion['fecha_registro']) && !empty($mi_organizacion['fecha_registro'])
                                                    ? date('d/m/Y', strtotime($mi_organizacion['fecha_registro']))
                                                    : 'No registrada') ?>
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
                                    <p class="text-sm text-gray-500 font-medium">Descripción</p>
                                    <p class="text-gray-800">
                                        <?= htmlspecialchars($mi_organizacion['descripcion'] ?? 'Sin descripción disponible') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-cog mr-3 text-blue-600"></i>
                            Configuración
                        </h3>
                        <button onclick="openModal('modalEditarOrganizacion')"
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition transform hover:scale-105">
                            <i class="fas fa-edit mr-2"></i>Editar Información
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
                    <i class="fas fa-clipboard-check mr-3"></i>Crear Nuevo Proyecto
                </h2>
                <button onclick="closeModal('modalNuevoProyecto')"
                    class="text-white hover:text-gray-200 text-3xl font-bold transition">&times;</button>
            </div>

            <form action="<?= $base_path ?>ProyectoController.php?action=crear" method="POST" class="p-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Proyecto *</label>
                        <input type="text" name="nombre" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Ej: Sistema de Gestión Académica">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Describe brevemente el proyecto..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Horas Estimadas *</label>
                            <input type="number" name="horas" required min="1"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                            <select name="estado"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="pendiente">Pendiente</option>
                                <option value="activo">Activo</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Asignar Líder *</label>
                        <select name="lider_proyecto_id" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="">Seleccionar líder...</option>
                            <?php foreach ($lideres as $lider): ?>
                            <option value="<?= $lider['id'] ?>">
                                <?= htmlspecialchars($lider['nombre'] . ' ' . $lider['apellido']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha Fin Estimada</label>
                            <input type="date" name="fecha_fin"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalNuevoProyecto')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>Crear Proyecto
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
                    <i class="fas fa-eye mr-3"></i>Detalles del Proyecto
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
                    <i class="fas fa-edit mr-3"></i>Editar Proyecto
                </h2>
                <button onclick="closeModal('modalEditarProyecto')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>

            <form action="<?= $base_path ?>ProyectoController.php?action=editar" method="POST" class="p-6"
                id="formEditarProyecto">
                <input type="hidden" name="id" id="edit_proyecto_id">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Proyecto *</label>
                        <input type="text" name="nombre" id="edit_proyecto_nombre" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" id="edit_proyecto_descripcion" rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Horas Estimadas *</label>
                            <input type="number" name="horas" id="edit_proyecto_horas" required min="1"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                            <select name="estado" id="edit_proyecto_estado"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                <option value="pendiente">Pendiente</option>
                                <option value="activo">Activo</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Asignar Líder *</label>
                        <select name="lider_proyecto_id" id="edit_proyecto_lider" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                            <option value="">Seleccionar líder...</option>
                            <?php foreach ($lideres as $lider): ?>
                            <option value="<?= $lider['id'] ?>">
                                <?= htmlspecialchars($lider['nombre'] . ' ' . $lider['apellido']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="edit_proyecto_fecha_inicio"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha Fin Estimada</label>
                            <input type="date" name="fecha_fin" id="edit_proyecto_fecha_fin"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalEditarProyecto')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white rounded-lg hover:from-yellow-700 hover:to-yellow-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
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
                    <i class="fas fa-eye mr-3"></i>Detalles del Líder
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
                    <i class="fas fa-edit mr-3"></i>Editar Líder
                </h2>
                <button onclick="closeModal('modalEditarLider')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>

            <form action="<?= $base_path ?>LiderController.php?action=editar" method="POST" class="p-6">
                <input type="hidden" name="id" id="edit_lider_id">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre *</label>
                            <input type="text" name="nombre" id="edit_lider_nombre" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Apellido *</label>
                            <input type="text" name="apellido" id="edit_lider_apellido" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" id="edit_lider_email" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                        <input type="tel" name="telefono" id="edit_lider_telefono"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                    </div>

                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800 mb-2 flex items-center">
                            <i class="fas fa-key mr-2 text-yellow-600"></i>Cambiar Contraseña (opcional)
                        </h4>
                        <p class="text-sm text-gray-600 mb-3">Dejar vacío para mantener la contraseña actual</p>
                        <input type="password" name="contrasena" placeholder="Nueva contraseña (mínimo 6 caracteres)"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalEditarLider')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white rounded-lg hover:from-yellow-700 hover:to-yellow-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
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
                    <i class="fas fa-user-plus mr-3"></i>Nuevo Líder
                </h2>
                <button onclick="closeModal('modalNuevoLider')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>

            <form action="<?= $base_path ?>LiderController.php?action=crear" method="POST" class="p-6">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre *</label>
                            <input type="text" name="nombre" id="liderNombre" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Apellido *</label>
                            <input type="text" name="apellido" id="liderApellido" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                        <input type="tel" name="telefono"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-lg border-2 border-green-200">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-key mr-2 text-green-600"></i>Credenciales de Acceso
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Usuario *</label>
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
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contraseña *</label>
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
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>Crear Líder
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
                    <i class="fas fa-edit mr-3"></i>Editar Organización
                </h2>
                <button onclick="closeModal('modalEditarOrganizacion')"
                    class="text-white hover:text-gray-200 text-3xl transition">&times;</button>
            </div>

            <form action="<?= $base_path ?>OrganizacionController.php?action=editar" method="POST" class="p-6">
                <input type="hidden" name="id" value="<?= $mi_organizacion['id'] ?>">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre *</label>
                        <input type="text" name="nombre" required
                            value="<?= htmlspecialchars($mi_organizacion['nombre']) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" required
                            value="<?= htmlspecialchars($mi_organizacion['email']) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                            <input type="tel" name="telefono"
                                value="<?= htmlspecialchars($mi_organizacion['telefono'] ?? '') ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Dirección</label>
                            <input type="text" name="direccion"
                                value="<?= htmlspecialchars($mi_organizacion['direccion'] ?? '') ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                        <textarea name="descripcion" rows="4"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?= htmlspecialchars($mi_organizacion['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-6 border-t">
                    <button type="button" onclick="closeModal('modalEditarOrganizacion')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
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
                    <i class="fas fa-question-circle mr-3"></i>Guía del Sistema
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
                            <h3 class="font-bold text-gray-800 mb-1">3. Mi Organización</h3>
                            <p class="text-gray-600 text-sm">Configura y actualiza la información de tu organización,
                                incluyendo datos de contacto y descripción.</p>
                        </div>
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg mt-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-1">Funcionalidades</h4>
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
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Cerrar Sesión</h3>
                <p class="text-gray-600 mb-6">¿Estás seguro de que deseas salir del sistema?</p>
                <div class="flex gap-3">
                    <button onclick="closeModal('modalLogout')"
                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition">
                        Cancelar
                    </button>
                    <a href="<?= $base_path ?>../index.php?action=logout"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 font-medium text-center shadow-lg transition">
                        Sí, Salir
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // CONFIGURACIÓN DE RUTAS
    const BASE_PATH = '<?= $base_path ?>';

    // GESTIÓN DE MODALES
    function openModal(id) {
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

    // CAMBIAR SECCIÓN
    function cambiarSeccion(seccion) {
        // Ocultar todas las secciones
        document.querySelectorAll('.seccion-content').forEach(s => s.classList.remove('active'));

        // Mostrar la sección seleccionada
        const seccionElement = document.getElementById(`seccion-${seccion}`);
        if (seccionElement) {
            seccionElement.classList.add('active');
        }

        // Actualizar enlaces del menú
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.dataset.seccion === seccion) {
                link.classList.add('active');
            }
        });

        // Actualizar títulos
        const titulos = {
            'proyectos': {
                mobile: '<i class="fas fa-project-diagram mr-2 text-blue-600"></i>Proyectos',
                desktop: '<i class="fas fa-project-diagram mr-3 text-blue-600"></i>Gestión de Proyectos'
            },
            'lideres': {
                mobile: '<i class="fas fa-user-tie mr-2 text-green-600"></i>Líderes',
                desktop: '<i class="fas fa-user-tie mr-3 text-green-600"></i>Gestión de Líderes'
            },
            'organizacion': {
                mobile: '<i class="fas fa-building mr-2 text-blue-600"></i>Mi Organización',
                desktop: '<i class="fas fa-building mr-3 text-blue-600"></i>Mi Organización'
            }
        };

        if (titulos[seccion]) {
            document.getElementById('tituloMobile').innerHTML = titulos[seccion].mobile;
            document.getElementById('tituloDesktop').innerHTML = titulos[seccion].desktop;
        }

        // Scroll to top
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
        fetch(`${BASE_PATH}ProyectoController.php?action=ver&id=${id}`)
            .then(response => response.text())
            .then(html => {
                // Extraer datos del proyecto (temporal - idealmente usar JSON)
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Obtener proyecto completo vía otra petición JSON
                return fetch(`${BASE_PATH}ProyectoController.php?action=datos&id=${id}`);
            })
            .then(response => response.json())
            .then(proyecto => {
                // Llenar formulario de edición
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
                // Fallback simple - cargar datos de PHP
                const proyectos = <?= json_encode($proyectos) ?>;
                const proyecto = proyectos.find(p => p.id == id);

                if (proyecto) {
                    document.getElementById('edit_proyecto_id').value = proyecto.id;
                    document.getElementById('edit_proyecto_nombre').value = proyecto.nombre_proyecto;
                    document.getElementById('edit_proyecto_descripcion').value = proyecto.descripcion_proyecto ||
                    '';
                    document.getElementById('edit_proyecto_horas').value = proyecto.horas;
                    document.getElementById('edit_proyecto_estado').value = proyecto.estado;
                    document.getElementById('edit_proyecto_lider').value = proyecto.lider_proyecto_id;
                    document.getElementById('edit_proyecto_fecha_inicio').value = proyecto.fecha_inicio || '';
                    document.getElementById('edit_proyecto_fecha_fin').value = proyecto.fecha_fin || '';

                    openModal('modalEditarProyecto');
                } else {
                    alert('Error al cargar los datos del proyecto');
                }
            });
    }

    // EDITAR LÍDER
    function editarLider(id) {
        const lideres = <?= json_encode($lideres) ?>;
        const lider = lideres.find(l => l.id == id);

        if (lider) {
            document.getElementById('edit_lider_id').value = lider.id;
            document.getElementById('edit_lider_nombre').value = lider.nombre;
            document.getElementById('edit_lider_apellido').value = lider.apellido;
            document.getElementById('edit_lider_email').value = lider.email;
            document.getElementById('edit_lider_telefono').value = lider.telefono || '';

            openModal('modalEditarLider');
        } else {
            alert('Error al cargar los datos del líder');
        }
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
            document.getElementById('usuarioInput').value = usuario;
        }
    }

    // TOGGLE PASSWORD VISIBILITY
    let passVisible = false;

    function togglePass() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        passVisible = !passVisible;
        input.type = passVisible ? 'text' : 'password';
        icon.className = passVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
    }

    // GENERAR PASSWORD ALEATORIO
    function genPass() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&*';
        let pass = '';
        for (let i = 0; i < 12; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('passwordInput').value = pass;
    }

    // INICIALIZACIÓN
    document.addEventListener('DOMContentLoaded', () => {
        // Auto-generar usuario cuando se escriben nombre/apellido
        const nombreInput = document.getElementById('liderNombre');
        const apellidoInput = document.getElementById('liderApellido');
        if (nombreInput && apellidoInput) {
            nombreInput.addEventListener('input', genUsuario);
            apellidoInput.addEventListener('input', genUsuario);
        }

        // Cerrar modales con ESC
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

        // Cerrar modal al hacer click fuera
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal(modal.id);
                }
            });
        });

        // Asegurar que la sección activa se muestre correctamente
        const seccionActiva = '<?= $seccion_activa ?>';
        if (seccionActiva) {
            cambiarSeccion(seccionActiva);
        }
    });
    </script>
</body>

</html>