<?php
// controllers/ProyectoController.php - ADAPTADO A TU ESTRUCTURA DE BD
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
    
    public function index() {
        $this->verificarAutenticacion();
        $proyectos = $this->proyectoModel->obtenerTodosProyectos();
        require_once __DIR__ . '/../views/Home.php';
    }
    
    /**
     * GUARDAR PROYECTO - ADAPTADO A TU BD
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
            
            // 1. ANÁLISIS CYNEFIN
            $analizador = new AnalizadorProyecto($_POST);
            $reporte = $analizador->generarReporte();
            
            error_log("=== Reporte generado: " . print_r($reporte, true));
            
            // 2. GUARDAR EN BASE DE DATOS (ADAPTADO)
            $proyecto_id = $this->guardarProyectoAdaptado($reporte);
            
            if (!$proyecto_id) {
                throw new Exception("Error al guardar el proyecto en la base de datos");
            }
            
            error_log("=== Proyecto guardado con ID: " . $proyecto_id);
            
            // 3. GUARDAR EN SESIÓN PARA RESULTADOS
            $_SESSION['reporte_caracterizacion'] = $reporte;
            $_SESSION['proyecto_id'] = $proyecto_id;
            
            // 4. REDIRIGIR A RESULTADOS
            header('Location: views/resultados_caracterizacion.php');
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
     * Guardar proyecto adaptado a la estructura actual de tu BD
     */
    private function guardarProyectoAdaptado($reporte) {
        try {
            // Obtener dominio_id
            $dominio_id = $this->obtenerDominioId($reporte['proyecto']['dominio_problema']);
            
            // Obtener pais_id
            $pais_id = $this->obtenerOCrearPais($reporte['proyecto']['pais']);
            
            // Calcular horas basadas en complejidad
            $horas = $reporte['proyecto']['tamano_estimado'] + ($reporte['complejidad']['total'] * 10);
            
            // Obtener organización del usuario (ajusta según tu lógica)
            $organizacion_id = $_SESSION['usuario']['organizacion_id'] ?? null;
            
            // USAR LA ESTRUCTURA ACTUAL DE TU BD
            $sql = "INSERT INTO proyectos 
                    (nombre, descripcion, horas, pais_id, dominio_id, 
                     lider_proyecto_id, organizacion_id, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $reporte['proyecto']['nombre'],
                $reporte['proyecto']['descripcion'],
                $horas,
                $pais_id,
                $dominio_id,
                $_SESSION['usuario']['id'], // lider_proyecto_id
                $organizacion_id
            ]);
            
            if (!$resultado) {
                error_log("Error SQL: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Error al ejecutar INSERT");
            }
            
            $proyecto_id = $this->db->lastInsertId();
            
            // Guardar relaciones
            $this->guardarEquipo($proyecto_id, $reporte['proyecto']['equipo']);
            $this->guardarCaracteristicas($proyecto_id, $reporte['complejidad']['factores']);
            
            return $proyecto_id;
            
        } catch (PDOException $e) {
            error_log("Error PDO en guardarProyectoAdaptado: " . $e->getMessage());
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    
    private function obtenerDominioId($nombreDominio) {
        if (empty($nombreDominio)) return null;
        
        try {
            $stmt = $this->db->prepare("SELECT id FROM dominios WHERE nombre = ?");
            $stmt->execute([$nombreDominio]);
            $dominio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $dominio ? $dominio['id'] : null;
            
        } catch (PDOException $e) {
            error_log("Error en obtenerDominioId: " . $e->getMessage());
            return null;
        }
    }
    
    private function obtenerOCrearPais($nombrePais) {
        if (empty($nombrePais)) return null;
        
        try {
            $stmt = $this->db->prepare("SELECT id FROM paises WHERE nombre = ?");
            $stmt->execute([$nombrePais]);
            $pais = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($pais) {
                return $pais['id'];
            }
            
            // Crear si no existe
            $stmt = $this->db->prepare("INSERT INTO paises (nombre) VALUES (?)");
            $stmt->execute([$nombrePais]);
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Error en obtenerOCrearPais: " . $e->getMessage());
            return null;
        }
    }
    
    private function guardarEquipo($proyecto_id, $equipo) {
        if (empty($equipo)) return;
        
        try {
            foreach ($equipo as $miembro) {
                if (empty($miembro['perfil'])) continue;
                
                $stmt = $this->db->prepare("SELECT id FROM perfiles WHERE nombre = ?");
                $stmt->execute([$miembro['perfil']]);
                $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($perfil) {
                    $stmt = $this->db->prepare("
                        INSERT INTO proyectos_perfiles (proyecto_id, perfil_id, cantidad) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([
                        $proyecto_id,
                        $perfil['id'],
                        $miembro['cantidad'] ?? 1
                    ]);
                }
            }
        } catch (PDOException $e) {
            error_log("Error en guardarEquipo: " . $e->getMessage());
        }
    }
    
    private function guardarCaracteristicas($proyecto_id, $factores) {
        if (empty($factores)) return;
        
        try {
            foreach ($factores as $factor) {
                $stmt = $this->db->prepare("SELECT id FROM caracteristicas WHERE nombre = ?");
                $stmt->execute([$factor]);
                $caracteristica = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($caracteristica) {
                    $stmt = $this->db->prepare("
                        INSERT INTO proyectos_caracteristicas (proyecto_id, caracteristica_id) 
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$proyecto_id, $caracteristica['id']]);
                }
            }
        } catch (PDOException $e) {
            error_log("Error en guardarCaracteristicas: " . $e->getMessage());
        }
    }
    
    public function eliminar($id) {
        $this->verificarAutenticacion();
        
        try {
            if (!is_numeric($id)) {
                throw new Exception("ID de proyecto inválido");
            }
            
            $stmt = $this->db->prepare(
                "SELECT id FROM proyectos WHERE id = ? AND lider_proyecto_id = ?"
            );
            $stmt->execute([$id, $_SESSION['usuario']['id']]);
            
            if (!$stmt->fetch()) {
                throw new Exception("Proyecto no encontrado o no tienes permiso");
            }
            
            $resultado = $this->proyectoModel->eliminarProyecto($id);
            
            if ($resultado) {
                $_SESSION['success'] = "Proyecto eliminado exitosamente";
            } else {
                throw new Exception("Error al eliminar el proyecto");
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: index.php?action=home');
        exit;
    }
    
    public function verCaracterizacion($id) {
        $this->verificarAutenticacion();
        
        try {
            $proyecto = $this->proyectoModel->obtenerProyectoPorId($id);
            
            if (!$proyecto) {
                throw new Exception("Proyecto no encontrado");
            }
            
            $_SESSION['proyecto_id'] = $id;
            require_once __DIR__ . '/../views/VerCaracterizacion.php';
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?action=home');
            exit;
        }
    }
}

/**
 * ANALIZADOR DE PROYECTO - CYNEFIN
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