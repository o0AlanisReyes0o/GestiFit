<?php
require_once '../conexion.php';

header('Content-Type: application/json');

$conexion = conectarDB();

// Consulta para obtener todas las membresías disponibles
$sql = "SELECT id_membresia, nombre, precio, duracion_dias, descripcion, beneficios, tipo 
        FROM membresias
        ORDER BY precio ASC";

$stmt = consultaDB($conexion, $sql, []);
$result = mysqli_stmt_get_result($stmt);

$planes = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Convertir beneficios separados por comas en array
    $beneficios = array_map('trim', explode(',', $row['beneficios']));
    
    // Determinar si es featured (puedes ajustar esta lógica)
    $featured = ($row['tipo'] === 'premium' || $row['tipo'] === 'vip');
    
    $planes[] = [
        'id' => $row['id_membresia'],
        'name' => $row['nombre'],
        'price' => $row['precio'],
        'duration' => $row['duracion_dias'] . ' días',
        'description' => $row['descripcion'],
        'features' => $beneficios,
        'featured' => $featured,
        'type' => $row['tipo']
    ];
}

// Verificar si el usuario tiene membresía activa para marcar current
$usuario_id = 1; // Cambiar por el ID real del usuario
$sql_current = "SELECT id_membresia FROM usuarios_membresias 
                WHERE id_usuario = ? AND estado = 'activa' 
                LIMIT 1";
$stmt_current = consultaDB($conexion, $sql_current, [$usuario_id]);
$result_current = mysqli_stmt_get_result($stmt_current);

$current_plan_id = null;
if (mysqli_num_rows($result_current) > 0) {
    $current = mysqli_fetch_assoc($result_current);
    $current_plan_id = $current['id_membresia'];
}

// Marcar planes como current y upgrade
foreach ($planes as &$plan) {
    $plan['current'] = ($plan['id'] === $current_plan_id);
    $plan['upgrade'] = ($current_plan_id && $plan['price'] > array_filter($planes, function($p) use ($current_plan_id) {
        return $p['id'] === $current_plan_id;
    })[0]['price']);
}

echo json_encode([
    'success' => true,
    'data' => $planes
]);

mysqli_close($conexion);
?>