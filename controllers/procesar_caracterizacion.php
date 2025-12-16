<?php
// controllers/procesar_caracterizacion.php
// VERSIÓN CORREGIDA

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol_id'] ?? 0) != 2) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// CORREGIDO: Usar la misma clase Database que en lider_home.php
require_once __DIR__ . "/../config/db.php";
$database = new Database();
$conn = $database->getConnection();

// Verificar conexión
if (!($conn instanceof PDO)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

class AnalizadorProyecto {
    private $datos;
    
    public function __construct($postData) {
        $this->datos = $postData;
    }

    public function determinarTripleRestriccion() {
        $restricciones = isset($this->datos['restricciones']) ? $this->datos['restricciones'] : [];
        $count = count($restricciones);
        
        if ($count === 1) {
            if (in_array('Tiempo', $restricciones)) return 1;
            if (in_array('Alcance', $restricciones)) return 2;
            if (in_array('Costo', $restricciones)) return 3;
        } elseif ($count === 2) {
            return 4;
        } elseif ($count === 3) {
            return 5;
        }
        return 0;
    }

    public function contarComplejidad() {
        return isset($this->datos['complejidad']) ? count($this->datos['complejidad']) : 0;
    }

    public function determinarDominioCynefin() {
        $tipoRestriccion = $this->determinarTripleRestriccion();
        $totalComplejidad = $this->contarComplejidad();

        if ($tipoRestriccion == 1) {
            if ($totalComplejidad < 3) return 'Claro';
            if ($totalComplejidad <= 4) return 'Complicado';
            if ($totalComplejidad <= 6) return 'Complejo';
            return 'Caótico';
        } elseif ($tipoRestriccion == 2) {
            if ($totalComplejidad == 0) return 'Claro';
            if ($totalComplejidad <= 2) return 'Complicado';
            if ($totalComplejidad <= 4) return 'Complejo';
            return 'Caótico';
        } elseif ($tipoRestriccion == 3) {
            return ($totalComplejidad == 0) ? 'Complejo' : 'Caótico';
        } elseif ($tipoRestriccion == 4) {
            return 'Complejo';
        } elseif ($tipoRestriccion == 5) {
            return 'Caótico';
        }

        return 'No determinado';
    }

    public function generarEstrategias($dominio) {
        $estrategias = [
            'Claro' => [
                'Tipo de acción' => 'Evidente',
                'Prácticas' => 'Emplear la mejor práctica',
                'Enfoque de gestión de proyecto' => 'Secuencial o por flujo tenso',
                'Modelo de ciclo de vida' => 'Secuencial',
                'Acuerdos de trabajo' => 'Básicos, con fundamentos al inicio',
                'Planificación' => 'Planificación inicial',
                'Dinámicas a explotar' => 'Controlar que el contexto no cambie',
                'Dinámicas a prevenir' => 'No detectar cambios del contexto por comodidad',
                'Enfoque ágil' => 'Cascada o Kanban'
            ],
            'Complicado' => [
                'Tipo de acción' => 'Analizar para seleccionar la mejor práctica',
                'Prácticas' => 'Escoger la más adecuada entre buenas prácticas',
                'Enfoque de gestión de proyecto' => 'Por flujo tenso o secuencial',
                'Modelo de ciclo de vida' => 'Secuencial',
                'Acuerdos de trabajo' => 'Definidos con fundamentos al inicio',
                'Planificación' => 'Planificación inicial',
                'Dinámicas a explotar' => 'Obtener la mejor práctica',
                'Dinámicas a prevenir' => 'No cambiar prácticas cuando no dan resultado',
                'Enfoque ágil' => 'Kanban o cascada'
            ],
            'Complejo' => [
                'Tipo de acción' => 'Experimentar para descubrir prácticas útiles',
                'Prácticas' => 'Emergen según resultados',
                'Enfoque de gestión de proyecto' => 'Empírico',
                'Modelo de ciclo de vida' => 'Iterativo e incremental',
                'Acuerdos de trabajo' => 'Se revisan en retrospectivas',
                'Planificación' => 'A corto plazo, revisada con frecuencia',
                'Dinámicas a explotar' => 'Detectar patrones',
                'Dinámicas a prevenir' => 'Evitar aumento innecesario de complejidad',
                'Enfoque ágil' => 'Scrum o Kanban'
            ],
            'Caótico' => [
                'Tipo de acción' => 'Actuar de inmediato',
                'Prácticas' => 'Usar las que garanticen estabilidad',
                'Enfoque de gestión de proyecto' => 'Empírico',
                'Modelo de ciclo de vida' => 'Iterativo e incremental',
                'Acuerdos de trabajo' => 'Establecidos por el líder o coach ágil',
                'Planificación' => 'Basada en la experiencia del equipo',
                'Dinámicas a explotar' => 'Restablecer orden',
                'Dinámicas a prevenir' => 'Permanecer demasiado tiempo en caos',
                'Enfoque ágil' => 'Scrum con prácticas adaptativas'
            ]
        ];
        return $estrategias[$dominio] ?? $estrategias['Complejo'];
    }

    private function obtenerDescripcionRestriccion($tipo) {
        $descripciones = [
            1 => 'Solo tiempo fijo',
            2 => 'Solo alcance fijo',
            3 => 'Solo costo fijo',
            4 => 'Dos factores fijos',
            5 => 'Tres factores fijos',
            0 => 'Sin restricciones fijas'
        ];
        return $descripciones[$tipo] ?? 'No determinado';
    }

    public function generarReporte() {
        $tripleRestriccion = $this->determinarTripleRestriccion();
        $complejidad = $this->contarComplejidad();
        $dominio = $this->determinarDominioCynefin();
        $estrategias = $this->generarEstrategias($dominio);

        return [
            'triple_restriccion' => [
                'tipo' => $tripleRestriccion,
                'descripcion' => $this->obtenerDescripcionRestriccion($tripleRestriccion),
                'factores' => $this->datos['restricciones'] ?? []
            ],
            'complejidad' => [
                'total' => $complejidad,
                'factores' => $this->datos['complejidad'] ?? []
            ],
            'dominio_cynefin' => $dominio,
            'estrategias' => $estrategias
        ];
    }
}

// PROCESAR Y GUARDAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Validar datos requeridos
    if (!isset($_POST['proyecto_id']) || empty($_POST['proyecto_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de proyecto requerido']);
        exit;
    }
    
    if (!isset($_POST['restricciones']) || empty($_POST['restricciones'])) {
        echo json_encode(['success' => false, 'message' => 'Debe seleccionar al menos una restricción']);
        exit;
    }
    
    if (!isset($_POST['complejidad']) || empty($_POST['complejidad'])) {
        echo json_encode(['success' => false, 'message' => 'Debe seleccionar al menos un factor de complejidad']);
        exit;
    }
    
    try {
        $analizador = new AnalizadorProyecto($_POST);
        $reporte = $analizador->generarReporte();
        
        $proyecto_id = (int)$_POST['proyecto_id'];
        $usuario_id = $_SESSION['usuario']['id'];
        
        // Verificar que el proyecto pertenece al usuario
        $sql_proyecto = "SELECT nombre, descripcion FROM proyectos WHERE id = :id AND lider_proyecto_id = :lider_id";
        $stmt_proyecto = $conn->prepare($sql_proyecto);
        $stmt_proyecto->execute([
            'id' => $proyecto_id,
            'lider_id' => $usuario_id
        ]);
        
        $proyecto = $stmt_proyecto->fetch(PDO::FETCH_ASSOC);
        
        if (!$proyecto) {
            echo json_encode(['success' => false, 'message' => 'Proyecto no encontrado o no autorizado']);
            exit;
        }
        
        // Verificar si ya existe caracterización
        $sql_check = "SELECT id FROM caracterizaciones WHERE proyecto_id = :proyecto_id";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute(['proyecto_id' => $proyecto_id]);
        $existe_caracterizacion = $stmt_check->fetch();
        
        // Preparar datos
        $restricciones_json = json_encode($_POST['restricciones']);
        $complejidad_json = json_encode($_POST['complejidad']);
        $estrategias_json = json_encode($reporte['estrategias']);
        $tipo_restriccion = $reporte['triple_restriccion']['tipo'];
        $dominio_cynefin = $reporte['dominio_cynefin'];
        
        if ($existe_caracterizacion) {
            // Actualizar
            $sql = "UPDATE caracterizaciones SET 
                    restricciones_json = :restricciones_json,
                    tipo_restriccion = :tipo_restriccion,
                    complejidad_json = :complejidad_json,
                    dominio_cynefin = :dominio_cynefin,
                    estrategias_json = :estrategias_json,
                    estado = 'completado',
                    usuario_id = :usuario_id,
                    updated_at = NOW()
                    WHERE proyecto_id = :proyecto_id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':restricciones_json' => $restricciones_json,
                ':tipo_restriccion' => $tipo_restriccion,
                ':complejidad_json' => $complejidad_json,
                ':dominio_cynefin' => $dominio_cynefin,
                ':estrategias_json' => $estrategias_json,
                ':usuario_id' => $usuario_id,
                ':proyecto_id' => $proyecto_id
            ]);
            
            $accion = 'actualizada';
        } else {
            // Insertar
            $sql = "INSERT INTO caracterizaciones 
                    (proyecto_id, restricciones_json, tipo_restriccion, complejidad_json, 
                     dominio_cynefin, estrategias_json, estado, usuario_id, created_at) 
                    VALUES (:proyecto_id, :restricciones_json, :tipo_restriccion, :complejidad_json,
                            :dominio_cynefin, :estrategias_json, 'completado', :usuario_id, NOW())";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':proyecto_id' => $proyecto_id,
                ':restricciones_json' => $restricciones_json,
                ':tipo_restriccion' => $tipo_restriccion,
                ':complejidad_json' => $complejidad_json,
                ':dominio_cynefin' => $dominio_cynefin,
                ':estrategias_json' => $estrategias_json,
                ':usuario_id' => $usuario_id
            ]);
            
            $accion = 'creada';
        }
        
        // Guardar en sesión
        $_SESSION['reporte_caracterizacion'] = [
            'proyecto' => [
                'id' => $proyecto_id,
                'nombre' => $proyecto['nombre'],
                'descripcion' => $proyecto['descripcion']
            ],
            'triple_restriccion' => $reporte['triple_restriccion'],
            'complejidad' => $reporte['complejidad'],
            'dominio_cynefin' => $reporte['dominio_cynefin'],
            'estrategias' => $reporte['estrategias']
        ];
        
        $_SESSION['proyecto_id'] = $proyecto_id;
        $_SESSION['success'] = "Caracterización $accion exitosamente";
        
        echo json_encode([
            'success' => true,
            'message' => "Caracterización $accion exitosamente",
            'redirect' => 'views/resultados_caracterizacion.php?proyecto_id=' . $proyecto_id
        ]);
        
    } catch (PDOException $e) {
        error_log("Error DB en procesar_caracterizacion: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error al guardar: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        error_log("Error en procesar_caracterizacion: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error al procesar: ' . $e->getMessage()
        ]);
    }
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}
?>