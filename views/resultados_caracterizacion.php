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
        'complejidad' => 'Complejidad',
        'factores' => 'factores',
        'dominio_cynefin' => 'Dominio Cynefin',
        'contexto_proyecto' => 'Contexto del proyecto',
        'informacion_proyecto' => 'Información del Proyecto',
        'nombre_proyecto' => 'Nombre del Proyecto',
        'tamano_estimado' => 'Tamaño Estimado',
        'horas' => 'horas',
        'pais_cliente' => 'País del Cliente',
        'dominio_problema' => 'Dominio del Problema',
        'descripcion' => 'Descripción',
        'conformacion_equipo' => 'Conformación del Equipo',
        'perfil' => 'Perfil',
        'cantidad' => 'Cantidad',
        'distribucion' => 'Distribución',
        'total' => 'Total',
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
        'perfiles_equipo' => 'Perfiles del Equipo'
    ],
    'en' => [
        'titulo' => 'Characterization Results',
        'subtitulo' => 'Strategic analysis based on the Cynefin framework',
        'proyecto' => 'Project',
        'generado' => 'Generated',
        'triple_restriccion' => 'Triple Constraint',
        'complejidad' => 'Complexity',
        'factores' => 'factors',
        'dominio_cynefin' => 'Cynefin Domain',
        'contexto_proyecto' => 'Project context',
        'informacion_proyecto' => 'Project Information',
        'nombre_proyecto' => 'Project Name',
        'tamano_estimado' => 'Estimated Size',
        'horas' => 'hours',
        'pais_cliente' => 'Client Country',
        'dominio_problema' => 'Problem Domain',
        'descripcion' => 'Description',
        'conformacion_equipo' => 'Team Composition',
        'perfil' => 'Profile',
        'cantidad' => 'Quantity',
        'distribucion' => 'Distribution',
        'total' => 'Total',
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
        'perfiles_equipo' => 'Team Profiles'
    ]
];

$t = $textos[$idioma_actual];

// Verificar si hay datos de caracterización
if (!isset($_SESSION['reporte_caracterizacion'])) {
    header('Location: ../index.php?action=lider_home');
    exit;
}

$reporte = $_SESSION['reporte_caracterizacion'];

// Validar y sanitizar datos
$reporte['proyecto'] = $reporte['proyecto'] ?? [];
$reporte['proyecto']['nombre'] = $reporte['proyecto']['nombre'] ?? $t['no_especificado'];
$reporte['proyecto']['tamano'] = $reporte['proyecto']['tamano'] ?? $t['no_especificado'];
$reporte['proyecto']['pais'] = $reporte['proyecto']['pais'] ?? $t['no_especificado'];
$reporte['proyecto']['dominio_problema'] = $reporte['proyecto']['dominio_problema'] ?? $t['no_especificado'];
$reporte['proyecto']['descripcion'] = $reporte['proyecto']['descripcion'] ?? $t['sin_descripcion'];
$reporte['proyecto']['equipo'] = $reporte['proyecto']['equipo'] ?? [];

$reporte['triple_restriccion'] = $reporte['triple_restriccion'] ?? ['tipo' => $t['no_definido'], 'descripcion' => ''];
$reporte['complejidad'] = $reporte['complejidad'] ?? ['total' => 0, 'factores' => []];
$reporte['dominio_cynefin'] = $reporte['dominio_cynefin'] ?? $t['no_clasificado'];
$reporte['estrategias'] = $reporte['estrategias'] ?? [];

