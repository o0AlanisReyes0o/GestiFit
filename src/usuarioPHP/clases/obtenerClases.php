<?php
header('Content-Type: application/json');
error_reporting(0);

require_once __DIR__ . '/../conexion.php';
require_once '../../autenticacion.php';

$response = ['success' => false, 'clases' => [], 'horario' => []];

try {
    $conn = conectarDB();

    // Consulta para obtener las clases
    $sqlClases = "SELECT c.*, u.nombre AS instructor_nombre, u.apellidoPaterno AS instructor_apellido 
                 FROM clases_grupales c
                 JOIN usuario u ON c.id_instructor = u.idUsuario
                 WHERE c.estado = 'disponible'";
    $resultClases = mysqli_query($conn, $sqlClases);
    
    if (!$resultClases) {
        throw new Exception("Error en consulta de clases: " . mysqli_error($conn));
    }
    
    $clases = [];
    while ($row = mysqli_fetch_assoc($resultClases)) {
        // Procesar días de la clase
        $sqlDias = "SELECT dia FROM clasedias WHERE idClase = ?";
        $stmtDias = mysqli_prepare($conn, $sqlDias);
        mysqli_stmt_bind_param($stmtDias, 'i', $row['id_clase']);
        mysqli_stmt_execute($stmtDias);
        $resultDias = mysqli_stmt_get_result($stmtDias);
        
        $dias = [];
        while ($dia = mysqli_fetch_assoc($resultDias)) {
            $dias[] = $dia['dia'];
        }
        mysqli_stmt_close($stmtDias);
        $row['dias'] = $dias;
        
        // Calcular duración
        $horaInicio = new DateTime($row['hora_inicio']);
        $horaFin = new DateTime($row['hora_fin']);
        $row['duracion'] = $horaFin->diff($horaInicio)->format('%H:%I');
        
        // Calcular cupos disponibles
        $sqlReservas = "SELECT COUNT(*) FROM reservas_clases WHERE id_clase = ?";
        $stmtReservas = mysqli_prepare($conn, $sqlReservas);
        mysqli_stmt_bind_param($stmtReservas, 'i', $row['id_clase']);
        mysqli_stmt_execute($stmtReservas);
        $resultReservas = mysqli_stmt_get_result($stmtReservas);
        $reservas = mysqli_fetch_row($resultReservas);
        mysqli_stmt_close($stmtReservas);
        $row['cupos_disponibles'] = $row['cupo_maximo'] - $reservas[0];
        
        // Procesar requisitos
        $row['requisitos'] = !empty($row['requisitos']) ? array_map('trim', explode(',', $row['requisitos'])) : [];
        
        $clases[] = $row;
    }

    // Consulta para el horario 
    $sqlHorario = "SELECT c.id_clase, c.nombre AS clase_nombre, cd.dia, c.hora_inicio, c.hora_fin, 
                  u.nombre AS instructor_nombre, u.apellidoPaterno AS instructor_apellido
                  FROM clases_grupales c
                  JOIN clasedias cd ON c.id_clase = cd.idClase
                  JOIN usuario u ON c.id_instructor = u.idUsuario
                  WHERE c.estado = 'disponible'
                  ORDER BY FIELD(cd.dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'), 
                  c.hora_inicio";
    
    $resultHorario = mysqli_query($conn, $sqlHorario);
    
    if (!$resultHorario) {
        throw new Exception("Error en consulta de horario: " . mysqli_error($conn));
    }
    
    $horarioData = mysqli_fetch_all($resultHorario, MYSQLI_ASSOC);
    
    // Organizar horario
    $horario = [];
    foreach ($horarioData as $clase) {
        $hora = date('G', strtotime($clase['hora_inicio']));
        $dia = $clase['dia'];
        
        if (!isset($horario[$hora])) {
            $horario[$hora] = [
                'hora' => date('g:i', strtotime($clase['hora_inicio'])) . ' - ' . date('g:i A', strtotime($clase['hora_fin']))
            ];
        }
        
        $horario[$hora][$dia] = [
            'nombre' => $clase['clase_nombre'],
            'instructor' => $clase['instructor_nombre'] . ' ' . $clase['instructor_apellido'],
            'id_clase' => $clase['id_clase']
        ];
    }

    $response = [
        'success' => true,
        'clases' => $clases,
        'horario' => array_values($horario) // Reindexar array
    ];

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code(500);
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>