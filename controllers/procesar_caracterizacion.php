<?php
// controllers/procesar_caracterizacion.php
// VERSIÓN ACTUALIZADA PARA LA ESTRUCTURA EXISTENTE DE LA TABLA

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['rol_id'] ?? 0) != 2) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// Usar la misma clase Database
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

// FUNCIÓN PARA PREPARAR JSON DEL EQUIPO
function prepararEquipoJson($equipo_perfil, $equipo_cantidad, $conn = null) {
    $equipo_array = [];
    
    if (is_array($equipo_perfil) && is_array($equipo_cantidad)) {
        for ($i = 0; $i < count($equipo_perfil); $i++) {
            $perfil_id = $equipo_perfil[$i];
            $cantidad = $equipo_cantidad[$i];
            
            if (!empty($perfil_id) && $cantidad > 0) {
                // Obtener nombre del perfil si tenemos conexión a BD
                $nombre_perfil = "Perfil #$perfil_id";
                if ($conn) {
                    try {
                        $sql = "SELECT nombre, descripcion FROM perfiles WHERE id = :id";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute(['id' => $perfil_id]);
                        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($perfil) {
                            $nombre_perfil = $perfil['nombre'];
                        }
                    } catch (Exception $e) {
                        error_log("Error al obtener perfil: " . $e->getMessage());
                    }
                }
                
                $equipo_array[] = [
                    'perfil_id' => $perfil_id,
                    'perfil_nombre' => $nombre_perfil,
                    'cantidad' => (int)$cantidad
                ];
            }
        }
    }
    
    return json_encode($equipo_array);
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
    
    if (!isset($_POST['dominio_id']) || empty($_POST['dominio_id'])) {
        echo json_encode(['success' => false, 'message' => 'Dominio del cliente requerido']);
        exit;
    }
    
    if (!isset($_POST['pais_id']) || empty($_POST['pais_id'])) {
        echo json_encode(['success' => false, 'message' => 'País del cliente requerido']);
        exit;
    }
    
    // Validar equipo (al menos un miembro)
    if ((!isset($_POST['equipo_perfil']) || empty($_POST['equipo_perfil'])) && 
        (!isset($_POST['equipo_perfil_custom']) || empty($_POST['equipo_perfil_custom']))) {
        echo json_encode(['success' => false, 'message' => 'Debe agregar al menos un miembro al equipo']);
        exit;
    }
    
    try {
        $analizador = new AnalizadorProyecto($_POST);
        $reporte = $analizador->generarReporte();
        
        $proyecto_id = (int)$_POST['proyecto_id'];
        $usuario_id = $_SESSION['usuario']['id'];
        $dominio_id = (int)$_POST['dominio_id'];
        $pais_id = (int)$_POST['pais_id'];
        $tipo_costo = isset($_POST['tipo_costo']) ? $_POST['tipo_costo'] : null;
        
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
        
        // 1. ACTUALIZAR PROYECTO CON DOMINIO Y PAÍS
        $sql_update_proyecto = "UPDATE proyectos SET 
                               dominio_id = :dominio_id, 
                               pais_id = :pais_id 
                               WHERE id = :proyecto_id";
        
        $stmt_update_proyecto = $conn->prepare($sql_update_proyecto);
        $stmt_update_proyecto->execute([
            'dominio_id' => $dominio_id,
            'pais_id' => $pais_id,
            'proyecto_id' => $proyecto_id
        ]);
        
        // 2. OBTENER NOMBRES DE DOMINIO Y PAÍS PARA GUARDAR EN CARACTERIZACIÓN
        $sql_datos = "SELECT 
            d.nombre as dominio_nombre,
            pa.nombre as pais_nombre
            FROM dominios d, paises pa
            WHERE d.id = :dominio_id AND pa.id = :pais_id";
        
        $stmt_datos = $conn->prepare($sql_datos);
        $stmt_datos->execute([
            'dominio_id' => $dominio_id,
            'pais_id' => $pais_id
        ]);
        $datos_proyecto = $stmt_datos->fetch(PDO::FETCH_ASSOC);
        
        // 3. PREPARAR DATOS PARA GUARDAR EN CARACTERIZACIÓN
        $restricciones_json = json_encode($_POST['restricciones']);
        $complejidad_json = json_encode($_POST['complejidad']);
        $estrategias_json = json_encode($reporte['estrategias']);
        $tipo_restriccion = $reporte['triple_restriccion']['tipo'];
        $dominio_cynefin = $reporte['dominio_cynefin'];
        
        // Preparar JSON del equipo
        $equipo_json = '';
        $nombres_perfiles = [];
        
        if (isset($_POST['equipo_perfil']) && isset($_POST['equipo_cantidad'])) {
            $equipo_json = prepararEquipoJson($_POST['equipo_perfil'], $_POST['equipo_cantidad'], $conn);
            
            // Obtener nombres de perfiles para la sesión
            foreach ($_POST['equipo_perfil'] as $perfil_id) {
                $sql_perfil = "SELECT nombre FROM perfiles WHERE id = :id";
                $stmt_perfil = $conn->prepare($sql_perfil);
                $stmt_perfil->execute(['id' => $perfil_id]);
                $perfil = $stmt_perfil->fetch(PDO::FETCH_ASSOC);
                if ($perfil) {
                    $nombres_perfiles[] = $perfil['nombre'];
                }
            }
        } elseif (isset($_POST['equipo_perfil_custom']) && isset($_POST['equipo_cantidad'])) {
            // Para perfiles personalizados, crear array simple
            $equipo_custom = [];
            for ($i = 0; $i < count($_POST['equipo_perfil_custom']); $i++) {
                if (!empty($_POST['equipo_perfil_custom'][$i])) {
                    $equipo_custom[] = [
                        'perfil_nombre' => $_POST['equipo_perfil_custom'][$i],
                        'cantidad' => (int)$_POST['equipo_cantidad'][$i]
                    ];
                    $nombres_perfiles[] = $_POST['equipo_perfil_custom'][$i];
                }
            }
            $equipo_json = json_encode($equipo_custom);
        }
        
        // 4. OBTENER NOMBRES DE FACTORES DE COMPLEJIDAD
        $nombres_complejidad = [];
        try {
            $ids_complejidad = array_map('intval', $_POST['complejidad']);
            if (!empty($ids_complejidad)) {
                $placeholders = implode(',', array_fill(0, count($ids_complejidad), '?'));
                $sql_factores = "SELECT nombre FROM caracteristicas WHERE id IN ($placeholders)";
                $stmt_factores = $conn->prepare($sql_factores);
                $stmt_factores->execute($ids_complejidad);
                $factores = $stmt_factores->fetchAll(PDO::FETCH_ASSOC);
                $nombres_complejidad = array_column($factores, 'nombre');
            }
        } catch (Exception $e) {
            $nombres_complejidad = $_POST['complejidad'];
        }
        
        // 5. GUARDAR O ACTUALIZAR CARACTERIZACIÓN
        $sql_check = "SELECT id FROM caracterizaciones WHERE proyecto_id = :proyecto_id";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute(['proyecto_id' => $proyecto_id]);
        $existe_caracterizacion = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($existe_caracterizacion) {
            // Actualizar caracterización existente
            $sql = "UPDATE caracterizaciones SET 
                    restricciones_json = :restricciones_json,
                    tipo_restriccion = :tipo_restriccion,
                    tipo_costo = :tipo_costo,
                    complejidad_json = :complejidad_json,
                    equipo_json = :equipo_json,
                    dominio_cynefin = :dominio_cynefin,
                    estrategias_json = :estrategias_json,
                    estado = 'completado',
                    usuario_id = :usuario_id,
                    dominio_proyecto = :dominio_proyecto,
                    pais_cliente = :pais_cliente,
                    updated_at = NOW()
                    WHERE proyecto_id = :proyecto_id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':restricciones_json' => $restricciones_json,
                ':tipo_restriccion' => $tipo_restriccion,
                ':tipo_costo' => $tipo_costo,
                ':complejidad_json' => $complejidad_json,
                ':equipo_json' => $equipo_json,
                ':dominio_cynefin' => $dominio_cynefin,
                ':estrategias_json' => $estrategias_json,
                ':usuario_id' => $usuario_id,
                ':dominio_proyecto' => $datos_proyecto['dominio_nombre'] ?? 'No definido',
                ':pais_cliente' => $datos_proyecto['pais_nombre'] ?? 'No definido',
                ':proyecto_id' => $proyecto_id
            ]);
            
            $caracterizacion_id = $existe_caracterizacion['id'];
            $accion = 'actualizada';
        } else {
            // Insertar nueva caracterización
            $sql = "INSERT INTO caracterizaciones 
                    (proyecto_id, restricciones_json, tipo_restriccion, tipo_costo, 
                     complejidad_json, equipo_json, dominio_cynefin, estrategias_json, 
                     estado, usuario_id, dominio_proyecto, pais_cliente, created_at) 
                    VALUES (:proyecto_id, :restricciones_json, :tipo_restriccion, :tipo_costo, 
                            :complejidad_json, :equipo_json, :dominio_cynefin, :estrategias_json, 
                            'completado', :usuario_id, :dominio_proyecto, :pais_cliente, NOW())";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':proyecto_id' => $proyecto_id,
                ':restricciones_json' => $restricciones_json,
                ':tipo_restriccion' => $tipo_restriccion,
                ':tipo_costo' => $tipo_costo,
                ':complejidad_json' => $complejidad_json,
                ':equipo_json' => $equipo_json,
                ':dominio_cynefin' => $dominio_cynefin,
                ':estrategias_json' => $estrategias_json,
                ':usuario_id' => $usuario_id,
                ':dominio_proyecto' => $datos_proyecto['dominio_nombre'] ?? 'No definido',
                ':pais_cliente' => $datos_proyecto['pais_nombre'] ?? 'No definido'
            ]);
            
            $caracterizacion_id = $conn->lastInsertId();
            $accion = 'creada';
        }
        
        // 6. GUARDAR EN SESIÓN PARA MOSTRAR EN RESULTADOS
        $_SESSION['reporte_caracterizacion'] = [
            'proyecto' => [
                'id' => $proyecto_id,
                'nombre' => $proyecto['nombre'],
                'descripcion' => $proyecto['descripcion'],
                'dominio_id' => $dominio_id,
                'dominio_nombre' => $datos_proyecto['dominio_nombre'] ?? 'No definido',
                'pais_id' => $pais_id,
                'pais_nombre' => $datos_proyecto['pais_nombre'] ?? 'No definido'
            ],
            'equipo' => json_decode($equipo_json, true) ?? [],
            'equipo_nombres' => $nombres_perfiles,
            'triple_restriccion' => [
                'tipo' => $reporte['triple_restriccion']['tipo'],
                'descripcion' => $reporte['triple_restriccion']['descripcion'],
                'factores' => $reporte['triple_restriccion']['factores'],
                'tipo_costo' => $tipo_costo
            ],
            'complejidad' => [
                'total' => $reporte['complejidad']['total'],
                'factores_ids' => $reporte['complejidad']['factores'],
                'factores_nombres' => $nombres_complejidad
            ],
            'dominio_cynefin' => $reporte['dominio_cynefin'],
            'estrategias' => $reporte['estrategias'],
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'caracterizacion_id' => $caracterizacion_id
        ];
        
        $_SESSION['proyecto_id'] = $proyecto_id;
        $_SESSION['success'] = "Caracterización $accion exitosamente";
        
        // Determinar URL de redirección según idioma
        $idioma = isset($_POST['idioma']) ? $_POST['idioma'] : 'es';
        $redirect_url = "views/resultados_caracterizacion.php?proyecto_id=$proyecto_id&idioma=$idioma";
        
        echo json_encode([
            'success' => true,
            'message' => "Caracterización $accion exitosamente",
            'redirect' => $redirect_url,
            'caracterizacion_id' => $caracterizacion_id
        ]);
        
    } catch (PDOException $e) {
        error_log("Error DB en procesar_caracterizacion: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error al guardar los datos: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        error_log("Error en procesar_caracterizacion: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error al procesar la caracterización: ' . $e->getMessage()
        ]);
    }
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}
?>