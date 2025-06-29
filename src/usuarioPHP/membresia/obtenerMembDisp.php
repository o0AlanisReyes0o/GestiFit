<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

try {
    $conexion = conectarDB();
    
    $sql = "SELECT 
                idMembresia AS id_membresia,
                nombre,
                costo AS precio,
                duracionMeses AS duracion_meses,
                descripcion,
                beneficios
            FROM membresia 
            ORDER BY costo ASC";
    
    $stmt = consultaDB($conexion, $sql);
    $result = mysqli_stmt_get_result($stmt);
    
    $membresias = [];
    while ($fila = mysqli_fetch_assoc($result)) {
        $membresias[] = [
            'id_membresia' => $fila['id_membresia'],
            'nombre' => $fila['nombre'],
            'precio' => (float)$fila['precio'],
            'duracion_meses' => $fila['duracion_meses'],
            'duracion_dias' => $fila['duracion_meses'] * 30, // Convertir meses a días aproximados
            'descripcion' => $fila['descripcion'],
            'beneficios' => array_map('trim', explode(',', $fila['beneficios']))
        ];
    }
    
    echo json_encode([
        'exito' => true,
        'membresias' => $membresias
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al obtener las membresías: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conexion)) {
        mysqli_close($conexion);
    }
}
?>