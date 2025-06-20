<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../conexion.php';

$response = ['exito' => false, 'mensaje' => ''];

$conn = conectarDB();

try {
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos");
    }

    // Iniciar transacción para asegurar integridad de datos
    mysqli_begin_transaction($conn);

    $datos = $_POST;

    // Validación básica de datos requeridos
    if (empty($datos['id_membresia']) || empty($datos['monto'])) {
        throw new Exception('Datos de pago incompletos');
    }

    // ID de usuario (en producción obtendrías esto de la sesión)
    $idUsuario = 1;
    $idMetodoPago = null;

    // === SECCIÓN 1: MANEJO DEL MÉTODO DE PAGO ===
    if (isset($datos['nuevo_metodo']) && $datos['nuevo_metodo'] == '1') {
        // Procesar nuevo método de pago
        $tipoMetodoPago = $datos['tipo_metodo'] ?? 'otro';
        $alias = $datos['alias_metodo'] ?? 'Nuevo método';

        // Obtener id_tipo desde el catálogo
        $idTipo = obtenerIdTipoMetodo($conn, $tipoMetodoPago);

        if (!$idTipo) {
            throw new Exception("Tipo de método de pago no válido");
        }

        $sqlNuevoMetodo = "INSERT INTO metodos_pago 
                          (id_usuario, id_tipo, alias, fecha_creacion, activo) 
                          VALUES (?, ?, ?, NOW(), TRUE)";
        $stmtNuevoMetodo = consultaDB($conn, $sqlNuevoMetodo, [
            $idUsuario, $idTipo, $alias
        ]);

        if ($stmtNuevoMetodo === false) {
            throw new Exception("Error al registrar nuevo método de pago: " . mysqli_error($conn));
        }

        $idMetodoPago = mysqli_insert_id($conn);
        mysqli_stmt_close($stmtNuevoMetodo);
    } else {
        // Usar método de pago existente
        if (empty($datos['id_metodo_pago'])) {
            throw new Exception('Método de pago no especificado');
        }
        $idMetodoPago = $datos['id_metodo_pago'];
    }

    // === SECCIÓN 2: REGISTRO DEL PAGO ===
    $referencia = 'SIM-' . strtoupper(uniqid());
    $sqlPago = "INSERT INTO pagos 
                (id_usuario, id_metodo_pago, id_membresia, fecha_pago, monto, estado_pago, referencia_pago) 
                VALUES (?, ?, ?, NOW(), ?, 'completado', ?)";

    $stmtPago = consultaDB($conn, $sqlPago, [
        $idUsuario,
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

    // === SECCIÓN 3: ACTUALIZACIÓN DE LA MEMBRESÍA ===
    
    // 3.1 Obtener duración de la membresía
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

    // Calcular fecha de finalización
    $fechaFin = date('Y-m-d', strtotime("+{$duracion_dias} days"));

    // 3.2 Verificar si ya tiene membresía activa
    $sqlVerificar = "SELECT id_relacion FROM usuarios_membresias 
                    WHERE id_usuario = ? AND estado = 'activa'";
    $stmtVerificar = consultaDB($conn, $sqlVerificar, [$idUsuario]);
    
    if ($stmtVerificar === false) {
        throw new Exception("Error al verificar membresía: " . mysqli_error($conn));
    }
    
    mysqli_stmt_store_result($stmtVerificar);
    $tieneMembresiaActiva = (mysqli_stmt_num_rows($stmtVerificar) > 0);
    mysqli_stmt_free_result($stmtVerificar);
    mysqli_stmt_close($stmtVerificar);

    if ($tieneMembresiaActiva) {
        // 3.3 Actualizar membresía existente
        $sqlActualizar = "UPDATE usuarios_membresias 
                         SET id_membresia = ?, fecha_inicio = CURDATE(), fecha_fin = ?, id_pago = ?
                         WHERE id_usuario = ? AND estado = 'activa'";
        $stmtActualizar = consultaDB($conn, $sqlActualizar, [
            $datos['id_membresia'],
            $fechaFin,
            $idPago,
            $idUsuario
        ]);
        
        if ($stmtActualizar === false) {
            throw new Exception("Error al actualizar membresía: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmtActualizar);
    } else {
        // 3.4 Crear nueva membresía
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

    // Confirmar todas las operaciones
    mysqli_commit($conn);

    $response['exito'] = true;
    $response['mensaje'] = 'Pago procesado y membresía actualizada correctamente';
    $response['referencia'] = $referencia;
    $response['id_pago'] = $idPago;

} catch (Exception $e) {
    // Revertir en caso de error
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

// Función auxiliar para obtener id_tipo del método de pago
function obtenerIdTipoMetodo($conn, $tipoMetodo) {
    $sql = "SELECT id_tipo FROM catalogo_metodos_pago WHERE nombre = ?";
    $stmt = consultaDB($conn, $sql, [$tipoMetodo]);

    if ($stmt && mysqli_stmt_bind_result($stmt, $idTipo) && mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        return $idTipo;
    }
    return null;
}