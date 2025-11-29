<?php
session_start();
require_once "../config/db.php";

// Crear la conexión CORREGIDA
$database = new Database();
$conn = $database->getConnection(); // ✅ Método correcto

// Verificar conexión
if (!$conn) {
    die("Error: No se pudo establecer conexión con la base de datos");
}

class AnalizadorProyecto {
    private $datos;
    
    public function __construct($postData) {
        $this->datos = $postData;
    }

    // Determinar tipo de triple restricción
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
        return 0; // Sin restricciones fijas
    }

    // Contar factores de complejidad
    public function contarComplejidad() {
        return isset($this->datos['complejidad']) ? count($this->datos['complejidad']) : 0;
    }

    // Determinar dominio Cynefin
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

    // Estrategias según dominio
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

        $equipo = [];
        if (isset($this->datos['equipo_json'])) {
            $equipo = json_decode($this->datos['equipo_json'], true);
        }

        return [
            'proyecto' => [
                'nombre' => $this->datos['nombre_proyecto'] ?? 'Sin nombre',
                'descripcion' => $this->datos['descripcion_proyecto'] ?? 'Sin descripción',
                'dominio_problema' => $this->datos['dominio_problema'] ?? 'Sin dominio',
                'tamano_estimado' => $this->datos['tamano_estimado'] ?? 0,
                'pais' => $this->datos['pais'] ?? 'No indicado',
                'equipo' => $equipo
            ],
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

// ===============================
// PROCESAR Y GUARDAR - ACTUALIZADO
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $analizador = new AnalizadorProyecto($_POST);
    $reporte = $analizador->generarReporte();

    // Guardar en la base de datos - CONSULTAS ACTUALIZADAS
    try {
        // 1. Obtener IDs de las relaciones
        $dominio_id = null;
        $pais_id = null;
        
        // Obtener dominio_id
        if (!empty($reporte['proyecto']['dominio_problema'])) {
            $stmt_dominio = $conn->prepare("SELECT id FROM dominios WHERE nombre = ?");
            $stmt_dominio->execute([$reporte['proyecto']['dominio_problema']]);
            $dominio_data = $stmt_dominio->fetch(PDO::FETCH_ASSOC);
            $dominio_id = $dominio_data ? $dominio_data['id'] : null;
        }

        // Obtener/crear pais_id
        if (!empty($reporte['proyecto']['pais'])) {
            $stmt_pais = $conn->prepare("SELECT id FROM paises WHERE nombre = ?");
            $stmt_pais->execute([$reporte['proyecto']['pais']]);
            $pais_data = $stmt_pais->fetch(PDO::FETCH_ASSOC);
            
            if (!$pais_data) {
                $stmt_insert_pais = $conn->prepare("INSERT INTO paises (nombre) VALUES (?)");
                $stmt_insert_pais->execute([$reporte['proyecto']['pais']]);
                $pais_id = $conn->lastInsertId();
            } else {
                $pais_id = $pais_data['id'];
            }
        }

        // 2. Insertar proyecto principal
        $sql_proyecto = "INSERT INTO proyectos 
                        (nombre, descripcion, horas, pais_id, dominio_id, organizacion_id) 
                        VALUES 
                        (:nombre, :descripcion, :horas, :pais_id, :dominio_id, :organizacion_id)";

        $stmt_proyecto = $conn->prepare($sql_proyecto);
        $stmt_proyecto->execute([
            ':nombre' => $reporte['proyecto']['nombre'],
            ':descripcion' => $reporte['proyecto']['descripcion'],
            ':horas' => $reporte['proyecto']['tamano_estimado'],
            ':pais_id' => $pais_id,
            ':dominio_id' => $dominio_id,
            ':organizacion_id' => $_SESSION['organizacion_id'] ?? 1 // Ajusta según tu lógica de sesión
        ]);
        
        $proyecto_id = $conn->lastInsertId();

        // 3. Insertar perfiles del equipo
        if (!empty($reporte['proyecto']['equipo'])) {
            foreach ($reporte['proyecto']['equipo'] as $miembro) {
                if (!empty($miembro['perfil'])) {
                    $stmt_perfil = $conn->prepare("SELECT id FROM perfiles WHERE nombre = ?");
                    $stmt_perfil->execute([$miembro['perfil']]);
                    $perfil_data = $stmt_perfil->fetch(PDO::FETCH_ASSOC);
                    
                    if ($perfil_data) {
                        $stmt_equipo = $conn->prepare("
                            INSERT INTO proyectos_perfiles (proyecto_id, perfil_id, cantidad) 
                            VALUES (?, ?, ?)
                        ");
                        $stmt_equipo->execute([
                            $proyecto_id, 
                            $perfil_data['id'], 
                            $miembro['cantidad'] ?? 1
                        ]);
                    }
                }
            }
        }

        // 4. Insertar características de complejidad
        if (!empty($reporte['complejidad']['factores'])) {
            foreach ($reporte['complejidad']['factores'] as $caracteristica_nombre) {
                $stmt_caracteristica = $conn->prepare("SELECT id FROM caracteristicas WHERE nombre = ?");
                $stmt_caracteristica->execute([$caracteristica_nombre]);
                $caracteristica_data = $stmt_caracteristica->fetch(PDO::FETCH_ASSOC);
                
                if ($caracteristica_data) {
                    $stmt_proy_caract = $conn->prepare("
                        INSERT INTO proyectos_caracteristicas (proyecto_id, caracteristica_id) 
                        VALUES (?, ?)
                    ");
                    $stmt_proy_caract->execute([$proyecto_id, $caracteristica_data['id']]);
                }
            }
        }

        // 5. Guardar análisis en sesión
        $_SESSION['reporte_caracterizacion'] = $reporte;
        $_SESSION['proyecto_id'] = $proyecto_id;

        header('Location: ../views/resultados_caracterizacion.php');
        exit;

    } catch (PDOException $e) {
        error_log("Error al guardar proyecto: " . $e->getMessage());
        $_SESSION['error'] = "Error al guardar el proyecto: " . $e->getMessage();
        header('Location: ../views/Home.php');
        exit;
    }
} else {
    header('Location: ../index.php');
    exit;
}
?>