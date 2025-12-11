<?php
session_start();
require_once "../config/db.php"; // Asegúrate de que apunta a la clase Database

// Crear la conexión
$db = new Database();
$conn = $db->connect(); // $conn ahora es un objeto PDO válido // Asegúrate de tener este archivo con la conexión PDO

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
        } elseif (isset($this->datos['nombre']) && isset($this->datos['rol'])) {
            foreach ($this->datos['nombre'] as $i => $nombre) {
                $equipo[] = [
                    'nombre' => $nombre,
                    'rol' => $this->datos['rol'][$i] ?? '',
                    'responsabilidad' => $this->datos['responsabilidad'][$i] ?? ''
                ];
            }
        }

        return [
            'proyecto' => [
                'nombre' => $this->datos['nombre_proyecto'] ?? 'Sin nombre',
                'entidad' => $this->datos['entidad'] ?? 'No especificada',
                'sector' => $this->datos['sector'] ?? 'No definido',
                'tamano' => $this->datos['tamano'] ?? 'No definido',
                'pais' => $this->datos['pais'] ?? 'No indicado',
                'dominio_problema' => $this->datos['dominio_problema'] ?? 'Sin dominio',
                'descripcion' => $this->datos['descripcion_proyecto'] ?? 'Sin descripción',
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
// PROCESAR Y GUARDAR
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $analizador = new AnalizadorProyecto($_POST);
    $reporte = $analizador->generarReporte();

    // Guardar en la base de datos
    try {
$sql = "INSERT INTO proyectos
    (nombre, pais_cliente, tamano_estimado, dominio_problema, descripcion, equipo, factores, complejidad_total, fecha_creacion)
    VALUES
    (:nombre, :pais_cliente, :tamano_estimado, :dominio_problema, :descripcion, :equipo, :factores, :complejidad_total, NOW())";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':nombre' => $reporte['proyecto']['nombre'],
    ':pais_cliente' => $reporte['proyecto']['pais'],
    ':tamano_estimado' => $reporte['proyecto']['tamano'],
    ':dominio_problema' => $reporte['proyecto']['dominio_problema'],
    ':descripcion' => $reporte['proyecto']['descripcion'],
    ':equipo' => json_encode($reporte['proyecto']['equipo']),
    ':factores' => json_encode([
        'restricciones' => $reporte['triple_restriccion']['factores'] ?? [],
        'complejidad' => $reporte['complejidad']['factores'] ?? [],
        'dominio_cynefin' => $reporte['dominio_cynefin'],
        'estrategias' => $reporte['estrategias']
    ]),
    ':complejidad_total' => $reporte['complejidad']['total']
]);

    } catch (PDOException $e) {
        die("Error al guardar en la base de datos: " . $e->getMessage());
    }

    // Guardar en sesión para mostrar resultados
    $_SESSION['reporte_caracterizacion'] = $reporte;
    header('Location:../views/resultados_caracterizacion.php');
    exit;
} else {
    header('Location: ../index.php');
    exit;
}
?>