<?php
// ========================================
// CONTROLLERS/OrganizacionController.php (CORREGIDO)
// ========================================

session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php?action=login_view');
    exit;
}

// Habilitar todos los errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/OrganizacionModel.php';

// Crear conexión y modelo
$database = new Database();
$db = $database->getConnection();
$organizacionModel = new OrganizacionModel($db);

$action = $_GET['action'] ?? $_POST['action'] ?? 'ver';

switch ($action) {
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<pre>";
            echo "Datos recibidos:\n";
            print_r($_POST);
            echo "</pre>";
            
            // Verificar que tenemos ID
            if (!isset($_POST['id'])) {
                $_SESSION['error'] = 'ID no proporcionado';
                header('Location: ../views/home.php?seccion=organizacion');
                exit;
            }
            
            $id = (int)$_POST['id'];
            
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? '')
            ];

            // Validaciones
            if (empty($datos['nombre'])) {
                $_SESSION['error'] = 'El nombre de la organización es obligatorio';
                header('Location: ../views/home.php?seccion=organizacion');
                exit;
            }

            if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email inválido';
                header('Location: ../views/home.php?seccion=organizacion');
                exit;
            }

            echo "<pre>Datos a guardar:\n";
            print_r($datos);
            echo "ID: $id\n";
            echo "</pre>";

            // Verificar si la organización existe
            $organizacionExistente = $organizacionModel->obtenerPorId($id);
            
            echo "<pre>Organización existente:\n";
            print_r($organizacionExistente);
            echo "</pre>";
            
            if (!$organizacionExistente) {
                // Crear organización si no existe
                echo "<p>Creando nueva organización...</p>";
                $datos['usuario_admin_id'] = $_SESSION['usuario']['id'] ?? 1;
                
                $resultado = crearOrganizacionDirecto($db, $datos);
                
                echo "<p>Resultado creación: " . ($resultado ? 'Éxito' : 'Fallo') . "</p>";
                
                if ($resultado) {
                    $_SESSION['success'] = 'Organización creada exitosamente';
                } else {
                    $_SESSION['error'] = 'Error al crear la organización';
                }
            } else {
                // Actualizar organización existente
                echo "<p>Actualizando organización existente...</p>";
                $resultado = actualizarOrganizacionDirecto($db, $id, $datos);
                
                echo "<p>Resultado actualización: " . ($resultado ? 'Éxito' : 'Fallo') . "</p>";
                
                if ($resultado) {
                    $_SESSION['success'] = 'Organización actualizada exitosamente';
                } else {
                    $_SESSION['error'] = 'Error al actualizar la organización';
                    // Mostrar error específico
                    error_log("Error específico en actualización: " . print_r($db->errorInfo(), true));
                }
            }

            // Redirigir
            echo "<p>Redirigiendo...</p>";
            header('Location: ../views/home.php?seccion=organizacion');
            exit;
        }
        break;

    case 'ver':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $datos = $organizacionModel->obtenerPorId($id);

            if ($datos) {
                header('Content-Type: application/json');
                echo json_encode($datos);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Organización no encontrada']);
            }
        }
        exit;

    default:
        header('Location: ../views/home.php?seccion=organizacion');
        exit;
}

// ========================================
// FUNCIONES AUXILIARES (SIN DUPLICADOS)
// ========================================

function crearOrganizacionDirecto($db, $datos) {
    try {
        echo "<pre>Ejecutando creación directa...</pre>";
        
        // QUERY SIN updated_at - según tu estructura de tabla
        $query = "INSERT INTO organizaciones 
                 (nombre, email, telefono, direccion, descripcion, usuario_admin_id) 
                  VALUES (:nombre, :email, :telefono, :direccion, :descripcion, :usuario_admin_id)";
        
        echo "<pre>Query: $query</pre>";
        echo "<pre>Datos:\n";
        print_r($datos);
        echo "</pre>";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":nombre", $datos['nombre']);
        $stmt->bindParam(":email", $datos['email']);
        $stmt->bindParam(":telefono", $datos['telefono']);
        $stmt->bindParam(":direccion", $datos['direccion']);
        $stmt->bindParam(":descripcion", $datos['descripcion']);
        $stmt->bindParam(":usuario_admin_id", $datos['usuario_admin_id']);
        
        $result = $stmt->execute();
        
        echo "<pre>Resultado execute: " . ($result ? 'true' : 'false') . "</pre>";
        if (!$result) {
            echo "<pre>Error info: " . print_r($stmt->errorInfo(), true) . "</pre>";
            error_log("Error en creación: " . print_r($stmt->errorInfo(), true));
        }
        
        return $result;
    } catch (Exception $e) {
        echo "<pre>Excepción: " . $e->getMessage() . "</pre>";
        error_log("Error crearOrganizacionDirecto: " . $e->getMessage());
        return false;
    }
}

function actualizarOrganizacionDirecto($db, $id, $datos) {
    try {
        echo "<pre>Ejecutando actualización directa...</pre>";
        
        // QUERY SIN updated_at - según tu estructura de tabla
        $query = "UPDATE organizaciones SET 
                  nombre = :nombre, 
                  email = :email, 
                  telefono = :telefono, 
                  direccion = :direccion, 
                  descripcion = :descripcion
                  WHERE id = :id";
        
        echo "<pre>Query: $query</pre>";
        echo "<pre>ID: $id</pre>";
        echo "<pre>Datos:\n";
        print_r($datos);
        echo "</pre>";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":nombre", $datos['nombre']);
        $stmt->bindParam(":email", $datos['email']);
        $stmt->bindParam(":telefono", $datos['telefono']);
        $stmt->bindParam(":direccion", $datos['direccion']);
        $stmt->bindParam(":descripcion", $datos['descripcion']);
        $stmt->bindParam(":id", $id);
        
        $result = $stmt->execute();
        
        echo "<pre>Resultado execute: " . ($result ? 'true' : 'false') . "</pre>";
        if (!$result) {
            echo "<pre>Error info: " . print_r($stmt->errorInfo(), true) . "</pre>";
            error_log("Error en actualización: " . print_r($stmt->errorInfo(), true));
        }
        
        return $result;
    } catch (Exception $e) {
        echo "<pre>Excepción: " . $e->getMessage() . "</pre>";
        error_log("Error actualizarOrganizacionDirecto: " . $e->getMessage());
        return false;
    }
}
?>