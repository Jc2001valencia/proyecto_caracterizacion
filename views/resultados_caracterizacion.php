<?php
session_start();

// Configuración de idioma
if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'es'; // Idioma por defecto
}

// Cambiar idioma si se solicita
if (isset($_GET['idioma']) && in_array($_GET['idioma'], ['es', 'en'])) {
    $_SESSION['idioma'] = $_GET['idioma'];
}

$idioma_actual = $_SESSION['idioma'];

// Textos multidioma
$textos = [
    'es' => [
        'titulo' => 'Resultados de Caracterización',
        'subtitulo' => 'Análisis estratégico basado en el framework Cynefin',
        'proyecto' => 'Proyecto',
        'generado' => 'Generado',
        'triple_restriccion' => 'Triple Restricción',
        'tipo_restriccion' => 'Tipo de Restricción',
        'restricciones' => 'Restricciones',
        'complejidad' => 'Complejidad',
        'factores' => 'factores',
        'dominio_cynefin' => 'Dominio Cynefin',
        'contexto_proyecto' => 'Contexto del proyecto',
        'informacion_proyecto' => 'Información del Proyecto',
        'informacion_completa' => 'Información Completa del Proyecto',
        'nombre_proyecto' => 'Nombre del Proyecto',
        'tamano_estimado' => 'Tamaño Estimado',
        'horas' => 'horas',
        'pais_cliente' => 'País del Cliente',
        'dominio_proyecto' => 'Dominio del Proyecto',
        'dominio_problema' => 'Dominio del Problema',
        'descripcion' => 'Descripción',
        'conformacion_equipo' => 'Conformación del Equipo',
        'perfil' => 'Perfil',
        'cantidad' => 'Cantidad',
        'distribucion' => 'Distribución',
        'total' => 'Total',
        'miembros' => 'miembros',
        'factores_complejidad' => 'Factores de Complejidad Identificados',
        'factor_complejidad' => 'Factor de complejidad',
        'recomendacion' => 'Recomendación',
        'recomendacion_texto' => 'Se identificaron {count} factores de complejidad. Considere estrategias específicas para mitigar cada uno.',
        'estrategias_recomendadas' => 'Estrategias Recomendadas',
        'recomendado' => 'Recomendado',
        'fue_util' => '¿Fue útil?',
        'comentar' => 'Comentar',
        'estrategia_personalizable' => 'Estrategia personalizable',
        'restaurar_original' => 'Restaurar original',
        'guardar_cambios' => 'Guardar Cambios',
        'cancelar' => 'Cancelar',
        'acciones_rapidas' => 'Acciones Rápidas',
        'imprimir' => 'Imprimir',
        'compartir' => 'Compartir',
        'dashboard' => 'Dashboard',
        'nuevo' => 'Nuevo',
        'exportar' => 'Exportar',
        'editar' => 'Editar',
        'ver_estrategias' => 'Ver Estrategias',
        'sistema_caracterizacion' => 'Sistema de Caracterización de Proyectos',
        'framework_cynefin' => 'Framework Cynefin',
        'exportar_pdf' => 'Exportar PDF',
        'exportar_excel' => 'Exportar Excel',
        'exportar_word' => 'Exportar Word',
        'compartir_resultados' => 'Compartir Resultados',
        'copiar_enlace' => 'Copiar Enlace',
        'enlace_copiado' => 'Enlace copiado al portapapeles',
        'agregar_comentario' => 'Agregar Comentario',
        'escribe_comentario' => 'Escribe tu comentario sobre esta estrategia...',
        'guardar' => 'Guardar',
        'modo_oscuro' => 'Modo Oscuro',
        'modo_claro' => 'Modo Claro',
        'no_especificado' => 'No especificado',
        'sin_descripcion' => 'Sin descripción',
        'no_definido' => 'No definido',
        'no_clasificado' => 'No clasificado',
        'no_registrado' => 'No se registró información del equipo',
        'guardando' => 'Guardando cambios...',
        'cambios_guardados' => '¡Cambios guardados correctamente!',
        'error_guardar' => 'Error al guardar los cambios',
        'error_conexion' => 'Error de conexión',
        'volver' => 'Volver',
        'nuevo_proyecto' => 'Nuevo Proyecto',
        'nivel_complejidad' => 'Nivel de complejidad',
        'perfiles_equipo' => 'Perfiles del Equipo',
        'estado_proyecto' => 'Estado',
        'fecha_creacion' => 'Fecha de Creación',
        'fecha_actualizacion' => 'Última Actualización',
        'tipo_restriccion_detalle' => 'Detalle de Restricción',
        'restricciones_identificadas' => 'Restricciones Identificadas',
        'complejidad_nivel' => 'Nivel de Complejidad',
        'alta' => 'Alta',
        'media' => 'Media',
        'baja' => 'Baja',
        'informacion_adicional' => 'Información Adicional',
        'ver_detalles' => 'Ver Detalles',
        'ocultar_detalles' => 'Ocultar Detalles',
        'id_proyecto' => 'ID Proyecto',
        'ver_todo' => 'Ver todo',
        'contraer' => 'Contraer',
        'conexion_error' => 'Error de conexión a la base de datos',
        'usar_datos_sesion' => 'Usando datos de sesión'
    ],
    'en' => [
        'titulo' => 'Characterization Results',
        'subtitulo' => 'Strategic analysis based on the Cynefin framework',
        'proyecto' => 'Project',
        'generado' => 'Generated',
        'triple_restriccion' => 'Triple Constraint',
        'tipo_restriccion' => 'Constraint Type',
        'restricciones' => 'Constraints',
        'complejidad' => 'Complexity',
        'factores' => 'factors',
        'dominio_cynefin' => 'Cynefin Domain',
        'contexto_proyecto' => 'Project context',
        'informacion_proyecto' => 'Project Information',
        'informacion_completa' => 'Complete Project Information',
        'nombre_proyecto' => 'Project Name',
        'tamano_estimado' => 'Estimated Size',
        'horas' => 'hours',
        'pais_cliente' => 'Client Country',
        'dominio_proyecto' => 'Project Domain',
        'dominio_problema' => 'Problem Domain',
        'descripcion' => 'Description',
        'conformacion_equipo' => 'Team Composition',
        'perfil' => 'Profile',
        'cantidad' => 'Quantity',
        'distribucion' => 'Distribution',
        'total' => 'Total',
        'miembros' => 'members',
        'factores_complejidad' => 'Identified Complexity Factors',
        'factor_complejidad' => 'Complexity factor',
        'recomendacion' => 'Recommendation',
        'recomendacion_texto' => '{count} complexity factors identified. Consider specific strategies to mitigate each one.',
        'estrategias_recomendadas' => 'Recommended Strategies',
        'recomendado' => 'Recommended',
        'fue_util' => 'Was it useful?',
        'comentar' => 'Comment',
        'estrategia_personalizable' => 'Customizable strategy',
        'restaurar_original' => 'Restore original',
        'guardar_cambios' => 'Save Changes',
        'cancelar' => 'Cancel',
        'acciones_rapidas' => 'Quick Actions',
        'imprimir' => 'Print',
        'compartir' => 'Share',
        'dashboard' => 'Dashboard',
        'nuevo' => 'New',
        'exportar' => 'Export',
        'editar' => 'Edit',
        'ver_estrategias' => 'View Strategies',
        'sistema_caracterizacion' => 'Project Characterization System',
        'framework_cynefin' => 'Cynefin Framework',
        'exportar_pdf' => 'Export PDF',
        'exportar_excel' => 'Export Excel',
        'exportar_word' => 'Export Word',
        'compartir_resultados' => 'Share Results',
        'copiar_enlace' => 'Copy Link',
        'enlace_copiado' => 'Link copied to clipboard',
        'agregar_comentario' => 'Add Comment',
        'escribe_comentario' => 'Write your comment about this strategy...',
        'guardar' => 'Save',
        'modo_oscuro' => 'Dark Mode',
        'modo_claro' => 'Light Mode',
        'no_especificado' => 'Not specified',
        'sin_descripcion' => 'No description',
        'no_definido' => 'Not defined',
        'no_clasificado' => 'Not classified',
        'no_registrado' => 'No team information registered',
        'guardando' => 'Saving changes...',
        'cambios_guardados' => 'Changes saved successfully!',
        'error_guardar' => 'Error saving changes',
        'error_conexion' => 'Connection error',
        'volver' => 'Back',
        'nuevo_proyecto' => 'New Project',
        'nivel_complejidad' => 'Complexity level',
        'perfiles_equipo' => 'Team Profiles',
        'estado_proyecto' => 'Status',
        'fecha_creacion' => 'Creation Date',
        'fecha_actualizacion' => 'Last Updated',
        'tipo_restriccion_detalle' => 'Constraint Detail',
        'restricciones_identificadas' => 'Identified Constraints',
        'complejidad_nivel' => 'Complexity Level',
        'alta' => 'High',
        'media' => 'Medium',
        'baja' => 'Low',
        'informacion_adicional' => 'Additional Information',
        'ver_detalles' => 'View Details',
        'ocultar_detalles' => 'Hide Details',
        'id_proyecto' => 'Project ID',
        'ver_todo' => 'View all',
        'contraer' => 'Collapse',
        'conexion_error' => 'Database connection error',
        'usar_datos_sesion' => 'Using session data'
    ]
];

