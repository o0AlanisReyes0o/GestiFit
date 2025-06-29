<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

try {
    $conexion = conectarDB();
    
    $sql = "SELECT * FROM membresias ORDER BY precio ASC";
    $stmt = consultaDB($conexion, $sql);
    $result = mysqli_stmt_get_result($stmt);
    
    $membresias = [];
    while ($fila = mysqli_fetch_assoc($result)) {
        $membresias[] = [
            'id_membresia' => $fila['id_membresia'],
            'nombre' => $fila['nombre'],
            'precio' => $fila['precio'],
            'duracion_dias' => $fila['duracion_dias'],
            'tipo' => $fila['tipo'],
            'beneficios' => json_decode($fila['beneficios'], true) ?: []
        ];
    }

    foreach ($membresias as &$m) {
        $m['precio'] = (float)$m['precio'];
    }
    
    echo json_encode([
        'exito' => true,
        'membresias' => $membresias
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}
?>