<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../conexion.php';
require_once '../../autenticacion.php';

$response = ['exito' => false, 'mensaje' => ''];

$conn = conectarDB();

try {
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos");
    }

    mysqli_begin_transaction($conn);

    $datos = $_POST;

    if (empty($datos['id_membresia']) || empty($datos['monto'])) {
        throw new Exception('Datos de pago incompletos');
    }

    $idMetodoPago = null;

    if (isset($datos['nuevo_metodo']) && $datos['nuevo_metodo'] == '1') {
        //$tipoMetodoPago = $datos['tipo_metodo'] ?? 'otro';
        $alias = $datos['alias_metodo'] ?? 'Nuevo método';
        $ultimosDigitos = $datos['ultimos_digitos'] ?? null;

        $idTipo = $datos['tipo_metodo'] ?? 0;

        if (!$idTipo) {
            throw new Exception("Tipo de método de pago no válido");
        }

        $sqlNuevoMetodo = "INSERT INTO metodos_pago 
                          (id_usuario, id_tipo, alias, ultimos_digitos, fecha_creacion, activo) 
                          VALUES (?, ?, ?, ?, NOW(), TRUE)";
        $stmtNuevoMetodo = consultaDB($conn, $sqlNuevoMetodo, [
            $usuario_id, $idTipo, $alias, $ultimosDigitos
        ]);

        if ($stmtNuevoMetodo === false) {
            throw new Exception("Error al registrar nuevo método de pago: " . mysqli_error($conn));
        }

        $idMetodoPago = mysqli_insert_id($conn);
        mysqli_stmt_close($stmtNuevoMetodo);
    } else {
        if (empty($datos['id_metodo_pago'])) {
            throw new Exception('Método de pago no especificado');
        }
        $idMetodoPago = $datos['id_metodo_pago'];
    }

    $referencia = 'SIM-' . strtoupper(uniqid());
    $sqlPago = "INSERT INTO pagos 
                (id_usuario, id_metodo_pago, id_membresia, fecha_pago, monto, estado_pago, referencia_pago) 
                VALUES (?, ?, ?, NOW(), ?, 'completado', ?)";

    $stmtPago = consultaDB($conn, $sqlPago, [
        $usuario_id,
        $idMetodoPago,
        $datos['id_membresia'],
        $datos['monto'],
        $referencia
    ]);

    if ($stmtPago === false) {
        throw new Exception("Error al registrar el pago: " . mysqli_error($conn));
    }

    $idPago = mysqli_insert_id($conn);
    mysqli_stmt_close($stmtPago);

    $sqlDuracion = "SELECT duracion_dias FROM membresias WHERE id_membresia = ?";
    $stmtDuracion = consultaDB($conn, $sqlDuracion, [$datos['id_membresia']]);
    
    if ($stmtDuracion === false) {
        throw new Exception("Error al consultar duración: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_result($stmtDuracion, $duracion_dias);
    if (!mysqli_stmt_fetch($stmtDuracion)) {
        throw new Exception("Membresía no encontrada");
    }
    
    mysqli_stmt_free_result($stmtDuracion);
    mysqli_stmt_close($stmtDuracion);

    $fechaFin = date('Y-m-d', strtotime("+{$duracion_dias} days"));

    $sqlVerificar = "SELECT id_relacion FROM usuarios_membresias 
                    WHERE id_usuario = ? AND estado = 'activa'";
    $stmtVerificar = consultaDB($conn, $sqlVerificar, [$usuario_id]);
    
    if ($stmtVerificar === false) {
        throw new Exception("Error al verificar membresía: " . mysqli_error($conn));
    }
    
    mysqli_stmt_store_result($stmtVerificar);
    $tieneMembresiaActiva = (mysqli_stmt_num_rows($stmtVerificar) > 0);
    mysqli_stmt_free_result($stmtVerificar);
    mysqli_stmt_close($stmtVerificar);

    if ($tieneMembresiaActiva) {
        $sqlActualizar = "UPDATE usuarios_membresias 
                         SET id_membresia = ?, fecha_inicio = CURDATE(), fecha_fin = ?, id_pago = ?
                         WHERE id_usuario = ? AND estado = 'activa'";
        $stmtActualizar = consultaDB($conn, $sqlActualizar, [
            $datos['id_membresia'],
            $fechaFin,
            $idPago,
            $usuario_id
        ]);
        
        if ($stmtActualizar === false) {
            throw new Exception("Error al actualizar membresía: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmtActualizar);
    } else {
        $sqlInsertar = "INSERT INTO usuarios_membresias 
                       (id_usuario, id_membresia, fecha_inicio, fecha_fin, estado, id_pago) 
                       VALUES (?, ?, CURDATE(), ?, 'activa', ?)";
        $stmtInsertar = consultaDB($conn, $sqlInsertar, [
            $idUsuario,
            $datos['id_membresia'],
            $fechaFin,
            $idPago
        ]);
        
        if ($stmtInsertar === false) {
            throw new Exception("Error al crear membresía: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmtInsertar);
    }

    mysqli_commit($conn);

    $response['exito'] = true;
    $response['mensaje'] = 'Pago procesado y membresía actualizada correctamente';
    $response['referencia'] = $referencia;
    $response['id_pago'] = $idPago;

} catch (Exception $e) {
    if ($conn) {
        mysqli_rollback($conn);
    }
    $response['mensaje'] = $e->getMessage();
} finally {
    if ($conn) {
        mysqli_close($conn);
    }
}

echo json_encode($response);

function obtenerIdTipoMetodo($conn, $tipoMetodo) {
    $sql = "SELECT id_tipo FROM catalogo_metodos_pago WHERE nombre = ?";
    $stmt = consultaDB($conn, $sql, [$tipoMetodo]);

    if ($stmt && mysqli_stmt_bind_result($stmt, $idTipo) && mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        return $idTipo;
    }
    return null;
}
?>