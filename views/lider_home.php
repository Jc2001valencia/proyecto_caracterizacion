<?php
// ========================================
// VIEWS/LIDER_HOME.PHP - Dashboard para Líderes (VERSIÓN MEJORADA)
// ========================================

// 1. SESIÓN Y AUTENTICACIÓN
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php?page=login');
    exit;
}

// Verificar que sea líder (rol_id = 2)
if (($_SESSION['usuario']['rol_id'] ?? 0) != 2) {
    $_SESSION['error'] = "Acceso denegado. Esta área es solo para líderes.";
    header('Location: index.php?action=home');
    exit;
}

// Configuración de idioma
if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'es';
}

// Cambiar idioma si se solicita
if (isset($_GET['idioma']) && in_array($_GET['idioma'], ['es', 'en'])) {
    $_SESSION['idioma'] = $_GET['idioma'];
}

$idioma_actual = $_SESSION['idioma'];

// Textos multidioma
$textos = [
    'es' => [
        'titulo_dashboard' => 'Dashboard Líder - Sistema de Caracterización',
        'mis_proyectos' => 'Mis Proyectos',
        'caracterizar_proyectos' => 'Caracteriza tus proyectos según el framework Cynefin',
        'proyectos_asignados' => 'proyectos asignados',
        'caracterizacion_completada' => 'Caracterización completada',
        'completa' => 'Completa',
        'dominio_cynefin' => 'Dominio Cynefin',
        'triple_restriccion' => 'Triple Restricción',
        'estado_caracterizacion' => 'Estado de caracterización',
        'pendiente' => 'Pendiente',
        'necesita_caracterizacion' => 'Este proyecto necesita ser caracterizado para determinar la estrategia de gestión óptima.',
        'ver_resultados' => 'Ver Resultados',
        'comenzar_caracterizacion' => 'Comenzar Caracterización',
        'no_hay_proyectos' => 'No hay proyectos asignados',
        'texto_sin_proyectos' => 'Actualmente no tienes proyectos asignados. Cuando un administrador te asigne proyectos, aparecerán aquí.',
        'recargar' => 'Recargar',
        'ayuda_framework' => 'Ayuda - Framework Cynefin',
        'que_es_caracterizacion' => '¿Qué es la caracterización de proyectos?',
        'descripcion_caracterizacion' => 'Es un proceso para determinar la mejor estrategia de gestión según el framework Cynefin, considerando la triple restricción (tiempo, alcance, costo) y los factores de complejidad.',
        'pasos_proceso' => 'Pasos del proceso',
        'triple_restriccion' => 'Triple Restricción',
        'identificar_factores' => 'Identifica qué factores son fijos en el proyecto.',
        'factores_complejidad' => 'Factores de Complejidad',
        'seleccionar_factores' => 'Selecciona los factores que aumentan la complejidad.',
        'analisis_cynefin' => 'Análisis Cynefin',
        'determinar_dominio' => 'El sistema determina el dominio y estrategias recomendadas.',
        'caracterizar_proyecto' => 'Caracterizar Proyecto',
        'completar_informacion' => 'Complete la información según el modelo Cynefin',
        'paso_triple_restriccion' => 'Paso 1: Triple Restricción',
        'seleccionar_factores_fijos' => 'Selecciona qué factores de la triple restricción son fijos para este proyecto:',
        'tiempo' => 'Tiempo',
        'fecha_limite_fija' => 'Fecha límite fija',
        'alcance' => 'Alcance',
        'funcionalidades_completas' => 'Funcionalidades completas',
        'costo' => 'Costo',
        'presupuesto_cerrado' => 'Presupuesto cerrado',
        'nota_seleccion' => 'Nota: Selecciona todos los factores que sean fijos en tu proyecto. Puedes seleccionar 1, 2 o los 3 factores.',
        'siguiente' => 'Siguiente',
        'paso_factores_complejidad' => 'Paso 2: Factores de Complejidad Añadida',
        'seleccionar_factores_complejidad' => 'Selecciona todos los factores de complejidad que aplican a este proyecto:',
        'factores_seleccionados' => 'factores seleccionados',
        'anterior' => 'Anterior',
        'completar_caracterizacion' => 'Completar Caracterización',
        'pais_cliente' => 'País del Cliente',
        'seleccionar_pais' => 'Seleccionar país',
        'equipo_desarrollo' => 'Equipo de Desarrollo',
        'agregar_miembro' => 'Agregar Miembro',
        'perfil' => 'Perfil',
        'cantidad' => 'Cantidad',
        'eliminar' => 'Eliminar',
        'no_hay_factores' => 'No hay factores de complejidad configurados',
        'error_conexion' => 'Error de conexión con el servidor',
        'error_seleccion_restriccion' => 'Debe seleccionar al menos una restricción fija',
        'error_seleccion_complejidad' => 'Debe seleccionar al menos un factor de complejidad',
        'idioma' => 'Idioma',
        'espanol' => 'Español',
        'ingles' => 'Inglés',
        'cerrar_sesion' => 'Cerrar Sesión',
        'panel_lider' => 'Panel del Líder',
        'caracterizacion_proyectos' => 'Caracterización de Proyectos',
        'horas_estimadas' => 'horas estimadas',
        'sin_caracterizar' => 'Sin caracterizar',
        'ver_estrategias' => 'Ver Estrategias',
        'editar' => 'Editar'
    ],
    'en' => [
        'titulo_dashboard' => 'Leader Dashboard - Characterization System',
        'mis_proyectos' => 'My Projects',
        'caracterizar_proyectos' => 'Characterize your projects according to the Cynefin framework',
        'proyectos_asignados' => 'assigned projects',
        'caracterizacion_completada' => 'Characterization completed',
        'completa' => 'Complete',
        'dominio_cynefin' => 'Cynefin Domain',
        'triple_restriccion' => 'Triple Constraint',
        'estado_caracterizacion' => 'Characterization status',
        'pendiente' => 'Pending',
        'necesita_caracterizacion' => 'This project needs to be characterized to determine the optimal management strategy.',
        'ver_resultados' => 'View Results',
        'comenzar_caracterizacion' => 'Start Characterization',
        'no_hay_proyectos' => 'No assigned projects',
        'texto_sin_proyectos' => 'You currently have no assigned projects. When an administrator assigns you projects, they will appear here.',
        'recargar' => 'Reload',
        'ayuda_framework' => 'Help - Cynefin Framework',
        'que_es_caracterizacion' => 'What is project characterization?',
        'descripcion_caracterizacion' => 'It is a process to determine the best management strategy according to the Cynefin framework, considering the triple constraint (time, scope, cost) and complexity factors.',
        'pasos_proceso' => 'Process steps',
        'triple_restriccion' => 'Triple Constraint',
        'identificar_factores' => 'Identify which factors are fixed in the project.',
        'factores_complejidad' => 'Complexity Factors',
        'seleccionar_factores' => 'Select factors that increase complexity.',
        'analisis_cynefin' => 'Cynefin Analysis',
        'determinar_dominio' => 'The system determines the domain and recommended strategies.',
        'caracterizar_proyecto' => 'Characterize Project',
        'completar_informacion' => 'Complete information according to the Cynefin model',
        'paso_triple_restriccion' => 'Step 1: Triple Constraint',
        'seleccionar_factores_fijos' => 'Select which triple constraint factors are fixed for this project:',
        'tiempo' => 'Time',
        'fecha_limite_fija' => 'Fixed deadline',
        'alcance' => 'Scope',
        'funcionalidades_completas' => 'Complete functionalities',
        'costo' => 'Cost',
        'presupuesto_cerrado' => 'Closed budget',
        'nota_seleccion' => 'Note: Select all factors that are fixed in your project. You can select 1, 2 or all 3 factors.',
        'siguiente' => 'Next',
        'paso_factores_complejidad' => 'Step 2: Added Complexity Factors',
        'seleccionar_factores_complejidad' => 'Select all complexity factors that apply to this project:',
        'factores_seleccionados' => 'factors selected',
        'anterior' => 'Previous',
        'completar_caracterizacion' => 'Complete Characterization',
        'pais_cliente' => 'Client Country',
        'seleccionar_pais' => 'Select country',
        'equipo_desarrollo' => 'Development Team',
        'agregar_miembro' => 'Add Member',
        'perfil' => 'Profile',
        'cantidad' => 'Quantity',
        'eliminar' => 'Delete',
        'no_hay_factores' => 'No complexity factors configured',
        'error_conexion' => 'Connection error with server',
        'error_seleccion_restriccion' => 'You must select at least one fixed restriction',
        'error_seleccion_complejidad' => 'You must select at least one complexity factor',
        'idioma' => 'Language',
        'espanol' => 'Spanish',
        'ingles' => 'English',
        'cerrar_sesion' => 'Logout',
        'panel_lider' => 'Leader Panel',
        'caracterizacion_proyectos' => 'Project Characterization',
        'horas_estimadas' => 'estimated hours',
        'sin_caracterizar' => 'Not characterized',
        'ver_estrategias' => 'View Strategies',
        'editar' => 'Edit'
    ]
];

