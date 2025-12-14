<?php
// ========================================
// CONTROLLERS/ProyectoController.php
// ========================================

session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php?action=login_view');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Proyecto.php';

$database = new Database();
$db = $database->getConnection();
$proyecto = new Proyecto($db);

$action = $_GET['action'] ?? $_POST['action'] ?? 'listar';

switch ($action) {
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'horas' => (int)$_POST['horas'],
                'estado' => $_POST['estado'] ?? 'pendiente',
                'lider_proyecto_id' => (int)$_POST['lider_proyecto_id'],
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null
            ];

            // Validaciones
            if (empty($datos['nombre'])) {
                $_SESSION['error'] = 'El nombre del proyecto es obligatorio';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }

            if ($datos['horas'] <= 0) {
                $_SESSION['error'] = 'Las horas deben ser mayor a 0';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }

            if ($datos['lider_proyecto_id'] <= 0) {
                $_SESSION['error'] = 'Debe asignar un líder al proyecto';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }

            $resultado = $proyecto->crear($datos);

            if ($resultado) {
                $_SESSION['success'] = "Proyecto '{$datos['nombre']}' creado exitosamente";
            } else {
                $_SESSION['error'] = 'Error al crear el proyecto';
            }

            header('Location: ../views/home.php?seccion=proyectos');
            exit;
        }
        break;

    case 'ver':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $datos = $proyecto->obtenerPorId($id);

            if ($datos) {
                // Generar HTML para el modal
                $estado_config = [
                    'activo' => ['color' => 'bg-green-100 text-green-800 border-green-300', 'icon' => 'fa-check-circle'],
                    'pendiente' => ['color' => 'bg-yellow-100 text-yellow-800 border-yellow-300', 'icon' => 'fa-clock'],
                    'finalizado' => ['color' => 'bg-blue-100 text-blue-800 border-blue-300', 'icon' => 'fa-flag-checkered']
                ];
                $config = $estado_config[$datos['estado']] ?? $estado_config['pendiente'];
                ?>
<div class="space-y-4">
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg border-l-4 border-blue-600">
        <h3 class="text-2xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($datos['nombre']) ?></h3>
        <p class="text-sm text-gray-600">ID: #<?= $datos['id'] ?> | Creado:
            <?= date('d/m/Y', strtotime($datos['created_at'])) ?></p>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600 font-medium mb-1">Estado</p>
            <span class="<?= $config['color'] ?> px-4 py-2 text-sm rounded-full border inline-flex items-center">
                <i class="fas <?= $config['icon'] ?> mr-2"></i>
                <?= ucfirst($datos['estado']) ?>
            </span>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600 font-medium mb-1">Horas Estimadas</p>
            <p class="text-2xl font-bold text-gray-800"><?= $datos['horas'] ?>h</p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600 font-medium mb-1">Líder del Proyecto</p>
            <p class="font-semibold text-gray-800"><?= htmlspecialchars($datos['nombre_lider'] ?? 'No asignado') ?></p>
            <?php if (!empty($datos['email_lider'])): ?>
            <p class="text-sm text-gray-600"><?= htmlspecialchars($datos['email_lider']) ?></p>
            <?php endif; ?>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600 font-medium mb-1">Fechas</p>
            <p class="text-sm text-gray-800">
                <i class="fas fa-calendar-alt mr-1 text-green-600"></i>
                Inicio: <?= $datos['fecha_inicio'] ? date('d/m/Y', strtotime($datos['fecha_inicio'])) : 'No definida' ?>
            </p>
            <p class="text-sm text-gray-800 mt-1">
                <i class="fas fa-calendar-check mr-1 text-blue-600"></i>
                Fin: <?= $datos['fecha_fin'] ? date('d/m/Y', strtotime($datos['fecha_fin'])) : 'No definida' ?>
            </p>
        </div>
    </div>

    <?php if (!empty($datos['descripcion'])): ?>
    <div class="bg-gray-50 p-4 rounded-lg">
        <p class="text-sm text-gray-600 font-medium mb-2">Descripción</p>
        <p class="text-gray-800"><?= nl2br(htmlspecialchars($datos['descripcion'])) ?></p>
    </div>
    <?php endif; ?>
</div>
<?php
            } else {
                echo '<p class="text-red-600 text-center py-4">Proyecto no encontrado</p>';
            }
        }
        exit;

    case 'datos':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $datos = $proyecto->obtenerPorId($id);

            header('Content-Type: application/json');
            if ($datos) {
                echo json_encode($datos);
            } else {
                echo json_encode(['error' => 'Proyecto no encontrado']);
            }
        }
        exit;

    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'horas' => (int)$_POST['horas'],
                'estado' => $_POST['estado'] ?? 'pendiente',
                'lider_proyecto_id' => (int)$_POST['lider_proyecto_id'],
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null
            ];

            // Validaciones
            if (empty($datos['nombre'])) {
                $_SESSION['error'] = 'El nombre del proyecto es obligatorio';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }

            $resultado = $proyecto->actualizar($id, $datos);

            if ($resultado) {
                $_SESSION['success'] = "Proyecto actualizado exitosamente";
            } else {
                $_SESSION['error'] = 'Error al actualizar el proyecto';
            }

            header('Location: ../views/home.php?seccion=proyectos');
            exit;
        }
        break;

    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            $resultado = $proyecto->eliminar($id);

            if ($resultado) {
                $_SESSION['success'] = 'Proyecto eliminado exitosamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar el proyecto';
            }

            header('Location: ../views/home.php?seccion=proyectos');
            exit;
        }
        break;

    default:
        header('Location: ../views/home.php?seccion=proyectos');
        exit;
}
?>