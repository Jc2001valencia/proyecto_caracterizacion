<?php
// ========================================
// VIEWS/LIDER_HOME.PHP - Dashboard para Líderes
// Sistema de Caracterización de Proyectos usando Framework Cynefin
// ========================================

// 1. SESIÓN Y AUTENTICACIÓN
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirigir si no hay sesión activa
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

// 2. CONFIGURACIÓN DE IDIOMA
if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'es'; // Idioma por defecto: Español
}

// Cambiar idioma si se solicita
if (isset($_GET['idioma']) && in_array($_GET['idioma'], ['es', 'en'])) {
    $_SESSION['idioma'] = $_GET['idioma'];
}

$idioma_actual = $_SESSION['idioma'];

// 3. TEXTOS MULTIDIOMA
$textos = [
    'es' => [
        // Títulos y encabezados
        'titulo_dashboard' => 'Dashboard Líder - Sistema de Caracterización',
        'panel_lider' => 'Panel del Líder',
        'caracterizacion_proyectos' => 'Caracterización de Proyectos',
        'mis_proyectos' => 'Mis Proyectos',
        'caracterizar_proyectos' => 'Caracteriza tus proyectos según el framework Cynefin',
        
        // Estados y mensajes
        'proyectos_asignados' => 'proyectos asignados',
        'caracterizacion_completada' => 'Caracterización completada',
        'completa' => 'Completa',
        'estado_caracterizacion' => 'Estado de caracterización',
        'pendiente' => 'Pendiente',
        'sin_caracterizar' => 'Sin caracterizar',
        'dominio_cynefin' => 'Dominio Cynefin',
        'triple_restriccion' => 'Triple Restricción',
        
        // Botones y acciones
        'ver_resultados' => 'Ver Resultados',
        'comenzar_caracterizacion' => 'Comenzar Caracterización',
        'ver_estrategias' => 'Ver Estrategias',
        'editar' => 'Editar',
        'recargar' => 'Recargar',
        'cerrar_sesion' => 'Cerrar Sesión',
        'idioma' => 'Idioma',
        'espanol' => 'Español',
        'ingles' => 'Inglés',
        'ayuda_framework' => 'Ayuda - Framework Cynefin',
        
        // Modal de ayuda
        'que_es_caracterizacion' => '¿Qué es la caracterización de proyectos?',
        'descripcion_caracterizacion' => 'Es un proceso para determinar la mejor estrategia de gestión según el framework Cynefin, considerando la triple restricción (tiempo, alcance, costo) y los factores de complejidad.',
        'pasos_proceso' => 'Pasos del proceso',
        'identificar_factores' => 'Identifica qué factores son fijos en el proyecto.',
        'factores_complejidad' => 'Factores de Complejidad',
        'seleccionar_factores' => 'Selecciona los factores que aumentan la complejidad.',
        'analisis_cynefin' => 'Análisis Cynefin',
        'determinar_dominio' => 'El sistema determina el dominio y estrategias recomendadas.',
        
        // Modal de caracterización
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
        
        // Nuevos textos
        'dominio_cliente' => 'Dominio del Cliente',
        'seleccionar_dominio' => 'Seleccionar dominio',
        'perfil_equipo' => 'Perfil del equipo',
        'seleccionar_perfil' => 'Seleccionar perfil',
        'informacion_proyecto' => 'Información del Proyecto',
        'instrucciones_sistema' => 'Instrucciones del Sistema',
        'mi_organizacion' => 'Mi Organización',
        'informacion_personal' => 'Información Personal',
        'estadisticas' => 'Estadísticas',
        'proyectos_recientes' => 'Proyectos Recientes',
        'proyectos_totales' => 'Proyectos Totales',
        'caracterizados' => 'Caracterizados',
        'horas_promedio' => 'Horas Promedio',
        'informacion_lider' => 'Información del Líder',
        'id_lider' => 'ID de Líder',
        'rol' => 'Rol',
        'lider_proyecto' => 'Líder de Proyecto',
        'ver_todos_proyectos' => 'Ver todos los proyectos',
        
        // Mensajes de error y validación
        'no_hay_factores' => 'No hay factores de complejidad configurados',
        'error_conexion' => 'Error de conexión con el servidor',
        'error_seleccion_restriccion' => 'Debe seleccionar al menos una restricción fija',
        'error_seleccion_complejidad' => 'Debe seleccionar al menos un factor de complejidad',
        'no_definido' => 'no definido',
        'dominio_no_disponible' => 'Dominios no disponibles',
        'perfiles_no_disponibles' => 'Perfiles no disponibles',
        'pais_no_disponible' => 'Países no disponibles',
        
        // Mensajes informativos
        'necesita_caracterizacion' => 'Este proyecto necesita ser caracterizado para determinar la estrategia de gestión óptima.',
        'no_hay_proyectos' => 'No hay proyectos asignados',
        'texto_sin_proyectos' => 'Actualmente no tienes proyectos asignados. Cuando un administrador te asigne proyectos, aparecerán aquí.',
        'horas_estimadas' => 'horas estimadas'
    ],
    'en' => [
        // Titles and headers
        'titulo_dashboard' => 'Leader Dashboard - Characterization System',
        'panel_lider' => 'Leader Panel',
        'caracterizacion_proyectos' => 'Project Characterization',
        'mis_proyectos' => 'My Projects',
        'caracterizar_proyectos' => 'Characterize your projects according to the Cynefin framework',
        
        // Status and messages
        'proyectos_asignados' => 'assigned projects',
        'caracterizacion_completada' => 'Characterization completed',
        'completa' => 'Complete',
        'estado_caracterizacion' => 'Characterization status',
        'pendiente' => 'Pending',
        'sin_caracterizar' => 'Not characterized',
        'dominio_cynefin' => 'Cynefin Domain',
        'triple_restriccion' => 'Triple Constraint',
        
        // Buttons and actions
        'ver_resultados' => 'View Results',
        'comenzar_caracterizacion' => 'Start Characterization',
        'ver_estrategias' => 'View Strategies',
        'editar' => 'Edit',
        'recargar' => 'Reload',
        'cerrar_sesion' => 'Logout',
        'idioma' => 'Language',
        'espanol' => 'Spanish',
        'ingles' => 'English',
        'ayuda_framework' => 'Help - Cynefin Framework',
        
        // Help modal
        'que_es_caracterizacion' => 'What is project characterization?',
        'descripcion_caracterizacion' => 'It is a process to determine the best management strategy according to the Cynefin framework, considering the triple constraint (time, scope, cost) and complexity factors.',
        'pasos_proceso' => 'Process steps',
        'identificar_factores' => 'Identify which factors are fixed in the project.',
        'factores_complejidad' => 'Complexity Factors',
        'seleccionar_factores' => 'Select factors that increase complexity.',
        'analisis_cynefin' => 'Cynefin Analysis',
        'determinar_dominio' => 'The system determines the domain and recommended strategies.',
        
        // Characterization modal
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
        
        // New texts
        'dominio_cliente' => 'Client Domain',
        'seleccionar_dominio' => 'Select domain',
        'perfil_equipo' => 'Team Profile',
        'seleccionar_perfil' => 'Select profile',
        'informacion_proyecto' => 'Project Information',
        'instrucciones_sistema' => 'System Instructions',
        'mi_organizacion' => 'My Organization',
        'informacion_personal' => 'Personal Information',
        'estadisticas' => 'Statistics',
        'proyectos_recientes' => 'Recent Projects',
        'proyectos_totales' => 'Total Projects',
        'caracterizados' => 'Characterized',
        'horas_promedio' => 'Average Hours',
        'informacion_lider' => 'Leader Information',
        'id_lider' => 'Leader ID',
        'rol' => 'Role',
        'lider_proyecto' => 'Project Leader',
        'ver_todos_proyectos' => 'View all projects',
        
        // Error and validation messages
        'no_hay_factores' => 'No complexity factors configured',
        'error_conexion' => 'Connection error with server',
        'error_seleccion_restriccion' => 'You must select at least one fixed restriction',
        'error_seleccion_complejidad' => 'You must select at least one complexity factor',
        'no_definido' => 'not defined',
        'dominio_no_disponible' => 'Domains not available',
        'perfiles_no_disponibles' => 'Profiles not available',
        'pais_no_disponible' => 'Countries not available',
        
        // Informative messages
        'necesita_caracterizacion' => 'This project needs to be characterized to determine the optimal management strategy.',
        'no_hay_proyectos' => 'No assigned projects',
        'texto_sin_proyectos' => 'You currently have no assigned projects. When an administrator assigns you projects, they will appear here.',
        'horas_estimadas' => 'estimated hours'
    ]
];

