<?php
session_start();

if (!isset($_SESSION['reporte_caracterizacion'])) {
    header('Location: index.html');
    exit;
}

$reporte = $_SESSION['reporte_caracterizacion'];

// Validar subarreglos antes de acceder
$reporte['proyecto'] = $reporte['proyecto'] ?? [];
$reporte['proyecto']['nombre'] = $reporte['proyecto']['nombre'] ?? 'No especificado';
$reporte['proyecto']['tamano'] = $reporte['proyecto']['tamano'] ?? 'No especificado';
$reporte['proyecto']['pais'] = $reporte['proyecto']['pais'] ?? 'No especificado';
$reporte['proyecto']['dominio_problema'] = $reporte['proyecto']['dominio_problema'] ?? 'No especificado';
$reporte['proyecto']['descripcion'] = $reporte['proyecto']['descripcion'] ?? 'Sin descripción';
$reporte['proyecto']['equipo'] = $reporte['proyecto']['equipo'] ?? [];

$reporte['triple_restriccion'] = $reporte['triple_restriccion'] ?? ['tipo' => 'No definido', 'descripcion' => ''];
$reporte['complejidad'] = $reporte['complejidad'] ?? ['total' => 0, 'factores' => []];
$reporte['dominio_cynefin'] = $reporte['dominio_cynefin'] ?? 'No clasificado';
$reporte['estrategias'] = $reporte['estrategias'] ?? [];