$t = $textos[$idioma_actual];

// Verificar si hay datos de caracterización
if (!isset($_SESSION['reporte_caracterizacion'])) {
    header('Location: ../index.php?action=lider_home');
    exit;
}

$reporte = $_SESSION['reporte_caracterizacion'];

// Inicializar conexión a BD
$datos_adicionales = [];
$id_proyecto = $_SESSION['proyecto_id'] ?? null;

// Intentar conectar a la base de datos usando config/db.php
try {
    // Incluir el archivo de configuración de la base de datos
    require_once '../config/db.php';
    
    // Obtener datos adicionales si hay conexión y proyecto ID
    if ($id_proyecto && isset($db) && $db !== null) {
        $stmt = $db->prepare("SELECT * FROM caracterizaciones WHERE proyecto_id = ?");
        $stmt->execute([$id_proyecto]);
        $datos_adicionales = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } else {
        throw new Exception("Conexión no disponible");
    }
} catch (Exception $e) {
    // Si hay error de conexión, usamos solo datos de sesión
    error_log("Error de conexión a BD: " . $e->getMessage());
    $datos_adicionales = [];
}

// Validar y completar datos del reporte
$reporte['proyecto'] = $reporte['proyecto'] ?? [];
$reporte['proyecto']['nombre'] = $reporte['proyecto']['nombre'] ?? $t['no_especificado'];
$reporte['proyecto']['tamano'] = $reporte['proyecto']['tamano'] ?? $t['no_especificado'];
$reporte['proyecto']['descripcion'] = $reporte['proyecto']['descripcion'] ?? $t['sin_descripcion'];
$reporte['proyecto']['equipo'] = $reporte['proyecto']['equipo'] ?? [];

// Inicializar arrays para datos adicionales
$reporte['datos_adicionales'] = [];

// Procesar datos adicionales si existen
if (!empty($datos_adicionales)) {
    // Restricciones
    if (!empty($datos_adicionales['restricciones_json'])) {
        $restricciones = json_decode($datos_adicionales['restricciones_json'], true);
        $reporte['datos_adicionales']['restricciones'] = is_array($restricciones) ? $restricciones : [];
    }
    
    // Tipo de restricción
    if (!empty($datos_adicionales['tipo_restriccion'])) {
        $tipos_restriccion = [
            1 => $idioma_actual === 'es' ? 'Tiempo' : 'Time',
            2 => $idioma_actual === 'es' ? 'Costo' : 'Cost',
            3 => $idioma_actual === 'es' ? 'Alcance' : 'Scope',
            4 => $idioma_actual === 'es' ? 'Tiempo y Costo' : 'Time and Cost',
            5 => $idioma_actual === 'es' ? 'Tiempo y Alcance' : 'Time and Scope',
            6 => $idioma_actual === 'es' ? 'Costo y Alcance' : 'Cost and Scope',
            7 => $idioma_actual === 'es' ? 'Triple Restricción' : 'Triple Constraint'
        ];
        $reporte['datos_adicionales']['tipo_restriccion'] = [
            'id' => $datos_adicionales['tipo_restriccion'],
            'nombre' => $tipos_restriccion[$datos_adicionales['tipo_restriccion']] ?? $t['no_definido']
        ];
    }
    
    // Factores de complejidad
    if (!empty($datos_adicionales['complejidad_json'])) {
        $complejidades = json_decode($datos_adicionales['complejidad_json'], true);
        if (is_array($complejidades)) {
            $mapeo_factores = [
                '1' => $idioma_actual === 'es' ? 'Alto número de stakeholders' : 'High number of stakeholders',
                '2' => $idioma_actual === 'es' ? 'Requisitos ambiguos o cambiantes' : 'Ambiguous or changing requirements',
                '3' => $idioma_actual === 'es' ? 'Tecnología nueva o no probada' : 'New or untested technology',
                '4' => $idioma_actual === 'es' ? 'Dependencias externas críticas' : 'Critical external dependencies',
                '5' => $idioma_actual === 'es' ? 'Equipo distribuido geográficamente' : 'Geographically distributed team',
                '6' => $idioma_actual === 'es' ? 'Restricciones regulatorias estrictas' : 'Strict regulatory constraints',
                '7' => $idioma_actual === 'es' ? 'Interdependencias complejas' : 'Complex interdependencies',
                '8' => $idioma_actual === 'es' ? 'Ambiente político complejo' : 'Complex political environment',
                '9' => $idioma_actual === 'es' ? 'Recursos limitados o compartidos' : 'Limited or shared resources',
                '10' => $idioma_actual === 'es' ? 'Altas expectativas de calidad' : 'High quality expectations'
            ];
            
            $factores_complejidad = [];
            foreach ($complejidades as $id_factor) {
                if (isset($mapeo_factores[$id_factor])) {
                    $factores_complejidad[] = $mapeo_factores[$id_factor];
                }
            }
            
            $reporte['complejidad']['factores'] = $factores_complejidad;
            $reporte['complejidad']['total'] = count($factores_complejidad);
            
            // Determinar nivel de complejidad
            $nivel = count($factores_complejidad);
            if ($nivel <= 3) {
                $reporte['complejidad']['nivel'] = $t['baja'];
                $reporte['complejidad']['color'] = 'green';
            } elseif ($nivel <= 6) {
                $reporte['complejidad']['nivel'] = $t['media'];
                $reporte['complejidad']['color'] = 'yellow';
            } else {
                $reporte['complejidad']['nivel'] = $t['alta'];
                $reporte['complejidad']['color'] = 'red';
            }
        }
    } elseif (isset($reporte['complejidad']['factores'])) {
        // Si no hay datos de BD pero sí hay en sesión, calcular nivel
        $nivel = count($reporte['complejidad']['factores']);
        if ($nivel <= 3) {
            $reporte['complejidad']['nivel'] = $t['baja'];
            $reporte['complejidad']['color'] = 'green';
        } elseif ($nivel <= 6) {
            $reporte['complejidad']['nivel'] = $t['media'];
            $reporte['complejidad']['color'] = 'yellow';
        } else {
            $reporte['complejidad']['nivel'] = $t['alta'];
            $reporte['complejidad']['color'] = 'red';
        }
    }
    
    // Información del equipo
    if (!empty($datos_adicionales['equipo_json'])) {
        $equipo_data = json_decode($datos_adicionales['equipo_json'], true);
        if (is_array($equipo_data) && !empty($equipo_data)) {
            $equipo_formateado = [];
            $total_miembros = 0;
            
            foreach ($equipo_data as $miembro) {
                if (isset($miembro['perfil_nombre']) && isset($miembro['cantidad'])) {
                    $equipo_formateado[] = [
                        'perfil' => $miembro['perfil_nombre'],
                        'cantidad' => $miembro['cantidad']
                    ];
                    $total_miembros += $miembro['cantidad'];
                }
            }
            
            $reporte['proyecto']['equipo'] = $equipo_formateado;
            $reporte['proyecto']['total_equipo'] = $total_miembros;
        }
    }
    
    // Dominio del proyecto
    if (!empty($datos_adicionales['dominio_proyecto'])) {
        $reporte['proyecto']['dominio_proyecto'] = $datos_adicionales['dominio_proyecto'];
    }
    
    // País del cliente
    if (!empty($datos_adicionales['pais_cliente'])) {
        $reporte['proyecto']['pais'] = $datos_adicionales['pais_cliente'];
    }
    
    // Dominio Cynefin
    if (!empty($datos_adicionales['dominio_cynefin'])) {
        $reporte['dominio_cynefin'] = $datos_adicionales['dominio_cynefin'];
    }
    
    // Estrategias
    if (!empty($datos_adicionales['estrategias_json'])) {
        $estrategias = json_decode($datos_adicionales['estrategias_json'], true);
        if (is_array($estrategias)) {
            $reporte['estrategias'] = $estrategias;
        }
    }
    
    // Estado y fechas
    $reporte['datos_adicionales']['estado'] = $datos_adicionales['estado'] ?? 1;
    $reporte['datos_adicionales']['created_at'] = $datos_adicionales['created_at'] ?? date('Y-m-d H:i:s');
    $reporte['datos_adicionales']['updated_at'] = $datos_adicionales['updated_at'] ?? date('Y-m-d H:i:s');
} else {
    // Si no hay datos adicionales, intentar usar datos de sesión
    if (isset($reporte['complejidad']['factores'])) {
        $reporte['complejidad']['total'] = count($reporte['complejidad']['factores']);
        
        // Calcular nivel de complejidad
        $nivel = $reporte['complejidad']['total'];
        if ($nivel <= 3) {
            $reporte['complejidad']['nivel'] = $t['baja'];
            $reporte['complejidad']['color'] = 'green';
        } elseif ($nivel <= 6) {
            $reporte['complejidad']['nivel'] = $t['media'];
            $reporte['complejidad']['color'] = 'yellow';
        } else {
            $reporte['complejidad']['nivel'] = $t['alta'];
            $reporte['complejidad']['color'] = 'red';
        }
    }
}

