<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

$idUsuario = $_POST['id_usuario'] ?? 0;

try {
    $conexion = conectarDB();

    // 1. Obtener membresía actual del usuario
    $sql = "SELECT um.idMembresia, um.fechaFin 
            FROM usuariomembresia um
            WHERE um.idUsuario = ? 
            AND um.estado = 'activa'
            AND (um.fechaFin IS NULL OR um.fechaFin >= CURDATE())
            ORDER BY um.fechaFin DESC
            LIMIT 1";

    $stmt = consultaDB($conexion, $sql, [$idUsuario]);
    $membresia_actual = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$membresia_actual) {
        throw new Exception('No tienes una membresía activa para renovar');
    }

    // 2. Obtener detalles de la membresía
    $sql_membresia = "SELECT duracionMeses FROM membresia WHERE idMembresia = ?";
    $stmt_membresia = consultaDB($conexion, $sql_membresia, [$membresia_actual['idMembresia']]);
    $membresia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_membresia));

    // 3. Calcular nuevas fechas (basado en meses)
    $nueva_fecha_inicio = date('Y-m-d');
    $nueva_fecha_fin = date('Y-m-d', strtotime("+{$membresia['duracionMeses']} months"));

    // 4. Actualizar membresía existente (en lugar de insertar nueva)
    $sql_update = "UPDATE usuariomembresia 
                  SET fechaInicio = ?, 
                      fechaFin = ?,
                      estado = 'activa'
                  WHERE idUsuario = ? 
                  AND idMembresia = ?
                  AND estado = 'activa'";

    $stmt_update = consultaDB($conexion, $sql_update, [
        $nueva_fecha_inicio,
        $nueva_fecha_fin,
        $idUsuario,
        $membresia_actual['idMembresia']
    ]);

    if (!$stmt_update) {
        throw new Exception('Error al actualizar la membresía');
    }

    // 5. Registrar el pago
    $sql_pago = "INSERT INTO pagos 
                (id_usuario, id_membresia, monto, estado_pago, fecha_pago)
                VALUES (?, ?, ?, 'completado', NOW())";
    
    // Obtener precio actual de la membresía
    $sql_precio = "SELECT costo FROM membresia WHERE idMembresia = ?";
    $stmt_precio = consultaDB($conexion, $sql_precio, [$membresia_actual['idMembresia']]);
    $precio = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_precio))['costo'];
    
    $stmt_pago = consultaDB($conexion, $sql_pago, [
        $idUsuario,
        $membresia_actual['idMembresia'],
        $precio
    ]);

    if (!$stmt_pago) {
        throw new Exception('Error al registrar el pago');
    }

    echo json_encode([
        'exito' => true, 
        'mensaje' => 'Membresía renovada con éxito',
        'fecha_inicio' => $nueva_fecha_inicio,
        'fecha_fin' => $nueva_fecha_fin
    ]);

} catch (Exception $e) {
    echo json_encode([
        'exito' => false, 
        'mensaje' => $e->getMessage()
    ]);
} finally {
    if (isset($conexion)) {
        mysqli_close($conexion);
    }
}
?>