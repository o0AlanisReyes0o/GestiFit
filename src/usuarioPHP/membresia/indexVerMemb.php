<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

$conexion = conectarDB();

$sql = "SELECT 
            m.idMembresia AS id_membresia,
            m.nombre AS plan_name,
            m.costo AS precio,
            m.duracionMeses AS duracion_meses,
            m.descripcion,
            m.beneficios,
            um.fechaInicio,
            um.fechaFin,
            DATEDIFF(um.fechaFin, um.fechaInicio) AS total_days,
            DATEDIFF(CURDATE(), um.fechaInicio) AS days_used,
            DATEDIFF(um.fechaFin, CURDATE()) AS days_remaining,
            um.estado,
            CASE 
                WHEN um.fechaFin >= CURDATE() AND um.estado = 'activa' THEN 1 
                ELSE 0 
            END AS is_active
        FROM usuariomembresia um
        JOIN membresia m ON um.idMembresia = m.idMembresia
        WHERE um.idUsuario = ?
        ORDER BY um.fechaFin DESC
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
$fecha_inicio = date('d/m/Y', strtotime($membresia['fechaInicio']));
$fecha_fin = date('d/m/Y', strtotime($membresia['fechaFin']));
$hoy = date('Y-m-d');

// Calcular días totales basados en duración en meses
$total_dias = $membresia['duracion_meses'] * 30; // Aproximación de 30 días por mes
$dias_usados = max(0, floor((strtotime($hoy) - strtotime($membresia['fechaInicio'])) / (60 * 60 * 24)));
$dias_restantes = max(0, floor((strtotime($membresia['fechaFin']) - strtotime($hoy)) / (60 * 60 * 24)));

// Determinar si se puede renovar
$canRenew = ($dias_restantes <= 7 || $membresia['estado'] !== 'activa');

// Formatear respuesta
$respuesta = [
    'success' => true,
    'data' => [
        'plan_name' => $membresia['plan_name'],
        'price' => (float)$membresia['precio'],
        'duration_months' => $membresia['duracion_meses'],
        'duration_days' => $total_dias,
        'description' => $membresia['descripcion'],
        'benefits' => array_map('trim', explode(',', $membresia['beneficios'])),
        'start_date' => $fecha_inicio,
        'end_date' => $fecha_fin,
        'days_used' => $dias_usados,
        'total_days' => $total_dias,
        'days_remaining' => $dias_restantes,
        'status' => $membresia['estado'],
        'is_active' => (bool)$membresia['is_active'],
        'can_renew' => $canRenew,
        'progress_percentage' => $total_dias > 0 ? round(($dias_usados / $total_dias) * 100) : 0
    ]
];

echo json_encode($respuesta);

mysqli_close($conexion);
?>