$t = $textos[$idioma_actual];

// 2. CONEXIÓN A BASE DE DATOS
require_once __DIR__ . '/../config/db.php';
$database = new Database();
$db = $database->getConnection();

if (!($db instanceof PDO)) {
    die("ERROR CRÍTICO: No se pudo conectar a la base de datos");
}

// 3. INICIALIZAR VARIABLES
$mis_proyectos = [];
$caracteristicas = [];
$error_message = null;
$success_message = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

$lider_id = $_SESSION['usuario']['id'];

// 4. CARGAR DATOS
try {
    // CONSULTA MEJORADA: Incluye información de caracterización si existe
    $sql_proyectos = "
        SELECT 
            p.id,
            p.nombre,
            p.descripcion,
            COALESCE(p.horas, 0) as horas,
            COALESCE(p.estado, 'pendiente') as estado,
            p.fecha_inicio,
            p.fecha_fin,
            p.lider_proyecto_id,
            p.created_at,
            p.pais_id,
            p.dominio_id,
            c.id as caracterizacion_id,
            c.dominio_cynefin,
            c.tipo_restriccion
        FROM proyectos p
        LEFT JOIN caracterizaciones c ON p.id = c.proyecto_id
        WHERE p.lider_proyecto_id = :lider_id 
        ORDER BY p.created_at DESC
    ";
    
    $stmt = $db->prepare($sql_proyectos);
    $stmt->execute(['lider_id' => $lider_id]);
    $mis_proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // CARGAR FACTORES DE COMPLEJIDAD
    try {
        $stmt = $db->query("SELECT id, nombre, descripcion FROM caracteristicas ORDER BY nombre");
        $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Usar factores por defecto
        $caracteristicas = [
            ['id' => 1, 'nombre' => 'Exigencias especiales del equipo', 'descripcion' => 'Requerimientos especiales para el equipo de desarrollo'],
            ['id' => 2, 'nombre' => 'Tiempo muy ajustado', 'descripcion' => 'Además de ser fijo, el tiempo está muy ajustado'],
            ['id' => 3, 'nombre' => 'Gran tamaño del proyecto', 'descripcion' => 'Muchas personas en el proyecto o gran cantidad de requisitos'],
            ['id' => 4, 'nombre' => 'Distancias en el equipo', 'descripcion' => 'Distancias física, temporal o cultural entre miembros'],
            ['id' => 5, 'nombre' => 'Dominio crítico', 'descripcion' => 'Impacto en vida, seguridad o grandes pérdidas'],
            ['id' => 6, 'nombre' => 'Poca experiencia', 'descripcion' => 'Poca experiencia en dominio, tecnologías o gestión'],
            ['id' => 7, 'nombre' => 'Cliente cambiante', 'descripcion' => 'El cliente cambia requisitos con alta frecuencia'],
            ['id' => 8, 'nombre' => 'Restricciones fuertes', 'descripcion' => 'Restricciones legales, de negocio u otras importantes']
        ];
    }
    
} catch (PDOException $e) {
    error_log("Error en lider_home: " . $e->getMessage());
    $error_message = "Error al cargar los proyectos. Contacte al administrador.";
}

