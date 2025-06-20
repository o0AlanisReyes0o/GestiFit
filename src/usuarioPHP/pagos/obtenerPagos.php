<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexion.php'; 

// Simular usuario (en producción usar sesión)
$usuario_id = 1;

try {
    $conexion = conectarDB();
    
    $sql = "SELECT 
                p.id_pago,
                p.fecha_pago,
                p.monto,
                c.nombre AS metodo_pago,
                p.estado_pago,
                m.nombre AS concepto,
                p.id_transaccion
            FROM pagos p
            LEFT JOIN membresias m ON p.id_membresia = m.id_membresia
            LEFT JOIN metodos_pago mp ON p.id_metodo_pago = mp.id_metodo
            LEFT JOIN catalogo_metodos_pago c ON mp.id_tipo = c.id_tipo
            WHERE p.id_usuario = ?
            ORDER BY p.fecha_pago DESC
            LIMIT 10";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $pagos = array();
    while ($fila = $result->fetch_assoc()) {
        $fila['monto'] = (float)$fila['monto'];
        $pagos[] = $fila;
    }

    echo json_encode([
        'exito' => true,
        'pagos' => $pagos
    ]);

} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage(),
        'pagos' => []
    ]);
} finally {
    if (isset($conexion)) {
        $conexion->close();
    }
}
?>