$estrategias_mostrar = $_SESSION['estrategias_modificadas'] ?? $reporte['estrategias'];
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

    .dark-mode .border-gray-200 {
        border-color: #4a5568;
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
                            <a href="?idioma=es&proyecto_id=<?php echo $_GET['proyecto_id'] ?? ''; ?>"
                                class="block px-4 py-2 hover:bg-gray-100 <?php echo $idioma_actual === 'es' ? 'bg-blue-50 text-blue-600' : ''; ?>">
                                <i class="fas fa-flag mr-2"></i>Español
                            </a>
                            <a href="?idioma=en&proyecto_id=<?php echo $_GET['proyecto_id'] ?? ''; ?>"
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
                </div>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                    <?php echo $t['subtitulo']; ?>
                </p>
            </div>
        </div>

        <!-- Resumen Ejecutivo -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 fade-in">
            <!-- Triple Restricción -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2"><?php echo $t['triple_restriccion']; ?></h3>
                        <p class="text-2xl font-bold">
                            <?php echo htmlspecialchars($reporte['triple_restriccion']['tipo']); ?></p>
                        <p class="text-blue-100 text-sm mt-1">
                            <?php echo htmlspecialchars($reporte['triple_restriccion']['descripcion']); ?>
                        </p>
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
                        <p class="text-green-100 text-sm mt-1"><?php echo $t['nivel_complejidad']; ?></p>
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
                <!-- Información del Proyecto -->
                <section class="bg-white rounded-2xl shadow-lg p-6 fade-in transition-colors duration-300"
                    id="project-info">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            <?php echo $t['informacion_proyecto']; ?>
                        </h2>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            ID: <?php echo htmlspecialchars($_SESSION['proyecto_id'] ?? 'N/A'); ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"><?php echo $t['nombre_proyecto']; ?></label>
                                <p class="text-gray-900 font-semibold">
                                    <?php echo htmlspecialchars($reporte['proyecto']['nombre']); ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"><?php echo $t['tamano_estimado']; ?></label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['tamano']); ?>
                                    <?php echo $t['horas']; ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"><?php echo $t['pais_cliente']; ?></label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['pais']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"><?php echo $t['dominio_problema']; ?></label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['dominio_problema']); ?>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"><?php echo $t['descripcion']; ?></label>
                                <p class="text-gray-900">
                                    <?php echo htmlspecialchars($reporte['proyecto']['descripcion']); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Conformación del Equipo -->
                    <?php if (!empty($reporte['proyecto']['equipo'])): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-users text-green-600 mr-2"></i>
                            <?php echo $t['conformacion_equipo']; ?>
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">
                                            <?php echo $t['perfil']; ?>
                                        </th>
                                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">
                                            <?php echo $t['cantidad']; ?>
                                        </th>
                                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                            <?php echo $t['distribucion']; ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php 
                                    $total_miembros = array_sum(array_column($reporte['proyecto']['equipo'], 'cantidad'));
                                    foreach ($reporte['proyecto']['equipo'] as $miembro): 
                                        $porcentaje = $total_miembros > 0 ? ($miembro['cantidad'] / $total_miembros) * 100 : 0;
                                    ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            <i class="fas fa-user-tie text-blue-500 mr-2"></i>
                                            <?= htmlspecialchars($miembro['perfil']) ?>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-600">
                                            <?= htmlspecialchars($miembro['cantidad']) ?>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-600">
                                            <div class="flex items-center justify-end">
                                                <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-blue-600 h-2 rounded-full"
                                                        style="width: <?= $porcentaje ?>%"></div>
                                                </div>
                                                <span class="text-xs"><?= round($porcentaje) ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="bg-gray-50 font-semibold">
                                        <td class="px-4 py-3 text-sm"><?php echo $t['total']; ?></td>
                                        <td class="px-4 py-3 text-center text-sm"><?= $total_miembros ?></td>
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
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                        <?php echo $t['factores_complejidad']; ?>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($reporte['complejidad']['factores'] as $index => $factor): ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start">
                            <div
                                class="bg-yellow-100 text-yellow-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="font-bold text-sm"><?= $index + 1 ?></span>
                            </div>
                            <div>
                                <p class="text-yellow-800 font-medium">
                                    <?php echo htmlspecialchars($factor); ?>
                                </p>
                                <p class="text-yellow-600 text-sm mt-1">
                                    <?php echo $t['factor_complejidad']; ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <i class="fas fa-lightbulb text-blue-600 text-xl mr-3"></i>
                            <div>
                                <p class="text-blue-800 font-medium">
                                    <?php echo $t['recomendacion']; ?>
                                </p>
                                <p class="text-blue-600 text-sm">
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
                        </h2>
                        <div class="flex space-x-2 no-print">
                            <button onclick="toggleEdicion()" id="btn-editar"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center text-sm">
                                <i class="fas fa-edit mr-2"></i>
                                <span id="btn-editar-text"><?php echo $t['editar']; ?></span>
                            </button>
                        </div>
                    </div>

                    <!-- Modo Visualización -->
                    <div id="modo-visualizacion" class="space-y-4">
                        <?php foreach ($estrategias_mostrar as $categoria => $valor): ?>
                        <div
                            class="estrategia-card bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border-l-4 border-purple-500">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-gray-900 text-lg">
                                    <?php echo htmlspecialchars($categoria); ?>
                                </h4>
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                    <?php echo $t['recomendado']; ?>
                                </span>
                            </div>
                            <p class="text-gray-700 leading-relaxed">
                                <?php echo htmlspecialchars($valor); ?>
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="rating-stars text-sm">
                                    <span class="text-gray-500 mr-2"><?php echo $t['fue_util']; ?></span>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="text-yellow-400">
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
                                    <?php echo htmlspecialchars($categoria); ?>
                                </label>
                                <textarea name="<?php echo htmlspecialchars($categoria); ?>"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    rows="3"
                                    placeholder="<?php echo $t['escribe_comentario']; ?>"><?php echo htmlspecialchars($valor); ?></textarea>
                                <div class="mt-2 flex justify-between items-center">
                                    <span
                                        class="text-xs text-gray-500"><?php echo $t['estrategia_personalizable']; ?></span>
                                    <button type="button"
                                        onclick="restaurarEstrategia('<?php echo htmlspecialchars($categoria); ?>')"
                                        class="text-xs text-gray-600 hover:text-gray-800 flex items-center">
                                        <i class="fas fa-undo mr-1"></i>
                                        <?php echo $t['restaurar_original']; ?>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
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
                        </form>
                    </div>

                    <!-- Mensajes de Estado -->
                    <div id="mensaje-estado" class="mt-4 hidden"></div>
                </section>

                <!-- Acciones Rápidas -->
                <section class="bg-white rounded-2xl shadow-lg p-6 no-print transition-colors duration-300"
                    id="quick-actions">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
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
                class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i><?php echo $t['volver']; ?>
            </button>
            <div class="flex gap-3">
                <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i><?php echo $t['imprimir']; ?>
                </button>
                <a href="../index.php?action=lider_home"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
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
                    <?php echo $t['agregar_comentario']; ?>
                </h3>
            </div>
            <div class="p-6">
                <textarea id="comentario-texto" class="w-full border border-gray-300 rounded-lg p-3 h-32"
                    placeholder="<?php echo $t['escribe_comentario']; ?>"></textarea>
                <div class="mt-4 flex justify-end space-x-3">
                    <button onclick="cerrarModalComentario()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        <?php echo $t['cancelar']; ?>
                    </button>
                    <button onclick="guardarComentario()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <?php echo $t['guardar']; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Variables globales
    let estrategiaActual = '';
    let darkMode = false;
    const estrategiasOriginales = <?php echo json_encode($reporte['estrategias']); ?>;
    const textos = <?php echo json_encode($textos); ?>;
    const idiomaActual = '<?php echo $idioma_actual; ?>';

    // Funciones de UI
    function toggleEdicion() {
        const visual = document.getElementById('modo-visualizacion');
        const edicion = document.getElementById('modo-edicion');
        const boton = document.getElementById('btn-editar');
        const editando = !edicion.classList.contains('hidden');

        visual.classList.toggle('hidden', !editando);
        edicion.classList.toggle('hidden', editando);

        if (editando) {
            boton.innerHTML = '<i class="fas fa-edit mr-2"></i><span id="btn-editar-text">' + textos[idiomaActual][
                'editar'
            ] + '</span>';
        } else {
            boton.innerHTML = '<i class="fas fa-eye mr-2"></i><span id="btn-editar-text">' + textos[idiomaActual][
                'ver_estrategias'
            ] + '</span>';
        }
    }

    function cancelarEdicion() {
        document.getElementById('modo-edicion').classList.add('hidden');
        document.getElementById('modo-visualizacion').classList.remove('hidden');
        document.getElementById('btn-editar').innerHTML =
            '<i class="fas fa-edit mr-2"></i><span id="btn-editar-text">' + textos[idiomaActual]['editar'] + '</span>';
        document.getElementById('form-estrategias').reset();
    }

    function restaurarEstrategia(categoria) {
        const textarea = document.querySelector(`textarea[name="${categoria}"]`);
        if (textarea && estrategiasOriginales[categoria]) {
            textarea.value = estrategiasOriginales[categoria];
        }
    }

    function mostrarComentario(categoria) {
        estrategiaActual = categoria;
        document.getElementById('modal-titulo').textContent = textos[idiomaActual]['agregar_comentario'] + ': ' +
            categoria;
        document.getElementById('modal-comentario').classList.remove('hidden');
        document.getElementById('comentario-texto').focus();
    }

    function cerrarModalComentario() {
        document.getElementById('modal-comentario').classList.add('hidden');
        document.getElementById('comentario-texto').value = '';
    }

    // Funciones de Exportación
    function exportarPDF() {
        alert('Exportando a PDF... (Funcionalidad en desarrollo)');
        // Aquí iría la lógica real para generar PDF
    }

    function exportarExcel() {
        alert('Exportando a Excel... (Funcionalidad en desarrollo)');
        // Aquí iría la lógica real para generar Excel
    }

    function exportarWord() {
        alert('Exportando a Word... (Funcionalidad en desarrollo)');
        // Aquí iría la lógica real para generar Word
    }

    function copiarEnlace() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert(textos[idiomaActual]['enlace_copiado']);
        }).catch(err => {
            console.error('Error al copiar enlace:', err);
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
                    }, 2000);
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
            alert('Por favor, escribe un comentario');
            return;
        }

        console.log('Guardando comentario para:', estrategiaActual, comentario);
        setTimeout(() => {
            cerrarModalComentario();
            alert('Comentario guardado correctamente');
        }, 500);
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
        } else {
            body.classList.remove('dark-mode');
            toggleBtn.innerHTML = '<i class="fas fa-moon mr-2"></i><span class="hidden sm:inline">' + textos[
                idiomaActual]['modo_oscuro'] + '</span>';
        }

        localStorage.setItem('darkMode', darkMode);
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

        // Manejar dropdowns
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            const button = dropdown.querySelector('button');
            const content = dropdown.querySelector('.dropdown-content');

            button.addEventListener('click', function(e) {
                e.stopPropagation();
                content.style.display = content.style.display === 'block' ? 'none' : 'block';
            });
        });

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-content').forEach(content => {
                content.style.display = 'none';
            });
        });
    });
    </script>
</body>

</html>