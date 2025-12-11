<?php
// ========================================
// VIEWS/HOME.PHP - VERSIÓN CORREGIDA COMPLETA
// ========================================

// 1. SESIÓN Y AUTENTICACIÓN
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php?action=login_view');
    exit;
}

// 2. CONEXIÓN A BASE DE DATOS
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();

if (!($db instanceof PDO)) {
    die("ERROR CRÍTICO: No se pudo conectar a la base de datos");
}

// 3. INICIALIZAR VARIABLES
$proyectos = [];
$dominios = [];
$perfiles = [];
$complejidades = [];
$paises = [];
$lideres = [];
$mi_organizacion = null;
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : null;
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : null;

// Limpiar mensajes de sesión
unset($_SESSION['error'], $_SESSION['success']);

// 4. DETERMINAR SECCIÓN ACTIVA
$seccion_activa = isset($_GET['seccion']) ? $_GET['seccion'] : 'proyectos';

// 5. CARGAR DATOS DE LA BASE DE DATOS
try {
    // PROYECTOS
    $stmt = $db->prepare("
        SELECT 
            p.id, 
            p.nombre AS nombre_proyecto,
            p.descripcion AS descripcion_proyecto,
            p.horas AS complejidad_total,
            p.pais,
            p.lider_proyecto_id,
            p.dominio_id,
            p.created_at,
            d.nombre AS dominio_cynefin
        FROM proyectos p
        LEFT JOIN dominios d ON p.dominio_id = d.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // DOMINIOS
    $stmt = $db->prepare("SELECT id, nombre FROM dominios ORDER BY nombre");
    $stmt->execute();
    $dominios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // PERFILES (roles)
    $stmt = $db->prepare("SELECT id, nombre, descripcion FROM roles ORDER BY nombre");
    $stmt->execute();
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($perfiles)) {
        $stmt = $db->prepare("SELECT id, nombre, descripcion FROM perfiles ORDER BY nombre");
        $stmt->execute();
        $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // CARACTERÍSTICAS DE COMPLEJIDAD
    $stmt = $db->prepare("SELECT id, nombre, descripcion FROM caracteristicas ORDER BY nombre");
    $stmt->execute();
    $complejidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // PAÍSES
    $stmt = $db->prepare("SELECT id, nombre FROM paises ORDER BY nombre");
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($paises)) {
        $paises = [
            ['id' => 1, 'nombre' => 'Colombia'],
            ['id' => 2, 'nombre' => 'Argentina'],
            ['id' => 3, 'nombre' => 'Chile'],
            ['id' => 4, 'nombre' => 'Perú'],
            ['id' => 5, 'nombre' => 'México'],
        ];
    }
    
    // LÍDERES
    $stmt = $db->prepare("
        SELECT id, nombre, apellido, email, telefono, especialidad, usuario, password, created_at 
        FROM lideres_proyecto 
        ORDER BY nombre
    ");
    $stmt->execute();
    $lideres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // MI ORGANIZACIÓN
    $usuario_id = $_SESSION['usuario']['id'] ?? 0;
    
    if ($usuario_id > 0) {
        $stmt = $db->prepare("
            SELECT 
                o.id, 
                o.nombre, 
                o.descripcion, 
                o.direccion, 
                o.telefono, 
                o.email,
                o.pais_id,
                o.activo,
                o.fecha_registro,
                o.fecha_actualizacion,
                p.nombre AS pais_nombre
            FROM organizaciones o
            LEFT JOIN paises p ON o.pais_id = p.id
            WHERE o.usuario_id = ? OR o.id = 1
            LIMIT 1
        ");
        $stmt->execute([$usuario_id]);
        $mi_organizacion = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$mi_organizacion) {
        $mi_organizacion = [
            'id' => 0,
            'nombre' => 'Mi Organización',
            'descripcion' => 'Complete su perfil',
            'direccion' => 'No especificada',
            'telefono' => 'No disponible',
            'email' => 'admin@organizacion.com',
            'pais_nombre' => 'Colombia',
            'pais_id' => 1,
            'activo' => 1,
            'fecha_registro' => date('Y-m-d H:i:s'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];
    }
    
} catch (PDOException $e) {
    error_log("Error al cargar datos: " . $e->getMessage());
    error_log("SQL Error: " . $e->getCode());
    $error_message = "Error al cargar los datos del sistema: " . $e->getMessage();
}

// 6. INFORMACIÓN DEL USUARIO
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
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
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
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
    }

    .step-indicator {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: all 0.3s;
    }

    .step-indicator.active {
        background-color: #3b82f6;
        color: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }

    .step-indicator.completed {
        background-color: #10b981;
        color: white;
    }

    .step-indicator.pending {
        background-color: #e5e7eb;
        color: #6b7280;
    }

    .sidebar-mobile {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-mobile.active {
        transform: translateX(0);
    }

    .nav-link.active {
        background-color: #374151;
        border-left: 4px solid #3b82f6;
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.3s;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .modal-content {
            max-height: 95vh;
        }
    }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Layout Principal -->
    <div class="flex min-h-screen">
        <!-- Sidebar Desktop -->
        <aside class="hidden lg:flex lg:w-64 bg-gray-800 text-white flex-col fixed h-screen">
            <!-- Header -->
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </h1>
            </div>

            <!-- Usuario -->
            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center font-bold">
                        <?= $inicial_usuario ?>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="font-medium truncate"><?= htmlspecialchars($nombre_usuario) ?></p>
                        <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars($email_usuario) ?></p>
                        <?php if ($mi_organizacion): ?>
                        <p class="text-xs text-green-400 truncate mt-1">
                            <i class="fas fa-building"></i> <?= htmlspecialchars($mi_organizacion['nombre']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="?seccion=proyectos"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 transition <?= $seccion_activa === 'proyectos' ? 'active' : '' ?>">
                    <i class="fas fa-project-diagram mr-2"></i>Proyectos
                    <span
                        class="float-right bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><?= count($proyectos) ?></span>
                </a>
                <a href="?seccion=lideres"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 transition <?= $seccion_activa === 'lideres' ? 'active' : '' ?>">
                    <i class="fas fa-user-tie mr-2"></i>Líderes
                    <span
                        class="float-right bg-green-500 text-white text-xs px-2 py-1 rounded-full"><?= count($lideres) ?></span>
                </a>
                <a href="?seccion=organizacion"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 transition <?= $seccion_activa === 'organizacion' ? 'active' : '' ?>">
                    <i class="fas fa-building mr-2"></i>Mi Organización
                </a>

                <div class="border-t border-gray-700 my-2"></div>
                <p class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold">Configuración</p>

                <a href="?seccion=dominios"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 transition <?= $seccion_activa === 'dominios' ? 'active' : '' ?>">
                    <i class="fas fa-sitemap mr-2"></i>Dominios
                    <span
                        class="float-right bg-purple-500 text-white text-xs px-2 py-1 rounded-full"><?= count($dominios) ?></span>
                </a>
                <a href="?seccion=perfiles"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 transition <?= $seccion_activa === 'perfiles' ? 'active' : '' ?>">
                    <i class="fas fa-users-cog mr-2"></i>Perfiles
                    <span
                        class="float-right bg-pink-500 text-white text-xs px-2 py-1 rounded-full"><?= count($perfiles) ?></span>
                </a>
                <a href="?seccion=complejidad"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 transition <?= $seccion_activa === 'complejidad' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group mr-2"></i>Complejidad
                    <span
                        class="float-right bg-red-500 text-white text-xs px-2 py-1 rounded-full"><?= count($complejidades) ?></span>
                </a>

                <div class="border-t border-gray-700 my-2"></div>

                <a href="?seccion=estadisticas"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 transition <?= $seccion_activa === 'estadisticas' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar mr-2"></i>Estadísticas
                </a>
            </nav>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-700 space-y-2">
                <button onclick="openModalAyuda()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-question-circle mr-2"></i>Ayuda
                </button>
                <button onclick="openModalLogout()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </button>
            </div>
        </aside>

        <!-- Overlay Mobile -->
        <div id="mobileOverlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30 hidden"
            onclick="toggleMobileSidebar()"></div>

        <!-- Sidebar Mobile -->
        <aside id="mobileSidebar" class="sidebar-mobile lg:hidden w-64 bg-gray-800 text-white fixed h-screen z-40">
            <div class="p-4 border-b border-gray-700 flex justify-between items-center">
                <h1 class="text-xl font-bold"><i class="fas fa-chart-line mr-2"></i>Dashboard</h1>
                <button onclick="toggleMobileSidebar()" class="text-white text-2xl">&times;</button>
            </div>

            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center font-bold">
                        <?= $inicial_usuario ?>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium"><?= htmlspecialchars($nombre_usuario) ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($email_usuario) ?></p>
                        <?php if ($mi_organizacion): ?>
                        <p class="text-xs text-green-400 mt-1">
                            <i class="fas fa-building"></i> <?= htmlspecialchars($mi_organizacion['nombre']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <nav class="p-4 space-y-2 overflow-y-auto" style="max-height: calc(100vh - 280px);">
                <a href="?seccion=proyectos" onclick="toggleMobileSidebar()"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 <?= $seccion_activa === 'proyectos' ? 'active' : '' ?>">
                    <i class="fas fa-project-diagram mr-2"></i>Proyectos
                    <span
                        class="float-right bg-blue-500 text-white text-xs px-2 py-1 rounded-full"><?= count($proyectos) ?></span>
                </a>
                <a href="?seccion=lideres" onclick="toggleMobileSidebar()"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 <?= $seccion_activa === 'lideres' ? 'active' : '' ?>">
                    <i class="fas fa-user-tie mr-2"></i>Líderes
                    <span
                        class="float-right bg-green-500 text-white text-xs px-2 py-1 rounded-full"><?= count($lideres) ?></span>
                </a>
                <a href="?seccion=organizacion" onclick="toggleMobileSidebar()"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 <?= $seccion_activa === 'organizacion' ? 'active' : '' ?>">
                    <i class="fas fa-building mr-2"></i>Mi Organización
                </a>

                <div class="border-t border-gray-700 my-2"></div>
                <p class="px-4 py-2 text-xs text-gray-400 uppercase font-semibold">Configuración</p>

                <a href="?seccion=dominios" onclick="toggleMobileSidebar()"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 <?= $seccion_activa === 'dominios' ? 'active' : '' ?>">
                    <i class="fas fa-sitemap mr-2"></i>Dominios
                </a>
                <a href="?seccion=perfiles" onclick="toggleMobileSidebar()"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 <?= $seccion_activa === 'perfiles' ? 'active' : '' ?>">
                    <i class="fas fa-users-cog mr-2"></i>Perfiles
                </a>
                <a href="?seccion=complejidad" onclick="toggleMobileSidebar()"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 <?= $seccion_activa === 'complejidad' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group mr-2"></i>Complejidad
                </a>

                <div class="border-t border-gray-700 my-2"></div>

                <a href="?seccion=estadisticas" onclick="toggleMobileSidebar()"
                    class="nav-link block px-4 py-3 rounded-lg hover:bg-gray-700 <?= $seccion_activa === 'estadisticas' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar mr-2"></i>Estadísticas
                </a>
            </nav>

            <div class="p-4 border-t border-gray-700 space-y-2 absolute bottom-0 left-0 right-0">
                <button onclick="openModalAyuda(); toggleMobileSidebar();"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-medium">
                    <i class="fas fa-question-circle mr-2"></i>Ayuda
                </button>
                <button onclick="openModalLogout(); toggleMobileSidebar();"
                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                </button>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 lg:ml-64 p-4 md:p-8">
            <!-- Header Mobile -->
            <div class="lg:hidden flex items-center justify-between mb-6">
                <button onclick="toggleMobileSidebar()" class="p-2 bg-gray-800 text-white rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-bold text-gray-800">
                    <?php
                    $titulos = [
                        'proyectos' => '<i class="fas fa-project-diagram mr-2 text-blue-600"></i>Proyectos',
                        'lideres' => '<i class="fas fa-user-tie mr-2 text-blue-600"></i>Líderes',
                        'organizacion' => '<i class="fas fa-building mr-2 text-blue-600"></i>Mi Organización',
                        'dominios' => '<i class="fas fa-sitemap mr-2 text-blue-600"></i>Dominios',
                        'perfiles' => '<i class="fas fa-users-cog mr-2 text-blue-600"></i>Perfiles',
                        'complejidad' => '<i class="fas fa-layer-group mr-2 text-blue-600"></i>Complejidad',
                        'estadisticas' => '<i class="fas fa-chart-bar mr-2 text-blue-600"></i>Estadísticas'
                    ];
                    echo $titulos[$seccion_activa] ?? $titulos['proyectos'];
                    ?>
                </h1>
                <div class="w-10"></div>
            </div>

            <!-- Título Desktop -->
            <div class="hidden lg:block mb-8">
                <h1 class="text-3xl font-bold text-gray-800">
                    <?php
                    $titulos_desktop = [
                        'proyectos' => '<i class="fas fa-project-diagram mr-3 text-blue-600"></i>Gestión de Proyectos',
                        'lideres' => '<i class="fas fa-user-tie mr-3 text-blue-600"></i>Gestión de Líderes',
                        'organizacion' => '<i class="fas fa-building mr-3 text-blue-600"></i>Mi Organización',
                        'dominios' => '<i class="fas fa-sitemap mr-3 text-blue-600"></i>Gestión de Dominios',
                        'perfiles' => '<i class="fas fa-users-cog mr-3 text-blue-600"></i>Gestión de Perfiles',
                        'complejidad' => '<i class="fas fa-layer-group mr-3 text-blue-600"></i>Gestión de Complejidad',
                        'estadisticas' => '<i class="fas fa-chart-bar mr-3 text-blue-600"></i>Estadísticas del Sistema'
                    ];
                    echo $titulos_desktop[$seccion_activa] ?? $titulos_desktop['proyectos'];
                    ?>
                </h1>
                <p class="text-gray-600 mt-2">Sistema de Caracterización - Framework Cynefin</p>
            </div>

            <!-- Mensajes -->
            <?php if ($error_message): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <p class="text-red-700"><?= htmlspecialchars($error_message) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <p class="text-green-700"><?= htmlspecialchars($success_message) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- CONTENIDO DINÁMICO SEGÚN SECCIÓN -->
            <div id="contenido-dinamico">
                <!-- Sección: Proyectos -->
                <div id="seccion-proyectos" class="tab-content <?= $seccion_activa === 'proyectos' ? 'active' : '' ?>">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Proyectos</h2>
                            <p class="text-gray-600 mt-1">
                                <i class="fas fa-folder mr-1"></i><?= count($proyectos) ?> proyecto(s) registrado(s)
                            </p>
                        </div>
                        <button onclick="openModalNuevoProyecto()"
                            class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-plus mr-2"></i>Nuevo Proyecto
                        </button>
                    </div>

                    <!-- Tabla -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre</th>
                                    <th
                                        class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dominio</th>
                                    <th
                                        class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Horas</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Líder</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($proyectos) > 0): ?>
                                <?php foreach ($proyectos as $p): 
                                    $nombre_lider = 'No asignado';
                                    $lider_id = null;
                                    if ($p['lider_proyecto_id']) {
                                        foreach ($lideres as $l) {
                                            if ($l['id'] == $p['lider_proyecto_id']) {
                                                $nombre_lider = $l['nombre'];
                                                $lider_id = $l['id'];
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?= htmlspecialchars($p['id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <?= htmlspecialchars($p['nombre_proyecto']) ?>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            <?php 
                                            $desc = $p['descripcion_proyecto'] ?? '';
                                            echo !empty($desc) ? (strlen($desc) > 60 ? substr(htmlspecialchars($desc), 0, 60) . '...' : htmlspecialchars($desc)) : '<span class="text-gray-400 italic">Sin descripción</span>';
                                            ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?= htmlspecialchars($p['dominio_cynefin'] ?? 'No definido') ?>
                                        </span>
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <?= htmlspecialchars($p['complejidad_total'] ?? '0') ?>h
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="text-sm <?= $p['lider_proyecto_id'] ? 'text-green-600' : 'text-gray-500' ?>">
                                            <?= htmlspecialchars($nombre_lider) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <a href="resultados_caracterizacion.php?id=<?= $p['id'] ?>"
                                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                                <i class="fas fa-eye mr-1"></i>Ver
                                            </a>
                                            <button onclick="asignarLiderProyecto(<?= $p['id'] ?>)"
                                                class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition">
                                                <i class="fas fa-user-tie mr-1"></i>Asignar Líder
                                            </button>
                                            <button
                                                onclick="eliminarProyecto(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre_proyecto'])) ?>')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                            <h3 class="text-xl font-medium text-gray-900 mb-2">No hay proyectos</h3>
                                            <p class="text-gray-500 mb-6">Crea tu primer proyecto para comenzar</p>
                                            <button onclick="openModalNuevoProyecto()"
                                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                                <i class="fas fa-plus mr-2"></i>Crear Proyecto
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Líderes -->
                <div id="seccion-lideres" class="tab-content <?= $seccion_activa === 'lideres' ? 'active' : '' ?>">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Líderes de Proyecto</h2>
                            <p class="text-gray-600 mt-1">
                                <i class="fas fa-users mr-1"></i><?= count($lideres) ?> líder(es) registrado(s)
                            </p>
                        </div>
                        <button onclick="openModalNuevoLider()"
                            class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-user-plus mr-2"></i>Nuevo Líder
                        </button>
                    </div>

                    <!-- Tabla -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuario</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contraseña</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($lideres) > 0): ?>
                                <?php foreach ($lideres as $l): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?= htmlspecialchars($l['id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <?= htmlspecialchars($l['nombre']) ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= htmlspecialchars($l['especialidad'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($l['email']) ?>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                                            <?= htmlspecialchars($l['usuario'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                                                <?= str_repeat('•', 8) ?>
                                            </span>
                                            <button
                                                onclick="mostrarContrasena(<?= $l['id'] ?>, '<?= htmlspecialchars($l['password']) ?>')"
                                                class="text-blue-600 hover:text-blue-800 text-sm">
                                                <i class="fas fa-eye mr-1"></i>Ver
                                            </button>
                                            <button
                                                onclick="copiarContrasena('<?= htmlspecialchars($l['password']) ?>')"
                                                class="text-green-600 hover:text-green-800 text-sm">
                                                <i class="fas fa-copy mr-1"></i>Copiar
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <button onclick="editarLider(<?= $l['id'] ?>)"
                                                class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </button>
                                            <button
                                                onclick="eliminarLider(<?= $l['id'] ?>, '<?= htmlspecialchars(addslashes($l['nombre'])) ?>')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-user-tie text-6xl text-gray-300 mb-4"></i>
                                            <h3 class="text-xl font-medium text-gray-900 mb-2">No hay líderes</h3>
                                            <p class="text-gray-500 mb-6">Agrega el primer líder de proyecto</p>
                                            <button onclick="openModalNuevoLider()"
                                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                                <i class="fas fa-user-plus mr-2"></i>Agregar Líder
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Mi Organización -->
                <div id="seccion-organizacion"
                    class="tab-content <?= $seccion_activa === 'organizacion' ? 'active' : '' ?>">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Mi Organización</h2>
                            <p class="text-gray-600 mt-1">
                                <i class="fas fa-building mr-1"></i>Perfil de la organización
                            </p>
                        </div>
                        <?php if ($mi_organizacion): ?>
                        <button onclick="openModalEditarOrganizacion()"
                            class="w-full md:w-auto bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-edit mr-2"></i>Editar Organización
                        </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($mi_organizacion): ?>
                    <!-- Perfil de Organización -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Header del Perfil -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold"><?= htmlspecialchars($mi_organizacion['nombre']) ?>
                                    </h3>
                                    <p class="text-blue-200 mt-1">
                                        <i class="fas fa-id-card mr-1"></i>ID:
                                        #<?= htmlspecialchars($mi_organizacion['id']) ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= ($mi_organizacion['activo'] ?? 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                        <?= ($mi_organizacion['activo'] ?? 1) ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido del Perfil -->
                        <div class="p-6">
                            <!-- Datos en formato de lista -->
                            <div class="space-y-4">
                                <!-- ID -->
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-hashtag text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">ID:</span>
                                    </div>
                                    <span
                                        class="font-mono text-gray-800">#<?= htmlspecialchars($mi_organizacion['id']) ?></span>
                                </div>

                                <!-- Nombre -->
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-building text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">Nombre:</span>
                                    </div>
                                    <span
                                        class="text-gray-800"><?= htmlspecialchars($mi_organizacion['nombre']) ?></span>
                                </div>

                                <!-- Descripción -->
                                <div class="border-b pb-3">
                                    <div class="flex items-start mb-2">
                                        <i class="fas fa-align-left text-blue-500 w-6 mt-1"></i>
                                        <span class="ml-2 font-medium text-gray-700">Descripción:</span>
                                    </div>
                                    <p class="text-gray-700 bg-gray-50 p-3 rounded">
                                        <?= htmlspecialchars($mi_organizacion['descripcion'] ?? 'No disponible') ?></p>
                                </div>

                                <!-- Dirección -->
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">Dirección:</span>
                                    </div>
                                    <span
                                        class="text-gray-800"><?= htmlspecialchars($mi_organizacion['direccion'] ?? 'No disponible') ?></span>
                                </div>

                                <!-- Teléfono -->
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">Teléfono:</span>
                                    </div>
                                    <span
                                        class="text-gray-800"><?= htmlspecialchars($mi_organizacion['telefono'] ?? 'No disponible') ?></span>
                                </div>

                                <!-- País -->
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-globe text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">País:</span>
                                    </div>
                                    <span
                                        class="text-gray-800"><?= htmlspecialchars($mi_organizacion['pais'] ?? 'No disponible') ?></span>
                                </div>

                                <!-- Email -->
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">Email:</span>
                                    </div>
                                    <span
                                        class="text-gray-800"><?= htmlspecialchars($mi_organizacion['email']) ?></span>
                                </div>

                                <!-- Activo -->
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-power-off text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">Activo:</span>
                                    </div>
                                    <span
                                        class="font-medium <?= ($mi_organizacion['activo'] ?? 1) ? 'text-green-600' : 'text-red-600' ?>">
                                        <?= ($mi_organizacion['activo'] ?? 1) ? 'Sí' : 'No' ?>
                                    </span>
                                </div>

                                <!-- Fecha Registro -->
                                <div class="flex items-center justify-between border-b pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-plus text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">Fecha Registro:</span>
                                    </div>
                                    <span
                                        class="text-gray-800"><?= date('d/m/Y H:i', strtotime($mi_organizacion['fecha_registro'])) ?></span>
                                </div>

                                <!-- Fecha Actualización -->
                                <div class="flex items-center justify-between pb-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-check text-blue-500 w-6"></i>
                                        <span class="ml-2 font-medium text-gray-700">Última Actualización:</span>
                                    </div>
                                    <span
                                        class="text-gray-800"><?= date('d/m/Y H:i', strtotime($mi_organizacion['fecha_actualizacion'])) ?></span>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="mt-8 pt-6 border-t">
                                <div class="flex flex-wrap gap-3">
                                    <button onclick="openModalEditarOrganizacion()"
                                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 font-medium flex items-center">
                                        <i class="fas fa-edit mr-2"></i>Editar
                                    </button>
                                    <button onclick="copiarPerfilCompleto()"
                                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium flex items-center">
                                        <i class="fas fa-copy mr-2"></i>Copiar Todo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Si no hay organización -->
                    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                        <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">No hay organización configurada</h3>
                        <p class="text-gray-500 mb-6">Contacta con el administrador del sistema</p>
                        <button onclick="location.reload()"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-sync mr-2"></i>Recargar
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Modal: Editar Organización -->
                <div id="modalEditarOrganizacion" class="modal-overlay hidden">
                    <div class="modal-content max-w-lg">
                        <div
                            class="sticky top-0 bg-white px-6 py-4 border-b flex justify-between items-center rounded-t-xl z-10">
                            <h2 class="text-xl font-bold text-gray-800">
                                <i class="fas fa-edit mr-2 text-yellow-600"></i>Editar Organización
                            </h2>
                            <button onclick="closeModalEditarOrganizacion()"
                                class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
                        </div>
                        <div class="p-6">
                            <?php if ($mi_organizacion): ?>
                            <form id="formEditarOrganizacion" action="../index.php?action=actualizar_organizacion"
                                method="POST">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($mi_organizacion['id']) ?>">

                                <div class="space-y-4">
                                    <!-- Información Básica -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Información Básica</h3>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre
                                                    *</label>
                                                <input type="text" name="nombre" required
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                    value="<?= htmlspecialchars($mi_organizacion['nombre']) ?>">
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                                <textarea name="descripcion" rows="2"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"><?= htmlspecialchars($mi_organizacion['descripcion'] ?? '') ?></textarea>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                                <input type="text" name="direccion"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                    value="<?= htmlspecialchars($mi_organizacion['direccion'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información de Contacto -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Información de Contacto
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Email
                                                    *</label>
                                                <input type="email" name="email" required
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                    value="<?= htmlspecialchars($mi_organizacion['email']) ?>">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                                <input type="tel" name="telefono"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                    value="<?= htmlspecialchars($mi_organizacion['telefono'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Configuración -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Configuración</h3>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <input type="checkbox" name="activo" value="1"
                                                    class="h-4 w-4 mr-2 text-yellow-600"
                                                    <?= ($mi_organizacion['activo'] ?? 1) ? 'checked' : '' ?>>
                                                <span class="text-sm font-medium text-gray-700">Organización
                                                    Activa</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-3 mt-6 pt-6 border-t">
                                    <button type="button" onclick="closeModalEditarOrganizacion()"
                                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">
                                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sección: Dominios -->
                <div id="seccion-dominios" class="tab-content <?= $seccion_activa === 'dominios' ? 'active' : '' ?>">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Dominios Cynefin</h2>
                            <p class="text-gray-600 mt-1">
                                <i class="fas fa-sitemap mr-1"></i><?= count($dominios) ?> dominio(s) registrado(s)
                            </p>
                        </div>
                        <button onclick="openModalNuevoDominio()"
                            class="w-full md:w-auto bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus mr-2"></i>Nuevo Dominio
                        </button>
                    </div>

                    <!-- Tabla de Dominios -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($dominios) > 0): ?>
                                <?php foreach ($dominios as $d): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?= htmlspecialchars($d['id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <?= htmlspecialchars($d['nombre']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            <?= htmlspecialchars($d['nombre'] ?? 'Sin descripción') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <button onclick="editarDominio(<?= $d['id'] ?>)"
                                                class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </button>
                                            <button
                                                onclick="eliminarDominio(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($d['nombre'])) ?>')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-sitemap text-6xl text-gray-300 mb-4"></i>
                                            <h3 class="text-xl font-medium text-gray-900 mb-2">No hay dominios</h3>
                                            <p class="text-gray-500 mb-6">Agrega el primer dominio Cynefin</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Perfiles -->
                <div id="seccion-perfiles" class="tab-content <?= $seccion_activa === 'perfiles' ? 'active' : '' ?>">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Perfiles de Equipo</h2>
                            <p class="text-gray-600 mt-1">
                                <i class="fas fa-users-cog mr-1"></i><?= count($perfiles) ?> perfil(es) registrado(s)
                            </p>
                        </div>
                        <button onclick="openModalNuevoPerfil()"
                            class="w-full md:w-auto bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus mr-2"></i>Nuevo Perfil
                        </button>
                    </div>

                    <!-- Tabla de Perfiles -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($perfiles) > 0): ?>
                                <?php foreach ($perfiles as $p): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?= htmlspecialchars($p['id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <?= htmlspecialchars($p['nombre']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            <?= htmlspecialchars($p['descripcion'] ?? 'Sin descripción') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <button onclick="editarPerfil(<?= $p['id'] ?>)"
                                                class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </button>
                                            <button
                                                onclick="eliminarPerfil(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-users-cog text-6xl text-gray-300 mb-4"></i>
                                            <h3 class="text-xl font-medium text-gray-900 mb-2">No hay perfiles</h3>
                                            <p class="text-gray-500 mb-6">Agrega el primer perfil de equipo</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Complejidad -->
                <div id="seccion-complejidad"
                    class="tab-content <?= $seccion_activa === 'complejidad' ? 'active' : '' ?>">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Factores de Complejidad</h2>
                            <p class="text-gray-600 mt-1">
                                <i class="fas fa-layer-group mr-1"></i><?= count($complejidades) ?> factor(es)
                                registrado(s)
                            </p>
                        </div>
                        <button onclick="openModalNuevoFactor()"
                            class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus mr-2"></i>Nuevo Factor
                        </button>
                    </div>

                    <!-- Tabla de Factores -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (count($complejidades) > 0): ?>
                                <?php foreach ($complejidades as $c): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?= htmlspecialchars($c['id']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <?= htmlspecialchars($c['nombre']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            <?= htmlspecialchars($c['descripcion'] ?? 'Sin descripción') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <button onclick="editarFactor(<?= $c['id'] ?>)"
                                                class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </button>
                                            <button
                                                onclick="eliminarFactor(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['nombre'])) ?>')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-layer-group text-6xl text-gray-300 mb-4"></i>
                                            <h3 class="text-xl font-medium text-gray-900 mb-2">No hay factores</h3>
                                            <p class="text-gray-500 mb-6">Agrega el primer factor de complejidad</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Estadísticas -->
                <div id="seccion-estadisticas"
                    class="tab-content <?= $seccion_activa === 'estadisticas' ? 'active' : '' ?>">
                    <!-- Header -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold text-gray-800">Estadísticas del Sistema</h2>
                        <p class="text-gray-600 mt-1">
                            <i class="fas fa-chart-bar mr-1"></i>Resumen y métricas del sistema
                        </p>
                    </div>

                    <!-- Tarjetas de Estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-project-diagram text-2xl text-blue-600"></i>
                                </div>
                                <span class="text-3xl font-bold text-gray-800"><?= count($proyectos) ?></span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Proyectos</h3>
                            <p class="text-gray-600 text-sm">Total de proyectos registrados</p>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user-tie text-2xl text-green-600"></i>
                                </div>
                                <span class="text-3xl font-bold text-gray-800"><?= count($lideres) ?></span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Líderes</h3>
                            <p class="text-gray-600 text-sm">Líderes de proyecto activos</p>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-2xl text-yellow-600"></i>
                                </div>
                                <span class="text-3xl font-bold text-gray-800"><?= $mi_organizacion ? 1 : 0 ?></span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Organizaciones</h3>
                            <p class="text-gray-600 text-sm">Organizaciones clientes</p>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                                </div>
                                <?php 
                                $total_horas = 0;
                                foreach ($proyectos as $p) {
                                    $total_horas += (int)$p['complejidad_total'];
                                }
                                ?>
                                <span
                                    class="text-3xl font-bold text-gray-800"><?= number_format($total_horas) ?>h</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Horas Totales</h3>
                            <p class="text-gray-600 text-sm">Horas estimadas en proyectos</p>
                        </div>
                    </div>

                    <!-- Distribución por Dominio -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Distribución por Dominio Cynefin</h3>
                        <div class="space-y-4">
                            <?php 
                            $dominios_count = [];
                            foreach ($proyectos as $p) {
                                $dominio = $p['dominio_cynefin'] ?? 'No definido';
                                $dominios_count[$dominio] = ($dominios_count[$dominio] ?? 0) + 1;
                            }
                            ?>
                            <?php foreach ($dominios_count as $nombre => $count): ?>
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($nombre) ?></span>
                                    <span class="font-medium text-gray-700"><?= $count ?></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full"
                                        style="width: <?= ($count / max(count($proyectos), 1)) * 100 ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Exportación -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Exportación de Datos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button onclick="exportarProyectos()"
                                class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-6 py-4 rounded-lg font-medium transition flex items-center justify-center">
                                <i class="fas fa-file-excel text-2xl mr-3"></i>
                                <div class="text-left">
                                    <div class="font-semibold">Exportar Proyectos</div>
                                    <div class="text-sm">Datos completos en Excel</div>
                                </div>
                            </button>
                            <button onclick="exportarReporte()"
                                class="bg-green-100 hover:bg-green-200 text-green-700 px-6 py-4 rounded-lg font-medium transition flex items-center justify-center">
                                <i class="fas fa-chart-pie text-2xl mr-3"></i>
                                <div class="text-left">
                                    <div class="font-semibold">Reporte Completo</div>
                                    <div class="text-sm">Estadísticas y métricas</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: Nuevo Proyecto -->
    <div id="modalNuevoProyecto" class="modal-overlay hidden">
        <div class="modal-content max-w-4xl">
            <!-- Header -->
            <div
                class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-xl z-10">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-clipboard-check mr-2 text-blue-600"></i>Nueva Caracterización
                </h2>
                <button onclick="closeModalNuevoProyecto()"
                    class="text-gray-400 hover:text-gray-600 text-3xl font-bold">
                    &times;
                </button>
            </div>

            <!-- Indicador de Pasos -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="step-indicator step-1 active">1</div>
                        <span class="ml-2 text-sm font-medium hidden md:inline">Información</span>
                    </div>
                    <div class="h-0.5 w-12 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="step-indicator step-2 pending">2</div>
                        <span class="ml-2 text-sm font-medium hidden md:inline">Restricción</span>
                    </div>
                    <div class="h-0.5 w-12 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="step-indicator step-3 pending">3</div>
                        <span class="ml-2 text-sm font-medium hidden md:inline">Complejidad</span>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <form id="formCaracterizacion" action="../index.php?action=guardar_proyecto" method="POST">
                <!-- Paso 1: Información -->
                <div id="step1" class="form-step active space-y-6 p-6">
                    <h3 class="text-lg font-semibold text-blue-600 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Información del Proyecto
                    </h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Proyecto *</label>
                        <input type="text" name="nombre_proyecto" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ej: Sistema de Gestión Académica">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dominio del Problema *</label>
                            <select name="dominio_problema" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione...</option>
                                <?php foreach ($dominios as $d): ?>
                                <option value="<?= htmlspecialchars($d['nombre']) ?>">
                                    <?= htmlspecialchars($d['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tamaño Estimado (horas)
                                *</label>
                            <input type="number" name="tamano_estimado" required min="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">País del Cliente *</label>
                        <select name="pais" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccione...</option>
                            <?php foreach ($paises as $pais): ?>
                            <option value="<?= htmlspecialchars($pais['nombre']) ?>">
                                <?= htmlspecialchars($pais['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descripción del Proyecto *</label>
                        <textarea name="descripcion_proyecto" required rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Describe el proyecto..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Conformación del Equipo *</label>
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div id="equipoContainer" class="space-y-3 mb-4"></div>
                            <button type="button" onclick="addPerfil()"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>Agregar Perfil
                            </button>
                        </div>
                        <input type="hidden" name="equipo_json" id="equipo_json">
                    </div>
                </div>

                <!-- Paso 2: Triple Restricción -->
                <div id="step2" class="form-step space-y-6 p-6">
                    <h3 class="text-lg font-semibold text-blue-600 mb-4">
                        <i class="fas fa-balance-scale mr-2"></i>Triple Restricción
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label
                            class="flex items-center p-4 border-2 border-gray-300 rounded-lg hover:border-blue-500 cursor-pointer transition">
                            <input type="checkbox" name="restricciones[]" value="Tiempo"
                                class="h-5 w-5 mr-3 text-blue-600">
                            <span class="font-medium">Tiempo</span>
                        </label>
                        <label
                            class="flex items-center p-4 border-2 border-gray-300 rounded-lg hover:border-blue-500 cursor-pointer transition">
                            <input type="checkbox" name="restricciones[]" value="Alcance"
                                class="h-5 w-5 mr-3 text-blue-600">
                            <span class="font-medium">Alcance</span>
                        </label>
                        <label
                            class="flex items-center p-4 border-2 border-gray-300 rounded-lg hover:border-blue-500 cursor-pointer transition">
                            <input type="checkbox" id="costoCheck" name="restricciones[]" value="Costo"
                                class="h-5 w-5 mr-3 text-blue-600" onchange="toggleCosto()">
                            <span class="font-medium">Costo</span>
                        </label>
                    </div>

                    <div id="costoOpciones" class="hidden p-4 border rounded-lg bg-gray-50">
                        <label class="block font-medium mb-3">Tipo de Contrato:</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="tipoCosto" value="Llave en mano" class="mr-3">
                                <span>Llave en mano</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipoCosto" value="Time & Material" class="mr-3">
                                <span>Time & Material</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Complejidad -->
                <div id="step3" class="form-step space-y-4 p-6">
                    <h3 class="text-lg font-semibold text-blue-600 mb-4">
                        <i class="fas fa-layer-group mr-2"></i>Factores de Complejidad
                    </h3>

                    <?php if (!empty($complejidades)): ?>
                    <?php foreach ($complejidades as $comp): ?>
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="complejidad[]" value="<?= htmlspecialchars($comp['nombre']) ?>"
                                class="mt-1 h-5 w-5 mr-3 text-blue-600">
                            <div>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($comp['nombre']) ?></span>
                                <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($comp['descripcion']) ?></p>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                        <p>No hay factores de complejidad disponibles</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Botones de Navegación -->
                <div class="flex justify-between mt-8 pt-6 border-t px-6 pb-6">
                    <button type="button" id="btnPrev" onclick="cambiarPaso(-1)"
                        class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 hidden">
                        <i class="fas fa-arrow-left mr-2"></i>Anterior
                    </button>
                    <button type="button" id="btnNext" onclick="cambiarPaso(1)"
                        class="ml-auto px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Siguiente<i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <button type="submit" id="btnSubmit"
                        class="ml-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 hidden">
                        <i class="fas fa-check mr-2"></i>Guardar Proyecto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Nuevo Líder -->
    <div id="modalNuevoLider" class="modal-overlay hidden">
        <div class="modal-content max-w-md">
            <div class="sticky top-0 bg-white px-6 py-4 border-b flex justify-between items-center rounded-t-xl z-10">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-user-plus mr-2 text-green-600"></i>Nuevo Líder
                </h2>
                <button onclick="closeModalNuevoLider()"
                    class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
            </div>
            <div class="p-6">
                <form id="formNuevoLider" action="../index.php?action=guardar_lider" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                            <input type="text" name="nombre" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ej: Juan Pérez">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="ejemplo@correo.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                <input type="tel" name="telefono"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="+57 300 123 4567">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Especialidad</label>
                            <input type="text" name="especialidad"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ej: Desarrollo de Software">
                        </div>

                        <!-- Usuario y Contraseña Generados -->
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-medium text-gray-700 mb-3">Credenciales de Acceso</h4>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario *</label>
                                    <div class="flex">
                                        <input type="text" name="usuario" required readonly
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg bg-gray-100"
                                            id="usuarioInput">
                                        <button type="button" onclick="generarUsuario()"
                                            class="px-3 bg-blue-100 border border-blue-300 rounded-r-lg hover:bg-blue-200">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Usuario generado automáticamente</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                                    <div class="flex">
                                        <input type="text" name="password" required readonly
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg bg-gray-100"
                                            id="passwordInput">
                                        <button type="button" onclick="generarPassword()"
                                            class="px-3 bg-blue-100 border border-blue-300 hover:bg-blue-200">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                        <button type="button" onclick="copiarPassword()"
                                            class="px-3 bg-green-100 border border-green-300 rounded-r-lg hover:bg-green-200 ml-1">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Contraseña generada automáticamente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeModalNuevoLider()"
                            class="flex-1 px-4 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                            <i class="fas fa-save mr-2"></i>Guardar Líder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Asignar Líder a Proyecto -->
    <div id="modalAsignarLider" class="modal-overlay hidden">
        <div class="modal-content max-w-md">
            <div class="sticky top-0 bg-white px-6 py-4 border-b flex justify-between items-center rounded-t-xl z-10">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-user-tie mr-2 text-blue-600"></i>Asignar Líder al Proyecto
                </h2>
                <button onclick="closeModalAsignarLider()"
                    class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
            </div>
            <div class="p-6">
                <form id="formAsignarLider">
                    <input type="hidden" id="proyectoIdAsignar" name="proyecto_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Líder *</label>
                            <select name="lider_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un líder...</option>
                                <?php foreach ($lideres as $l): ?>
                                <option value="<?= htmlspecialchars($l['id']) ?>">
                                    <?= htmlspecialchars($l['nombre']) ?>
                                    (<?= htmlspecialchars($l['especialidad'] ?? 'General') ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notas (opcional)</label>
                            <textarea name="notas" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Observaciones sobre la asignación..."></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeModalAsignarLider()"
                            class="flex-1 px-4 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-check mr-2"></i>Asignar Líder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Ayuda -->
    <div id="modalAyuda" class="modal-overlay hidden">
        <div class="modal-content max-w-3xl">
            <div class="sticky top-0 bg-white px-6 py-4 border-b flex justify-between items-center rounded-t-xl z-10">
                <h2 class="text-2xl font-bold text-blue-700">
                    <i class="fas fa-question-circle mr-2"></i>Guía de Uso
                </h2>
                <button onclick="closeModalAyuda()" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
            </div>
            <div class="p-6 space-y-6 text-gray-700">
                <p>Sistema de caracterización de proyectos basado en el framework Cynefin.</p>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">1. Caracterización del Proyecto</h3>
                    <p>Complete la información del proyecto en 3 pasos: información básica, restricciones y complejidad.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">2. Asignación de Líderes</h3>
                    <p>Puede asignar líderes a proyectos existentes usando el botón "Asignar Líder" en la tabla de
                        proyectos.</p>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">3. Creación de Líderes</h3>
                    <p>Al crear un nuevo líder, el sistema genera automáticamente usuario y contraseña que puede copiar.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">4. Mi Organización</h3>
                    <p>En esta sección puede ver y editar la información de su organización.</p>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal: Logout -->
    <div id="modalLogout" class="modal-overlay hidden">
        <div class="modal-content max-w-md">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-out-alt text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Cerrar Sesión</h3>
                <p class="text-gray-600 mb-6">¿Está seguro que desea cerrar la sesión?</p>
                <div class="flex gap-3">
                    <button onclick="closeModalLogout()"
                        class="flex-1 px-4 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                        Cancelar
                    </button>
                    <a href="../index.php?action=logout"
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-center">
                        Sí, Salir
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    // ===== FUNCIONES PARA ORGANIZACIÓN =====
    function mostrarContrasenaOrganizacion(contrasena) {
        if (contrasena) {
            alert(`Contraseña de la organización:\n\n${contrasena}\n\nPuede copiarla si es necesario.`);
        } else {
            alert('No hay contraseña configurada para esta organización.');
        }
    }

    function copiarTexto(texto) {
        if (texto) {
            navigator.clipboard.writeText(texto).then(() => {
                alert('Texto copiado al portapapeles');
            });
        } else {
            alert('No hay texto para copiar');
        }
    }

    function copiarPerfilCompleto() {
        // Crear un resumen del perfil para copiar
        const org = <?= json_encode($mi_organizacion ?? []) ?>;
        let perfilTexto = `=== PERFIL DE ORGANIZACIÓN ===\n\n`;
        perfilTexto += `ID: ${org.id}\n`;
        perfilTexto += `Nombre: ${org.nombre}\n`;
        perfilTexto += `Descripción: ${org.descripcion || 'N/A'}\n`;
        perfilTexto += `Dirección: ${org.direccion || 'N/A'}\n`;
        perfilTexto += `Teléfono: ${org.telefono || 'N/A'}\n`;
        perfilTexto += `Email: ${org.email}\n`;
        perfilTexto += `País: ${org.pais || 'N/A'}\n`;
        perfilTexto += `Estado: ${org.activo ? 'Activo' : 'Inactivo'}\n`;
        perfilTexto += `Registro: ${org.fecha_registro}\n`;
        perfilTexto += `Actualización: ${org.fecha_actualizacion}\n`;

        navigator.clipboard.writeText(perfilTexto).then(() => {
            alert('Perfil copiado al portapapeles');
        });
    }

    // ===== CONFIGURACIÓN =====
    let pasoActual = 1;
    const totalPasos = 3;
    const perfilesDisponibles = <?= json_encode($perfiles) ?>;

    // ===== FUNCIONES DE NAVEGACIÓN =====
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('mobileOverlay');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('hidden');
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }

    // ===== FUNCIONES DE MODALES =====
    function openModalNuevoProyecto() {
        document.getElementById('modalNuevoProyecto').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        pasoActual = 1;
        mostrarPaso(1);
        if (document.querySelectorAll('#equipoContainer > div').length === 0) {
            addPerfil();
        }
    }

    function closeModalNuevoProyecto() {
        document.getElementById('modalNuevoProyecto').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('formCaracterizacion').reset();
        document.getElementById('equipoContainer').innerHTML = '';
        pasoActual = 1;
        mostrarPaso(1);
    }

    function openModalNuevoLider() {
        document.getElementById('modalNuevoLider').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        generarUsuario();
        generarPassword();
    }

    function closeModalNuevoLider() {
        document.getElementById('modalNuevoLider').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('formNuevoLider').reset();
    }

    function asignarLiderProyecto(proyectoId) {
        document.getElementById('proyectoIdAsignar').value = proyectoId;
        document.getElementById('modalAsignarLider').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalAsignarLider() {
        document.getElementById('modalAsignarLider').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('formAsignarLider').reset();
    }

    function openModalEditarOrganizacion() {
        document.getElementById('modalEditarOrganizacion').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalEditarOrganizacion() {
        document.getElementById('modalEditarOrganizacion').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openModalCrearOrganizacion() {
        document.getElementById('modalCrearOrganizacion').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalCrearOrganizacion() {
        document.getElementById('modalCrearOrganizacion').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openModalAyuda() {
        document.getElementById('modalAyuda').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalAyuda() {
        document.getElementById('modalAyuda').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openModalLogout() {
        document.getElementById('modalLogout').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalLogout() {
        document.getElementById('modalLogout').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // ===== FUNCIONES PARA LÍDERES =====
    function generarUsuario() {
        const nombreInput = document.querySelector('#formNuevoLider input[name="nombre"]');
        const usuarioInput = document.getElementById('usuarioInput');

        if (nombreInput && nombreInput.value) {
            // Crear usuario a partir del nombre
            let usuario = nombreInput.value.toLowerCase()
                .trim()
                .replace(/\s+/g, '.')
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Quitar acentos
                .replace(/[^a-z0-9.]/g, '');

            // Agregar números aleatorios si es muy corto
            if (usuario.length < 3) {
                usuario += Math.floor(Math.random() * 1000);
            }

            usuarioInput.value = usuario;
        } else {
            // Generar usuario aleatorio
            const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            let randomUser = 'user_';
            for (let i = 0; i < 6; i++) {
                randomUser += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            usuarioInput.value = randomUser;
        }
    }

    function generarPassword() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&';
        let password = '';
        for (let i = 0; i < 10; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('passwordInput').value = password;
    }

    function copiarPassword() {
        const passwordInput = document.getElementById('passwordInput');
        passwordInput.select();
        passwordInput.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(passwordInput.value).then(() => {
            alert('Contraseña copiada al portapapeles');
        });
    }

    function mostrarContrasena(id, password) {
        alert(`Contraseña del líder #${id}:\n\n${password}\n\nPuede copiarla para compartir.`);
    }

    function copiarContrasena(password) {
        navigator.clipboard.writeText(password).then(() => {
            alert('Contraseña copiada al portapapeles');
        });
    }

    // ===== PASOS DEL FORMULARIO =====
    function mostrarPaso(paso) {
        for (let i = 1; i <= totalPasos; i++) {
            const stepEl = document.getElementById(`step${i}`);
            const indicator = document.querySelector(`.step-${i}`);

            if (stepEl) {
                if (i === paso) {
                    stepEl.classList.add('active');
                } else {
                    stepEl.classList.remove('active');
                }
            }

            if (indicator) {
                indicator.classList.remove('active', 'completed', 'pending');
                if (i === paso) {
                    indicator.classList.add('active');
                } else if (i < paso) {
                    indicator.classList.add('completed');
                } else {
                    indicator.classList.add('pending');
                }
            }
        }

        document.getElementById('btnPrev').classList.toggle('hidden', paso === 1);
        document.getElementById('btnNext').classList.toggle('hidden', paso === totalPasos);
        document.getElementById('btnSubmit').classList.toggle('hidden', paso !== totalPasos);
    }

    function cambiarPaso(direccion) {
        if (direccion === 1 && !validarPaso(pasoActual)) {
            return;
        }

        pasoActual += direccion;
        pasoActual = Math.max(1, Math.min(totalPasos, pasoActual));
        mostrarPaso(pasoActual);
    }

    function validarPaso(paso) {
        const stepEl = document.getElementById(`step${paso}`);
        const campos = stepEl.querySelectorAll('[required]');
        let valido = true;

        campos.forEach(campo => {
            if (!campo.value.trim()) {
                valido = false;
                campo.classList.add('border-red-500');
            } else {
                campo.classList.remove('border-red-500');
            }
        });

        if (paso === 1) {
            const equipo = JSON.parse(document.getElementById('equipo_json').value || '[]');
            if (equipo.length === 0) {
                alert('Debe agregar al menos un perfil al equipo');
                return false;
            }
        }

        if (!valido) {
            alert('Complete todos los campos requeridos');
            return false;
        }

        return true;
    }

    // ===== GESTIÓN DE EQUIPO =====
    function addPerfil() {
        const container = document.getElementById('equipoContainer');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 bg-white p-3 rounded-lg border';
        div.innerHTML = `
            <select class="flex-1 border rounded px-3 py-2" onchange="actualizarEquipoJSON()">
                <option value="">Seleccione perfil...</option>
                ${perfilesDisponibles.map(p => `<option value="${p.nombre}">${p.nombre}</option>`).join('')}
            </select>
            <input type="number" min="1" value="1" class="w-24 border rounded px-3 py-2" onchange="actualizarEquipoJSON()">
            <button type="button" onclick="this.parentElement.remove(); actualizarEquipoJSON()" 
                    class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
        actualizarEquipoJSON();
    }

    function actualizarEquipoJSON() {
        const perfiles = [];
        document.querySelectorAll('#equipoContainer > div').forEach(div => {
            const select = div.querySelector('select');
            const input = div.querySelector('input');
            if (select.value && input.value) {
                perfiles.push({
                    perfil: select.value,
                    cantidad: parseInt(input.value)
                });
            }
        });
        document.getElementById('equipo_json').value = JSON.stringify(perfiles);
    }

    function toggleCosto() {
        const checked = document.getElementById('costoCheck').checked;
        document.getElementById('costoOpciones').classList.toggle('hidden', !checked);
    }

    // ===== FUNCIONES DE ELIMINACIÓN =====
    function eliminarProyecto(id, nombre) {
        if (confirm(`¿Está seguro de eliminar el proyecto "${nombre}"?`)) {
            window.location.href = `../index.php?action=eliminar_proyecto&id=${id}`;
        }
    }

    function eliminarLider(id, nombre) {
        if (confirm(`¿Está seguro de eliminar al líder "${nombre}"?`)) {
            window.location.href = `../index.php?action=eliminar_lider&id=${id}`;
        }
    }

    function eliminarDominio(id, nombre) {
        if (confirm(`¿Está seguro de eliminar el dominio "${nombre}"?`)) {
            window.location.href = `../index.php?action=eliminar_dominio&id=${id}`;
        }
    }

    function eliminarPerfil(id, nombre) {
        if (confirm(`¿Está seguro de eliminar el perfil "${nombre}"?`)) {
            window.location.href = `../index.php?action=eliminar_perfil&id=${id}`;
        }
    }

    function eliminarFactor(id, nombre) {
        if (confirm(`¿Está seguro de eliminar el factor "${nombre}"?`)) {
            window.location.href = `../index.php?action=eliminar_factor&id=${id}`;
        }
    }

    // ===== FUNCIONES DE EDICIÓN =====
    function editarLider(id) {
        alert(`Editar líder con ID: ${id} - Implementar funcionalidad`);
    }

    function editarDominio(id) {
        alert(`Editar dominio con ID: ${id} - Implementar funcionalidad`);
    }

    function editarPerfil(id) {
        alert(`Editar perfil con ID: ${id} - Implementar funcionalidad`);
    }

    function editarFactor(id) {
        alert(`Editar factor con ID: ${id} - Implementar funcionalidad`);
    }

    // ===== FUNCIONES DE EXPORTACIÓN =====
    function exportarProyectos() {
        alert('Exportando proyectos... - Implementar funcionalidad');
    }

    function exportarReporte() {
        alert('Generando reporte... - Implementar funcionalidad');
    }

    // ===== INICIALIZACIÓN =====
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar primera sección
        document.getElementById('seccion-<?= $seccion_activa ?>').classList.add('active');

        // Configurar formulario de asignar líder
        document.getElementById('formAsignarLider').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../index.php?action=asignar_lider_proyecto', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Líder asignado correctamente');
                        closeModalAsignarLider();
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al asignar líder');
                });
        });

        // Auto-generar usuario cuando se escribe el nombre
        const nombreInput = document.querySelector('#formNuevoLider input[name="nombre"]');
        if (nombreInput) {
            nombreInput.addEventListener('blur', function() {
                if (!document.getElementById('usuarioInput').value) {
                    generarUsuario();
                }
            });
        }

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalAyuda();
                closeModalLogout();
                closeModalNuevoProyecto();
                closeModalNuevoLider();
                closeModalAsignarLider();
                closeModalEditarOrganizacion();
                closeModalCrearOrganizacion();
            }
        });

        // Cerrar modales al hacer click fuera
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        });
    });
    </script>
</body>

</html>