// Inicializar valores por defecto si no existen
$reporte['triple_restriccion'] = $reporte['triple_restriccion'] ?? ['tipo' => $t['no_definido'], 'descripcion' => ''];
$reporte['complejidad'] = $reporte['complejidad'] ?? ['total' => 0, 'factores' => [], 'nivel' => $t['baja'], 'color' => 'gray'];
$reporte['dominio_cynefin'] = $reporte['dominio_cynefin'] ?? $t['no_clasificado'];
$reporte['estrategias'] = $reporte['estrategias'] ?? [];

$estrategias_mostrar = $_SESSION['estrategias_modificadas'] ?? $reporte['estrategias'];

// Mostrar mensaje de conexión si no hay datos de BD
$mostrar_mensaje_conexion = empty($datos_adicionales) && $id_proyecto;
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma_actual; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['titulo']; ?> - <?php echo htmlspecialchars($reporte['proyecto']['nombre']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            font-size: 12pt;
            background: white !important;
        }

        .bg-gray-50 {
            background: white !important;
        }

        .shadow-lg {
            box-shadow: none !important;
        }

        .border {
            border: 1px solid #000 !important;
        }

        .print-break {
            page-break-before: always;
        }

        .strategies-container {
            max-height: none !important;
            overflow: visible !important;
        }
    }

    .estrategia-card {
        transition: all 0.3s ease;
        border-left: 4px solid;
    }

    .estrategia-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .cynefin-obvio {
        border-left-color: #10B981;
        background-color: #ECFDF5;
    }

    .cynefin-complicado {
        border-left-color: #3B82F6;
        background-color: #EFF6FF;
    }

    .cynefin-complejo {
        border-left-color: #8B5CF6;
        background-color: #F5F3FF;
    }

    .cynefin-caotico {
        border-left-color: #EF4444;
        background-color: #FEF2F2;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
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

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 200px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dark-mode {
        background-color: #1a202c;
        color: #e2e8f0;
    }

    .dark-mode .bg-white {
        background-color: #2d3748;
    }

    .dark-mode .text-gray-900 {
        color: #e2e8f0;
    }

    .dark-mode .text-gray-700 {
        color: #cbd5e0;
    }

    .dark-mode .text-gray-600 {
        color: #a0aec0;
    }

    .dark-mode .bg-gray-50 {
        background-color: #2d3748;
    }

    .dark-mode .bg-blue-50 {
        background-color: #2a4365;
    }

    .dark-mode .bg-yellow-50 {
        background-color: #744210;
    }

    .dark-mode .bg-green-50 {
        background-color: #22543d;
    }

    .dark-mode .bg-purple-50 {
        background-color: #44337a;
    }

    .rating-stars {
        display: flex;
        gap: 2px;
    }

    .rating-stars input {
        display: none;
    }

    .rating-stars label {
        cursor: pointer;
        color: #D1D5DB;
        transition: color 0.2s;
    }

    .rating-stars input:checked~label,
    .rating-stars label:hover,
    .rating-stars label:hover~label {
        color: #F59E0B;
    }

    .strategies-container {
        max-height: 600px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .strategies-container::-webkit-scrollbar {
        width: 6px;
    }

    .strategies-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .strategies-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .strategies-container::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    .info-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        margin: 2px;
    }

    .info-badge.blue {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .info-badge.green {
        background-color: #d1fae5;
        color: #065f46;
    }

    .info-badge.yellow {
        background-color: #fef3c7;
        color: #92400e;
    }

    .info-badge.red {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .info-badge.purple {
        background-color: #f3e8ff;
        color: #6b21a8;
    }

    .info-badge.gray {
        background-color: #f3f4f6;
        color: #374151;
    }

    .expandable-section {
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .expandable-section.collapsed {
        max-height: 0;
        opacity: 0;
    }

    .expandable-section.expanded {
        max-height: 1000px;
        opacity: 1;
    }

    .progress-bar {
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        transition: width 0.3s ease;
    }

    .alert-warning {
        background-color: #fef3c7;
        border-color: #fbbf24;
        color: #92400e;
    }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen transition-colors duration-300" id="body">
    <!-- Header Navigation -->
    <nav class="bg-white shadow-sm border-b no-print transition-colors duration-300" id="nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl mr-3"></i>
                    <h1 class="text-lg font-semibold text-gray-900">
                        <?php echo $t['sistema_caracterizacion']; ?>
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Selector de Idioma -->
                    <div class="dropdown relative">
                        <button
                            class="flex items-center text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-globe mr-2"></i>
                            <span class="hidden sm:inline"><?php echo strtoupper($idioma_actual); ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="dropdown-content right-0 mt-2">
                            <a href="?idioma=es&proyecto_id=<?php echo $id_proyecto ?? ''; ?>"
                                class="block px-4 py-2 hover:bg-gray-100 <?php echo $idioma_actual === 'es' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                                <i class="fas fa-flag mr-2"></i>Español
                            </a>
                            <a href="?idioma=en&proyecto_id=<?php echo $id_proyecto ?? ''; ?>"
                                class="block px-4 py-2 hover:bg-gray-100 <?php echo $idioma_actual === 'en' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                                <i class="fas fa-flag-usa mr-2"></i>English
                            </a>
                        </div>
                    </div>

                    <!-- Exportar Dropdown -->
                    <div class="dropdown relative">
                        <button
                            class="flex items-center text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-download mr-2"></i>
                            <span class="hidden sm:inline"><?php echo $t['exportar']; ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="dropdown-content right-0 mt-2">
                            <button onclick="exportarPDF()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                <?php echo $t['exportar_pdf']; ?>
                            </button>
                            <button onclick="exportarExcel()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-file-excel text-green-500 mr-2"></i>
                                <?php echo $t['exportar_excel']; ?>
                            </button>
                            <button onclick="exportarWord()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-file-word text-blue-500 mr-2"></i>
                                <?php echo $t['exportar_word']; ?>
                            </button>
                        </div>
                    </div>

                    <!-- Compartir Dropdown -->
                    <div class="dropdown relative">
                        <button
                            class="flex items-center text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                            <i class="fas fa-share-alt mr-2"></i>
                            <span class="hidden sm:inline"><?php echo $t['compartir']; ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="dropdown-content right-0 mt-2">
                            <button onclick="compartirResultados()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-share mr-2"></i>
                                <?php echo $t['compartir_resultados']; ?>
                            </button>
                            <button onclick="copiarEnlace()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-link mr-2"></i>
                                <?php echo $t['copiar_enlace']; ?>
                            </button>
                        </div>
                    </div>

                    <button onclick="window.print()"
                        class="flex items-center text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-print mr-2"></i>
                        <span class="hidden sm:inline"><?php echo $t['imprimir']; ?></span>
                    </button>

                    <button onclick="toggleDarkMode()" id="dark-mode-toggle"
                        class="flex items-center text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-moon mr-2"></i>
                        <span class="hidden sm:inline"><?php echo $t['modo_oscuro']; ?></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensaje de conexión si es necesario -->
        <?php if ($mostrar_mensaje_conexion): ?>
        <div class="mb-6 fade-in">
            <div class="alert-warning border rounded-xl p-4 flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="font-medium"><?php echo $t['conexion_error']; ?></p>
                    <p class="text-sm mt-1"><?php echo $t['usar_datos_sesion']; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Encabezado Principal -->
        <div class="text-center mb-12 fade-in">
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-6 transition-colors duration-300" id="header-card">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    <?php echo $t['titulo']; ?>
                </h1>
                <div class="flex flex-col lg:flex-row items-center justify-center gap-4 mb-6">
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full font-semibold">
                        <i class="fas fa-project-diagram mr-2"></i>
                        <?php echo $t['proyecto']; ?>:
                        <?php echo htmlspecialchars($reporte['proyecto']['nombre']); ?>
                    </div>
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold">
                        <i class="fas fa-clock mr-2"></i>
                        <?php echo $t['generado']; ?>: <?php echo date('d/m/Y H:i'); ?>
                    </div>
                    <?php if ($id_proyecto): ?>
                    <div class="bg-purple-100 text-purple-800 px-4 py-2 rounded-full font-semibold">
                        <i class="fas fa-hashtag mr-2"></i>
                        <?php echo $t['id_proyecto']; ?>: <?php echo htmlspecialchars($id_proyecto); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                    <?php echo $t['subtitulo']; ?>
                </p>
            </div>
        </div>

        <!-- Resumen Ejecutivo -->
        <section class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8 fade-in">
            <!-- ID y Estado -->
            <div class="bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2"><?php echo $t['id_proyecto']; ?></h3>
                        <p class="text-2xl font-bold">
                            <?php echo $id_proyecto ? htmlspecialchars($id_proyecto) : 'N/A'; ?>
                        </p>
                        <p class="text-gray-100 text-sm mt-1">
                            <?php echo $t['estado_proyecto']; ?>:
                            <span class="font-semibold">
                                <?php echo isset($reporte['datos_adicionales']['estado']) ? 'Activo' : 'N/A'; ?>
                            </span>
                        </p>
                    </div>
                    <i class="fas fa-id-card text-3xl opacity-80"></i>
                </div>
            </div>

            <!-- Triple Restricción -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2"><?php echo $t['triple_restriccion']; ?></h3>
                        <p class="text-2xl font-bold">
                            <?php 
                            if (isset($reporte['datos_adicionales']['tipo_restriccion'])) {
                                echo htmlspecialchars($reporte['datos_adicionales']['tipo_restriccion']['nombre']);
                            } else {
                                echo htmlspecialchars($reporte['triple_restriccion']['tipo']);
                            }
                            ?>
                        </p>
                        <p class="text-blue-100 text-sm mt-1"><?php echo $t['tipo_restriccion_detalle']; ?></p>
                    </div>
                    <i class="fas fa-balance-scale text-3xl opacity-80"></i>
                </div>
            </div>

            <!-- Complejidad -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2"><?php echo $t['complejidad']; ?></h3>
                        <p class="text-2xl font-bold">
                            <?php echo htmlspecialchars($reporte['complejidad']['total']); ?>
                            <?php echo $t['factores']; ?>
                        </p>
                        <p class="text-green-100 text-sm mt-1">
                            <?php echo $t['nivel_complejidad']; ?>:
                            <span class="font-semibold"><?php echo $reporte['complejidad']['nivel']; ?></span>
                        </p>
                    </div>
                    <i class="fas fa-layer-group text-3xl opacity-80"></i>
                </div>
            </div>

            <!-- Dominio Cynefin -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2"><?php echo $t['dominio_cynefin']; ?></h3>
                        <p class="text-2xl font-bold"><?php echo htmlspecialchars($reporte['dominio_cynefin']); ?></p>
                        <p class="text-purple-100 text-sm mt-1"><?php echo $t['contexto_proyecto']; ?></p>
                    </div>
                    <i class="fas fa-compass text-3xl opacity-80"></i>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Columna Izquierda - Información del Proyecto -->
            <div class="xl:col-span-2 space-y-8">
                <!-- Información Completa del Proyecto -->
                <section class="bg-white rounded-2xl shadow-lg p-6 fade-in transition-colors duration-300"
                    id="project-info">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            <?php echo $t['informacion_completa']; ?>
                        </h2>
                        <button onclick="toggleDetalles()" id="toggle-detalles"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center no-print">
                            <i class="fas fa-chevron-down mr-2" id="toggle-icon"></i>
                            <span id="toggle-text"><?php echo $t['ver_detalles']; ?></span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-signature mr-2"></i><?php echo $t['nombre_proyecto']; ?>
                                </label>
                                <p class="text-gray-900 font-semibold">
                                    <?php echo htmlspecialchars($reporte['proyecto']['nombre']); ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-ruler-combined mr-2"></i><?php echo $t['tamano_estimado']; ?>
                                </label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['tamano']); ?>
                                    <?php echo $t['horas']; ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-globe-americas mr-2"></i><?php echo $t['pais_cliente']; ?>
                                </label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['pais'] ?? $t['no_especificado']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-sitemap mr-2"></i><?php echo $t['dominio_proyecto']; ?>
                                </label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['dominio_proyecto'] ?? $t['no_especificado']); ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-tags mr-2"></i><?php echo $t['dominio_problema']; ?>
                                </label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['dominio_problema'] ?? $t['no_especificado']); ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-align-left mr-2"></i><?php echo $t['descripcion']; ?>
                                </label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['descripcion']); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional (expandible) -->
                    <div id="detalles-adicionales" class="expandable-section collapsed mt-6">
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-list-alt text-green-600 mr-2"></i>
                                <?php echo $t['informacion_adicional']; ?>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Restricciones -->
                                <?php if (!empty($reporte['datos_adicionales']['restricciones'])): ?>
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <label class="block text-sm font-medium text-blue-700 mb-2">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        <?php echo $t['restricciones_identificadas']; ?>
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($reporte['datos_adicionales']['restricciones'] as $restriccion): ?>
                                        <span class="info-badge blue">
                                            <i class="fas fa-lock mr-1"></i>
                                            <?php echo htmlspecialchars($restriccion); ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Fechas -->
                                <div class="bg-green-50 rounded-lg p-4">
                                    <label class="block text-sm font-medium text-green-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <?php echo $t['fecha_creacion']; ?>
                                    </label>
                                    <p class="text-green-800">
                                        <?php 
                                        if (isset($reporte['datos_adicionales']['created_at'])) {
                                            echo date('d/m/Y H:i', strtotime($reporte['datos_adicionales']['created_at']));
                                        } else {
                                            echo $t['no_especificado'];
                                        }
                                        ?>
                                    </p>

                                    <label class="block text-sm font-medium text-green-700 mt-3 mb-2">
                                        <i class="fas fa-calendar-check mr-2"></i>
                                        <?php echo $t['fecha_actualizacion']; ?>
                                    </label>
                                    <p class="text-green-800">
                                        <?php 
                                        if (isset($reporte['datos_adicionales']['updated_at'])) {
                                            echo date('d/m/Y H:i', strtotime($reporte['datos_adicionales']['updated_at']));
                                        } else {
                                            echo $t['no_especificado'];
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conformación del Equipo -->
                    <?php if (!empty($reporte['proyecto']['equipo'])): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-users text-purple-600 mr-2"></i>
                            <?php echo $t['conformacion_equipo']; ?>
                            <?php if (isset($reporte['proyecto']['total_equipo'])): ?>
                            <span class="ml-2 text-sm font-normal text-gray-500">
                                (<?php echo $reporte['proyecto']['total_equipo']; ?>
                                <?php echo $t['miembros']; ?>)
                            </span>
                            <?php endif; ?>
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">
                                            <i class="fas fa-user-tie mr-2"></i><?php echo $t['perfil']; ?>
                                        </th>
                                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">
                                            <i class="fas fa-users mr-2"></i><?php echo $t['cantidad']; ?>
                                        </th>
                                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                            <i class="fas fa-chart-pie mr-2"></i><?php echo $t['distribucion']; ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php 
                                    $total_miembros = $reporte['proyecto']['total_equipo'] ?? array_sum(array_column($reporte['proyecto']['equipo'], 'cantidad'));
                                    foreach ($reporte['proyecto']['equipo'] as $miembro): 
                                        $porcentaje = $total_miembros > 0 ? ($miembro['cantidad'] / $total_miembros) * 100 : 0;
                                    ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            <i class="fas fa-user-tie text-blue-500 mr-2"></i>
                                            <?= htmlspecialchars($miembro['perfil']) ?>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-600">
                                            <span
                                                class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                                <?= htmlspecialchars($miembro['cantidad']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-600">
                                            <div class="flex items-center justify-end">
                                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                    <div class="bg-blue-600 h-2 rounded-full"
                                                        style="width: <?= $porcentaje ?>%"></div>
                                                </div>
                                                <span
                                                    class="text-xs font-medium w-10 text-left"><?= round($porcentaje) ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="bg-gray-50 font-semibold">
                                        <td class="px-4 py-3 text-sm">
                                            <i class="fas fa-calculator mr-2"></i><?php echo $t['total']; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <span
                                                class="inline-block bg-blue-600 text-white px-2 py-1 rounded-full text-xs font-medium">
                                                <?= $total_miembros ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm">100%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- Factores de Complejidad -->
                <?php if (!empty($reporte['complejidad']['factores'])): ?>
                <section class="bg-white rounded-2xl shadow-lg p-6 fade-in print-break transition-colors duration-300"
                    id="complexity-factors">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                            <?php echo $t['factores_complejidad']; ?>
                            <span class="ml-3 text-sm font-normal text-gray-500">
                                (<?php echo $reporte['complejidad']['total']; ?> <?php echo $t['factores']; ?>)
                            </span>
                        </h2>
                        <span class="info-badge <?php echo $reporte['complejidad']['color']; ?>">
                            <?php echo $t['complejidad_nivel']; ?>: <?php echo $reporte['complejidad']['nivel']; ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($reporte['complejidad']['factores'] as $index => $factor): ?>
                        <div
                            class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start transition hover:border-yellow-300">
                            <div class="flex-shrink-0">
                                <div
                                    class="bg-yellow-100 text-yellow-800 rounded-full w-10 h-10 flex items-center justify-center mr-4">
                                    <span class="font-bold text-lg"><?= $index + 1 ?></span>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <p class="text-yellow-800 font-medium">
                                    <?php echo htmlspecialchars($factor); ?>
                                </p>
                                <p class="text-yellow-600 text-sm mt-1 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <?php echo $t['factor_complejidad']; ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-blue-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <p class="text-blue-800 font-medium text-lg">
                                    <i class="fas fa-bullhorn mr-2"></i><?php echo $t['recomendacion']; ?>
                                </p>
                                <p class="text-blue-600 mt-1">
                                    <?php echo str_replace('{count}', count($reporte['complejidad']['factores']), $t['recomendacion_texto']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <!-- Columna Derecha - Estrategias -->
            <div class="space-y-8">
                <!-- Estrategias Recomendadas -->
                <section class="bg-white rounded-2xl shadow-lg p-6 fade-in transition-colors duration-300"
                    id="strategies-section">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center mb-4 sm:mb-0">
                            <i class="fas fa-chess-knight text-purple-600 mr-3"></i>
                            <?php echo $t['estrategias_recomendadas']; ?>
                            <span class="ml-3 text-sm font-normal text-gray-500">
                                (<?php echo count($estrategias_mostrar); ?>)
                            </span>
                        </h2>
                        <div class="flex space-x-2 no-print">
                            <button onclick="toggleEdicion()" id="btn-editar"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center text-sm">
                                <i class="fas fa-edit mr-2"></i>
                                <span id="btn-editar-text"><?php echo $t['editar']; ?></span>
                            </button>
                            <button onclick="toggleScroll()" id="btn-scroll"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center text-sm">
                                <i class="fas fa-expand mr-2"></i>
                                <span id="btn-scroll-text"><?php echo $t['ver_todo']; ?></span>
                            </button>
                        </div>
                    </div>

                    <!-- Contenedor scrollable para estrategias -->
                    <div id="strategies-container" class="strategies-container">
                        <!-- Modo Visualización -->
                        <div id="modo-visualizacion" class="space-y-4">
                            <?php foreach ($estrategias_mostrar as $categoria => $valor): ?>
                            <div
                                class="estrategia-card bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border-l-4 border-purple-500">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-900 text-lg">
                                        <i class="fas fa-chess-board mr-2"></i>
                                        <?php echo htmlspecialchars($categoria); ?>
                                    </h4>
                                    <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-star mr-1"></i><?php echo $t['recomendado']; ?>
                                    </span>
                                </div>
                                <p class="text-gray-700 leading-relaxed">
                                    <?php echo htmlspecialchars($valor); ?>
                                </p>
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="rating-stars text-sm">
                                        <span class="text-gray-500 mr-2"><?php echo $t['fue_util']; ?></span>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <label class="text-yellow-400 cursor-pointer">
                                            <i class="fas fa-star"></i>
                                        </label>
                                        <?php endfor; ?>
                                    </div>
                                    <button onclick="mostrarComentario('<?php echo htmlspecialchars($categoria); ?>')"
                                        class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                        <i class="fas fa-comment mr-1"></i>
                                        <?php echo $t['comentar']; ?>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Modo Edición -->
                        <div id="modo-edicion" class="hidden space-y-4">
                            <form id="form-estrategias" class="space-y-4">
                                <?php foreach ($estrategias_mostrar as $categoria => $valor): ?>
                                <div
                                    class="estrategia-card bg-gradient-to-r from-blue-50 to-white rounded-xl p-4 border-l-4 border-blue-500">
                                    <label class="block font-semibold text-gray-900 mb-2 text-lg">
                                        <i class="fas fa-edit mr-2"></i><?php echo htmlspecialchars($categoria); ?>
                                    </label>
                                    <textarea name="<?php echo htmlspecialchars($categoria); ?>"
                                        class="w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        rows="3"
                                        placeholder="<?php echo $t['escribe_comentario']; ?>"><?php echo htmlspecialchars($valor); ?></textarea>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs text-gray-500 flex items-center">
                                            <i
                                                class="fas fa-magic mr-1"></i><?php echo $t['estrategia_personalizable']; ?>
                                        </span>
                                        <button type="button"
                                            onclick="restaurarEstrategia('<?php echo htmlspecialchars($categoria); ?>')"
                                            class="text-xs text-gray-600 hover:text-gray-800 flex items-center transition">
                                            <i class="fas fa-undo mr-1"></i>
                                            <?php echo $t['restaurar_original']; ?>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </form>
                        </div>
                    </div>

                    <!-- Botones de acción en modo edición -->
                    <div id="botones-edicion" class="hidden flex flex-col sm:flex-row gap-3 pt-4 border-t mt-4">
                        <button type="button" onclick="guardarEstrategias()"
                            class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center font-semibold">
                            <i class="fas fa-save mr-2"></i>
                            <?php echo $t['guardar_cambios']; ?>
                        </button>
                        <button type="button" onclick="cancelarEdicion()"
                            class="flex-1 bg-gray-500 text-white py-3 rounded-lg hover:bg-gray-600 transition flex items-center justify-center font-semibold">
                            <i class="fas fa-times mr-2"></i>
                            <?php echo $t['cancelar']; ?>
                        </button>
                    </div>

                    <!-- Mensajes de Estado -->
                    <div id="mensaje-estado" class="mt-4 hidden"></div>
                </section>

                <!-- Acciones Rápidas -->
                <section class="bg-white rounded-2xl shadow-lg p-6 no-print transition-colors duration-300"
                    id="quick-actions">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                        <?php echo $t['acciones_rapidas']; ?>
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="window.print()"
                            class="bg-blue-600 text-white py-2 px-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center text-sm">
                            <i class="fas fa-print mr-2"></i>
                            <?php echo $t['imprimir']; ?>
                        </button>
                        <button onclick="compartirResultados()"
                            class="bg-green-600 text-white py-2 px-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center text-sm">
                            <i class="fas fa-share mr-2"></i>
                            <?php echo $t['compartir']; ?>
                        </button>
                        <a href="../index.php?action=lider_home"
                            class="bg-gray-600 text-white py-2 px-3 rounded-lg hover:bg-gray-700 transition flex items-center justify-center text-sm">
                            <i class="fas fa-home mr-2"></i>
                            <?php echo $t['dashboard']; ?>
                        </a>
                        <a href="../index.php?action=lider_home"
                            class="bg-purple-600 text-white py-2 px-3 rounded-lg hover:bg-purple-700 transition flex items-center justify-center text-sm">
                            <i class="fas fa-plus mr-2"></i>
                            <?php echo $t['nuevo']; ?>
                        </a>
                    </div>
                </section>
            </div>
        </div>

        <!-- Botones finales -->
        <div class="mt-8 flex justify-between no-print">
            <button onclick="window.history.back()"
                class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition flex items-center">
                <i class="fas fa-arrow-left mr-2"></i><?php echo $t['volver']; ?>
            </button>
            <div class="flex gap-3">
                <button onclick="window.print()"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-print mr-2"></i><?php echo $t['imprimir']; ?>
                </button>
                <a href="../index.php?action=lider_home"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition flex items-center">
                    <i class="fas fa-plus mr-2"></i><?php echo $t['nuevo_proyecto']; ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer
        class="mt-12 pt-8 border-t border-gray-200 text-center text-gray-600 no-print transition-colors duration-300"
        id="footer">
        <p class="mb-2"><?php echo $t['sistema_caracterizacion']; ?> - <?php echo $t['framework_cynefin']; ?></p>
        <p class="text-sm"><?php echo $t['generado']; ?> <?php echo date('d/m/Y \a \l\a\s H:i'); ?></p>
    </footer>

    <!-- Modal de Comentarios -->
    <div id="modal-comentario" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300"
            id="modal-comentario-content">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="modal-titulo">
                    <i class="fas fa-comment mr-2"></i><?php echo $t['agregar_comentario']; ?>
                </h3>
            </div>
            <div class="p-6">
                <textarea id="comentario-texto"
                    class="w-full border border-gray-300 rounded-lg p-3 h-32 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="<?php echo $t['escribe_comentario']; ?>"></textarea>
                <div class="mt-4 flex justify-end space-x-3">
                    <button onclick="cerrarModalComentario()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                        <i class="fas fa-times mr-2"></i><?php echo $t['cancelar']; ?>
                    </button>
                    <button onclick="guardarComentario()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-save mr-2"></i><?php echo $t['guardar']; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Variables globales
    let estrategiaActual = '';
    let darkMode = false;
    let detallesExpandidos = false;
    let scrollExpandido = false;
    const estrategiasOriginales = <?php echo json_encode($reporte['estrategias']); ?>;
    const textos = <?php echo json_encode($textos); ?>;
    const idiomaActual = '<?php echo $idioma_actual; ?>';

    // Funciones de UI
    function toggleEdicion() {
        const visual = document.getElementById('modo-visualizacion');
        const edicion = document.getElementById('modo-edicion');
        const botones = document.getElementById('botones-edicion');
        const boton = document.getElementById('btn-editar');
        const editando = !edicion.classList.contains('hidden');

        visual.classList.toggle('hidden', !editando);
        edicion.classList.toggle('hidden', editando);
        botones.classList.toggle('hidden', !editando);

        if (editando) {
            boton.innerHTML = '<i class="fas fa-edit mr-2"></i><span id="btn-editar-text">' + textos[idiomaActual][
                'editar'
            ] + '</span>';
            boton.classList.remove('bg-purple-600');
            boton.classList.add('bg-blue-600');
        } else {
            boton.innerHTML = '<i class="fas fa-eye mr-2"></i><span id="btn-editar-text">' + textos[idiomaActual][
                'ver_estrategias'
            ] + '</span>';
            boton.classList.remove('bg-blue-600');
            boton.classList.add('bg-purple-600');
        }
    }

    function cancelarEdicion() {
        document.getElementById('modo-edicion').classList.add('hidden');
        document.getElementById('modo-visualizacion').classList.remove('hidden');
        document.getElementById('botones-edicion').classList.add('hidden');
        document.getElementById('btn-editar').innerHTML =
            '<i class="fas fa-edit mr-2"></i><span id="btn-editar-text">' + textos[idiomaActual]['editar'] + '</span>';
        document.getElementById('btn-editar').classList.remove('bg-purple-600');
        document.getElementById('btn-editar').classList.add('bg-blue-600');
        document.getElementById('form-estrategias').reset();
    }

    function restaurarEstrategia(categoria) {
        const textarea = document.querySelector(`textarea[name="${categoria}"]`);
        if (textarea && estrategiasOriginales[categoria]) {
            textarea.value = estrategiasOriginales[categoria];
            // Efecto visual
            textarea.classList.add('border-green-500');
            setTimeout(() => {
                textarea.classList.remove('border-green-500');
            }, 1000);
        }
    }

    function toggleDetalles() {
        detallesExpandidos = !detallesExpandidos;
        const detalles = document.getElementById('detalles-adicionales');
        const toggleBtn = document.getElementById('toggle-detalles');
        const toggleIcon = document.getElementById('toggle-icon');
        const toggleText = document.getElementById('toggle-text');

        if (detallesExpandidos) {
            detalles.classList.remove('collapsed');
            detalles.classList.add('expanded');
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
            toggleText.textContent = textos[idiomaActual]['ocultar_detalles'];
        } else {
            detalles.classList.remove('expanded');
            detalles.classList.add('collapsed');
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
            toggleText.textContent = textos[idiomaActual]['ver_detalles'];
        }
    }

    function toggleScroll() {
        scrollExpandido = !scrollExpandido;
        const container = document.getElementById('strategies-container');
        const boton = document.getElementById('btn-scroll');

        if (scrollExpandido) {
            container.style.maxHeight = 'none';
            container.style.overflow = 'visible';
            boton.innerHTML = '<i class="fas fa-compress mr-2"></i><span id="btn-scroll-text">' + textos[idiomaActual][
                'contraer'
            ] + '</span>';
            boton.classList.remove('bg-gray-600');
            boton.classList.add('bg-purple-600');
        } else {
            container.style.maxHeight = '600px';
            container.style.overflow = 'auto';
            boton.innerHTML = '<i class="fas fa-expand mr-2"></i><span id="btn-scroll-text">' + textos[idiomaActual][
                'ver_todo'
            ] + '</span>';
            boton.classList.remove('bg-purple-600');
            boton.classList.add('bg-gray-600');
        }
    }

    function mostrarComentario(categoria) {
        estrategiaActual = categoria;
        document.getElementById('modal-titulo').innerHTML =
            '<i class="fas fa-comment-dots mr-2"></i>' + textos[idiomaActual]['agregar_comentario'] +
            '<br><span class="text-sm font-normal text-gray-600">' + categoria + '</span>';
        document.getElementById('modal-comentario').classList.remove('hidden');
        document.getElementById('comentario-texto').focus();
    }

    function cerrarModalComentario() {
        document.getElementById('modal-comentario').classList.add('hidden');
        document.getElementById('comentario-texto').value = '';
    }

    // Funciones de Exportación
    function exportarPDF() {
        showNotification('Exportando a PDF... (Funcionalidad en desarrollo)', 'info');
    }

    function exportarExcel() {
        showNotification('Exportando a Excel... (Funcionalidad en desarrollo)', 'info');
    }

    function exportarWord() {
        showNotification('Exportando a Word... (Funcionalidad en desarrollo)', 'info');
    }

    function copiarEnlace() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification(textos[idiomaActual]['enlace_copiado'], 'success');
        }).catch(err => {
            console.error('Error al copiar enlace:', err);
            showNotification('Error al copiar enlace', 'error');
        });
    }

    // Funciones de Guardado
    function guardarEstrategias() {
        const formData = new FormData(document.getElementById('form-estrategias'));
        const msg = document.getElementById('mensaje-estado');

        msg.innerHTML = `
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg flex items-center">
                <i class="fas fa-sync-alt animate-spin mr-3"></i>
                <span>${textos[idiomaActual]['guardando']}</span>
            </div>
        `;
        msg.classList.remove('hidden');

        // Agregar ID del proyecto
        formData.append('proyecto_id', '<?php echo $id_proyecto; ?>');

        fetch('../controllers/guardar_estrategias.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    msg.innerHTML = `
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span>${textos[idiomaActual]['cambios_guardados']}</span>
                    </div>
                `;
                    setTimeout(() => {
                        msg.classList.add('hidden');
                        location.reload();
                    }, 1500);
                } else {
                    msg.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ${textos[idiomaActual]['error_guardar']}: ${data.message || ''}
                    </div>
                `;
                }
            })
            .catch(error => {
                msg.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    ${textos[idiomaActual]['error_conexion']}
                </div>
            `;
                console.error('Error:', error);
            });
    }

    function guardarComentario() {
        const comentario = document.getElementById('comentario-texto').value.trim();
        if (!comentario) {
            showNotification('Por favor, escribe un comentario', 'warning');
            return;
        }

        // Simular guardado
        showNotification('Comentario guardado correctamente', 'success');
        setTimeout(() => {
            cerrarModalComentario();
        }, 1000);
    }

    // Funciones utilitarias
    function compartirResultados() {
        if (navigator.share) {
            navigator.share({
                title: textos[idiomaActual]['titulo'] + ' - ' +
                    '<?php echo htmlspecialchars($reporte['proyecto']['nombre']); ?>',
                text: textos[idiomaActual]['subtitulo'],
                url: window.location.href
            });
        } else {
            copiarEnlace();
        }
    }

    function toggleDarkMode() {
        darkMode = !darkMode;
        const body = document.getElementById('body');
        const toggleBtn = document.getElementById('dark-mode-toggle');

        if (darkMode) {
            body.classList.add('dark-mode');
            toggleBtn.innerHTML = '<i class="fas fa-sun mr-2"></i><span class="hidden sm:inline">' + textos[
                idiomaActual]['modo_claro'] + '</span>';
            toggleBtn.classList.remove('text-gray-600');
            toggleBtn.classList.add('text-yellow-400');
        } else {
            body.classList.remove('dark-mode');
            toggleBtn.innerHTML = '<i class="fas fa-moon mr-2"></i><span class="hidden sm:inline">' + textos[
                idiomaActual]['modo_oscuro'] + '</span>';
            toggleBtn.classList.remove('text-yellow-400');
            toggleBtn.classList.add('text-gray-600');
        }

        localStorage.setItem('darkMode', darkMode);
    }

    function showNotification(message, type = 'info') {
        // Crear notificación
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transform transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
        } text-white`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Remover después de 3 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar preferencia de modo oscuro
        const savedDarkMode = localStorage.getItem('darkMode') === 'true';
        if (savedDarkMode) {
            darkMode = true;
            toggleDarkMode();
        }

        // Agregar animaciones a los elementos
        const elements = document.querySelectorAll('.fade-in');
        elements.forEach((el, index) => {
            el.style.animationDelay = `${index * 0.1}s`;
        });

        // Manejar rating stars
        document.querySelectorAll('.rating-stars label').forEach(star => {
            star.addEventListener('click', function() {
                const stars = this.parentElement.querySelectorAll('label');
                const clickedIndex = Array.from(stars).indexOf(this);

                stars.forEach((s, index) => {
                    if (index <= clickedIndex) {
                        s.classList.add('text-yellow-400');
                        s.classList.remove('text-gray-300');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });

                showNotification('¡Gracias por tu valoración!', 'success');
            });
        });

        // Manejar dropdowns en móviles
        document.querySelectorAll('.dropdown button').forEach(button => {
            button.addEventListener('click', function(e) {
                if (window.innerWidth < 768) {
                    e.stopPropagation();
                    const content = this.parentElement.querySelector('.dropdown-content');
                    content.style.display = content.style.display === 'block' ? 'none' :
                        'block';
                }
            });
        });

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-content').forEach(content => {
                content.style.display = 'none';
            });
        });
    });

    // Variables globales para comentarios y ratings
    let estrategiaActual = '';
    let ratingSeleccionado = 0;
    const proyectoId = '<?php echo $id_proyecto; ?>';

    /**
     * Mostrar modal de comentario
     */
    function mostrarComentario(categoria) {
        estrategiaActual = categoria;
        document.getElementById('modal-titulo').innerHTML =
            '<i class="fas fa-comment-dots mr-2"></i>' + textos[idiomaActual]['agregar_comentario'] +
            '<br><span class="text-sm font-normal text-gray-600">' + categoria + '</span>';
        document.getElementById('modal-comentario').classList.remove('hidden');
        document.getElementById('modal-comentario').classList.add('flex');
        document.getElementById('comentario-texto').focus();

        // Cargar comentarios existentes
        cargarComentarios(categoria);
    }

    /**
     * Cerrar modal de comentario
     */
    function cerrarModalComentario() {
        document.getElementById('modal-comentario').classList.add('hidden');
        document.getElementById('modal-comentario').classList.remove('flex');
        document.getElementById('comentario-texto').value = '';
        ratingSeleccionado = 0;
        resetearEstrellas();
    }

    /**
     * Guardar comentario
     */
    async function guardarComentario() {
        const comentario = document.getElementById('comentario-texto').value.trim();

        if (!comentario) {
            showNotification('Por favor, escribe un comentario', 'warning');
            return;
        }

        if (comentario.length > 5000) {
            showNotification('El comentario es demasiado largo (máximo 5000 caracteres)', 'warning');
            return;
        }

        try {
            // Mostrar indicador de carga
            const btnGuardar = event.target;
            const textoOriginal = btnGuardar.innerHTML;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';
            btnGuardar.disabled = true;

            const formData = new FormData();
            formData.append('proyecto_id', proyectoId);
            formData.append('estrategia_nombre', estrategiaActual);
            formData.append('comentario', comentario);

            if (ratingSeleccionado > 0) {
                formData.append('rating', ratingSeleccionado);
            }

            const response = await fetch('../controllers/guardar_comentario.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Comentario guardado correctamente', 'success');
                document.getElementById('comentario-texto').value = '';
                ratingSeleccionado = 0;
                resetearEstrellas();

                // Recargar comentarios
                setTimeout(() => {
                    cargarComentarios(estrategiaActual);
                }, 500);
            } else {
                showNotification(data.message || 'Error al guardar el comentario', 'error');
            }

            // Restaurar botón
            btnGuardar.innerHTML = textoOriginal;
            btnGuardar.disabled = false;

        } catch (error) {
            console.error('Error al guardar comentario:', error);
            showNotification('Error de conexión al guardar el comentario', 'error');
        }
    }

    /**
     * Guardar rating de estrategia
     */
    async function guardarRating(estrategiaNombre, rating) {
        try {
            const formData = new FormData();
            formData.append('proyecto_id', proyectoId);
            formData.append('estrategia_nombre', estrategiaNombre);
            formData.append('rating', rating);

            const response = await fetch('../controllers/guardar_rating.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showNotification('¡Gracias por tu valoración!', 'success');

                // Actualizar visualización con estadísticas
                if (data.data && data.data.estadisticas) {
                    actualizarEstadisticasRating(estrategiaNombre, data.data.estadisticas);
                }
            } else {
                showNotification(data.message || 'Error al guardar la valoración', 'error');
            }

        } catch (error) {
            console.error('Error al guardar rating:', error);
            showNotification('Error de conexión al guardar la valoración', 'error');
        }
    }

    /**
     * Cargar comentarios de una estrategia
     */
    async function cargarComentarios(estrategiaNombre) {
        try {
            const response = await fetch(
                `../controllers/obtener_comentarios.php?proyecto_id=${proyectoId}&estrategia_nombre=${encodeURIComponent(estrategiaNombre)}`
            );

            const data = await response.json();

            if (data.success) {
                mostrarComentariosEnModal(data.data.comentarios, data.data.estadisticas);
            } else {
                console.error('Error al cargar comentarios:', data.message);
            }

        } catch (error) {
            console.error('Error al cargar comentarios:', error);
        }
    }

    /**
     * Mostrar comentarios en el modal
     */
    function mostrarComentariosEnModal(comentarios, estadisticas) {
        const contenedor = document.getElementById('lista-comentarios');
        if (!contenedor) return;

        // Limpiar contenedor
        contenedor.innerHTML = '';

        // Mostrar estadísticas si existen
        if (estadisticas && estadisticas.total_valoraciones > 0) {
            const statsHTML = `
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-2xl font-bold text-blue-600">${estadisticas.rating_promedio}</span>
                        <span class="text-gray-600 ml-1">/ 5</span>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        ${estadisticas.total_valoraciones} valoración${estadisticas.total_valoraciones !== 1 ? 'es' : ''}
                    </div>
                </div>
                <div class="flex items-center mt-2">
                    ${generarEstrellas(estadisticas.rating_promedio)}
                </div>
            </div>
        `;
            contenedor.innerHTML += statsHTML;
        }

        // Mostrar comentarios
        if (comentarios.length === 0) {
            contenedor.innerHTML += `
            <div class="text-center text-gray-500 py-4">
                <i class="fas fa-comments text-3xl mb-2 opacity-50"></i>
                <p>No hay comentarios todavía. ¡Sé el primero en comentar!</p>
            </div>
        `;
            return;
        }

        comentarios.forEach(comentario => {
            const comentarioHTML = `
            <div class="border-b pb-3 mb-3 last:border-b-0">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center">
                        <div class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${comentario.usuario_nombre}</p>
                            <p class="text-xs text-gray-500">${comentario.created_at_formatted}</p>
                        </div>
                    </div>
                    ${comentario.rating ? `
                        <div class="flex items-center">
                            ${generarEstrellas(comentario.rating)}
                        </div>
                    ` : ''}
                </div>
                <p class="text-gray-700 text-sm">${escapeHtml(comentario.comentario)}</p>
            </div>
        `;
            contenedor.innerHTML += comentarioHTML;
        });
    }

    /**
     * Generar HTML de estrellas para visualización
     */
    function generarEstrellas(rating) {
        const ratingRedondeado = Math.round(rating * 2) / 2; // Redondear a .5
        let estrellasHTML = '';

        for (let i = 1; i <= 5; i++) {
            if (i <= ratingRedondeado) {
                estrellasHTML += '<i class="fas fa-star text-yellow-400"></i>';
            } else if (i - 0.5 === ratingRedondeado) {
                estrellasHTML += '<i class="fas fa-star-half-alt text-yellow-400"></i>';
            } else {
                estrellasHTML += '<i class="far fa-star text-gray-300"></i>';
            }
        }

        return estrellasHTML;
    }

    /**
     * Actualizar estadísticas de rating en la interfaz
     */
    function actualizarEstadisticasRating(estrategiaNombre, estadisticas) {
        // Buscar el elemento de la estrategia y actualizar
        const estrategiaCard = document.querySelector(`[data-estrategia="${estrategiaNombre}"]`);
        if (estrategiaCard) {
            let statsElement = estrategiaCard.querySelector('.rating-stats');
            if (!statsElement) {
                statsElement = document.createElement('div');
                statsElement.className = 'rating-stats text-xs text-gray-600 mt-2';
                estrategiaCard.querySelector('.rating-stars').parentElement.appendChild(statsElement);
            }
            statsElement.innerHTML = `
            ${estadisticas.rating_promedio} / 5 (${estadisticas.total_valoraciones} valoración${estadisticas.total_valoraciones !== 1 ? 'es' : ''})
        `;
        }
    }

    /**
     * Resetear estrellas de rating
     */
    function resetearEstrellas() {
        document.querySelectorAll('#modal-rating-stars label').forEach(star => {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        });
    }

    /**
     * Escapar HTML para prevenir XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Inicializar funcionalidad de ratings en las estrellas
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar clicks en estrellas de rating de cada estrategia
        document.querySelectorAll('.rating-stars label').forEach(star => {
            star.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const container = this.closest('.rating-stars');
                const estrategiaCard = this.closest('.estrategia-card');
                const estrategiaNombre = estrategiaCard.querySelector('h4').textContent.trim();

                const stars = container.querySelectorAll('label');
                const clickedIndex = Array.from(stars).indexOf(this);
                const rating = clickedIndex + 1;

                // Actualizar visualización
                stars.forEach((s, index) => {
                    if (index <= clickedIndex) {
                        s.classList.add('text-yellow-400');
                        s.classList.remove('text-gray-300');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });

                // Guardar rating
                guardarRating(estrategiaNombre, rating);
            });
        });

        // Agregar contenedor de comentarios al modal si no existe
        const modalContent = document.getElementById('modal-comentario-content');
        if (modalContent && !document.getElementById('lista-comentarios')) {
            const comentariosSection = document.createElement('div');
            comentariosSection.className = 'border-t pt-4 mt-4 max-h-64 overflow-y-auto';
            comentariosSection.innerHTML = `
            <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-comments mr-2"></i>Comentarios anteriores
            </h4>
            <div id="lista-comentarios"></div>
        `;

            // Insertar antes de los botones de acción
            const modalBody = modalContent.querySelector('.p-6:last-child');
            modalBody.insertBefore(comentariosSection, modalBody.querySelector('.mt-4'));
        }

        // Agregar estrellas de rating al modal si no existen
        if (!document.getElementById('modal-rating-stars')) {
            const ratingSection = document.createElement('div');
            ratingSection.id = 'modal-rating-stars';
            ratingSection.className = 'rating-stars flex gap-1 mb-3';
            ratingSection.innerHTML = `
            <span class="text-sm text-gray-600 mr-2">Valorar:</span>
            ${[1,2,3,4,5].map(i => `
                <label class="text-gray-300 cursor-pointer hover:text-yellow-400 transition" data-rating="${i}">
                    <i class="fas fa-star text-xl"></i>
                </label>
            `).join('')}
        `;

            // Agregar event listeners a las estrellas del modal
            ratingSection.querySelectorAll('label').forEach((star, index) => {
                star.addEventListener('click', function() {
                    ratingSeleccionado = index + 1;

                    // Actualizar visualización
                    ratingSection.querySelectorAll('label').forEach((s, i) => {
                        if (i <= index) {
                            s.classList.add('text-yellow-400');
                            s.classList.remove('text-gray-300');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-gray-300');
                        }
                    });
                });
            });

            const textarea = document.getElementById('comentario-texto');
            textarea.parentElement.insertBefore(ratingSection, textarea);
        }
    });
    </script>
</body>

</html>