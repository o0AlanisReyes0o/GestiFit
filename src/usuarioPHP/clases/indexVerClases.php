<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

try {
    $conexion = conectarDB();

    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos', 500);
    }

    // Consulta modificada para nueva estructura
    $sql = "SELECT 
            c.id_clase,
            c.nombre AS class_name,
            CONCAT(u.nombre, ' ', u.apellidoPaterno) AS instructor,
            TIME_FORMAT(c.hora_inicio, '%H:%i') AS start_time,
            TIME_FORMAT(c.hora_fin, '%H:%i') AS end_time,
            cd.dia AS day_of_week,
            DATE_FORMAT(CURDATE(), '%d/%m/%Y') AS fecha_actual,
            IF(
                cd.dia = ELT(WEEKDAY(CURDATE()) + 1, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'),
                1,
                0
            ) AS is_today
        FROM reservas_clases rc
        JOIN clases_grupales c ON rc.id_clase = c.id_clase
        JOIN Usuario u ON c.id_instructor = u.idUsuario
        JOIN clase_dias cd ON c.id_clase = cd.id_clase
        WHERE rc.id_usuario = ?
        ORDER BY 
            is_today DESC,
            FIELD(cd.dia, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'),
            c.hora_inicio
        LIMIT 3";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error, 500);
    }

    $stmt->bind_param('i', $usuario_id);
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error, 500);
    }

    $result = $stmt->get_result();
    $clases = [];

    while ($clase = $result->fetch_assoc()) {
        $clases[] = [
            'id' => $clase['id_clase'],
            'class_name' => $clase['class_name'],
            'instructor' => $clase['instructor'],
            'start_time' => $clase['start_time'],
            'end_time' => $clase['end_time'],
            'day' => $clase['day_of_week'],
            'is_today' => (bool)$clase['is_today']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $clases
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
} finally {
    if (isset($conexion)) {
        $conexion->close();
    }
}
?>