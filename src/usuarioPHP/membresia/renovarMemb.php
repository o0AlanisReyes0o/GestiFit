<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

$idUsuario = $_POST['id_usuario'] ?? 0;

$conexion = conectarDB();

// 1. Obtener membresía actual del usuario
$sql = "SELECT um.id_membresia, um.fecha_fin 
        FROM usuarios_membresias um
        WHERE um.id_usuario = ? AND um.estado = 'activa'
        ORDER BY um.fecha_fin DESC
        LIMIT 1";

$stmt = consultaDB($conexion, $sql, [$idUsuario]);
$membresia_actual = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$membresia_actual) {
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes membresía activa para renovar']);
    exit;
}

// 2. Obtener detalles de la membresía
$sql_membresia = "SELECT duracion_dias FROM membresias WHERE id_membresia = ?";
$stmt_membresia = consultaDB($conexion, $sql_membresia, [$membresia_actual['id_membresia']]);
$membresia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_membresia));

// 3. Calcular nuevas fechas
$nueva_fecha_inicio = date('Y-m-d');
$nueva_fecha_fin = date('Y-m-d', strtotime("+{$membresia['duracion_dias']} days"));

// 4. Insertar nueva renovación
$sql_insert = "INSERT INTO usuarios_membresias 
               (id_usuario, id_membresia, fecha_inicio, fecha_fin, estado)
               VALUES (?, ?, ?, ?, 'activa')";

$stmt_insert = consultaDB($conexion, $sql_insert, [
    $idUsuario,
    $membresia_actual['id_membresia'],
    $nueva_fecha_inicio,
    $nueva_fecha_fin
]);

if ($stmt_insert) {
    // 5. Registrar el pago
    $sql_pago = "INSERT INTO pagos 
                 (id_usuario, id_membresia, monto, estado_pago)
                 VALUES (?, ?, ?, 'completado')";
    
    // Obtener precio actual de la membresía
    $sql_precio = "SELECT precio FROM membresias WHERE id_membresia = ?";
    $stmt_precio = consultaDB($conexion, $sql_precio, [$membresia_actual['id_membresia']]);
    $precio = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_precio))['precio'];
    
    consultaDB($conexion, $sql_pago, [
        $idUsuario,
        $membresia_actual['id_membresia'],
        $precio
    ]);
    
    echo json_encode(['exito' => true, 'mensaje' => 'Membresía renovada con éxito']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al renovar membresía']);
}

mysqli_close($conexion);
?>