// Asignar textos según idioma
$t = $textos[$idioma_actual];

// 4. CONEXIÓN A BASE DE DATOS
require_once __DIR__ . '/../config/db.php';
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!($db instanceof PDO)) {
        throw new Exception("ERROR CRÍTICO: No se pudo conectar a la base de datos");
    }
    
} catch (Exception $e) {
    error_log("Error de conexión a BD: " . $e->getMessage());
    die("Error de conexión a la base de datos. Contacte al administrador.");
}

// 5. CARGAR DATOS DESDE LA BASE DE DATOS
try {
    // Cargar dominios desde la base de datos
    $sql_dominios = "SELECT id, nombre FROM dominios ORDER BY nombre";
    $stmt = $db->query($sql_dominios);
    $dominios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cargar países desde la base de datos
    $sql_paises = "SELECT id, nombre FROM paises ORDER BY nombre";
    $stmt = $db->query($sql_paises);
    $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cargar perfiles desde la base de datos
    $sql_perfiles = "SELECT id, nombre, descripcion FROM perfiles ORDER BY nombre";
    $stmt = $db->query($sql_perfiles);
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cargar factores de complejidad desde la base de datos
    try {
        $stmt = $db->query("SELECT id, nombre, descripcion FROM caracteristicas WHERE activo = 1 ORDER BY nombre");
        $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Si no existe la tabla o hay error, usar factores por defecto
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
    
    // Cargar proyectos del líder con información de caracterización
    $lider_id = $_SESSION['usuario']['id'];
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
            d.nombre as dominio_nombre,
            pa.nombre as pais_nombre,
            c.id as caracterizacion_id,
            c.dominio_cynefin,
            c.tipo_restriccion,
            c.created_at as caracterizado_en
        FROM proyectos p
        LEFT JOIN dominios d ON p.dominio_id = d.id
        LEFT JOIN paises pa ON p.pais_id = pa.id
        LEFT JOIN caracterizaciones c ON p.id = c.proyecto_id
        WHERE p.lider_proyecto_id = :lider_id 
        ORDER BY p.created_at DESC
    ";
    
    $stmt = $db->prepare($sql_proyectos);
    $stmt->execute(['lider_id' => $lider_id]);
    $mis_proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Error en lider_home al cargar datos: " . $e->getMessage());
    $error_message = "Error al cargar los datos. Contacte al administrador.";
}

// 6. INICIALIZAR VARIABLES RESTANTES
$error_message = null;
$success_message = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

$lider_id = $_SESSION['usuario']['id'];
$nombre_usuario = $_SESSION['usuario']['nombre'] ?? 'Líder';
$apellido_usuario = $_SESSION['usuario']['apellido'] ?? '';
$email_usuario = $_SESSION['usuario']['email'] ?? '';
$inicial_usuario = strtoupper(substr($nombre_usuario, 0, 1));
$nombre_completo = trim($nombre_usuario . ' ' . $apellido_usuario);
$cantidad_proyectos = count($mis_proyectos);

// URL base para JavaScript
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$base_path = dirname($_SERVER['PHP_SELF']);
$base_url = rtrim($protocol . "://" . $host . $base_path, '/');

// 7. CONFIGURACIONES PARA LA VISTA
$tipos_restriccion = [
    1 => ['nombre' => 'Solo tiempo fijo', 'color' => 'bg-blue-100 text-blue-800'],
    2 => ['nombre' => 'Solo alcance fijo', 'color' => 'bg-purple-100 text-purple-800'],
    3 => ['nombre' => 'Solo costo fijo', 'color' => 'bg-green-100 text-green-800'],
    4 => ['nombre' => 'Dos factores fijos', 'color' => 'bg-yellow-100 text-yellow-800'],
    5 => ['nombre' => 'Tres factores fijos', 'color' => 'bg-red-100 text-red-800']
];

$dominios_cynefin = [
    'Claro' => ['color' => 'bg-green-100 text-green-800', 'icon' => 'fa-check-circle'],
    'Complicado' => ['color' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-search'],
    'Complejo' => ['color' => 'bg-purple-100 text-purple-800', 'icon' => 'fa-cogs'],
    'Caótico' => ['color' => 'bg-red-100 text-red-800', 'icon' => 'fa-fire'],
    'No determinado' => ['color' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question']
];

$estado_colores = [
    'activo' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-play-circle'],
    'pendiente' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock'],
    'finalizado' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-check-circle'],
    'pausado' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-pause-circle'],
    'cancelado' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle']
];
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma_actual; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['titulo_dashboard']; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
    * {
        font-family: 'Inter', sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    .sidebar {
        background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .card-project {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    .card-project:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
        border-color: #3b82f6;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 95vw;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.9) translateY(30px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal-overlay.show .modal-content {
        transform: scale(1) translateY(0);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        transition: all 0.3s;
        font-weight: 500;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(37, 99, 235, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        transition: all 0.3s;
        font-weight: 500;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(5, 150, 105, 0.3);
    }

    .form-step {
        display: none;
        animation: fadeIn 0.4s ease;
    }

    .form-step.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
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

    .equipo-item {
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .equipo-item:hover {
        background-color: #f8fafc;
        transform: translateX(2px);
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 180px;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .dropdown:hover .dropdown-content {
        display: block;
        animation: fadeIn 0.2s ease;
    }

    .check-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .check-card:hover {
        transform: translateY(-2px);
    }

    .check-card input:checked+div {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }

    /* Scrollbar personalizado */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modal-content {
            max-width: 98vw;
            max-height: 95vh;
            margin: 10px;
        }

        .card-project {
            margin-bottom: 1rem;
        }
    }
    </style>
</head>

<body class="min-h-screen">
    <!-- Sidebar para Desktop -->
    <aside class="hidden lg:flex lg:w-64 sidebar flex-col fixed h-screen z-30 text-white">
        <!-- Logo y título -->
        <div class="p-6 border-b border-blue-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-tie text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold"><?php echo $t['panel_lider']; ?></h1>
                    <p class="text-xs text-blue-200"><?php echo $t['caracterizacion_proyectos']; ?></p>
                </div>
            </div>
        </div>

        <!-- Información del usuario -->
        <div class="p-4 border-b border-blue-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-400 rounded-full flex items-center justify-center font-semibold shadow-md">
                    <?php echo $inicial_usuario; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm truncate"><?php echo htmlspecialchars($nombre_completo); ?></p>
                    <p class="text-xs text-blue-200 truncate"><?php echo htmlspecialchars($email_usuario); ?></p>
                </div>
            </div>
        </div>

        <!-- Navegación -->
        <nav class="flex-1 p-4">
            <div class="space-y-1">
                <!-- Botón de Información del Líder/Organización -->
                <button onclick="openModal('modalOrganizacion')"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-700 text-blue-200 transition">
                    <i class="fas fa-user-cog"></i>
                    <span class="font-medium"><?php echo $t['mi_organizacion']; ?></span>
                </button>

                <a href="index.php?action=lider_home&idioma=<?php echo $idioma_actual; ?>"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-700 text-white shadow-sm">
                    <i class="fas fa-project-diagram"></i>
                    <span class="font-medium"><?php echo $t['mis_proyectos']; ?></span>
                    <?php if ($cantidad_proyectos > 0): ?>
                    <span class="ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-1 font-bold">
                        <?php echo $cantidad_proyectos; ?>
                    </span>
                    <?php endif; ?>
                </a>
            </div>
        </nav>

        <!-- Configuraciones y acciones -->
        <div class="p-4 border-t border-blue-700 space-y-2">
            <!-- Botón de Instrucciones -->
            <button onclick="openModal('modalInstrucciones')"
                class="w-full flex items-center gap-2 px-4 py-2.5 text-blue-200 hover:bg-blue-700 rounded-lg transition">
                <i class="fas fa-book-open"></i>
                <span class="font-medium"><?php echo $t['instrucciones_sistema']; ?></span>
            </button>

            <!-- Selector de idioma -->
            <div class="dropdown relative">
                <button
                    class="w-full flex items-center gap-2 px-4 py-2.5 text-blue-200 hover:bg-blue-700 rounded-lg transition">
                    <i class="fas fa-globe"></i>
                    <span class="font-medium"><?php echo $t['idioma']; ?></span>
                    <i class="fas fa-chevron-down ml-auto text-xs"></i>
                </button>
                <div class="dropdown-content left-0 bottom-full mb-2">
                    <a href="?action=lider_home&idioma=es"
                        class="block px-4 py-2 hover:bg-blue-50 <?php echo $idioma_actual === 'es' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700'; ?>">
                        <i class="fas fa-flag mr-2"></i><?php echo $t['espanol']; ?>
                    </a>
                    <a href="?action=lider_home&idioma=en"
                        class="block px-4 py-2 hover:bg-blue-50 <?php echo $idioma_actual === 'en' ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-700'; ?>">
                        <i class="fas fa-flag-usa mr-2"></i><?php echo $t['ingles']; ?>
                    </a>
                </div>
            </div>

            <!-- Ayuda -->
            <button onclick="openModal('modalAyuda')"
                class="w-full flex items-center gap-2 px-4 py-2.5 text-blue-200 hover:bg-blue-700 rounded-lg transition">
                <i class="fas fa-question-circle"></i>
                <span class="font-medium"><?php echo $t['ayuda_framework']; ?></span>
            </button>

            <!-- Cerrar sesión -->
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
                    <!-- Botón menú móvil -->
                    <div class="lg:hidden">
                        <button onclick="toggleMobileSidebar()" class="p-2 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-bars text-xl text-gray-700"></i>
                        </button>
                    </div>

                    <!-- Título y descripción -->
                    <div class="flex-1 ml-3 lg:ml-0">
                        <h1 class="text-2xl font-bold text-gray-800"><?php echo $t['mis_proyectos']; ?></h1>
                        <p class="text-gray-600 text-sm"><?php echo $t['caracterizar_proyectos']; ?></p>
                    </div>

                    <!-- Contador de proyectos -->
                    <div class="hidden md:flex items-center gap-3">
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                            <span class="font-medium text-blue-700">
                                <?php echo $cantidad_proyectos; ?> <?php echo $t['proyectos_asignados']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido de la página -->
        <div class="p-4 md:p-6">
            <!-- Mensajes del sistema -->
            <?php if ($error_message): ?>
            <div class="mb-6 animate-fade-in">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-red-800">Error</p>
                            <p class="text-red-600 text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
            <div class="mb-6 animate-fade-in">
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium text-green-800">Éxito</p>
                            <p class="text-green-600 text-sm"><?php echo htmlspecialchars($success_message); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Grid de Proyectos -->
            <?php if ($cantidad_proyectos > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($mis_proyectos as $proyecto): 
                    // Preparar datos del proyecto
                    $estado = $proyecto['estado'] ?? 'pendiente';
                    $color_estado = $estado_colores[$estado] ?? $estado_colores['pendiente'];
                    
                    // Verificar si está caracterizado
                    $caracterizado = !empty($proyecto['caracterizacion_id']);
                    $dominio_cynefin = $proyecto['dominio_cynefin'] ?? 'No determinado';
                    $tipo_restriccion_num = $proyecto['tipo_restriccion'] ?? 0;
                    
                    // Determinar texto de restricción
                    $tipo_restriccion = $t['sin_caracterizar'];
                    if ($caracterizado && isset($tipos_restriccion[$tipo_restriccion_num])) {
                        $tipo_restriccion = $tipos_restriccion[$tipo_restriccion_num]['nombre'];
                    }
                    
                    // Información del proyecto
                    $nombre = htmlspecialchars($proyecto['nombre'] ?? 'Sin nombre');
                    $descripcion = htmlspecialchars($proyecto['descripcion'] ?? 'Sin descripción');
                    $horas = $proyecto['horas'] ?? 0;
                    $id_proyecto = $proyecto['id'];
                    $dominio_nombre = htmlspecialchars($proyecto['dominio_nombre'] ?? 'No asignado');
                    $pais_nombre = htmlspecialchars($proyecto['pais_nombre'] ?? 'No asignado');
                ?>
                <div class="card-project animate-fade-in">
                    <!-- Encabezado de la tarjeta -->
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 text-lg truncate"><?php echo $nombre; ?></h3>
                                <p class="text-xs text-gray-500 mt-1">ID: #<?php echo $id_proyecto; ?></p>
                            </div>
                            <span
                                class="badge-status <?php echo $color_estado['bg']; ?> <?php echo $color_estado['text']; ?>">
                                <i class="fas <?php echo $color_estado['icon']; ?> mr-1"></i>
                                <?php echo ucfirst($estado); ?>
                            </span>
                        </div>

                        <p class="text-gray-600 text-sm line-clamp-2 mb-3"><?php echo $descripcion; ?></p>

                        <div class="flex flex-wrap gap-2 text-xs text-gray-500 mb-2">
                            <?php if ($dominio_nombre !== 'No asignado'): ?>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                <i class="fas fa-building mr-1"></i><?php echo $dominio_nombre; ?>
                            </span>
                            <?php endif; ?>

                            <?php if ($pais_nombre !== 'No asignado'): ?>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                <i class="fas fa-globe mr-1"></i><?php echo $pais_nombre; ?>
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock text-gray-400 mr-1"></i>
                            <span><?php echo $horas; ?> <?php echo $t['horas_estimadas']; ?></span>
                        </div>
                    </div>

                    <!-- Estado de Caracterización -->
                    <div class="p-5">
                        <?php if ($caracterizado): ?>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-sm font-medium text-gray-700"><?php echo $t['caracterizacion_completada']; ?></span>
                                <span class="text-xs text-green-600 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i><?php echo $t['completa']; ?>
                                </span>
                            </div>

                            <!-- Dominio Cynefin -->
                            <div class="mb-3">
                                <span class="text-xs text-gray-500"><?php echo $t['dominio_cynefin']; ?>:</span>
                                <?php $dominio_config = $dominios_cynefin[$dominio_cynefin] ?? $dominios_cynefin['No determinado']; ?>
                                <span class="ml-2 badge-status <?php echo $dominio_config['color']; ?>">
                                    <i class="fas <?php echo $dominio_config['icon']; ?> mr-1"></i>
                                    <?php echo $dominio_cynefin; ?>
                                </span>
                            </div>

                            <!-- Triple Restricción -->
                            <div class="mb-3">
                                <span class="text-xs text-gray-500"><?php echo $t['triple_restriccion']; ?>:</span>
                                <span class="ml-2 text-sm font-medium"><?php echo $tipo_restriccion; ?></span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="verResultados(<?php echo $id_proyecto; ?>)"
                                class="flex-1 btn-primary py-2.5 rounded-lg font-medium text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-chart-bar"></i>
                                <?php echo $t['ver_estrategias']; ?>
                            </button>
                            <button onclick="editarCaracterizacion(<?php echo $id_proyecto; ?>)"
                                class="px-4 py-2.5 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 font-medium text-sm flex items-center justify-center">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-sm font-medium text-gray-700"><?php echo $t['estado_caracterizacion']; ?></span>
                                <span class="text-xs text-yellow-600 font-medium">
                                    <i class="fas fa-clock mr-1"></i><?php echo $t['pendiente']; ?>
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 mb-4">
                                <?php echo $t['necesita_caracterizacion']; ?>
                            </p>
                        </div>

                        <button onclick="caracterizarProyecto(<?php echo $id_proyecto; ?>)"
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
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 text-center max-w-2xl mx-auto mt-8 animate-fade-in">
                <div
                    class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
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
    <div id="modalCaracterizar" class="modal-overlay hidden">
        <div class="modal-content max-w-4xl">
            <!-- Encabezado del modal -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-clipboard-check text-blue-600"></i>
                            <span id="modalTitulo"><?php echo $t['caracterizar_proyecto']; ?></span>
                        </h2>
                        <p class="text-sm text-gray-500 mt-1" id="modalSubtitulo">
                            <?php echo $t['completar_informacion']; ?>
                        </p>
                    </div>
                    <button onclick="closeModal('modalCaracterizar')"
                        class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-xl text-gray-500"></i>
                    </button>
                </div>
            </div>

            <!-- Formulario de caracterización -->
            <form id="formCaracterizacion" method="POST" class="p-6">
                <input type="hidden" name="proyecto_id" id="proyecto_id">
                <input type="hidden" name="idioma" value="<?php echo $idioma_actual; ?>">

                <!-- Paso 1: Información del proyecto -->
                <div id="paso1" class="form-step active">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            <?php echo $t['informacion_proyecto']; ?>
                        </h3>

                        <!-- Dominio del Cliente (desde BD) -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building text-blue-600 mr-2"></i>
                                <?php echo $t['dominio_cliente']; ?>
                            </label>
                            <?php if (!empty($dominios)): ?>
                            <select name="dominio_id"
                                class="w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required>
                                <option value=""><?php echo $t['seleccionar_dominio']; ?></option>
                                <?php foreach ($dominios as $dominio): ?>
                                <option value="<?php echo $dominio['id']; ?>">
                                    <?php echo htmlspecialchars($dominio['nombre']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php else: ?>
                            <div
                                class="text-center py-3 text-yellow-600 bg-yellow-50 rounded-lg border border-yellow-200">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <?php echo $t['dominio_no_disponible']; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- País del Cliente (desde BD) -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-globe-americas text-blue-600 mr-2"></i>
                                <?php echo $t['pais_cliente']; ?>
                            </label>
                            <?php if (!empty($paises)): ?>
                            <select name="pais_id"
                                class="w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required>
                                <option value=""><?php echo $t['seleccionar_pais']; ?></option>
                                <?php foreach ($paises as $pais): ?>
                                <option value="<?php echo $pais['id']; ?>">
                                    <?php echo htmlspecialchars($pais['nombre']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php else: ?>
                            <div
                                class="text-center py-3 text-yellow-600 bg-yellow-50 rounded-lg border border-yellow-200">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <?php echo $t['pais_no_disponible']; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Equipo de Desarrollo -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-users text-green-600"></i>
                                <?php echo $t['equipo_desarrollo']; ?>
                            </h4>

                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-sm text-gray-600 mb-3">
                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                    Ingresa los perfiles y la cantidad de cada uno en tu equipo de desarrollo.
                                </p>

                                <div id="equipo-container" class="space-y-3">
                                    <!-- Primer miembro del equipo -->
                                    <div
                                        class="equipo-item grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-gray-200 rounded-lg bg-white">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                <?php echo $t['perfil_equipo']; ?>
                                            </label>
                                            <?php if (!empty($perfiles)): ?>
                                            <select name="equipo_perfil[]"
                                                class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                required>
                                                <option value=""><?php echo $t['seleccionar_perfil']; ?></option>
                                                <?php foreach ($perfiles as $perfil): ?>
                                                <option value="<?php echo $perfil['id']; ?>">
                                                    <?php echo htmlspecialchars($perfil['nombre']); ?>
                                                    <?php if (!empty($perfil['descripcion'])): ?>
                                                    (<?php echo htmlspecialchars($perfil['descripcion']); ?>)
                                                    <?php endif; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php else: ?>
                                            <input type="text" name="equipo_perfil_custom[]"
                                                class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Ej: Desarrollador Senior" required>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                <?php echo $t['cantidad']; ?>
                                            </label>
                                            <input type="number" name="equipo_cantidad[]" min="1"
                                                class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Ej: 2" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" onclick="agregarMiembroEquipo()"
                                    class="mt-4 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 font-medium flex items-center gap-2 transition">
                                    <i class="fas fa-plus"></i>
                                    <?php echo $t['agregar_miembro']; ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Botón siguiente -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" onclick="siguientePaso()"
                            class="ml-auto px-6 py-3 btn-primary rounded-lg font-medium flex items-center gap-2">
                            <?php echo $t['siguiente']; ?>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Paso 2: Triple Restricción -->
                <div id="paso2" class="form-step">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-balance-scale text-blue-600"></i>
                            <?php echo $t['paso_triple_restriccion']; ?>
                        </h3>

                        <p class="text-gray-600 mb-6">
                            <?php echo $t['seleccionar_factores_fijos']; ?>
                        </p>

                        <!-- Opciones de triple restricción -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- Tiempo -->
                            <label class="check-card relative cursor-pointer">
                                <input type="checkbox" name="restricciones[]" value="Tiempo" class="sr-only peer">
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50">
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

                            <!-- Alcance -->
                            <label class="check-card relative cursor-pointer">
                                <input type="checkbox" name="restricciones[]" value="Alcance" class="sr-only peer">
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 peer-checked:border-purple-500 peer-checked:bg-purple-50">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center peer-checked:bg-purple-100">
                                            <i class="fas fa-list-check text-gray-600 peer-checked:text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo $t['alcance']; ?></p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo $t['funcionalidades_completas']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Costo -->
                            <label class="check-card relative cursor-pointer">
                                <input type="checkbox" name="restricciones[]" value="Costo" id="costoCheckbox"
                                    class="sr-only peer" onchange="toggleCostoOpciones()">
                                <div
                                    class="p-4 border-2 border-gray-200 rounded-xl transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center peer-checked:bg-green-100">
                                            <i class="fas fa-money-bill text-gray-600 peer-checked:text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo $t['costo']; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $t['presupuesto_cerrado']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Opciones de tipo de costo -->
                        <div id="costoOpciones" class="mb-6 hidden">
                            <h4 class="text-md font-semibold text-gray-700 mb-3">Tipo de contrato de costo:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label
                                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="tipo_costo" value="Llave en mano"
                                        class="mr-3 h-5 w-5 text-blue-600">
                                    <div>
                                        <p class="font-medium text-gray-800">Llave en mano</p>
                                        <p class="text-xs text-gray-500">Precio fijo por proyecto completo</p>
                                    </div>
                                </label>
                                <label
                                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="tipo_costo" value="Time & Material"
                                        class="mr-3 h-5 w-5 text-blue-600">
                                    <div>
                                        <p class="font-medium text-gray-800">Time & Material</p>
                                        <p class="text-xs text-gray-500">Precio basado en tiempo y materiales</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Instrucciones -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400 mt-1"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong><?php echo $t['nota_seleccion']; ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de navegación -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" onclick="anteriorPaso()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium flex items-center gap-2 transition">
                            <i class="fas fa-arrow-left"></i>
                            <?php echo $t['anterior']; ?>
                        </button>
                        <button type="button" onclick="siguientePaso()"
                            class="px-6 py-3 btn-primary rounded-lg font-medium flex items-center gap-2">
                            <?php echo $t['siguiente']; ?>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Paso 3: Factores de Complejidad -->
                <div id="paso3" class="form-step">
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-layer-group text-purple-600"></i>
                            <?php echo $t['paso_factores_complejidad']; ?>
                        </h3>

                        <p class="text-gray-600 mb-6">
                            <?php echo $t['seleccionar_factores_complejidad']; ?>
                        </p>

                        <!-- Factores de complejidad desde BD -->
                        <?php if (!empty($caracteristicas)): ?>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-80 overflow-y-auto p-2 mb-6 border border-gray-200 rounded-lg">
                            <?php foreach ($caracteristicas as $caracteristica): ?>
                            <label
                                class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                <input type="checkbox" name="complejidad[]" value="<?php echo $caracteristica['id']; ?>"
                                    class="mt-1 mr-3 h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">
                                        <?php echo htmlspecialchars($caracteristica['nombre']); ?>
                                    </p>
                                    <?php if (!empty($caracteristica['descripcion'])): ?>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <?php echo htmlspecialchars($caracteristica['descripcion']); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Contador de selección -->
                        <div id="contadorComplejidad"
                            class="mt-4 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg mb-6 border border-gray-200">
                            <i class="fas fa-check-circle mr-2 text-green-500"></i>
                            <span id="seleccionados">0</span> <?php echo $t['factores_seleccionados']; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-8 text-gray-500 mb-6 border border-gray-200 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-3xl mb-3 text-yellow-500"></i>
                            <p><?php echo $t['no_hay_factores']; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botones de navegación -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <button type="button" onclick="anteriorPaso()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium flex items-center gap-2 transition">
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

                <!-- Mensajes de error -->
                <div id="mensajeError" class="hidden mt-4 p-3 bg-red-50 text-red-700 rounded-lg border border-red-200">
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Ayuda -->
    <div id="modalAyuda" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-question-circle text-blue-600"></i>
                        <?php echo $t['ayuda_framework']; ?>
                    </h2>
                    <button onclick="closeModal('modalAyuda')" class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-xl text-gray-500"></i>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Qué es la caracterización -->
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <h3 class="font-semibold text-blue-800 mb-2"><?php echo $t['que_es_caracterizacion']; ?></h3>
                        <p class="text-blue-700">
                            <?php echo $t['descripcion_caracterizacion']; ?>
                        </p>
                    </div>

                    <!-- Pasos del proceso -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800"><?php echo $t['pasos_proceso']; ?>:</h4>

                        <!-- Paso 1 -->
                        <div class="flex items-start gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold">1</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800"><?php echo $t['triple_restriccion']; ?></h5>
                                <p class="text-sm text-gray-600"><?php echo $t['identificar_factores']; ?></p>
                            </div>
                        </div>

                        <!-- Paso 2 -->
                        <div class="flex items-start gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <div
                                class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-600 font-bold">2</span>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800"><?php echo $t['factores_complejidad']; ?></h5>
                                <p class="text-sm text-gray-600"><?php echo $t['seleccionar_factores']; ?></p>
                            </div>
                        </div>

                        <!-- Paso 3 -->
                        <div class="flex items-start gap-3 p-3 bg-white border border-gray-200 rounded-lg">
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

    <!-- Modal: Instrucciones -->
    <div id="modalInstrucciones" class="modal-overlay hidden">
        <div class="modal-content max-w-3xl">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-book-open text-blue-600"></i>
                            <?php echo $t['instrucciones_sistema']; ?>
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Guía completa para el uso de la herramienta</p>
                    </div>
                    <button onclick="closeModal('modalInstrucciones')"
                        class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-xl text-gray-500"></i>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="space-y-6 overflow-y-auto max-h-[60vh] pr-2">
                    <section class="space-y-4">
                        <h3 class="text-lg font-semibold text-blue-600 mb-2">1️⃣ Caracterización del proyecto</h3>
                        <p class="text-gray-700">
                            Al hacer clic en <strong>"Comenzar Caracterización"</strong>, se solicita completar
                            información descriptiva del proyecto e información para determinar su complejidad. Debe
                            indicar los factores fijos de la triple restricción y los factores de complejidad añadida
                            del proyecto.
                        </p>

                        <h3 class="text-lg font-semibold text-blue-600 mb-2">2️⃣ Factores de la triple restricción</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700">
                            <li><strong>Tiempo fijo:</strong> El proyecto tiene una fecha límite inamovible o
                                sancionable.</li>
                            <li><strong>Alcance fijo:</strong> Debe cumplirse con la entrega completa, sin reducción de
                                funcionalidades.</li>
                            <li><strong>Costo fijo:</strong> Existe un presupuesto cerrado o un equipo definido sin
                                posibilidad de ampliación.</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-blue-600 mb-2">3️⃣ Factores de complejidad añadida</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700">
                            <li><strong>Equipo de desarrollo:</strong> Conocimientos técnicos avanzados o perfiles
                                especializados.</li>
                            <li><strong>Restricción de tiempo:</strong> Plazos muy ajustados para el alcance del
                                proyecto.</li>
                            <li><strong>Tamaño:</strong> Gran cantidad de personas o requisitos.</li>
                            <li><strong>Desarrollo global:</strong> Diferencias geográficas, horarias o culturales en el
                                equipo.</li>
                            <li><strong>Criticidad del problema:</strong> Alto impacto económico, ambiental o en la
                                seguridad.</li>
                            <li><strong>Poca experiencia:</strong> El equipo tiene bajo dominio del problema o
                                tecnologías.</li>
                            <li><strong>Requisitos cambiantes:</strong> El cliente modifica los requisitos con
                                frecuencia.</li>
                            <li><strong>Otras restricciones:</strong> Legales, del negocio o de otra índole.</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-blue-600 mb-2">4️⃣ Resultados del análisis</h3>
                        <p class="text-gray-700 mb-2">
                            Luego de hacer clic en <strong>"Completar Caracterización"</strong>, la herramienta
                            caracterizará el proyecto mostrando:
                        </p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700">
                            <li><strong>Tipo de contrato:</strong> El más adecuado según la complejidad.</li>
                            <li><strong>Tipo de acción:</strong> Cómo debe actuar el líder del proyecto.</li>
                            <li><strong>Prácticas recomendadas:</strong> Mejores prácticas o patrones sugeridos.</li>
                            <li><strong>Enfoque de gestión:</strong> Predictivo, empírico o por flujo tenso.</li>
                            <li><strong>Modelo de ciclo de vida:</strong> Más adecuado para la situación del proyecto.
                            </li>
                            <li><strong>Acuerdos de trabajo:</strong> Nivel de acuerdos necesarios con el cliente.</li>
                            <li><strong>Planificación:</strong> Si debe ser completa o ajustarse progresivamente.</li>
                            <li><strong>Dinámicas para explotar y prevenir:</strong> Estrategias para reducir o evitar
                                la complejidad.</li>
                            <li><strong>Enfoque ágil:</strong> Método ágil más conveniente.</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-blue-600 mb-2">5️⃣ Estrategias adicionales</h3>
                        <p class="text-gray-700">
                            En la sección <strong>"Ver Estrategias"</strong>, el líder puede indicar estrategias,
                            técnicas y herramientas
                            adicionales o distintas a las sugeridas que hayan tenido resultados satisfactorios.
                        </p>

                        <h3 class="text-lg font-semibold text-blue-600 mb-2">6️⃣ Encuesta de satisfacción</h3>
                        <p class="text-gray-700">
                            Finalmente, en la sección <strong>"Satisfacción"</strong> se presenta una encuesta de
                            satisfacción basada
                            en la técnica <strong>SUS</strong> (System Usability Scale), con el fin de evaluar la
                            utilidad y
                            precisión de esta guía.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Información de la Organización -->
    <div id="modalOrganizacion" class="modal-overlay hidden">
        <div class="modal-content max-w-2xl">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-user-cog text-blue-600"></i>
                            <?php echo $t['mi_organizacion']; ?>
                        </h2>
                        <p class="text-sm text-gray-500 mt-1"><?php echo $t['informacion_lider']; ?></p>
                    </div>
                    <button onclick="closeModal('modalOrganizacion')"
                        class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-xl text-gray-500"></i>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <?php
                // Cargar información del líder desde la base de datos
                try {
                    $sql_lider = "SELECT * FROM usuarios WHERE id = :lider_id";
                    $stmt = $db->prepare($sql_lider);
                    $stmt->execute(['lider_id' => $lider_id]);
                    $info_lider = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Cargar estadísticas del líder
                    $sql_stats = "
                        SELECT 
                            COUNT(p.id) as total_proyectos,
                            SUM(CASE WHEN c.id IS NOT NULL THEN 1 ELSE 0 END) as proyectos_caracterizados,
                            AVG(p.horas) as promedio_horas
                        FROM proyectos p
                        LEFT JOIN caracterizaciones c ON p.id = c.proyecto_id
                        WHERE p.lider_proyecto_id = :lider_id
                    ";
                    $stmt = $db->prepare($sql_stats);
                    $stmt->execute(['lider_id' => $lider_id]);
                    $stats_lider = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    $info_lider = [];
                    $stats_lider = [];
                }
                ?>

                <div class="space-y-6">
                    <!-- Información personal -->
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <h3 class="font-semibold text-blue-800 mb-3"><?php echo $t['informacion_personal']; ?></h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-blue-600">
                                    <?php echo $t['nombre_completo'] ?? 'Nombre completo'; ?>:</p>
                                <p class="font-medium"><?php echo htmlspecialchars($nombre_completo); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600">Email:</p>
                                <p class="font-medium"><?php echo htmlspecialchars($email_usuario); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600"><?php echo $t['id_lider']; ?>:</p>
                                <p class="font-medium">#<?php echo $lider_id; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600"><?php echo $t['rol']; ?>:</p>
                                <p class="font-medium"><?php echo $t['lider_proyecto']; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="bg-white rounded-xl p-4 border border-gray-200">
                        <h3 class="font-semibold text-gray-800 mb-3"><?php echo $t['estadisticas']; ?></h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="text-center p-3 bg-green-50 rounded-lg border border-green-100">
                                <p class="text-2xl font-bold text-green-600">
                                    <?php echo $stats_lider['total_proyectos'] ?? 0; ?>
                                </p>
                                <p class="text-sm text-green-700"><?php echo $t['proyectos_totales']; ?></p>
                            </div>
                            <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                <p class="text-2xl font-bold text-blue-600">
                                    <?php echo $stats_lider['proyectos_caracterizados'] ?? 0; ?>
                                </p>
                                <p class="text-sm text-blue-700"><?php echo $t['caracterizados']; ?></p>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg border border-purple-100">
                                <p class="text-2xl font-bold text-purple-600">
                                    <?php echo round($stats_lider['promedio_horas'] ?? 0); ?>
                                </p>
                                <p class="text-sm text-purple-700"><?php echo $t['horas_promedio']; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Proyectos recientes -->
                    <div class="bg-white rounded-xl p-4 border border-gray-200">
                        <h3 class="font-semibold text-gray-800 mb-3"><?php echo $t['proyectos_recientes']; ?></h3>
                        <?php if (!empty($mis_proyectos)): ?>
                        <div class="space-y-2">
                            <?php 
                            $proyectos_recientes = array_slice($mis_proyectos, 0, 3);
                            foreach ($proyectos_recientes as $proyecto): 
                            ?>
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                <div class="flex-1">
                                    <p class="font-medium text-sm"><?php echo htmlspecialchars($proyecto['nombre']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <?php echo $proyecto['estado']; ?> •
                                        <?php echo $proyecto['horas']; ?> <?php echo $t['horas_estimadas']; ?>
                                    </p>
                                </div>
                                <span
                                    class="text-xs <?php echo $proyecto['caracterizacion_id'] ? 'text-green-600' : 'text-yellow-600'; ?>">
                                    <i
                                        class="fas fa-<?php echo $proyecto['caracterizacion_id'] ? 'check' : 'clock'; ?>"></i>
                                </span>
                            </div>
                            <?php endforeach; ?>

                            <?php if (count($mis_proyectos) > 3): ?>
                            <div class="text-center pt-2">
                                <button onclick="closeModal('modalOrganizacion')"
                                    class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                    <i class="fas fa-list mr-1"></i><?php echo $t['ver_todos_proyectos']; ?>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-gray-500 text-center py-4"><?php echo $t['no_hay_proyectos']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    // ===========================================
    // VARIABLES GLOBALES
    // ===========================================
    let pasoActual = 1;
    let totalPasos = 3;
    let proyectoSeleccionado = null;
    let contadorEquipo = 1;
    let perfilesDisponibles = <?php echo json_encode($perfiles); ?>;
    const BASE_URL = '<?php echo $base_url; ?>';

    // ===========================================
    // FUNCIONES DE MODALES
    // ===========================================
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

    // ===========================================
    // FUNCIONES DE CARACTERIZACIÓN
    // ===========================================
    function caracterizarProyecto(proyectoId) {
        proyectoSeleccionado = proyectoId;
        document.getElementById('proyecto_id').value = proyectoId;

        // Resetear formulario
        pasoActual = 1;
        mostrarPaso(1);
        document.getElementById('formCaracterizacion').reset();
        document.getElementById('mensajeError').classList.add('hidden');
        document.getElementById('costoOpciones').classList.add('hidden');

        // Reiniciar contador de equipo
        contadorEquipo = 1;
        const equipoContainer = document.getElementById('equipo-container');

        if (perfilesDisponibles && perfilesDisponibles.length > 0) {
            equipoContainer.innerHTML = `
                <div class="equipo-item grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-gray-200 rounded-lg bg-white">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <?php echo $t['perfil_equipo']; ?>
                        </label>
                        <select name="equipo_perfil[]"
                            class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                            <option value=""><?php echo $t['seleccionar_perfil']; ?></option>
                            <?php foreach ($perfiles as $perfil): ?>
                            <option value="<?php echo $perfil['id']; ?>">
                                <?php echo htmlspecialchars($perfil['nombre']); ?>
                                <?php if (!empty($perfil['descripcion'])): ?>
                                (<?php echo htmlspecialchars($perfil['descripcion']); ?>)
                                <?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <?php echo $t['cantidad']; ?>
                        </label>
                        <input type="number" name="equipo_cantidad[]" min="1"
                            class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ej: 2" required>
                    </div>
                </div>
            `;
        } else {
            equipoContainer.innerHTML = `
                <div class="equipo-item grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-gray-200 rounded-lg bg-white">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <?php echo $t['perfil_equipo']; ?>
                        </label>
                        <input type="text" name="equipo_perfil_custom[]"
                            class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ej: Desarrollador Senior" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <?php echo $t['cantidad']; ?>
                        </label>
                        <input type="number" name="equipo_cantidad[]" min="1"
                            class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Ej: 2" required>
                    </div>
                </div>
            `;
        }

        // Reiniciar contador de complejidad
        actualizarContadorComplejidad();

        // Actualizar título del modal
        document.getElementById('modalTitulo').textContent =
        `<?php echo $t['caracterizar_proyecto']; ?> #${proyectoId}`;

        // Abrir modal
        openModal('modalCaracterizar');
    }

    function editarCaracterizacion(proyectoId) {
        // Por ahora, redirigir a caracterización (podría cargar datos existentes)
        caracterizarProyecto(proyectoId);
    }

    function verResultados(proyectoId) {
        window.location.href =
            `../views/resultados_caracterizacion.php?proyecto_id=${proyectoId}&idioma=<?php echo $idioma_actual; ?>`;
    }

    // ===========================================
    // FUNCIONES DE INTERFAZ
    // ===========================================
    function toggleCostoOpciones() {
        const costoCheckbox = document.getElementById('costoCheckbox');
        const costoOpciones = document.getElementById('costoOpciones');

        if (costoCheckbox.checked) {
            costoOpciones.classList.remove('hidden');
        } else {
            costoOpciones.classList.add('hidden');
            // Limpiar selección de tipo de costo
            document.querySelectorAll('input[name="tipo_costo"]').forEach(radio => {
                radio.checked = false;
            });
        }
    }

    function agregarMiembroEquipo() {
        contadorEquipo++;
        const equipoContainer = document.getElementById('equipo-container');
        const nuevoMiembro = document.createElement('div');
        nuevoMiembro.className =
            'equipo-item grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-gray-200 rounded-lg bg-white';

        if (perfilesDisponibles && perfilesDisponibles.length > 0) {
            // Usar select con perfiles de BD
            nuevoMiembro.innerHTML = `
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <?php echo $t['perfil_equipo']; ?>
                    </label>
                    <select name="equipo_perfil[]"
                        class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                        <option value=""><?php echo $t['seleccionar_perfil']; ?></option>
                        <?php foreach ($perfiles as $perfil): ?>
                        <option value="<?php echo $perfil['id']; ?>">
                            <?php echo htmlspecialchars($perfil['nombre']); ?>
                            <?php if (!empty($perfil['descripcion'])): ?>
                            (<?php echo htmlspecialchars($perfil['descripcion']); ?>)
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <?php echo $t['cantidad']; ?>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="equipo_cantidad[]" min="1" 
                            class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="Ej: 3" 
                            required>
                        <button type="button" onclick="eliminarMiembroEquipo(this)" 
                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition" 
                            title="<?php echo $t['eliminar']; ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        } else {
            // Usar input de texto si no hay perfiles en BD
            nuevoMiembro.innerHTML = `
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <?php echo $t['perfil_equipo']; ?>
                    </label>
                    <input type="text" name="equipo_perfil_custom[]" 
                        class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        placeholder="Ej: Desarrollador Junior" 
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <?php echo $t['cantidad']; ?>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="equipo_cantidad[]" min="1" 
                            class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="Ej: 3" 
                            required>
                        <button type="button" onclick="eliminarMiembroEquipo(this)" 
                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition" 
                            title="<?php echo $t['eliminar']; ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        equipoContainer.appendChild(nuevoMiembro);
    }

    function eliminarMiembroEquipo(boton) {
        const item = boton.closest('.equipo-item');
        if (item && contadorEquipo > 1) {
            item.remove();
            contadorEquipo--;
        }
    }

    function actualizarContadorComplejidad() {
        const checkboxes = document.querySelectorAll('input[name="complejidad[]"]:checked');
        const contador = document.getElementById('seleccionados');
        if (contador) {
            contador.textContent = checkboxes.length;
        }
    }

    function mostrarPaso(paso) {
        // Ocultar todos los pasos
        for (let i = 1; i <= totalPasos; i++) {
            const pasoEl = document.getElementById(`paso${i}`);
            if (pasoEl) {
                pasoEl.classList.toggle('active', i === paso);
                pasoEl.classList.toggle('hidden', i !== paso);
            }
        }
    }

    function siguientePaso() {
        // Validar paso actual
        if (pasoActual === 1) {
            // Validar dominio
            const dominioSelect = document.querySelector('select[name="dominio_id"]');
            if (!dominioSelect || !dominioSelect.value) {
                mostrarError('<?php echo $t['seleccionar_dominio']; ?>');
                return;
            }

            // Validar país
            const paisSelect = document.querySelector('select[name="pais_id"]');
            if (!paisSelect || !paisSelect.value) {
                mostrarError('<?php echo $t['seleccionar_pais']; ?>');
                return;
            }

            // Validar equipo
            const perfiles = document.querySelectorAll(
                'select[name="equipo_perfil[]"], input[name="equipo_perfil_custom[]"]');
            const cantidades = document.querySelectorAll('input[name="equipo_cantidad[]"]');

            perfiles.forEach((perfil, index) => {
                if (!perfil.value.trim()) {
                    mostrarError('<?php echo $t['perfil_equipo']; ?> ' + (index + 1) +
                        ' <?php echo $t['no_definido']; ?>');
                    return false;
                }
            });

            cantidades.forEach((cantidad, index) => {
                if (!cantidad.value || cantidad.value < 1) {
                    mostrarError('<?php echo $t['cantidad']; ?> ' + (index + 1) +
                        ' <?php echo $t['no_definido']; ?>');
                    return false;
                }
            });
        } else if (pasoActual === 2) {
            // Validar triple restricción
            const restricciones = document.querySelectorAll('input[name="restricciones[]"]:checked');
            if (restricciones.length === 0) {
                mostrarError('<?php echo $t['error_seleccion_restriccion']; ?>');
                return;
            }

            // Validar tipo de costo si se seleccionó costo
            const costoCheckbox = document.getElementById('costoCheckbox');
            if (costoCheckbox.checked) {
                const tipoCostoSeleccionado = document.querySelector('input[name="tipo_costo"]:checked');
                if (!tipoCostoSeleccionado) {
                    mostrarError('Debe seleccionar un tipo de contrato de costo');
                    return;
                }
            }
        }

        // Avanzar al siguiente paso
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

    function recargarPagina() {
        window.location.reload();
    }

    function toggleMobileSidebar() {
        alert(
            'Para una mejor experiencia, por favor usa un dispositivo con pantalla más grande o gira tu dispositivo móvil.');
    }

    // ===========================================
    // EVENTOS Y INICIALIZACIÓN
    // ===========================================
    document.addEventListener('DOMContentLoaded', function() {
        // Contador de factores de complejidad
        const checkboxesComplejidad = document.querySelectorAll('input[name="complejidad[]"]');
        checkboxesComplejidad.forEach(checkbox => {
            checkbox.addEventListener('change', actualizarContadorComplejidad);
        });

        // Inicializar contador
        actualizarContadorComplejidad();

        // Manejar envío del formulario
        const form = document.getElementById('formCaracterizacion');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validar factores de complejidad
                const complejidad = document.querySelectorAll('input[name="complejidad[]"]:checked');
                if (complejidad.length === 0) {
                    mostrarError('<?php echo $t['error_seleccion_complejidad']; ?>');
                    return false;
                }

                // Mostrar estado de carga
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    <?php echo $t['completar_caracterizacion']; ?>...
                `;
                submitBtn.disabled = true;

                // Enviar formulario
                const formData = new FormData(form);
                const controllerUrl = BASE_URL + '/controllers/procesar_caracterizacion.php';

                console.log('Enviando a:', controllerUrl);

                fetch(controllerUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta:', data);
                        if (data.success) {
                            // Redirigir a resultados
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.href =
                                    `../views/resultados_caracterizacion.php?proyecto_id=${proyectoSeleccionado}&idioma=<?php echo $idioma_actual; ?>`;
                            }
                        } else {
                            mostrarError(data.message || 'Error al procesar la caracterización');
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

        // Cerrar modales con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['modalCaracterizar', 'modalAyuda', 'modalInstrucciones', 'modalOrganizacion'].forEach(
                    id => {
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

        // Agregar animación a las tarjetas de proyectos
        const projectCards = document.querySelectorAll('.card-project');
        projectCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
    </script>
</body>

</html>