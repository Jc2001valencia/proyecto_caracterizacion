<?php
require_once(__DIR__ . '/../config/db.php');

// Crear conexión PDO
$database = new Database();
$conn = $database->connect();

// Consultar proyectos
$query = "SELECT id, nombre AS nombre_proyecto, descripcion AS descripcion_proyecto, 
                 dominio_problema AS dominio_cynefin, complejidad_total 
          FROM proyectos 
          ORDER BY id DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consultar dominios predefinidos
$queryDominios = "SELECT id, nombre FROM dominios_problema ORDER BY nombre";
$stmtDominios = $conn->prepare($queryDominios);
$stmtDominios->execute();
$dominios = $stmtDominios->fetchAll(PDO::FETCH_ASSOC);

// Consultar perfiles predefinidos
$queryPerfiles = "SELECT id, nombre FROM perfiles ORDER BY nombre";
$stmtPerfiles = $conn->prepare($queryPerfiles);
$stmtPerfiles->execute();
$perfiles = $stmtPerfiles->fetchAll(PDO::FETCH_ASSOC);

// Consultar factores de complejidad
$queryComplejidades = "SELECT id, nombre, descripcion FROM factores_complejidad ORDER BY nombre";
$stmtComplejidades = $conn->prepare($queryComplejidades);
$stmtComplejidades->execute();
$complejidades = $stmtComplejidades->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Proyectos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .tooltip {
        position: relative;
        display: inline-block;
        cursor: help;
    }

    .tooltip .tooltiptext {
        visibility: hidden;
        width: 220px;
        background-color: #333;
        color: #fff;
        text-align: left;
        border-radius: 8px;
        padding: 8px;
        position: absolute;
        z-index: 10;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.875rem;
    }

    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

    .form-step {
        transition: all 0.3s ease-in-out;
    }

    .mobile-menu {
        display: none;
    }

    @media (max-width: 768px) {
        .sidebar {
            display: none;
        }

        .mobile-menu {
            display: block;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .card-mobile {
            margin: 0.5rem;
            padding: 1rem;
        }
    }

    .complexity-factor {
        border-left: 3px solid #3b82f6;
        padding-left: 1rem;
        margin-bottom: 1rem;
    }

    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .step {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 0.5rem;
        font-weight: bold;
        transition: all 0.3s;
    }

    .step.active {
        background-color: #3b82f6;
        color: white;
    }

    .step.completed {
        background-color: #10b981;
        color: white;
    }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <!-- Mobile Header -->
    <div class="mobile-menu bg-gray-800 text-white p-4 lg:hidden">
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-bold">Dashboard</h1>
            <button id="mobileMenuButton" class="text-white">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileNav" class="hidden mt-4 space-y-2">
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Proyectos</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Perfiles</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Organizaciones</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Configuración</a>
            <button onclick="openModalInstrucciones()"
                class="w-full text-left px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                Instrucciones
            </button>
            <button class="w-full text-left px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                Salir
            </button>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row">
        <!-- Sidebar Desktop -->
        <aside class="w-64 bg-gray-800 text-white hidden lg:flex lg:flex-col">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">
                Dashboard
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700 bg-gray-700">
                    <i class="fas fa-project-diagram mr-2"></i>Proyectos
                </a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-users mr-2"></i>Perfiles
                </a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-building mr-2"></i>Organizaciones
                </a>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-cog mr-2"></i>Configuración
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700 space-y-2">
                <button onclick="openModalInstrucciones()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-book mr-2"></i>Instrucciones
                </button>
                <button
                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Salir
                </button>
            </div>
        </aside>

        <!-- Contenido principal -->
        <main class="flex-1 p-4 lg:p-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl lg:text-3xl font-bold mb-6 text-gray-800 flex items-center">
                    <i class="fas fa-project-diagram mr-3 text-blue-600"></i>Gestión de Proyectos
                </h1>

                <!-- Tarjeta principal -->
                <div class="bg-white shadow-md rounded-lg p-4 lg:p-6 card-mobile">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 lg:mb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4 lg:mb-0">Lista de Proyectos</h2>
                        <div class="flex space-x-2">
                            <button onclick="openModal()"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center w-full lg:w-auto">
                                <i class="fas fa-plus mr-2"></i>Nuevo Proyecto
                            </button>
                            <button onclick="exportToExcel()"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center w-full lg:w-auto">
                                <i class="fas fa-file-excel mr-2"></i>Exportar
                            </button>
                        </div>
                    </div>

                    <!-- Búsqueda y Filtros -->
                    <div
                        class="mb-4 flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-2 lg:space-y-0">
                        <div class="relative flex-1 lg:max-w-md">
                            <input type="text" id="searchInput" placeholder="Buscar proyectos..."
                                class="w-full border rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <div class="flex space-x-2">
                            <select id="filterDomain"
                                class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todos los dominios</option>
                                <?php foreach ($dominios as $dominio): ?>
                                <option value="<?= htmlspecialchars($dominio['nombre']) ?>">
                                    <?= htmlspecialchars($dominio['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Tabla Responsive -->
                    <div class="overflow-x-auto table-responsive">
                        <table class="w-full border-collapse border border-gray-200 text-sm">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="border p-2 text-center">ID</th>
                                    <th class="border p-2 text-center">Nombre</th>
                                    <th class="border p-2 text-center hidden md:table-cell">Descripción</th>
                                    <th class="border p-2 text-center">Dominio</th>
                                    <th class="border p-2 text-center hidden lg:table-cell">Complejidad</th>
                                    <th class="border p-2 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="projectTable">
                                <?php if (count($proyectos) > 0): ?>
                                <?php foreach ($proyectos as $p): ?>
                                <tr class="hover:bg-gray-50 project-row">
                                    <td class="border p-2 text-center"><?= htmlspecialchars($p['id']) ?></td>
                                    <td class="border p-2 text-center font-semibold text-gray-800">
                                        <?= htmlspecialchars($p['nombre_proyecto']) ?>
                                    </td>
                                    <td class="border p-2 text-center text-gray-600 hidden md:table-cell">
                                        <?= strlen($p['descripcion_proyecto']) > 60 
                                                ? htmlspecialchars(substr($p['descripcion_proyecto'], 0, 60)) . '...' 
                                                : htmlspecialchars($p['descripcion_proyecto']); ?>
                                    </td>
                                    <td class="border p-2 text-center text-gray-700">
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            <?= htmlspecialchars($p['dominio_cynefin']) ?>
                                        </span>
                                    </td>
                                    <td class="border p-2 text-center text-gray-700 hidden lg:table-cell">
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            <?= htmlspecialchars($p['complejidad_total']) ?>
                                        </span>
                                    </td>
                                    <td class="border p-2 text-center">
                                        <div
                                            class="flex flex-col lg:flex-row lg:space-x-2 space-y-1 lg:space-y-0 justify-center">
                                            <a href="../views/resultados_caracterizacion.php?id=<?= $p['id'] ?>"
                                                class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition flex items-center justify-center text-sm">
                                                <i class="fas fa-eye mr-1"></i>Ver
                                            </a>
                                            <a href="../controllers/eliminar_proyecto.php?id=<?= $p['id'] ?>"
                                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition flex items-center justify-center text-sm"
                                                onclick="return confirm('¿Estás seguro de eliminar este proyecto?');">
                                                <i class="fas fa-trash mr-1"></i>Eliminar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6" class="border p-6 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-lg">No hay proyectos registrados</p>
                                        <button onclick="openModal()"
                                            class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                            Crear primer proyecto
                                        </button>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Mostrando <span id="showingCount"><?= count($proyectos) ?></span> de
                            <?= count($proyectos) ?> proyectos
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border rounded disabled:opacity-50" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="px-3 py-1 border rounded bg-blue-600 text-white">1</button>
                            <button class="px-3 py-1 border rounded">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de caracterización -->
    <div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[95vh] overflow-hidden flex flex-col">
            <!-- Header del Modal -->
            <div class="flex justify-between items-center p-6 border-b">
                <h2 class="text-xl font-bold">Caracterización del proyecto</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl"
                    aria-label="Cerrar">&times;</button>
            </div>

            <!-- Indicador de Pasos -->
            <div class="step-indicator p-4 bg-gray-50 border-b">
                <div class="step active" id="step1-indicator">1</div>
                <div class="step" id="step2-indicator">2</div>
                <div class="step" id="step3-indicator">3</div>
            </div>

            <!-- Contenido del Modal -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="formCaracterizacion" action="../controllers/procesar_caracterizacion.php" method="POST"
                    novalidate>

                    <!-- Paso 1: Información del proyecto -->
                    <div class="form-step" id="step1">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600">
                            <i class="fas fa-info-circle mr-2"></i>Información del proyecto
                        </h3>

                        <div class="space-y-4">
                            <!-- Nombre del proyecto -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">Nombre del proyecto:</label>
                                <input type="text" name="nombre_proyecto"
                                    class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ejemplo: Sistema de gestión académica" required>
                            </div>

                            <!-- Dominio del problema - LISTA DESPLEGABLE -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">Dominio del problema:</label>
                                <select name="dominio_problema"
                                    class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                                    <option value="">Seleccione un dominio...</option>
                                    <?php foreach ($dominios as $dominio): ?>
                                    <option value="<?= htmlspecialchars($dominio['nombre']) ?>">
                                        <?= htmlspecialchars($dominio['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Breve descripción del proyecto -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">Breve descripción del
                                    proyecto:</label>
                                <textarea name="descripcion_proyecto"
                                    class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    rows="3" placeholder="Resumen general del proyecto..." required></textarea>
                            </div>

                            <!-- Tamaño estimado -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">Tamaño estimado (horas):</label>
                                <input type="number" name="tamano_estimado"
                                    class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ejemplo: 200" min="1" required>
                            </div>

                            <!-- País del cliente -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">País del cliente:</label>
                                <input type="text" name="pais"
                                    class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ejemplo: Colombia" required>
                            </div>

                            <!-- Conformación del equipo - PERFILES PREDEFINIDOS -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">Conformación del equipo:</label>

                                <div class="border rounded-lg bg-white p-4">
                                    <div class="space-y-3" id="equipoContainer">
                                        <!-- Los perfiles se agregarán dinámicamente aquí -->
                                    </div>

                                    <div
                                        class="mt-4 flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-2 lg:space-y-0">
                                        <div class="text-sm text-gray-500">
                                            Seleccione los perfiles y cantidad necesaria
                                        </div>
                                        <button type="button" onclick="addPerfil()"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                                            <i class="fas fa-plus mr-2"></i>Agregar Perfil
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" name="equipo_json" id="equipo_json">
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Triple restricción -->
                    <div class="form-step hidden" id="step2">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600">
                            <i class="fas fa-sliders-h mr-2"></i>Factores fijos de la triple restricción
                        </h3>

                        <div class="space-y-4">
                            <!-- Información sobre triple restricción -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <button type="button" onclick="toggleInfo()"
                                    class="flex items-center text-blue-700 font-semibold mb-2">
                                    <i class="fas fa-info-circle mr-2"></i>Información sobre triple restricción
                                </button>

                                <div id="infoTriple" class="text-sm text-blue-700 space-y-2">
                                    <p><strong>1. Tiempo fijo:</strong> Fecha de entrega comprometida, no puede
                                        cambiarse sin costo extra.</p>
                                    <p><strong>2. Alcance fijo:</strong> Alcance definido; cambios pueden solicitarse,
                                        pero no alteran contrato principal.</p>
                                    <p><strong>3. Costo fijo:</strong> Precio total fijado. Seleccione tipo de contrato:
                                        <strong>Llave en mano</strong> o <strong>Time & Material</strong>.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="restricciones[]" value="Tiempo" class="mr-3 h-5 w-5">
                                    <span class="font-medium">Tiempo</span>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="restricciones[]" value="Alcance" class="mr-3 h-5 w-5">
                                    <span class="font-medium">Alcance</span>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" id="costoCheckbox" name="restricciones[]" value="Costo"
                                        class="mr-3 h-5 w-5" onchange="toggleCostoOpciones()">
                                    <span class="font-medium">Costo</span>
                                </label>
                            </div>

                            <!-- Opciones de Costo fijo -->
                            <div id="costoOpciones" class="mt-4 p-4 border rounded-lg bg-gray-50 hidden">
                                <label class="block font-semibold text-gray-700 mb-3">Tipo de contrato:</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="tipoCosto" value="Llave en mano" class="mr-3 h-4 w-4">
                                        <span>Llave en mano</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="tipoCosto" value="Time & Material"
                                            class="mr-3 h-4 w-4">
                                        <span>Time & Material</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Complejidad añadida - DESDE BASE DE DATOS -->
                    <div class="form-step hidden" id="step3">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600">
                            <i class="fas fa-layer-group mr-2"></i>Complejidad añadida
                        </h3>
                        <p class="text-gray-600 mb-6">Seleccione los factores que añaden complejidad al proyecto:</p>

                        <div class="space-y-4">
                            <?php foreach ($complejidades as $complejidad): ?>
                            <div class="complexity-factor">
                                <label class="flex items-start mb-2 cursor-pointer">
                                    <input type="checkbox" name="complejidad[]"
                                        value="<?= htmlspecialchars($complejidad['nombre']) ?>"
                                        class="mr-3 mt-1 h-5 w-5">
                                    <div>
                                        <span
                                            class="font-medium text-gray-800"><?= htmlspecialchars($complejidad['nombre']) ?></span>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <?= htmlspecialchars($complejidad['descripcion']) ?>
                                        </p>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Botones de navegación -->
                    <div class="flex justify-between mt-8 pt-6 border-t">
                        <button type="button" id="btnPrev" onclick="changeStep(-1)"
                            class="px-6 py-3 bg-gray-300 rounded-lg hover:bg-gray-400 transition hidden flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>Anterior
                        </button>
                        <button type="button" id="btnNext" onclick="changeStep(1)"
                            class="ml-auto px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                            Siguiente<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        <button type="submit" id="btnSubmit"
                            class="ml-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition hidden flex items-center">
                            <i class="fas fa-check mr-2"></i>Enviar caracterización
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Instrucciones -->
    <div id="modalInstrucciones"
        class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-blue-700 flex items-center">
                        <i class="fas fa-book mr-3"></i>Instrucciones de uso
                    </h2>
                    <button onclick="closeModalInstrucciones()"
                        class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[70vh] space-y-6 text-gray-700">
                <!-- Contenido de instrucciones (igual al anterior) -->
                <p>
                    Esta herramienta ofrece una guía para la selección de estrategias, técnicas y herramientas para la
                    <strong>gestión ágil de proyectos</strong> en función de la complejidad técnica y ambiental que
                    presenten.
                </p>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2 flex items-center">
                        <span
                            class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center mr-2 text-sm">1</span>
                        Caracterización del proyecto
                    </h3>
                    <p>
                        Al hacer clic en <strong>“Nuevo proyecto”</strong>, se solicita completar información
                        descriptiva del
                        proyecto e información para determinar su complejidad. Debe indicar los factores fijos de la
                        triple
                        restricción y los factores de complejidad añadida del proyecto.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2 flex items-center">
                        <span
                            class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center mr-2 text-sm">2</span>
                        Factores de la triple restricción
                    </h3>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li><strong>Tiempo fijo:</strong> el proyecto tiene una fecha límite inamovible o sancionable.
                        </li>
                        <li><strong>Alcance fijo:</strong> debe cumplirse con la entrega completa, sin reducción de
                            funcionalidades.</li>
                        <li><strong>Costo fijo:</strong> existe un presupuesto cerrado o un equipo definido sin
                            posibilidad de ampliación.</li>
                    </ul>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2 flex items-center">
                        <span
                            class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center mr-2 text-sm">3</span>
                        Factores de complejidad añadida
                    </h3>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li><strong>Equipo de desarrollo:</strong> conocimientos técnicos avanzados o perfiles
                            especializados.</li>
                        <li><strong>Restricción de tiempo:</strong> plazos muy ajustados para el alcance del proyecto.
                        </li>
                        <li><strong>Tamaño:</strong> gran cantidad de personas o requisitos.</li>
                        <li><strong>Desarrollo global:</strong> diferencias geográficas, horarias o culturales en el
                            equipo.</li>
                        <li><strong>Criticidad del problema:</strong> alto impacto económico, ambiental o en la
                            seguridad.</li>
                        <li><strong>Poca experiencia:</strong> el equipo tiene bajo dominio del problema o tecnologías.
                        </li>
                        <li><strong>Requisitos cambiantes:</strong> el cliente modifica los requisitos con frecuencia.
                        </li>
                        <li><strong>Otras restricciones:</strong> legales, del negocio o de otra índole.</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>

    <script>
    // Variables globales
    let currentStep = 1;
    const totalSteps = 3;

    // Mobile Menu
    document.getElementById('mobileMenuButton').addEventListener('click', function() {
        const mobileNav = document.getElementById('mobileNav');
        mobileNav.classList.toggle('hidden');
    });

    // Funciones del Modal Principal
    function openModal() {
        document.getElementById("modalForm").classList.remove("hidden");
        document.body.style.overflow = 'hidden';
        currentStep = 1;
        showStep(currentStep);
        addPerfil("Desarrollador", 1);
    }

    function closeModal() {
        document.getElementById("modalForm").classList.add("hidden");
        document.body.style.overflow = 'auto';
        document.getElementById("formCaracterizacion").reset();
        currentStep = 1;
    }

    // Navegación entre pasos
    function showStep(step) {
        // Ocultar todos los pasos
        document.querySelectorAll(".form-step").forEach(s => s.classList.add("hidden"));

        // Mostrar paso actual
        document.getElementById(`step${step}`).classList.remove("hidden");

        // Actualizar indicadores
        document.querySelectorAll('.step').forEach((s, i) => {
            s.classList.remove('active', 'completed');
            if (i + 1 === step) {
                s.classList.add('active');
            } else if (i + 1 < step) {
                s.classList.add('completed');
            }
        });

        // Actualizar botones
        document.getElementById("btnPrev").classList.toggle("hidden", step === 1);
        document.getElementById("btnNext").classList.toggle("hidden", step === totalSteps);
        document.getElementById("btnSubmit").classList.toggle("hidden", step !== totalSteps);
    }

    function changeStep(direction) {
        const newStep = currentStep + direction;

        // Validar antes de avanzar
        if (direction === 1 && !validateStep(currentStep)) {
            return;
        }

        currentStep = Math.max(1, Math.min(totalSteps, newStep));
        showStep(currentStep);
    }

    function validateStep(step) {
        const currentStepElement = document.getElementById(`step${step}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let valid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                valid = false;
                field.classList.add('border-red-500');

                // Scroll to first error
                if (valid === false) {
                    field.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!valid) {
            alert('⚠️ Complete los campos requeridos antes de continuar');
            return false;
        }

        // Validación específica para el equipo
        if (step === 1) {
            const equipo = JSON.parse(document.getElementById('equipo_json').value || '[]');
            if (equipo.length === 0) {
                alert('⚠️ Debe agregar al menos un perfil al equipo');
                return false;
            }
        }

        return true;
    }

    // Gestión de Perfiles del Equipo
    function addPerfil(perfilSeleccionado = "", cantidad = 1) {
        const container = document.getElementById("equipoContainer");
        const div = document.createElement("div");
        div.className = "flex flex-col lg:flex-row lg:items-center gap-2 equipo-perfil p-3 border rounded-lg bg-white";

        div.innerHTML = `
            <select class="perfil-select border rounded p-2 flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateEquipoJSON()">
                <option value="">Seleccione perfil...</option>
                <?php foreach ($perfiles as $perfil): ?>
                <option value="<?= htmlspecialchars($perfil['nombre']) ?>" ${'<?= htmlspecialchars($perfil['nombre']) ?>' === perfilSeleccionado ? 'selected' : ''}>
                    <?= htmlspecialchars($perfil['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 whitespace-nowrap">Cantidad:</label>
                <input type="number" min="1" value="${cantidad}" 
                       class="cantidad-input w-20 border rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       onchange="updateEquipoJSON()">
            </div>
            <button type="button" onclick="removePerfil(this)" 
                    class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>
        `;

        container.appendChild(div);
        updateEquipoJSON();
    }

    function removePerfil(btn) {
        btn.closest('.equipo-perfil').remove();
        updateEquipoJSON();
    }

    function updateEquipoJSON() {
        const perfiles = document.querySelectorAll('.equipo-perfil');
        const equipo = [];

        perfiles.forEach(perfilDiv => {
            const perfilSelect = perfilDiv.querySelector('.perfil-select');
            const cantidadInput = perfilDiv.querySelector('.cantidad-input');
            const perfil = perfilSelect ? perfilSelect.value : '';
            const cantidad = cantidadInput ? parseInt(cantidadInput.value) || 0 : 0;

            if (perfil && cantidad > 0) {
                equipo.push({
                    perfil,
                    cantidad
                });
            }
        });

        document.getElementById('equipo_json').value = JSON.stringify(equipo);
    }

    // Funciones de ayuda
    function toggleInfo() {
        document.getElementById("infoTriple").classList.toggle("hidden");
    }

    function toggleCostoOpciones() {
        const costoChecked = document.getElementById("costoCheckbox").checked;
        document.getElementById("costoOpciones").classList.toggle("hidden", !costoChecked);
    }

    // Funciones del Modal de Instrucciones
    function openModalInstrucciones() {
        document.getElementById('modalInstrucciones').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalInstrucciones() {
        document.getElementById('modalInstrucciones').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Búsqueda y Filtros
    document.getElementById('searchInput').addEventListener('input', filterProjects);
    document.getElementById('filterDomain').addEventListener('change', filterProjects);

    function filterProjects() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const domainFilter = document.getElementById('filterDomain').value.toLowerCase();
        const rows = document.querySelectorAll('.project-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const domain = row.cells[3].textContent.toLowerCase();
            const matchesSearch = name.includes(searchTerm);
            const matchesDomain = !domainFilter || domain.includes(domainFilter);

            if (matchesSearch && matchesDomain) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('showingCount').textContent = visibleCount;
    }

    // Exportar a Excel (función básica)
    function exportToExcel() {
        alert('Función de exportación a Excel - Próximamente');
        // Aquí iría la lógica para exportar los datos a Excel
    }

    // Cerrar modales al hacer clic fuera
    document.addEventListener('click', function(event) {
        const modalForm = document.getElementById('modalForm');
        const modalInstrucciones = document.getElementById('modalInstrucciones');

        if (event.target === modalForm) {
            closeModal();
        }
        if (event.target === modalInstrucciones) {
            closeModalInstrucciones();
        }
    });

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar tooltips si es necesario
        const tooltips = document.querySelectorAll('.tooltip');
        tooltips.forEach(tooltip => {
            tooltip.addEventListener('mouseenter', function() {
                this.querySelector('.tooltiptext').style.visibility = 'visible';
                this.querySelector('.tooltiptext').style.opacity = '1';
            });
            tooltip.addEventListener('mouseleave', function() {
                this.querySelector('.tooltiptext').style.visibility = 'hidden';
                this.querySelector('.tooltiptext').style.opacity = '0';
            });
        });
    });
    </script>
</body>

</html>