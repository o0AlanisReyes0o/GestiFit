<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';
header('Content-Type: application/json');

$conexion = conectarDB();

$sql = "SELECT 
            m.nombre AS nombre,
            m.costo AS precio,
            m.duracionMeses AS duracion_meses,
            m.descripcion,
            m.beneficios,
            um.fechaInicio,
            um.fechaFin,
            um.estado,
            DATEDIFF(um.fechaFin, um.fechaInicio) AS total_dias,
            DATEDIFF(CURDATE(), um.fechaInicio) AS dias_usados,
            DATEDIFF(um.fechaFin, CURDATE()) AS dias_restantes
        FROM usuariomembresia um
        JOIN membresia m ON um.idMembresia = m.idMembresia
        WHERE um.idUsuario = ? 
        AND um.estado = 'activa'
        AND (um.fechaFin IS NULL OR um.fechaFin >= CURDATE())
        ORDER BY um.fechaFin DESC
        LIMIT 1";

$stmt = consultaDB($conexion, $sql, [$usuario_id]);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        'exito' => false, 
        'mensaje' => 'No tienes una membresía activa'
    ]);
    exit;
}

$membresia = mysqli_fetch_assoc($result);

// Formatear fechas
$fecha_inicio = date('d/m/Y', strtotime($membresia['fechaInicio']));
$fecha_fin = date('d/m/Y', strtotime($membresia['fechaFin']));

// Calcular días totales basados en duración en meses
$total_dias = $membresia['duracion_meses'] * 30; // Aproximación de 30 días por mes
$dias_restantes = max(0, $membresia['dias_restantes']);

// Formatear respuesta
$respuesta = [
    'exito' => true,
    'membresia' => [
        'nombre' => $membresia['nombre'],
        'precio' => (float)$membresia['precio'],
        'duracion_meses' => $membresia['duracion_meses'],
        'duracion_dias' => $total_dias,
        'descripcion' => $membresia['descripcion'],
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'dias_usados' => max(0, $membresia['dias_usados']),
        'dias_restantes' => $dias_restantes,
        'estado' => $membresia['estado'],
        'beneficios' => array_map('trim', explode(',', $membresia['beneficios'])),
        'progreso' => round(($membresia['dias_usados'] / $total_dias) * 100)
    ]
];

echo json_encode($respuesta);

mysqli_close($conexion);
?>