// 5. DEFINIR VARIABLES PARA LA VISTA
$nombre_usuario = $_SESSION['usuario']['nombre'] ?? 'Líder';
$apellido_usuario = $_SESSION['usuario']['apellido'] ?? '';
$email_usuario = $_SESSION['usuario']['email'] ?? '';
$inicial_usuario = strtoupper(substr($nombre_usuario, 0, 1));
$nombre_completo = trim($nombre_usuario . ' ' . $apellido_usuario);
$cantidad_proyectos = count($mis_proyectos);

// Lista de países
$paises = [
    'AR' => 'Argentina',
    'BO' => 'Bolivia',
    'BR' => 'Brasil',
    'CL' => 'Chile',
    'CO' => 'Colombia',
    'CR' => 'Costa Rica',
    'CU' => 'Cuba',
    'DO' => 'República Dominicana',
    'EC' => 'Ecuador',
    'SV' => 'El Salvador',
    'GT' => 'Guatemala',
    'HN' => 'Honduras',
    'MX' => 'México',
    'NI' => 'Nicaragua',
    'PA' => 'Panamá',
    'PY' => 'Paraguay',
    'PE' => 'Perú',
    'PR' => 'Puerto Rico',
    'UY' => 'Uruguay',
    'VE' => 'Venezuela',
    'US' => 'Estados Unidos',
    'CA' => 'Canadá',
    'ES' => 'España',
    'FR' => 'Francia',
    'DE' => 'Alemania',
    'IT' => 'Italia',
    'UK' => 'Reino Unido',
    'PT' => 'Portugal',
    'OTRO' => 'Otro país'
];

// Tipos de restricción según Excel
$tipos_restriccion = [
    1 => ['nombre' => 'Solo tiempo fijo', 'color' => 'bg-blue-100 text-blue-800'],
    2 => ['nombre' => 'Solo alcance fijo', 'color' => 'bg-purple-100 text-purple-800'],
    3 => ['nombre' => 'Solo costo fijo', 'color' => 'bg-green-100 text-green-800'],
    4 => ['nombre' => 'Dos factores fijos', 'color' => 'bg-yellow-100 text-yellow-800'],
    5 => ['nombre' => 'Tres factores fijos', 'color' => 'bg-red-100 text-red-800']
];

