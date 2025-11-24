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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Proyectos</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>

<body class="bg-gray-100 h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="p-6 text-2xl font-bold border-b border-gray-700">
            Dashboard
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Proyectos</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Perfiles</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Organizaciones</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Configuración</a>
        </nav>
        <div class="p-4 border-t border-gray-700 space-y-2">
            <!-- Botón para abrir el modal -->
            <button onclick="openModalInstrucciones()"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-all duration-300">
                Instrucciones de uso
            </button>
            <button class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Salir</button>
        </div>
    </aside>

    <!-- Contenido principal -->
    <main class="flex-1 p-8 overflow-y-auto bg-gray-50">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Gestión de Proyectos</h1>

        <!-- Tarjeta principal -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Lista de Proyectos</h2>
                <button onclick="openModal()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    + Nuevo Proyecto
                </button>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-200 text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border p-2 text-center">ID</th>
                            <th class="border p-2 text-center">Nombre</th>
                            <th class="border p-2 text-center">Descripción</th>
                            <th class="border p-2 text-center">Dominio</th>
                            <th class="border p-2 text-center">Complejidad</th>
                            <th class="border p-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($proyectos) > 0): ?>
                        <?php foreach ($proyectos as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border p-2 text-center"><?= htmlspecialchars($p['id']) ?></td>
                            <td class="border p-2 text-center font-semibold text-gray-800">
                                <?= htmlspecialchars($p['nombre_proyecto']) ?>
                            </td>
                            <td class="border p-2 text-center text-gray-600">
                                <?= strlen($p['descripcion_proyecto']) > 60 
                                        ? htmlspecialchars(substr($p['descripcion_proyecto'], 0, 60)) . '...' 
                                        : htmlspecialchars($p['descripcion_proyecto']); ?>
                            </td>
                            <td class="border p-2 text-center text-gray-700">
                                <?= htmlspecialchars($p['dominio_cynefin']) ?>
                            </td>
                            <td class="border p-2 text-center text-gray-700">
                                <?= htmlspecialchars($p['complejidad_total']) ?>
                            </td>
                            <td class="border p-2 text-center space-x-2">
                                <a href="../views/resultados_caracterizacion.php?id=<?= $p['id'] ?>"
                                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">
                                    Ver
                                </a>
                                <a href="../controllers/eliminar_proyecto.php?id=<?= $p['id'] ?>"
                                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition"
                                    onclick="return confirm('¿Deseas eliminar este proyecto?');">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="border p-3 text-center text-gray-500">
                                No hay proyectos registrados.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>


    <!-- Modal de caracterización (3 pasos) con tabla dinámica de equipo -->
    <div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 overflow-y-auto max-h-[90vh] relative">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Caracterización del proyecto</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl"
                    aria-label="Cerrar">&times;</button>
            </div>

            <!-- Formulario multisección -->
            <form id="formCaracterizacion" class="space-y-6" action="../controllers/procesar_caracterizacion.php"
                method="POST" novalidate>

                <!-- Paso 1: Información del proyecto + Conformación del equipo -->
                <!-- Paso 1: Información del proyecto -->
                <div class="form-step" id="step1">
                    <h3 class="text-lg font-semibold mb-3">1. Información del proyecto</h3>

                    <div class="space-y-4">
                        <!-- Nombre del proyecto -->
                        <div>
                            <label class="block font-semibold text-gray-700">Nombre del proyecto:</label>
                            <input type="text" name="nombre_proyecto" class="w-full border rounded p-2"
                                placeholder="Ejemplo: Sistema de gestión académica" required>
                        </div>

                        <!-- Dominio del problema -->
                        <div>
                            <label class="block font-semibold text-gray-700">Dominio del problema:</label>
                            <textarea name="dominio_problema" class="w-full border rounded p-2" rows="2"
                                placeholder="Describe el área o contexto principal del problema..." required></textarea>
                        </div>

                        <!-- Breve descripción del proyecto -->
                        <div>
                            <label class="block font-semibold text-gray-700">Breve descripción del proyecto:</label>
                            <textarea name="descripcion_proyecto" class="w-full border rounded p-2" rows="2"
                                placeholder="Resumen general del proyecto..." required></textarea>
                        </div>

                        <!-- Tamaño estimado -->
                        <div>
                            <label class="block font-semibold text-gray-700">Tamaño estimado:</label>
                            <input type="text" name="tamano_estimado" class="w-full border rounded p-2"
                                placeholder="Ejemplo: pequeño, mediano o grande" required>
                        </div>

                        <!-- País del cliente -->
                        <div>
                            <label class="block font-semibold text-gray-700">País del cliente:</label>
                            <input type="text" name="pais" class="w-full border rounded p-2"
                                placeholder="Ejemplo: Colombia" required>
                        </div>

                        <!-- Conformación del equipo -->
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">Conformación del equipo:</label>

                            <!-- Tabla dinámica -->
                            <div class="border rounded bg-white">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-semibold">Cant.</th>
                                            <th class="px-3 py-2 text-left font-semibold">Perfil</th>
                                        </tr>
                                    </thead>
                                    <tbody id="equipoTbody" class="divide-y"></tbody>
                                </table>

                                <div class="p-3 flex items-center justify-between">
                                    <div class="text-xs text-gray-500">
                                        Agrega los roles y la cantidad por rol (Ej: 1 desarrollador, 2 testers).
                                    </div>
                                    <button type="button" onclick="addEquipoRow()"
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Agregar miembro
                                    </button>
                                </div>
                            </div>

                            <!-- Campo oculto con el JSON del equipo -->
                            <input type="hidden" name="equipo_json" id="equipo_json">
                        </div>
                    </div>
                </div>


                <!-- Paso 2: Triple restricción -->
                <div class="form-step hidden" id="step2">
                    <h3 class="text-lg font-semibold mb-3">2. Indique los factores fijos de la triple restricción</h3>

                    <div class="space-y-3">
                        <!-- Botón de información -->
                        <button type="button" onclick="toggleInfo()"
                            class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            ℹ️ Información sobre triple restricción
                        </button>

                        <!-- Sección de información (inicialmente oculta) -->
                        <div id="infoTriple"
                            class="mt-2 p-3 border border-gray-300 rounded bg-gray-50 hidden text-sm text-gray-700">
                            <p><strong>1. Tiempo fijo:</strong> Fecha de entrega comprometida, no puede cambiarse sin
                                costo extra.</p>
                            <p><strong>2. Alcance fijo:</strong> Alcance definido; cambios pueden solicitarse, pero no
                                alteran contrato principal.</p>
                            <p><strong>3. Costo fijo:</strong> Precio total fijado. Seleccione tipo de contrato:
                                <strong>Llave en mano</strong> o <strong>Time & Material</strong>.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="restricciones[]" value="Tiempo" class="mr-2">Tiempo
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="restricciones[]" value="Alcance" class="mr-2">Alcance
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="costoCheckbox" name="restricciones[]" value="Costo"
                                    class="mr-2" onchange="toggleCostoOpciones()">Costo
                            </label>
                        </div>

                        <!-- Opciones de Costo fijo (solo si se selecciona Costo) -->
                        <div id="costoOpciones" class="mt-2 hidden">
                            <label class="flex items-center">
                                <input type="radio" name="tipoCosto" value="Llave en mano" class="mr-2">Llave en mano
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipoCosto" value="Time & Material" class="mr-2">Time &
                                Material
                            </label>
                        </div>
                    </div>
                </div>



                <!-- Paso 3: Complejidad añadida -->
                <div class="form-step hidden" id="step3">
                    <h3 class="text-lg font-semibold mb-3">Complejidad añadida</h3>
                    <p class="text-gray-600 mb-4">Seleccione los factores que añaden complejidad al proyecto:</p>

                    <div class="space-y-4">
                        <div class="complexity-factor">
                            <div class="flex items-center mb-1">
                                <input type="checkbox" name="complejidad[]" value="Equipo de desarrollo" class="mr-2">
                                <span class="font-medium">Equipo de desarrollo</span>
                            </div>
                            <p class="text-sm text-gray-500 ml-6">Exigencias especiales requeridas para el equipo de
                                desarrollo y nivel de trabajo en equipo.</p>
                        </div>

                        <div class="complexity-factor">
                            <div class="flex items-center mb-1">
                                <input type="checkbox" name="complejidad[]" value="Restricción de tiempo" class="mr-2">
                                <span class="font-medium">Restricción de tiempo</span>
                            </div>
                            <p class="text-sm text-gray-500 ml-6">Además de ser fijo, el tiempo está muy ajustado.</p>
                        </div>

                        <div class="complexity-factor">
                            <div class="flex items-center mb-1">
                                <input type="checkbox" name="complejidad[]" value="Tamaño" class="mr-2">
                                <span class="font-medium">Tamaño</span>
                            </div>
                            <p class="text-sm text-gray-500 ml-6">Muchas personas en el proyecto o gran cantidad de
                                requisitos.</p>
                        </div>

                        <div class="complexity-factor">
                            <div class="flex items-center mb-1">
                                <input type="checkbox" name="complejidad[]" value="Desarrollo global" class="mr-2">
                                <span class="font-medium">Desarrollo global</span>
                            </div>
                            <p class="text-sm text-gray-500 ml-6">Existen distancias física, temporal o cultural entre
                                los miembros del equipo.</p>
                        </div>

                        <div class="complexity-factor">
                            <div class="flex items-center mb-1">
                                <input type="checkbox" name="complejidad[]" value="Criticidad del problema"
                                    class="mr-2">
                                <span class="font-medium">Criticidad del problema</span>
                            </div>
                            <p class="text-sm text-gray-500 ml-6">El dominio del problema es crítico: impacto en la
                                vida, la seguridad, grandes pérdidas de dinero, etc.</p>
                        </div>

                        <div class="complexity-factor">
                            <div class="flex items-center mb-1">
                                <input type="checkbox" name="complejidad[]" value="Poca experiencia" class="mr-2">
                                <span class="font-medium">Poca experiencia</span>
                            </div>
                            <p class="text-sm text-gray-500 ml-6">El equipo posee poca experiencia en el dominio del
                                problema, en las tecnologías a emplear o en el proceso y gestión del proyecto.</p>
                        </div>

                        <div class="complexity-factor">
                            <div class="flex items-center mb-1">
                                <input type="checkbox" name="complejidad[]" value="Requisitos variables" class="mr-2">
                                <span class="font-medium">Requisitos variables</span>
                            </div>
                            <p class="text-sm text-gray-500 ml-6">El cliente cambia los requisitos con alta frecuencia.
                            </p>
                        </div>

                        <div class="complexity-factor">
                            <div class="flex items-center mb-1">
                                <input type="checkbox" name="complejidad[]" value="Otras restricciones" class="mr-2">
                                <span class="font-medium">Otras restricciones</span>
                            </div>
                            <p class="text-sm text-gray-500 ml-6">Restricciones fuertes del negocio, legales, etc. u
                                otros factores de complejidad importantes.</p>
                        </div>
                    </div>
                </div>

                <!-- Botones de navegación -->
                <div class="flex justify-between mt-6">
                    <button type="button" id="btnPrev" onclick="changeStep(-1)"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 hidden">Anterior</button>
                    <button type="button" id="btnNext" onclick="changeStep(1)"
                        class="ml-auto px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Siguiente</button>
                    <button type="submit" id="btnSubmit"
                        class="ml-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 hidden">Enviar
                        caracterización</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Instrucciones -->
    <div id="modalInstrucciones"
        class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-8 relative overflow-hidden">

            <!-- Botón cerrar -->
            <button onclick="closeModalInstrucciones()"
                class="absolute top-3 right-4 text-gray-500 hover:text-gray-700 text-3xl font-bold">&times;</button>

            <h2 class="text-2xl font-bold mb-4 text-center text-blue-700">📘 Instrucciones para el uso de la herramienta
            </h2>

            <div class="overflow-y-auto max-h-[70vh] pr-4 text-gray-700 space-y-6">

                <p>
                    Esta herramienta ofrece una guía para la selección de estrategias, técnicas y herramientas para la
                    <strong>gestión ágil de proyectos</strong> en función de la complejidad técnica y ambiental que
                    presenten.
                </p>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">1️⃣ Caracterización del proyecto</h3>
                    <p>
                        Al hacer clic en <strong>“Nuevo proyecto”</strong>, se solicita completar información
                        descriptiva del
                        proyecto e información para determinar su complejidad. Debe indicar los factores fijos de la
                        triple
                        restricción y los factores de complejidad añadida del proyecto.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">2️⃣ Factores de la triple restricción</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Tiempo fijo:</strong> el proyecto tiene una fecha límite inamovible o sancionable.
                        </li>
                        <li><strong>Alcance fijo:</strong> debe cumplirse con la entrega completa, sin reducción de
                            funcionalidades.</li>
                        <li><strong>Costo fijo:</strong> existe un presupuesto cerrado o un equipo definido sin
                            posibilidad de ampliación.</li>
                    </ul>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">3️⃣ Factores de complejidad añadida</h3>
                    <ul class="list-disc list-inside space-y-1">
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

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">4️⃣ Resultados del análisis</h3>
                    <p>
                        Luego de hacer clic en <strong>“Enviar”</strong>, la herramienta caracterizará el proyecto
                        mostrando:
                    </p>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Tipo de contrato:</strong> el más adecuado según la complejidad.</li>
                        <li><strong>Tipo de acción:</strong> cómo debe actuar el líder del proyecto (inmediata,
                            analítica, experimental, etc.).</li>
                        <li><strong>Prácticas recomendadas:</strong> mejores prácticas o patrones sugeridos.</li>
                        <li><strong>Enfoque de gestión:</strong> predictivo, empírico o por flujo tenso.</li>
                        <li><strong>Modelo de ciclo de vida:</strong> más adecuado para la situación del proyecto.</li>
                        <li><strong>Acuerdos de trabajo:</strong> nivel de acuerdos necesarios con el cliente.</li>
                        <li><strong>Planificación:</strong> si debe ser completa o ajustarse progresivamente.</li>
                        <li><strong>Dinámicas para explotar y prevenir:</strong> estrategias para reducir o evitar la
                            complejidad.</li>
                        <li><strong>Enfoque ágil:</strong> método ágil más conveniente.</li>
                    </ul>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">5️⃣ Estrategias adicionales</h3>
                    <p>
                        En la sección <strong>“XXXXXXX”</strong>, el líder puede indicar estrategias, técnicas y
                        herramientas
                        adicionales o distintas a las sugeridas que hayan tenido resultados satisfactorios.
                    </p>
                </section>

                <section>
                    <h3 class="text-lg font-semibold text-blue-600 mb-2">6️⃣ Encuesta de satisfacción</h3>
                    <p>
                        Finalmente, en la sección <strong>“XXXXXX”</strong> se presenta una encuesta de satisfacción
                        basada
                        en la técnica <strong>SUS</strong> (System Usability Scale), con el fin de evaluar la utilidad y
                        precisión de esta guía.
                    </p>
                </section>
            </div>
        </div>
    </div>

    <script>
    // JavaScript CORREGIDO para el modal

    let currentStep = 1;

    // Inicializar el modal cuando se abre
    function openModal() {
        document.getElementById("modalForm").classList.remove("hidden");
        currentStep = 1;
        showStep(currentStep);
        // Agregar una fila por defecto al equipo
        if (document.querySelectorAll('#equipoTbody .equipo-row').length === 0) {
            addEquipoRow(1, "desarrollador");
        }
    }

    function closeModal() {
        document.getElementById("modalForm").classList.add("hidden");
        // Resetear el formulario
        document.getElementById("formCaracterizacion").reset();
        currentStep = 1;
    }

    // Mostrar paso actual
    function showStep(step) {
        document.querySelectorAll(".form-step").forEach((s, i) => {
            s.classList.toggle("hidden", i !== step - 1);
        });

        document.getElementById("btnPrev").classList.toggle("hidden", step === 1);
        document.getElementById("btnNext").classList.toggle("hidden", step === 3);
        document.getElementById("btnSubmit").classList.toggle("hidden", step !== 3);
    }

    // Cambiar entre pasos
    function changeStep(direction) {
        const newStep = currentStep + direction;

        // Validar campos requeridos antes de avanzar del paso 1
        if (direction === 1 && currentStep === 1) {
            const requiredFields = document.querySelectorAll('#step1 [required]');
            let valid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!valid) {
                alert('⚠️ Complete los campos requeridos antes de continuar');
                return;
            }
        }

        currentStep = Math.max(1, Math.min(3, newStep));
        showStep(currentStep);
    }

    /* ------------ Tabla dinámica del equipo ------------- */
    function addEquipoRow(cant = 1, perfil = "") {
        const tr = document.createElement("tr");
        tr.className = "equipo-row";
        tr.innerHTML = `
    <td class="px-3 py-2">
      <input type="number" min="1" step="1" class="equipo-cant-input w-20 border rounded p-1" value="${cant}">
    </td>
    <td class="px-3 py-2">
      <input type="text" class="equipo-perfil-input w-full border rounded p-1" value="${perfil}" placeholder="Ej: desarrollador">
    </td>
    <td class="px-3 py-2">
      <button type="button" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600" onclick="removeEquipoRow(this)">Eliminar</button>
    </td>
  `;

        tr.querySelectorAll("input").forEach(inp => inp.addEventListener("input", updateEquipoJSON));
        document.getElementById("equipoTbody").appendChild(tr);
        updateEquipoJSON();
    }

    function removeEquipoRow(btn) {
        const tr = btn.closest("tr");
        tr.remove();
        updateEquipoJSON();
    }

    function updateEquipoJSON() {
        const rows = document.querySelectorAll("#equipoTbody .equipo-row");
        const arr = [];

        rows.forEach(r => {
            const cant = parseInt(r.querySelector(".equipo-cant-input").value) || 0;
            const perfil = r.querySelector(".equipo-perfil-input").value.trim();
            if (perfil && cant > 0) {
                arr.push({
                    cantidad: cant,
                    perfil: perfil
                });
            }
        });

        document.getElementById("equipo_json").value = JSON.stringify(arr);
    }

    // Funciones de ayuda
    function toggleInfo() {
        document.getElementById("infoTriple").classList.toggle("hidden");
    }

    function toggleCostoOpciones() {
        const costoChecked = document.getElementById("costoCheckbox").checked;
        document.getElementById("costoOpciones").classList.toggle("hidden", !costoChecked);
    }

    function addEquipoRow() {
        const tbody = document.getElementById("equipoTbody");
        const tr = document.createElement("tr");

        tr.innerHTML = `
    <td class="px-3 py-2"><input type="number" min="1" class="cant w-full border rounded p-1" placeholder="Ej: 2"></td>
    <td class="px-3 py-2"><input type="text" class="perfil w-full border rounded p-1" placeholder="Ej: Desarrollador"></td>
  `;
        tbody.appendChild(tr);
    }

    // Antes de enviar el formulario, genera el JSON
    document.querySelector("form").addEventListener("submit", function(e) {
        const filas = document.querySelectorAll("#equipoTbody tr");
        const equipo = [];

        filas.forEach(fila => {
            const cantidad = fila.querySelector(".cant").value.trim();
            const perfil = fila.querySelector(".perfil").value.trim();
            if (cantidad && perfil) {
                equipo.push({
                    cantidad,
                    perfil
                });
            }
        });

        document.getElementById("equipo_json").value = JSON.stringify(equipo);
    });

    function openModalInstrucciones() {
        document.getElementById('modalInstrucciones').classList.remove('hidden');
    }

    function closeModalInstrucciones() {
        document.getElementById('modalInstrucciones').classList.add('hidden');
    }
    </script>
</body>

</html>