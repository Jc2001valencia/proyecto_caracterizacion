<?php
// ========================================
// VIEWS/HOME.PHP - VERSIÓN MEJORADA
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
$error_message = null;
$success_message = null;

// 4. MENSAJES DE LA SESIÓN
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

// 5. CARGAR DATOS DE LA BASE DE DATOS
try {
    // Proyectos
    $stmt = $db->prepare("
        SELECT 
            p.id, 
            p.nombre AS nombre_proyecto, 
            p.descripcion AS descripcion_proyecto, 
            d.nombre AS dominio_cynefin, 
            p.horas AS complejidad_total,
            p.created_at
        FROM proyectos p
        LEFT JOIN dominios d ON p.dominio_id = d.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Dominios
    $stmt = $db->prepare("SELECT id, nombre FROM dominios ORDER BY nombre");
    $stmt->execute();
    $dominios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Perfiles
    $stmt = $db->prepare("SELECT id, nombre FROM perfiles ORDER BY nombre");
    $stmt->execute();
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Características de complejidad
    $stmt = $db->prepare("SELECT id, nombre, descripcion FROM caracteristicas ORDER BY nombre");
    $stmt->execute();
    $complejidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Países
    $stmt = $db->prepare("SELECT id, nombre FROM paises ORDER BY nombre");
    $stmt->execute();
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Si no hay países, usar lista predeterminada
    if (empty($paises)) {
        $paises = [
            ['id' => 1, 'nombre' => 'Colombia'],
            ['id' => 2, 'nombre' => 'Argentina'],
            ['id' => 3, 'nombre' => 'Chile'],
            ['id' => 4, 'nombre' => 'Perú'],
            ['id' => 5, 'nombre' => 'México'],
            ['id' => 6, 'nombre' => 'España'],
            ['id' => 7, 'nombre' => 'Estados Unidos'],
        ];
    }
    
} catch (PDOException $e) {
    error_log("Error al cargar datos: " . $e->getMessage());
    $error_message = "Error al cargar los datos del sistema";
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
                    </div>
                </div>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 p-4 space-y-2">
                <a href="../index.php?action=home"
                    class="block px-4 py-3 rounded-lg bg-gray-700 hover:bg-gray-600 transition">
                    <i class="fas fa-project-diagram mr-2"></i>Proyectos
                </a>
                <a href="#" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-chart-bar mr-2"></i>Estadísticas
                </a>
                <a href="#" class="block px-4 py-3 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-cog mr-2"></i>Configuración
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
                    </div>
                </div>
            </div>

            <nav class="p-4 space-y-2">
                <a href="../index.php?action=home" onclick="toggleMobileSidebar()"
                    class="block px-4 py-3 rounded-lg bg-gray-700">
                    <i class="fas fa-project-diagram mr-2"></i>Proyectos
                </a>
                <a href="#" onclick="toggleMobileSidebar()" class="block px-4 py-3 rounded-lg hover:bg-gray-700">
                    <i class="fas fa-chart-bar mr-2"></i>Estadísticas
                </a>
                <a href="#" onclick="toggleMobileSidebar()" class="block px-4 py-3 rounded-lg hover:bg-gray-700">
                    <i class="fas fa-cog mr-2"></i>Configuración
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
                    <i class="fas fa-project-diagram mr-2 text-blue-600"></i>Proyectos
                </h1>
                <div class="w-10"></div>
            </div>

            <!-- Título Desktop -->
            <div class="hidden lg:block mb-8">
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-project-diagram mr-3 text-blue-600"></i>Gestión de Proyectos
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

            <!-- Tarjeta Principal -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Proyectos</h2>
                        <p class="text-gray-600 mt-1">
                            <i class="fas fa-folder mr-1"></i><?= count($proyectos) ?> proyecto(s) registrado(s)
                        </p>
                    </div>
                    <button onclick="openModal()"
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
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (count($proyectos) > 0): ?>
                            <?php foreach ($proyectos as $p): ?>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <a href="resultados_caracterizacion.php?id=<?= $p['id'] ?>" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white
                                            rounded-md hover:bg-green-700 transition">
                                            <i class="fas fa-eye mr-1"></i>Ver
                                        </a>
                                        <button
                                            onclick="eliminarProyecto(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre_proyecto'])) ?>')"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                            <i class="fas fa-trash mr-1"></i>Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-xl font-medium text-gray-900 mb-2">No hay proyectos</h3>
                                        <p class="text-gray-500 mb-6">Crea tu primer proyecto para comenzar</p>
                                        <button onclick="openModal()"
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
        </main>
    </div>

    <!-- Modal: Caracterización -->
    <div id="modalCaracterizacion" class="modal-overlay hidden">
        <div class="modal-content max-w-4xl">
            <!-- Header -->
            <div
                class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-xl z-10">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-clipboard-check mr-2 text-blue-600"></i>Nueva Caracterización
                </h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-3xl font-bold">
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
                <div id="step1" class="form-step active space-y-6">
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
                <div id="step2" class="form-step space-y-6">
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
                <div id="step3" class="form-step space-y-4">
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
                <div class="flex justify-between mt-8 pt-6 border-t">
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
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">2. Triple Restricción</h3>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li><strong>Tiempo:</strong> Fecha límite inamovible</li>
                        <li><strong>Alcance:</strong> Funcionalidades completas sin reducción</li>
                        <li><strong>Costo:</strong> Presupuesto cerrado</li>
                    </ul>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">3. Factores de Complejidad</h3>
                    <p>Seleccione los factores que apliquen a su proyecto para obtener recomendaciones precisas.</p>
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
    // ===== CONFIGURACIÓN =====
    let pasoActual = 1;
    const totalPasos = 3;
    const perfilesDisponibles = <?= json_encode($perfiles) ?>;

    // ===== MODALES =====
    function openModal() {
        document.getElementById('modalCaracterizacion').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        pasoActual = 1;
        mostrarPaso(1);
        if (document.querySelectorAll('#equipoContainer > div').length === 0) {
            addPerfil();
        }
    }

    function closeModal() {
        document.getElementById('modalCaracterizacion').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('formCaracterizacion').reset();
        document.getElementById('equipoContainer').innerHTML = '';
        pasoActual = 1;
        mostrarPaso(1);
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

    function eliminarProyecto(id, nombre) {
        if (confirm(`¿Está seguro de eliminar el proyecto "${nombre}"?`)) {
            window.location.href = `../index.php?action=eliminar_proyecto&id=${id}`;
        }
    }

    // ===== INICIALIZACIÓN =====
    document.addEventListener('DOMContentLoaded', function() {
        mostrarPaso(1);

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closeModalAyuda();
                closeModalLogout();
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