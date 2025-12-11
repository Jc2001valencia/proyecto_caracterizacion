<?php
// controllers/ProyectoController.php - ADAPTADO A TU ESTRUCTURA ACTUAL DE BD
require_once __DIR__ . '/../models/ProyectoModel.php';

class ProyectoController {
    private $proyectoModel;
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->proyectoModel = new ProyectoModel($db);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function verificarAutenticacion() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['error'] = "Debes iniciar sesión";
            header("Location: index.php?action=login_view");
            exit;
        }
    }
    
    /**
     * GUARDAR PROYECTO - ADAPTADO A TU BD ACTUAL
     */
    public function guardar() {
        $this->verificarAutenticacion();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido";
            header('Location: index.php?action=home');
            exit;
        }
        
        try {
            error_log("=== Iniciando guardado de proyecto");
            error_log("POST data: " . print_r($_POST, true));
            
            // 1. Validar datos mínimos
            if (empty($_POST['nombre'])) {
                throw new Exception("El nombre del proyecto es obligatorio");
            }
            
            // 2. ANÁLISIS CYNEFIN (si hay datos de caracterización)
            $reporte = $this->generarReporteCaracterizacion($_POST);
            
            error_log("=== Reporte generado: " . print_r($reporte, true));
            
            // 3. GUARDAR EN BASE DE DATOS (ADAPTADO A TU BD)
            $proyecto_id = $this->guardarProyectoAdaptado($reporte, $_POST);
            
            if (!$proyecto_id) {
                throw new Exception("Error al guardar el proyecto en la base de datos");
            }
            
            error_log("=== Proyecto guardado con ID: " . $proyecto_id);
            
            // 4. Guardar equipo si existe (como JSON)
            $this->guardarEquipoJson($proyecto_id, $reporte);
            
            // 5. Guardar complejidad si existe
            $this->guardarComplejidadJson($proyecto_id, $reporte);
            
            // 6. GUARDAR EN SESIÓN PARA RESULTADOS
            $_SESSION['reporte_caracterizacion'] = $reporte;
            $_SESSION['proyecto_id'] = $proyecto_id;
            $_SESSION['success'] = "Proyecto creado exitosamente";
            
            // 7. REDIRIGIR (dependiendo del contexto)
            if (isset($_POST['from_home']) && $_POST['from_home'] == '1') {
                // Viene del home.php (creación básica)
                header('Location: index.php?action=home&seccion=proyectos');
            } else {
                // Viene del formulario completo de caracterización
                header('Location: index.php?action=verResultados&id=' . $proyecto_id);
            }
            exit;
            
        } catch (Exception $e) {
            error_log("=== ERROR en guardar(): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = "Error al procesar el proyecto: " . $e->getMessage();
            header('Location: index.php?action=home');
            exit;
        }
    }
    
    /**
     * Generar reporte de caracterización (si hay datos)
     */
    private function generarReporteCaracterizacion($postData) {
        // Si no hay datos de caracterización, crear reporte básico
        if (!isset($postData['restricciones']) && !isset($postData['complejidad'])) {
            return [
                'proyecto' => [
                    'nombre' => $postData['nombre'] ?? 'Sin nombre',
                    'descripcion' => $postData['descripcion'] ?? '',
                    'horas' => $postData['horas'] ?? 0,
                    'pais' => $postData['pais'] ?? ''
                ],
                'triple_restriccion' => [
                    'tipo' => 'No caracterizado',
                    'factores' => []
                ],
                'complejidad' => [
                    'total' => 0,
                    'factores' => []
                ],
                'dominio_cynefin' => 'No determinado',
                'estrategias' => []
            ];
        }
        
        // Si hay datos, usar el analizador Cynefin
        $analizador = new AnalizadorProyecto($postData);
        return $analizador->generarReporte();
    }
    
    /**
     * Guardar proyecto adaptado a tu estructura de BD
     */
    private function guardarProyectoAdaptado($reporte, $postData) {
        try {
            // Calcular horas (si no se proporcionan, estimar)
            $horas = $postData['horas'] ?? 0;
            if ($horas == 0 && isset($reporte['proyecto']['tamano'])) {
                $horas = $reporte['proyecto']['tamano'] * 10;
            }
            
            // Obtener dominio_problema si existe
            $dominio_problema = $reporte['proyecto']['dominio_problema'] ?? '';
            
            // Calcular complejidad_total
            $complejidad_total = $reporte['complejidad']['total'] ?? 0;
            
            // Preparar datos del equipo como JSON
            $equipo_json = isset($reporte['proyecto']['equipo']) 
                ? json_encode($reporte['proyecto']['equipo']) 
                : null;
            
            // USAR LA ESTRUCTURA ACTUAL DE TU BD
            $sql = "INSERT INTO proyectos 
                    (nombre, descripcion, horas, pais, dominio_id, 
                     usuario_id, equipo_json, complejidad_total, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            
            // Mapear dominio_problema a dominio_id si es necesario
            $dominio_id = $this->obtenerDominioId($dominio_problema);
            
            // Obtener usuario_id desde sesión
            $usuario_id = $_SESSION['usuario']['id'] ?? 0;
            
            // Pais (texto directo en tu BD)
            $pais = $reporte['proyecto']['pais'] ?? '';
            
            $resultado = $stmt->execute([
                $reporte['proyecto']['nombre'],
                $reporte['proyecto']['descripcion'] ?? '',
                $horas,
                $pais,
                $dominio_id,
                $usuario_id,
                $equipo_json,
                $complejidad_total
            ]);
            
            if (!$resultado) {
                error_log("Error SQL: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Error al ejecutar INSERT: " . implode(', ', $stmt->errorInfo()));
            }
            
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Error PDO en guardarProyectoAdaptado: " . $e->getMessage());
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    
    private function obtenerDominioId($nombreDominio) {
        if (empty($nombreDominio)) return null;
        
        try {
            // Primero buscar si existe
            $stmt = $this->db->prepare("SELECT id FROM dominios WHERE nombre = ?");
            $stmt->execute([$nombreDominio]);
            $dominio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($dominio) {
                return $dominio['id'];
            }
            
            // Si no existe, puedes crear uno nuevo o usar null
            // Dependiendo de tu lógica de negocio
            return null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerDominioId: " . $e->getMessage());
            return null;
        }
    }
    
    private function guardarEquipoJson($proyecto_id, $reporte) {
        if (!isset($reporte['proyecto']['equipo'])) return;
        
        try {
            $equipo_json = json_encode($reporte['proyecto']['equipo']);
            
            $stmt = $this->db->prepare("
                UPDATE proyectos SET equipo_json = ? WHERE id = ?
            ");
            $stmt->execute([$equipo_json, $proyecto_id]);
            
        } catch (PDOException $e) {
            error_log("Error en guardarEquipoJson: " . $e->getMessage());
        }
    }
    
    private function guardarComplejidadJson($proyecto_id, $reporte) {
        if (!isset($reporte['complejidad']['factores'])) return;
        
        try {
            // Aquí podrías guardar en una tabla aparte si necesitas
            // Por ahora solo actualizamos el campo complejidad_total
            $complejidad_total = $reporte['complejidad']['total'] ?? 0;
            
            $stmt = $this->db->prepare("
                UPDATE proyectos SET complejidad_total = ? WHERE id = ?
            ");
            $stmt->execute([$complejidad_total, $proyecto_id]);
            
        } catch (PDOException $e) {
            error_log("Error en guardarComplejidadJson: " . $e->getMessage());
        }
    }
    
    /**
     * Método para crear proyecto básico desde home.php
     */
    public function crearBasico() {
        $this->verificarAutenticacion();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido";
            header('Location: index.php?action=home&seccion=proyectos');
            exit;
        }
        
        try {
            // Validar datos mínimos
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $horas = (int)($_POST['horas'] ?? 0);
            $estado = $_POST['estado'] ?? 'pendiente';
            $fecha_inicio = $_POST['fecha_inicio'] ?? null;
            $fecha_fin = $_POST['fecha_fin'] ?? null;
            $lider_proyecto_id = (int)($_POST['lider_proyecto_id'] ?? 0);
            
            if (empty($nombre)) {
                throw new Exception("El nombre del proyecto es obligatorio");
            }
            
            if ($horas <= 0) {
                throw new Exception("Las horas estimadas deben ser mayores a 0");
            }
            
            if ($lider_proyecto_id <= 0) {
                throw new Exception("Debe seleccionar un líder de proyecto");
            }
            
            // Insertar proyecto básico
            $sql = "INSERT INTO proyectos 
                    (nombre, descripcion, horas, lider_proyecto_id, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $nombre,
                $descripcion,
                $horas,
                $lider_proyecto_id
            ]);
            
            if (!$resultado) {
                throw new Exception("Error al crear proyecto: " . implode(', ', $stmt->errorInfo()));
            }
            
            $proyecto_id = $this->db->lastInsertId();
            
            $_SESSION['success'] = "Proyecto creado exitosamente. ID: " . $proyecto_id;
            header('Location: index.php?action=home&seccion=proyectos');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=home&seccion=proyectos');
            exit;
        }
    }
    
    /**
     * Obtener todos los proyectos
     */
    public function obtenerTodos() {
        $this->verificarAutenticacion();
        
        try {
            $sql = "SELECT 
                    p.id, 
                    p.nombre AS nombre_proyecto,
                    p.descripcion AS descripcion_proyecto,
                    p.horas,
                    'pendiente' as estado, -- Valor por defecto
                    p.created_at,
                    CONCAT(u.nombre, ' ', u.apellido) AS nombre_lider
                FROM proyectos p
                LEFT JOIN usuarios u ON p.lider_proyecto_id = u.id
                ORDER BY p.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerTodos: " . $e->getMessage());
            return [];
        }
    }
    
    public function eliminar($id) {
        $this->verificarAutenticacion();
        
        try {
            if (!is_numeric($id)) {
                throw new Exception("ID de proyecto inválido");
            }
            
            // Verificar que el proyecto existe
            $stmt = $this->db->prepare("SELECT id FROM proyectos WHERE id = ?");
            $stmt->execute([$id]);
            
            if (!$stmt->fetch()) {
                throw new Exception("Proyecto no encontrado");
            }
            
            // Eliminar proyecto
            $stmt = $this->db->prepare("DELETE FROM proyectos WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                $_SESSION['success'] = "Proyecto eliminado exitosamente";
            } else {
                throw new Exception("Error al eliminar el proyecto");
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: index.php?action=home&seccion=proyectos');
        exit;
    }
    
    /**
     * Ver resultados de caracterización
     */
    public function verResultados($id = null) {
        $this->verificarAutenticacion();
        
        try {
            $proyecto_id = $id ?? ($_GET['id'] ?? $_SESSION['proyecto_id'] ?? 0);
            
            if (!$proyecto_id) {
                throw new Exception("ID de proyecto no especificado");
            }
            
            // Obtener proyecto de la base de datos
            $stmt = $this->db->prepare("
                SELECT p.*, 
                       CONCAT(u.nombre, ' ', u.apellido) as nombre_lider
                FROM proyectos p
                LEFT JOIN usuarios u ON p.lider_proyecto_id = u.id
                WHERE p.id = ?
            ");
            $stmt->execute([$proyecto_id]);
            $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$proyecto) {
                throw new Exception("Proyecto no encontrado");
            }
            
            // Si hay reporte en sesión, usarlo
            $reporte = $_SESSION['reporte_caracterizacion'] ?? null;
            
            // Si no hay reporte, intentar reconstruir desde datos del proyecto
            if (!$reporte && isset($proyecto['equipo_json'])) {
                $reporte = $this->reconstruirReporte($proyecto);
            }
            
            require_once __DIR__ . '/../views/resultados_caracterizacion.php';
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=home&seccion=proyectos');
            exit;
        }
    }
    
    private function reconstruirReporte($proyecto) {
        // Reconstruir reporte básico desde datos del proyecto
        $equipo = [];
        if (!empty($proyecto['equipo_json'])) {
            $equipo = json_decode($proyecto['equipo_json'], true) ?? [];
        }
        
        return [
            'proyecto' => [
                'nombre' => $proyecto['nombre'] ?? '',
                'descripcion' => $proyecto['descripcion'] ?? '',
                'horas' => $proyecto['horas'] ?? 0,
                'pais' => $proyecto['pais'] ?? '',
                'equipo' => $equipo
            ],
            'triple_restriccion' => [
                'tipo' => 'No caracterizado',
                'factores' => []
            ],
            'complejidad' => [
                'total' => $proyecto['complejidad_total'] ?? 0,
                'factores' => []
            ],
            'dominio_cynefin' => 'No determinado',
            'estrategias' => []
        ];
    }
}

/**
 * ANALIZADOR DE PROYECTO - CYNEFIN (Mantenido igual)
 */
class AnalizadorProyecto {
    private $datos;
    
    public function __construct($postData) {
        $this->datos = $postData;
    }
    
    public function determinarTripleRestriccion() {
        $restricciones = $this->datos['restricciones'] ?? [];
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
                'Enfoque de gestión' => 'Secuencial o por flujo tenso',
                'Modelo de ciclo de vida' => 'Secuencial',
                'Enfoque ágil' => 'Cascada o Kanban'
            ],
            'Complicado' => [
                'Tipo de acción' => 'Analizar para seleccionar la mejor práctica',
                'Prácticas' => 'Escoger la más adecuada entre buenas prácticas',
                'Enfoque de gestión' => 'Por flujo tenso o secuencial',
                'Modelo de ciclo de vida' => 'Secuencial',
                'Enfoque ágil' => 'Kanban o cascada'
            ],
            'Complejo' => [
                'Tipo de acción' => 'Experimentar para descubrir prácticas útiles',
                'Prácticas' => 'Emergen según resultados',
                'Enfoque de gestión' => 'Empírico',
                'Modelo de ciclo de vida' => 'Iterativo e incremental',
                'Enfoque ágil' => 'Scrum o Kanban'
            ],
            'Caótico' => [
                'Tipo de acción' => 'Actuar de inmediato',
                'Prácticas' => 'Usar las que garanticen estabilidad',
                'Enfoque de gestión' => 'Empírico',
                'Modelo de ciclo de vida' => 'Iterativo e incremental',
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
        
        $equipo = [];
        if (isset($this->datos['equipo_json'])) {
            $equipo = json_decode($this->datos['equipo_json'], true) ?? [];
        }
        
        return [
            'proyecto' => [
                'nombre' => $this->datos['nombre_proyecto'] ?? 'Sin nombre',
                'descripcion' => $this->datos['descripcion_proyecto'] ?? '',
                'dominio_problema' => $this->datos['dominio_problema'] ?? '',
                'tamano_estimado' => $this->datos['tamano_estimado'] ?? 0,
                'tamano' => $this->datos['tamano_estimado'] ?? 0,
                'pais' => $this->datos['pais'] ?? '',
                'equipo' => $equipo
            ],
            'triple_restriccion' => [
                'tipo' => $this->obtenerDescripcionRestriccion($tripleRestriccion),
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
?>