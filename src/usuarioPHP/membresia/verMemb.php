<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

$conexion = conectarDB();

// Consulta para obtener todas las membresías disponibles
$sql = "SELECT idMembresia AS id_membresia, 
               nombre, 
               costo AS precio, 
               duracionMeses AS duracion_meses,
               descripcion,
               beneficios
        FROM membresia
        ORDER BY costo ASC";

$stmt = consultaDB($conexion, $sql, []);
$result = mysqli_stmt_get_result($stmt);

$planes = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Convertir beneficios separados por comas en array
    $beneficios = array_map('trim', explode(',', $row['beneficios']));
    
    // Determinar si es featured (Premium en este caso)
    $featured = ($row['nombre'] === 'Premium');
    
    $planes[] = [
        'id' => $row['id_membresia'],
        'name' => $row['nombre'],
        'price' => (float)$row['precio'],
        'duration' => $row['duracion_meses'] . ' mes(es)',
        'description' => $row['descripcion'],
        'features' => $beneficios,
        'featured' => $featured,
        'type' => strtolower($row['nombre'])
    ];
}

// Consulta para obtener la membresía actual del usuario
$sql_current = "SELECT um.idMembresia, m.costo 
                FROM usuariomembresia um
                JOIN membresia m ON um.idMembresia = m.idMembresia
                WHERE um.idUsuario = ? 
                AND um.estado = 'activa'
                AND (um.fechaFin IS NULL OR um.fechaFin >= CURDATE())
                LIMIT 1";

$stmt_current = consultaDB($conexion, $sql_current, [$usuario_id]);
$result_current = mysqli_stmt_get_result($stmt_current);

$current_plan_id = null;
$current_plan_price = null;

if (mysqli_num_rows($result_current) > 0) {
    $current = mysqli_fetch_assoc($result_current);
    $current_plan_id = $current['idMembresia'];
    $current_plan_price = (float)$current['costo'];
}

// Marcar planes como current y upgrade
foreach ($planes as &$plan) {
    $plan['current'] = ($plan['id'] === $current_plan_id);
    
    // Solo comparar precios si tenemos un plan actual válido
    if ($current_plan_price !== null) {
        $plan['upgrade'] = ($plan['price'] > $current_plan_price);
    } else {
        $plan['upgrade'] = false;
    }
}

echo json_encode([
    'success' => true,
    'data' => $planes
]);

mysqli_close($conexion);
?>