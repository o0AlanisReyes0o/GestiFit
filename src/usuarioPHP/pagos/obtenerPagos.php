<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexion.php'; 
require_once '../../autenticacion.php';

try {
    $conexion = conectarDB();
    
    $sql = "SELECT 
                p.id_pago,
                p.fecha_pago,
                p.monto,
                c.nombre AS metodo_pago,
                p.estado_pago,
                m.nombre AS concepto,
                p.referencia_pago
            FROM pagos p
            LEFT JOIN membresia m ON p.id_membresia = m.idMembresia
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
        // Formatear datos para consistencia
        $pago = [
            'id_pago' => $fila['id_pago'],
            'fecha_pago' => date('d/m/Y H:i', strtotime($fila['fecha_pago'])),
            'monto' => (float)$fila['monto'],
            'metodo_pago' => $fila['metodo_pago'] ?? 'No especificado',
            'estado_pago' => $fila['estado_pago'],
            'concepto' => $fila['concepto'] ?? 'Membresía',
            'referencia' => $fila['referencia_pago'] ?? 'N/A'
        ];
        $pagos[] = $pago;
    }

    echo json_encode([
        'exito' => true,
        'pagos' => $pagos,
        'total' => count($pagos)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al obtener el historial de pagos: ' . $e->getMessage(),
        'pagos' => [],
        'total' => 0
    ]);
} finally {
    if (isset($conexion)) {
        $conexion->close();
    }
}
?>