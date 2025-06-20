<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Desactivar errores en producción
ini_set('display_errors', 0);
error_reporting(0);

require_once '../conexion.php'; // Ajusta la ruta según tu estructura

try {
    // Obtener ID de usuario (en producción debería venir de la sesión)
    $usuario_id = 1; // Cambiar por el ID real del usuario

    $conexion = conectarDB();
    if (!$conexion) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Consulta para obtener métodos del usuario con información del catálogo
    $sql = "SELECT 
                mp.id_metodo,
                mp.id_tipo,
                cmp.nombre AS tipo_pago,
                cmp.descripcion,
                mp.alias,
                mp.ultimos_digitos,
                mp.fecha_creacion
            FROM metodos_pago mp
            JOIN catalogo_metodos_pago cmp ON mp.id_tipo = cmp.id_tipo
            WHERE mp.id_usuario = ? AND mp.activo = TRUE
            ORDER BY mp.fecha_creacion DESC";

    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
    }

    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $metodos = array();
    while ($fila = mysqli_fetch_assoc($result)) {
        // Formatear la información según el tipo de pago
        $descripcion = $fila['descripcion'];
        
        if ($fila['tipo_pago'] === 'tarjeta' && $fila['ultimos_digitos']) {
            $descripcion .= " (•••• " . $fila['ultimos_digitos'] . ")";
        }
        
        if ($fila['alias']) {
            $descripcion .= " - " . $fila['alias'];
        }

        $metodos[] = [
            'id_metodo' => (int)$fila['id_metodo'],
            'id_tipo' => (int)$fila['id_tipo'],
            'tipo' => $fila['tipo_pago'],
            'descripcion' => $descripcion,
            'alias' => $fila['alias'],
            'ultimos_digitos' => $fila['ultimos_digitos'],
            'fecha_creacion' => $fila['fecha_creacion']
        ];
    }

    // Consulta para obtener todos los tipos disponibles del catálogo
    $sql_catalogo = "SELECT id_tipo, nombre, descripcion FROM catalogo_metodos_pago";
    $result_catalogo = mysqli_query($conexion, $sql_catalogo);

    $catalogo = array();
    while ($fila = mysqli_fetch_assoc($result_catalogo)) {
        $catalogo[] = [
            'id_tipo' => (int)$fila['id_tipo'],
            'nombre' => $fila['nombre'],
            'descripcion' => $fila['descripcion']
        ];
    }

    echo json_encode([
        'exito' => true,
        'metodos_usuario' => $metodos,
        'catalogo_tipos' => $catalogo
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage(),
        'metodos_usuario' => [],
        'catalogo_tipos' => []
    ]);
} finally {
    if (isset($conexion)) {
        mysqli_close($conexion);
    }
}
?>



