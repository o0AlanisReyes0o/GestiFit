<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';
header('Content-Type: application/json');

$conexion = conectarDB();

$sql = "SELECT m.nombre, m.precio, m.duracion_dias, m.descripcion, m.beneficios, 
               um.fecha_inicio, um.fecha_fin, um.estado
        FROM usuarios_membresias um
        JOIN membresias m ON um.id_membresia = m.id_membresia
        WHERE um.id_usuario = ? AND um.estado = 'activa'
        ORDER BY um.fecha_fin DESC
        LIMIT 1";

$stmt = consultaDB($conexion, $sql, [$usuario_id]);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes membresía activa']);
    exit;
}

$membresia = mysqli_fetch_assoc($result);

// Calcular días restantes
$hoy = date('Y-m-d');
$dias_restantes = max(0, floor((strtotime($membresia['fecha_fin']) - strtotime($hoy)) / (60 * 60 * 24)));

// Formatear respuesta
$respuesta = [
    'exito' => true,
    'membresia' => [
        'nombre' => $membresia['nombre'],
        'precio' => $membresia['precio'],
        'duracion' => $membresia['duracion_dias'],
        'fecha_inicio' => $membresia['fecha_inicio'],
        'fecha_fin' => $membresia['fecha_fin'],
        'dias_restantes' => $dias_restantes,
        'estado' => $membresia['estado'],
        'beneficios' => array_map('trim', explode(',', $membresia['beneficios']))
    ]
];

echo json_encode($respuesta);

mysqli_close($conexion);
?>