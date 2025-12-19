<?php
// ========================================
// CONTROLLERS/ProyectoController.php - VERSIÓN CORREGIDA
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

// ===== FUNCIÓN HELPER: OBTENER organizacion_id DESDE LA BD =====
function obtenerOrganizacionUsuario($db, $usuario_id) {
    try {
        $stmt = $db->prepare("SELECT organizacion_id FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && isset($usuario['organizacion_id'])) {
            return intval($usuario['organizacion_id']);
        }
        return null;
    } catch (PDOException $e) {
        error_log("Error al obtener organizacion_id: " . $e->getMessage());
        return null;
    }
}

// ===== OBTENER DATOS DEL USUARIO =====
$usuario_id = isset($_SESSION['usuario']['id']) ? intval($_SESSION['usuario']['id']) : 0;

// Consultar organizacion_id desde la BD (NO desde la sesión)
$organizacion_id = obtenerOrganizacionUsuario($db, $usuario_id);

// Validar que el usuario tenga organización asignada
if (!$organizacion_id || $organizacion_id <= 0) {
    $_SESSION['error'] = "Tu usuario no tiene una organización asignada. Por favor ejecuta: <code>UPDATE usuarios SET organizacion_id = 8 WHERE id = {$usuario_id};</code>";
    header('Location: ../views/home.php?seccion=proyectos');
    exit;
}

// Log para debug
error_log("ProyectoController - Usuario ID: {$usuario_id}, Organización ID (BD): {$organizacion_id}");

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
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'organizacion_id' => $organizacion_id,  // ← DESDE LA BD
                'usuario_id' => $usuario_id             // ← DE LA SESIÓN
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

            // Validar que el líder pertenezca a la misma organización
            $stmt = $db->prepare("SELECT id, organizacion_id FROM usuarios WHERE id = ? AND rol_id = 2");
            $stmt->execute([$datos['lider_proyecto_id']]);
            $lider = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$lider) {
                $_SESSION['error'] = 'El líder seleccionado no existe';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }
            
            if ($lider['organizacion_id'] != $organizacion_id) {
                $_SESSION['error'] = "El líder seleccionado no pertenece a tu organización. Líder org: {$lider['organizacion_id']}, Tu org: {$organizacion_id}";
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }

            // Log antes de crear
            error_log("Creando proyecto con datos: " . print_r($datos, true));

            $resultado = $proyecto->crear($datos);

            if ($resultado) {
                $_SESSION['success'] = "Proyecto '{$datos['nombre']}' creado exitosamente";
            } else {
                $_SESSION['error'] = 'Error al crear el proyecto. Revisa los logs del servidor.';
                error_log("Error al crear proyecto: " . print_r($datos, true));
            }

            header('Location: ../views/home.php?seccion=proyectos');
            exit;
        }
        break;

    case 'ver':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            $datos = $proyecto->obtenerPorId($id);

            // Validar pertenencia a la organización
            if ($datos && $datos['organizacion_id'] != $organizacion_id) {
                echo '<div class="text-center py-12">';
                echo '<div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">';
                echo '<i class="fas fa-lock text-4xl text-red-600"></i>';
                echo '</div>';
                echo '<p class="text-red-600 font-medium">No tienes permiso para ver este proyecto</p>';
                echo '</div>';
                exit;
            }

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

    <!-- Debug info -->
    <div class="bg-blue-50 border-l-4 border-blue-600 p-3 text-xs">
        <p class="text-gray-600">
            <strong>Debug:</strong> Org ID: <?= $datos['organizacion_id'] ?> |
            Creado por: Usuario #<?= $datos['usuario_id'] ?? 'N/A' ?>
        </p>
    </div>
</div>
<?php
            } else {
                echo '<div class="text-center py-12">';
                echo '<div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">';
                echo '<i class="fas fa-inbox text-4xl text-gray-300"></i>';
                echo '</div>';
                echo '<p class="text-gray-600 font-medium">Proyecto no encontrado</p>';
                echo '</div>';
            }
        }
        exit;

    case 'datos':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            $datos = $proyecto->obtenerPorId($id);

            header('Content-Type: application/json');
            
            // Validar pertenencia a la organización
            if ($datos && $datos['organizacion_id'] != $organizacion_id) {
                echo json_encode(['error' => 'No tienes permiso para acceder a este proyecto']);
            } elseif ($datos) {
                echo json_encode($datos);
            } else {
                echo json_encode(['error' => 'Proyecto no encontrado']);
            }
        }
        exit;

    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];

            // Verificar que el proyecto pertenece a la organización
            $proyecto_actual = $proyecto->obtenerPorId($id);
            if (!$proyecto_actual || $proyecto_actual['organizacion_id'] != $organizacion_id) {
                $_SESSION['error'] = 'No tienes permiso para editar este proyecto';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }
            
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'horas' => (int)$_POST['horas'],
                'estado' => $_POST['estado'] ?? 'pendiente',
                'lider_proyecto_id' => (int)$_POST['lider_proyecto_id'],
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'organizacion_id' => $organizacion_id,  // ← MANTENER LA ORGANIZACIÓN
                'usuario_id' => $usuario_id             // ← MANTENER EL USUARIO
            ];

            // Validaciones
            if (empty($datos['nombre'])) {
                $_SESSION['error'] = 'El nombre del proyecto es obligatorio';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }

            // Validar que el líder pertenezca a la misma organización
            $stmt = $db->prepare("SELECT id, organizacion_id FROM usuarios WHERE id = ? AND rol_id = 2");
            $stmt->execute([$datos['lider_proyecto_id']]);
            $lider = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$lider) {
                $_SESSION['error'] = 'El líder seleccionado no existe';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }
            
            if ($lider['organizacion_id'] != $organizacion_id) {
                $_SESSION['error'] = 'El líder seleccionado no pertenece a tu organización';
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
            
            // Verificar que el proyecto pertenece a la organización
            $proyecto_actual = $proyecto->obtenerPorId($id);
            if (!$proyecto_actual || $proyecto_actual['organizacion_id'] != $organizacion_id) {
                $_SESSION['error'] = 'No tienes permiso para eliminar este proyecto';
                header('Location: ../views/home.php?seccion=proyectos');
                exit;
            }
            
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