$estrategias_mostrar = $_SESSION['estrategias_modificadas'] ?? $reporte['estrategias'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de caracterización</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="max-w-6xl mx-auto px-6 py-10">

        <!-- Encabezado -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-semibold text-blue-700">
                Caracterización del proyecto
            </h1>
            <p class="text-gray-500 mt-2">Resultados del análisis según el modelo Cynefin</p>
        </div>

        <!-- Información del Proyecto -->
        <section class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">1. Información del proyecto</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <span class="font-medium">Nombre del proyecto:</span>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($reporte['proyecto']['nombre']); ?>
                    </p>
                </div>
                <div>
                    <span class="font-medium">Tamaño estimado:</span>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($reporte['proyecto']['tamano']); ?>
                    </p>
                </div>
                <div>
                    <span class="font-medium">País del cliente:</span>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($reporte['proyecto']['pais']); ?>
                    </p>
                </div>
                <div>
                    <span class="font-medium">Dominio del problema:</span>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($reporte['proyecto']['dominio_problema']); ?>
                    </p>
                </div>
                <div class="md:col-span-2">
                    <span class="font-medium">Breve descripción:</span>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($reporte['proyecto']['descripcion']); ?>
                    </p>
                </div>
            </div>

            <!-- Conformación del equipo -->
            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Conformación del equipo</h3>
                <?php if (!empty($reporte['proyecto']['equipo'])): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full border mt-3">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Cantidad</th>
                                <th class="px-4 py-2 text-left">Perfil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reporte['proyecto']['equipo'])): ?>
                            <?php foreach ($reporte['proyecto']['equipo'] as $miembro): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($miembro['cantidad']) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($miembro['perfil']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center py-2 text-gray-500">No se registró equipo de
                                    desarrollo.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-gray-500 italic">No se ha registrado información del equipo.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Resumen general -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 p-5 rounded-lg border border-blue-100">
                <h3 class="text-blue-800 font-semibold mb-2">Triple restricción</h3>
                <p class="text-2xl font-semibold text-blue-600">
                    <?php echo htmlspecialchars($reporte['triple_restriccion']['tipo']); ?>
                </p>
                <p class="text-sm text-blue-700 mt-1">
                    <?php echo htmlspecialchars($reporte['triple_restriccion']['descripcion']); ?>
                </p>
            </div>

            <div class="bg-green-50 p-5 rounded-lg border border-green-100">
                <h3 class="text-green-800 font-semibold mb-2">Complejidad añadida</h3>
                <p class="text-2xl font-semibold text-green-600">
                    <?php echo htmlspecialchars($reporte['complejidad']['total']); ?> factores
                </p>
                <p class="text-sm text-green-700 mt-1">Factores identificados</p>
            </div>

            <div class="bg-purple-50 p-5 rounded-lg border border-purple-100">
                <h3 class="text-purple-800 font-semibold mb-2">Dominio Cynefin</h3>
                <p class="text-2xl font-semibold text-purple-600">
                    <?php echo htmlspecialchars($reporte['dominio_cynefin']); ?>
                </p>
                <p class="text-sm text-purple-700 mt-1">Contexto del proyecto</p>
            </div>
        </section>

        <!-- Factores de complejidad -->
        <?php if (!empty($reporte['complejidad']['factores'])): ?>
        <section class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Factores de complejidad identificados</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <?php foreach ($reporte['complejidad']['factores'] as $factor): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-yellow-800">
                    • <?php echo htmlspecialchars($factor); ?>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Estrategias -->
        <section class="bg-white shadow rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Estrategias recomendadas</h2>
                <button onclick="toggleEdicion()" id="btn-editar"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    ✏️ Editar
                </button>
            </div>

            <!-- Visual -->
            <div id="modo-visualizacion" class="space-y-4">
                <?php foreach ($estrategias_mostrar as $categoria => $valor): ?>
                <div class="border-l-4 border-blue-500 pl-4">
                    <h4 class="font-medium text-gray-700"><?php echo htmlspecialchars($categoria); ?></h4>
                    <p class="text-gray-600"><?php echo htmlspecialchars($valor); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Edición -->
            <div id="modo-edicion" class="hidden space-y-4">
                <form id="form-estrategias" class="space-y-4">
                    <?php foreach ($estrategias_mostrar as $categoria => $valor): ?>
                    <div class="border-l-4 border-green-500 pl-4">
                        <label class="block font-medium text-gray-700 mb-1">
                            <?php echo htmlspecialchars($categoria); ?>
                        </label>
                        <textarea name="<?php echo htmlspecialchars($categoria); ?>"
                            class="w-full border border-gray-300 rounded p-2 text-gray-600"
                            rows="2"><?php echo htmlspecialchars($valor); ?></textarea>
                    </div>
                    <?php endforeach; ?>

                    <div class="flex gap-3">
                        <button type="button" onclick="guardarEstrategias()"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            💾 Guardar
                        </button>
                        <button type="button" onclick="cancelarEdicion()"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <div id="mensaje-estado" class="mt-4 hidden"></div>
        </section>

        <!-- Botones finales -->
        <div class="flex justify-between">
            <button onclick="window.history.back()"
                class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                Volver
            </button>
            <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Imprimir
            </button>
            <a href="index.html" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Nuevo proyecto
            </a>
        </div>
    </div>

    <script>
    function toggleEdicion() {
        const visual = document.getElementById('modo-visualizacion');
        const edicion = document.getElementById('modo-edicion');
        const boton = document.getElementById('btn-editar');
        const editando = !edicion.classList.contains('hidden');
        visual.classList.toggle('hidden', !editando);
        edicion.classList.toggle('hidden', editando);
        boton.textContent = editando ? '✏️ Editar' : '👀 Ver estrategias';
    }

    function cancelarEdicion() {
        document.getElementById('modo-edicion').classList.add('hidden');
        document.getElementById('modo-visualizacion').classList.remove('hidden');
        document.getElementById('btn-editar').textContent = '✏️ Editar';
        document.getElementById('form-estrategias').reset();
    }

    function guardarEstrategias() {
        const formData = new FormData(document.getElementById('form-estrategias'));
        const msg = document.getElementById('mensaje-estado');

        msg.innerHTML = '<div class="bg-blue-100 text-blue-700 p-3 rounded">Guardando cambios...</div>';
        msg.classList.remove('hidden');

        fetch('guardar_estrategias.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                msg.innerHTML = data.success ?
                    '<div class="bg-green-100 text-green-700 p-3 rounded">Cambios guardados correctamente</div>' :
                    '<div class="bg-red-100 text-red-700 p-3 rounded">Error: ' + data.message + '</div>';
                setTimeout(() => msg.classList.add('hidden'), 2000);
            })
            .catch(() => {
                msg.innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded">Error al guardar</div>';
            });
    }
    </script>
</body>

</html>