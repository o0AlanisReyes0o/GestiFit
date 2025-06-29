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

    // Start transaction
    mysqli_begin_transaction($conn);

    $datos = $_POST;

    // Validate required fields
    if (empty($datos['id_membresia']) || empty($datos['monto'])) {
        throw new Exception('Datos de pago incompletos');
    }

    // Process payment method
    $idMetodoPago = processPaymentMethod($conn, $datos, $usuario_id);

    // Register payment
    $referencia = 'GESTI-' . strtoupper(uniqid());
    $idPago = registerPayment($conn, $usuario_id, $idMetodoPago, $datos, $referencia);

    // Process membership
    processMembership($conn, $usuario_id, $datos['id_membresia'], $idPago);

    // Commit transaction
    mysqli_commit($conn);

    $response = [
        'exito' => true,
        'mensaje' => 'Pago procesado correctamente',
        'referencia' => $referencia,
        'id_pago' => $idPago
    ];

} catch (Exception $e) {
    if ($conn) {
        mysqli_rollback($conn);
    }
    $response['mensaje'] = $e->getMessage();
    http_response_code(500);
} finally {
    if ($conn) {
        // Close all statements and connection
        mysqli_close($conn);
    }
    // Ensure JSON output
    echo json_encode($response);
    exit;
}

// Helper functions
function processPaymentMethod($conn, $datos, $usuario_id) {
    if (!isset($datos['nuevo_metodo']) || $datos['nuevo_metodo'] != '1') {
        if (empty($datos['id_metodo_pago'])) {
            throw new Exception('Método de pago no especificado');
        }
        return $datos['id_metodo_pago'];
    }

    $alias = $datos['alias_metodo'] ?? 'Nuevo método';
    $ultimosDigitos = $datos['ultimos_digitos'] ?? null;
    $idTipo = $datos['tipo_metodo'] ?? 0;

    if (!$idTipo) {
        throw new Exception("Tipo de método de pago no válido");
    }

    $sql = "INSERT INTO metodos_pago 
            (id_usuario, id_tipo, alias, ultimos_digitos, fecha_creacion, activo) 
            VALUES (?, ?, ?, ?, NOW(), 1)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt || !mysqli_stmt_bind_param($stmt, "iiss", $usuario_id, $idTipo, $alias, $ultimosDigitos) || !mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al registrar método de pago");
    }

    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id;
}

function registerPayment($conn, $usuario_id, $idMetodoPago, $datos, $referencia) {
    $sql = "INSERT INTO pagos 
            (id_usuario, id_metodo_pago, id_membresia, fecha_pago, monto, estado_pago, referencia_pago) 
            VALUES (?, ?, ?, NOW(), ?, 'completado', ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt || !mysqli_stmt_bind_param($stmt, "iiids", $usuario_id, $idMetodoPago, $datos['id_membresia'], $datos['monto'], $referencia) || !mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al registrar el pago");
    }

    $id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id;
}

function processMembership($conn, $usuario_id, $idMembresia, $idPago) {
    // Get membership duration
    $sql = "SELECT duracionMeses FROM membresia WHERE idMembresia = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt || !mysqli_stmt_bind_param($stmt, "i", $idMembresia) || !mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al obtener duración de membresía");
    }

    mysqli_stmt_bind_result($stmt, $duracionMeses);
    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception("Membresía no encontrada");
    }
    mysqli_stmt_close($stmt);

    $fechaFin = date('Y-m-d', strtotime("+{$duracionMeses} months"));

    // Check existing membership
    $sql = "SELECT idUsuario FROM usuariomembresia WHERE idUsuario = ? AND estado = 'activa'";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt || !mysqli_stmt_bind_param($stmt, "i", $usuario_id) || !mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al verificar membresía existente");
    }

    mysqli_stmt_store_result($stmt);
    $exists = (mysqli_stmt_num_rows($stmt)) > 0;
    mysqli_stmt_close($stmt);

    // Update or insert membership
    if ($exists) {
        $sql = "UPDATE usuariomembresia 
               SET idMembresia = ?, fechaInicio = CURDATE(), fechaFin = ?
               WHERE idUsuario = ? AND estado = 'activa'";
    } else {
        $sql = "INSERT INTO usuariomembresia 
               (idUsuario, idMembresia, fechaInicio, fechaFin, estado) 
               VALUES (?, ?, CURDATE(), ?, 'activa')";
    }

    $stmt = mysqli_prepare($conn, $sql);
    $params = $exists ? 
        [$idMembresia, $fechaFin, $usuario_id] : 
        [$usuario_id, $idMembresia, $fechaFin];

    if (!$stmt || !mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params) || !mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al " . ($exists ? "actualizar" : "crear") . " membresía");
    }
    mysqli_stmt_close($stmt);
}
?>