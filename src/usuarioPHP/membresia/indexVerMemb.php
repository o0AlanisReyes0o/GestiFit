<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

$conexion = conectarDB();

$sql = "SELECT 
            m.id_membresia,
            m.nombre AS plan_name,
            m.precio,
            m.duracion_dias,
            m.descripcion,
            m.beneficios,
            m.tipo,
            um.fecha_inicio,
            um.fecha_fin,
            DATEDIFF(um.fecha_fin, um.fecha_inicio) AS total_days,
            DATEDIFF(CURDATE(), um.fecha_inicio) AS days_used,
            DATEDIFF(um.fecha_fin, CURDATE()) AS days_remaining,
            um.estado,
            CASE 
                WHEN um.fecha_fin >= CURDATE() THEN 1 
                ELSE 0 
            END AS is_active
        FROM usuarios_membresias um
        JOIN membresias m ON um.id_membresia = m.id_membresia
        WHERE um.id_usuario = ?
        AND um.estado = 'activa'
        ORDER BY um.fecha_fin DESC
        LIMIT 1";

$stmt = consultaDB($conexion, $sql, [$usuario_id]);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        'success' => true,
        'data' => null,
        'message' => 'No tienes una membresía activa'
    ]);
    exit;
}

$membresia = mysqli_fetch_assoc($result);

// Formatear fechas
$fecha_inicio = date('d/m/Y', strtotime($membresia['fecha_inicio']));
$fecha_fin = date('d/m/Y', strtotime($membresia['fecha_fin']));
$hoy = date('Y-m-d');
// Determinar si se puede renovar
$canRenew = ($membresia['days_remaining'] <= 7 || !$membresia['is_active']);
$dias_restantes = max(0, floor((strtotime($membresia['fecha_fin']) - strtotime($hoy)) / (60 * 60 * 24)));
// Formatear respuesta
$respuesta = [
    'success' => true,
    'data' => [
        'plan_name' => $membresia['plan_name'],
        'price' => $membresia['precio'],
        'duration_days' => $membresia['duracion_dias'],
        'description' => $membresia['descripcion'],
        'benefits' => $membresia['beneficios'],
        'type' => $membresia['tipo'],
        'start_date' => $fecha_inicio,
        'end_date' => $fecha_fin,
        'days_used' => max(0, $membresia['days_used']),
        'total_days' => $membresia['total_days'],
        'days_remaining' => $dias_restantes,
        'status' => $membresia['estado'],
        'is_active' => (bool)$membresia['is_active'],
        'can_renew' => $canRenew,
        'progress_percentage' => round(($membresia['days_used'] / $membresia['total_days']) * 100)
    ]
];

echo json_encode($respuesta);

mysqli_close($conexion);
?>