// Dominios Cynefin
$dominios_cynefin = [
    'Claro' => ['color' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle'],
    'Complicado' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-search'],
    'Complejo' => ['color' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-cogs'],
    'Caótico' => ['color' => 'bg-red-100 text-red-800', 'icon' => 'fa-fire'],
    'No determinado' => ['color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question']
];
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['titulo_dashboard']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    .sidebar {
        background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);
    }

    .card-project {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
        background: white;
    }

    .card-project:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #3b82f6;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 90vw;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.95) translateY(20px);
        transition: all 0.3s ease;
    }

    .modal-overlay.show .modal-content {
        transform: scale(1) translateY(0);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        transition: all 0.3s;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
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

    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
        animation: fadeIn 0.3s ease;
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

    .equipo-item {
        transition: all 0.3s ease;
    }

    .equipo-item:hover {
        background-color: #f9fafb;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1000;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }
    </style>
</head>

<body class="min-h-screen">
    <!-- Sidebar Desktop -->
    <aside class="hidden lg:flex lg:w-64 sidebar flex-col fixed h-screen z-30 text-white">
        <div class="p-6 border-b border-blue-700">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold"><?php echo $t['panel_lider']; ?></h1>
                    <p class="text-xs text-blue-200"><?php echo $t['caracterizacion_proyectos']; ?></p>
                </div>
            </div>
        </div>

        <div class="p-4 border-b border-blue-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center font-semibold">
                    <?= $inicial_usuario ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm truncate"><?= htmlspecialchars($nombre_completo) ?></p>
                    <p class="text-xs text-blue-200 truncate"><?= htmlspecialchars($email_usuario) ?></p>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-4">
            <div class="space-y-1">
                <a href="index.php?action=lider_home&idioma=<?php echo $idioma_actual; ?>"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-700 text-white">
                    <i class="fas fa-project-diagram"></i>
                    <span class="font-medium"><?php echo $t['mis_proyectos']; ?></span>
                    <?php if ($cantidad_proyectos > 0): ?>
                    <span class="ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-1"><?= $cantidad_proyectos ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-blue-700 space-y-2">
            <div class="dropdown relative">
                <button class="w-full flex items-center gap-2 px-4 py-2.5 text-blue-200 hover:bg-blue-700 rounded-lg transition">
                    <i class="fas fa-globe"></i>
                    <span class="font-medium"><?php echo $t['idioma']; ?></span>
                </button>
                <div class="dropdown-content left-0 bottom-full mb-2">
                    <a href="?action=lider_home&idioma=es" class="block px-4 py-2 hover:bg-blue-100 <?php echo $idioma_actual === 'es' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                        <i class="fas fa-flag mr-2"></i><?php echo $t['espanol']; ?>
                    </a>
                    <a href="?action=lider_home&idioma=en" class="block px-4 py-2 hover:bg-blue-100 <?php echo $idioma_actual === 'en' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                        <i class="fas fa-flag-usa mr-2"></i><?php echo $t['ingles']; ?>
                    </a>
                </div>
            </div>
            
            <button onclick="openModal('modalAyuda')"
                class="w-full flex items-center gap-2 px-4 py-2.5 text-blue-200 hover:bg-blue-700 rounded-lg transition">
                <i class="fas fa-question-circle"></i>
                <span class="font-medium"><?php echo $t['ayuda_framework']; ?></span>
            </button>
            
            <a href="index.php?action=logout"
                class="w-full flex items-center gap-2 px-4 py-2.5 text-red-300 hover:bg-red-900/20 rounded-lg transition">
                <i class="fas fa-sign-out-alt"></i>
                <span class="font-medium"><?php echo $t['cerrar_sesion']; ?></span>
            </a>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="lg:ml-64 min-h-screen">
        <!-- Header Superior -->
        <header class="bg-white border-b border-gray-200 shadow-sm">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="lg:hidden">
                        <button onclick="toggleMobileSidebar()" class="p-2 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-bars text-xl text-gray-700"></i>
                        </button>
                    </div>

                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-800"><?php echo $t['mis_proyectos']; ?></h1>
                        <p class="text-gray-600 text-sm"><?php echo $t['caracterizar_proyectos']; ?></p>
                    </div>

                    <div class="hidden md:flex items-center gap-3">
                        <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-lg">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                            <span class="font-medium text-blue-700"><?= $cantidad_proyectos ?>
                                <?php echo $t['proyectos_asignados']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido -->
        <div class="p-6">
            <!-- Mensajes del Sistema -->
            <?php if ($error_message): ?>
            <div class="mb-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-red-800">Error</p>
                            <p class="text-red-600 text-sm"><?= htmlspecialchars($error_message) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
            <div class="mb-6">
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-green-800">Éxito</p>
                            <p class="text-green-600 text-sm"><?= htmlspecialchars($success_message) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Grid de Proyectos -->
            <?php if ($cantidad_proyectos > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($mis_proyectos as $proyecto): 
                    // Determinar estado del proyecto
                    $estado = $proyecto['estado'] ?? 'pendiente';
                    $estado_colores = [
                        'activo' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-play-circle'],
                        'pendiente' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock'],
                        'finalizado' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-check-circle'],
                        'pausado' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-pause-circle']
                    ];
                    $color_estado = $estado_colores[$estado] ?? $estado_colores['pendiente'];
                    
                    // Verificar si ya está caracterizado
                    $caracterizado = !empty($proyecto['caracterizacion_id']) || !empty($proyecto['dominio_cynefin']);
                    
                    // Obtener información de caracterización si existe
                    $dominio_cynefin = $proyecto['dominio_cynefin'] ?? 'No determinado';
                    $tipo_restriccion = $t['sin_caracterizar'];
                    
                    if ($caracterizado) {
                        $tipo_restriccion_num = $proyecto['tipo_restriccion'] ?? 0;
                        $tipo_restriccion = $tipos_restriccion[$tipo_restriccion_num]['nombre'] ?? $t['sin_caracterizar'];
                    }
                    
                    $nombre = htmlspecialchars($proyecto['nombre'] ?? 'Sin nombre');
                    $descripcion = htmlspecialchars($proyecto['descripcion'] ?? 'Sin descripción');
                    $horas = $proyecto['horas'] ?? 0;
                    $id_proyecto = $proyecto['id'];
                ?>
                <div class="card-project rounded-xl shadow-md overflow-hidden">
                    <!-- Encabezado de la Card -->
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 text-lg truncate"><?= $nombre ?></h3>
                                <p class="text-xs text-gray-500 mt-1">ID: #<?= $id_proyecto ?></p>
                            </div>
                            <span class="badge-status <?= $color_estado['bg'] ?> <?= $color_estado['text'] ?>">
                                <i class="fas <?= $color_estado['icon'] ?> mr-1"></i>
                                <?= ucfirst($estado) ?>
                            </span>
                        </div>

                        <p class="text-gray-600 text-sm line-clamp-2 mb-3"><?= $descripcion ?></p>

                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock text-gray-400 mr-1"></i>
                            <span><?= $horas ?> <?php echo $t['horas_estimadas']; ?></span>
                        </div>
                    </div>

                    <!-- Estado de Caracterización -->
                    <div class="p-5">
                        <?php if ($caracterizado): ?>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700"><?php echo $t['caracterizacion_completada']; ?></span>
                                <span class="text-xs text-green-600 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i><?php echo $t['completa']; ?>
                                </span>
                            </div>

                            <!-- Dominio Cynefin -->
                            <div class="mb-3">
                                <span class="text-xs text-gray-500"><?php echo $t['dominio_cynefin']; ?>:</span>
                                <?php $dominio_config = $dominios_cynefin[$dominio_cynefin] ?? $dominios_cynefin['No determinado']; ?>
                                <span class="ml-2 badge-status <?= $dominio_config['color'] ?>">
                                    <i class="fas <?= $dominio_config['icon'] ?> mr-1"></i>
                                    <?= $dominio_cynefin ?>
                                </span>
                            </div>

                            <!-- Triple Restricción -->
                            <div class="mb-3">
                                <span class="text-xs text-gray-500"><?php echo $t['triple_restriccion']; ?>:</span>
                                <span class="ml-2 text-sm font-medium"><?= $tipo_restriccion ?></span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="verResultados(<?= $id_proyecto ?>)"
                                class="flex-1 btn-primary py-2.5 rounded-lg font-medium text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-chart-bar"></i>
                                <?php echo $t['ver_estrategias']; ?>
                            </button>
                            <button onclick="editarCaracterizacion(<?= $id_proyecto ?>)"
                                class="px-4 py-2.5 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 font-medium text-sm flex items-center justify-center">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700"><?php echo $t['estado_caracterizacion']; ?></span>
                                <span class="text-xs text-yellow-600 font-medium">
                                    <i class="fas fa-clock mr-1"></i><?php echo $t['pendiente']; ?>
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 mb-4">
                                <?php echo $t['necesita_caracterizacion']; ?>
                            </p>
                        </div>

                        <button onclick="caracterizarProyecto(<?= $id_proyecto ?>)"
                            class="w-full btn-success py-2.5 rounded-lg font-medium text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-clipboard-check"></i>
                            <?php echo $t['comenzar_caracterizacion']; ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Estado Vacío -->
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3"><?php echo $t['no_hay_proyectos']; ?></h3>
                <p class="text-gray-500 max-w-md mx-auto mb-8">
                    <?php echo $t['texto_sin_proyectos']; ?>
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button onclick="recargarPagina()"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition inline-flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i><?php echo $t['recargar']; ?>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal: Caracterización de Proyecto -->
    <div id="modalCaracterizar" class="modal-overlay">
        <div class="modal-content max-w-4xl">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-clipboard-check text-blue-600"></i>
                            <span id="modalTitulo"><?php echo $t['caracterizar_proyecto']; ?></span>
                        </h2>
                        <p class="text-sm text-gray-500 mt-1" id="modalSubtitulo"><?php echo $t['completar_informacion']; ?></p>
                    </div>
                    <button onclick="closeModal('modalCaracterizar')"
                        class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-xl text-gray-500"></i>
                    </button>
                </div>
            </div>

            <form id="formCaracterizacion" method="POST" class="p-6">
                <input type="hidden" name="proyecto_id" id="proyecto_id">
                <input type="hidden" name="idioma" value="<?php echo $idioma_actual; ?>">

                <!-- Paso 1: Información del Proyecto -->
                <div id="paso1" class="form-step active">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <?php echo $t['paso_triple_restriccion']; ?>
                        </h3>

                        <p class="text-gray-600 mb-6">
                            <?php echo $t['seleccionar_factores_fijos']; ?>
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- Tiempo Fijo -->
                            <label class="relative cursor-pointer">
                                <input type="checkbox" name="restricciones[]" value="Tiempo" class="sr-only peer">
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center peer-checked:bg-blue-100">
                                            <i class="fas fa-clock text-gray-600 peer-checked:text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo $t['tiempo']; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $t['fecha_limite_fija']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Alcance Fijo -->
                            <label class="relative cursor-pointer">
                                <input type="checkbox" name="restricciones[]" value="Alcance" class="sr-only peer">
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 hover:border-purple-300 peer-checked:border-purple-500 peer-checked:bg-purple-50">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center peer-checked:bg-purple-100">
                                            <i class="fas fa-list-check text-gray-600 peer-checked:text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo $t['alcance']; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $t['funcionalidades_completas']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Costo Fijo -->
                            <label class="relative cursor-pointer">
                                <input type="checkbox" name="restricciones[]" value="Costo" class="sr-only peer">
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 hover:border-green-300 peer-checked:border-green-500 peer-checked:bg-green-50">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center peer-checked:bg-green-100">
                                            <i class="fas fa-money-bill text-gray-600 peer-checked:text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo $t['costo']; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $t['presupuesto_cerrado']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- País del Cliente -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <?php echo $t['pais_cliente']; ?>
                            </label>
                            <select name="pais_cliente" class="w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value=""><?php echo $t['seleccionar_pais']; ?></option>
                                <?php foreach ($paises as $codigo => $nombre): ?>
                                <option value="<?php echo $codigo; ?>"><?php echo $nombre; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Instrucciones -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong><?php echo $t['nota_seleccion']; ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" onclick="siguientePaso()"
                            class="ml-auto px-6 py-3 btn-primary rounded-lg font-medium flex items-center gap-2">
                            <?php echo $t['siguiente']; ?>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Paso 2: Factores de Complejidad y Equipo -->
                <div id="paso2" class="form-step">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-layer-group text-purple-600"></i>
                            <?php echo $t['paso_factores_complejidad']; ?>
                        </h3>

                        <p class="text-gray-600 mb-6">
                            <?php echo $t['seleccionar_factores_complejidad']; ?>
                        </p>

                        <?php if (!empty($caracteristicas)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2 mb-6">
                            <?php foreach ($caracteristicas as $caracteristica): ?>
                            <label
                                class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                <input type="checkbox" name="complejidad[]"
                                    value="<?= htmlspecialchars($caracteristica['nombre']) ?>"
                                    class="mt-1 mr-3 h-5 w-5 text-blue-600 rounded border-gray-300">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">
                                        <?= htmlspecialchars($caracteristica['nombre']) ?></p>
                                    <?php if (!empty($caracteristica['descripcion'])): ?>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <?= htmlspecialchars($caracteristica['descripcion']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Contador de selección -->
                        <div id="contadorComplejidad" class="mt-4 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg mb-6">
                            <i class="fas fa-check-circle mr-2 text-green-500"></i>
                            <span id="seleccionados">0</span> <?php echo $t['factores_seleccionados']; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-8 text-gray-500 mb-6">
                            <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                            <p><?php echo $t['no_hay_factores']; ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Equipo de Desarrollo -->
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-users text-green-600"></i>
                                <?php echo $t['equipo_desarrollo']; ?>
                            </h4>
                            
                            <div id="equipo-container" class="space-y-3">
                                <!-- Primer miembro del equipo -->
                                <div class="equipo-item grid grid-cols-1 md:grid-cols-2 gap-3 p-3 border border-gray-200 rounded-lg">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <?php echo $t['perfil']; ?>
                                        </label>
                                        <input type="text" name="equipo_perfil[]" 
                                               class="w-full border border-gray-300 rounded-lg p-2 text-gray-700" 
                                               placeholder="Ej: Desarrollador Senior">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <?php echo $t['cantidad']; ?>
                                        </label>
                                        <input type="number" name="equipo_cantidad[]" min="1" 
                                               class="w-full border border-gray-300 rounded-lg p-2 text-gray-700" 
                                               placeholder="Ej: 2">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" onclick="agregarMiembroEquipo()" 
                                    class="mt-4 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 font-medium flex items-center gap-2">
                                <i class="fas fa-plus"></i>
                                <?php echo $t['agregar_miembro']; ?>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" onclick="anteriorPaso()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            <?php echo $t['anterior']; ?>
                        </button>
                        <button type="submit"
                            class="px-6 py-3 btn-success rounded-lg font-medium flex items-center gap-2">
                            <i class="fas fa-check"></i>
                            <?php echo $t['completar_caracterizacion']; ?>
                        </button>
                    </div>
                </div>

                <!-- Mensajes de validación -->
                <div id="mensajeError" class="hidden mt-4 p-3 bg-red-50 text-red-700 rounded-lg"></div>
            </form>
        </div>
    </div>

    <!-- Modal: Ayuda -->
    <div id="modalAyuda" class="modal-overlay">
        <div class="modal-content max-w-2xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-question-circle text-blue-600"></i>
                        <?php echo $t['ayuda_framework']; ?>
                    </h2>
                    <button onclick="closeModal('modalAyuda')" class="p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-times text-xl text-gray-500"></i>
                    </button>
                </div>

                <div class="space-y-6">
                    <div class="bg-blue-50 rounded-xl p-4">
                        <h3 class="font-semibold text-blue-800 mb-2"><?php echo $t['que_es_caracterizacion']; ?></h3>
                        <p class="text-blue-700">
                            <?php echo $t['descripcion_caracterizacion']; ?>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800"><?php echo $t['pasos_proceso']; ?>:</h4>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold">1</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800"><?php echo $t['triple_restriccion']; ?></h5>
                                <p class="text-sm text-gray-600"><?php echo $t['identificar_factores']; ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-600 font-bold">2</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800"><?php echo $t['factores_complejidad']; ?></h5>
                                <p class="text-sm text-gray-600"><?php echo $t['seleccionar_factores']; ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-green-600 font-bold">3</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800"><?php echo $t['analisis_cynefin']; ?></h5>
                                <p class="text-sm text-gray-600"><?php echo $t['determinar_dominio']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Variables globales
    let pasoActual = 1;
    let totalPasos = 2;
    let proyectoSeleccionado = null;
    let contadorEquipo = 1;

    // ===== FUNCIONES DE MODALES =====
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

    // ===== FUNCIONES DE CARACTERIZACIÓN =====
    function caracterizarProyecto(proyectoId) {
        proyectoSeleccionado = proyectoId;
        document.getElementById('proyecto_id').value = proyectoId;

        // Resetear formulario
        pasoActual = 1;
        mostrarPaso(1);
        document.getElementById('formCaracterizacion').reset();
        document.getElementById('mensajeError').classList.add('hidden');
        
        // Reiniciar contador de equipo
        contadorEquipo = 1;
        const equipoContainer = document.getElementById('equipo-container');
        equipoContainer.innerHTML = `
            <div class="equipo-item grid grid-cols-1 md:grid-cols-2 gap-3 p-3 border border-gray-200 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <?php echo $t['perfil']; ?>
                    </label>
                    <input type="text" name="equipo_perfil[]" 
                           class="w-full border border-gray-300 rounded-lg p-2 text-gray-700" 
                           placeholder="Ej: Desarrollador Senior">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <?php echo $t['cantidad']; ?>
                    </label>
                    <input type="number" name="equipo_cantidad[]" min="1" 
                           class="w-full border border-gray-300 rounded-lg p-2 text-gray-700" 
                           placeholder="Ej: 2">
                </div>
            </div>
        `;

        document.getElementById('modalTitulo').textContent = `<?php echo $t['caracterizar_proyecto']; ?> #${proyectoId}`;
        openModal('modalCaracterizar');
    }

    function editarCaracterizacion(proyectoId) {
        caracterizarProyecto(proyectoId);
    }

    function verResultados(proyectoId) {
        window.location.href = `views/resultados_caracterizacion.php?proyecto_id=${proyectoId}&idioma=<?php echo $idioma_actual; ?>`;
    }

    // ===== FUNCIONES DE EQUIPO =====
    function agregarMiembroEquipo() {
        contadorEquipo++;
        const equipoContainer = document.getElementById('equipo-container');
        const nuevoMiembro = document.createElement('div');
        nuevoMiembro.className = 'equipo-item grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-gray-200 rounded-lg';
        nuevoMiembro.innerHTML = `
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <?php echo $t['perfil']; ?>
                </label>
                <input type="text" name="equipo_perfil[]" 
                       class="w-full border border-gray-300 rounded-lg p-2 text-gray-700" 
                       placeholder="Ej: Desarrollador Junior">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <?php echo $t['cantidad']; ?>
                </label>
                <div class="flex items-center gap-2">
                    <input type="number" name="equipo_cantidad[]" min="1" 
                           class="w-full border border-gray-300 rounded-lg p-2 text-gray-700" 
                           placeholder="Ej: 3">
                    <button type="button" onclick="eliminarMiembroEquipo(this)" 
                            class="p-2 text-red-600 hover:text-red-800" title="<?php echo $t['eliminar']; ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        equipoContainer.appendChild(nuevoMiembro);
    }

    function eliminarMiembroEquipo(boton) {
        const item = boton.closest('.equipo-item');
        if (item && contadorEquipo > 1) {
            item.remove();
            contadorEquipo--;
        }
    }

    // ===== FUNCIONES DE CONTADOR =====
    function actualizarContadorComplejidad() {
        const checkboxes = document.querySelectorAll('input[name="complejidad[]"]:checked');
        const contador = document.getElementById('seleccionados');
        if (contador) {
            contador.textContent = checkboxes.length;
        }
    }

    // ===== NAVEGACIÓN DE PASOS =====
    function mostrarPaso(paso) {
        for (let i = 1; i <= totalPasos; i++) {
            const pasoEl = document.getElementById(`paso${i}`);
            if (pasoEl) {
                pasoEl.classList.toggle('active', i === paso);
            }
        }
    }

    function siguientePaso() {
        if (pasoActual === 1) {
            const restricciones = document.querySelectorAll('input[name="restricciones[]"]:checked');
            const pais = document.querySelector('select[name="pais_cliente"]').value;
            
            if (restricciones.length === 0) {
                mostrarError('<?php echo $t['error_seleccion_restriccion']; ?>');
                return;
            }
            
            if (!pais) {
                mostrarError('<?php echo $t['pais_cliente']; ?> <?php echo $t['seleccionar_pais']; ?>');
                return;
            }
        }

        if (pasoActual < totalPasos) {
            pasoActual++;
            mostrarPaso(pasoActual);
            document.getElementById('mensajeError').classList.add('hidden');
        }
    }

    function anteriorPaso() {
        if (pasoActual > 1) {
            pasoActual--;
            mostrarPaso(pasoActual);
            document.getElementById('mensajeError').classList.add('hidden');
        }
    }

    function mostrarError(mensaje) {
        const errorDiv = document.getElementById('mensajeError');
        errorDiv.textContent = mensaje;
        errorDiv.classList.remove('hidden');
        errorDiv.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    // ===== FUNCIONES UTILITARIAS =====
    function recargarPagina() {
        window.location.reload();
    }

    function toggleMobileSidebar() {
        alert('Para versión móvil, por favor usa un dispositivo con pantalla más grande.');
    }

    // ===== EVENTOS =====
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formCaracterizacion');
        if (form) {
            // Contador de factores de complejidad
            const checkboxesComplejidad = document.querySelectorAll('input[name="complejidad[]"]');
            checkboxesComplejidad.forEach(checkbox => {
                checkbox.addEventListener('change', actualizarContadorComplejidad);
            });
            
            // Inicializar contador
            actualizarContadorComplejidad();
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validar factores de complejidad
                const complejidad = document.querySelectorAll('input[name="complejidad[]"]:checked');
                if (complejidad.length === 0) {
                    mostrarError('<?php echo $t['error_seleccion_complejidad']; ?>');
                    return false;
                }

                // Validar equipo
                const perfiles = document.querySelectorAll('input[name="equipo_perfil[]"]');
                const cantidades = document.querySelectorAll('input[name="equipo_cantidad[]"]');
                let equipoValido = true;
                
                perfiles.forEach((perfil, index) => {
                    if (!perfil.value.trim()) {
                        mostrarError('<?php echo $t['perfil']; ?> ' + (index + 1) + ' <?php echo $t['no_definido']; ?>');
                        equipoValido = false;
                    }
                });
                
                cantidades.forEach((cantidad, index) => {
                    if (!cantidad.value || cantidad.value < 1) {
                        mostrarError('<?php echo $t['cantidad']; ?> ' + (index + 1) + ' <?php echo $t['no_definido']; ?>');
                        equipoValido = false;
                    }
                });
                
                if (!equipoValido) {
                    return false;
                }

                // Mostrar loading
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><?php echo $t['completar_caracterizacion']; ?>...';
                submitBtn.disabled = true;

                // Enviar formulario
                const formData = new FormData(form);
                const controllerUrl = '../controllers/procesar_caracterizacion.php';

                fetch(controllerUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        mostrarError(data.message || '<?php echo $t['error_conexion']; ?>');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError('<?php echo $t['error_conexion']; ?>: ' + error.message);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });

                return false;
            });
        }

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['modalCaracterizar', 'modalAyuda'].forEach(id => {
                    const modal = document.getElementById(id);
                    if (modal && !modal.classList.contains('hidden')) {
                        closeModal(id);
                    }
                });
            }
        });

        // Cerrar modal al hacer clic fuera
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });
    });
    </script>
</body>
</html>