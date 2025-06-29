<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../conexion.php';
session_start();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método no permitido';
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id_clase']) || empty($data['dia'])) {
    $response['message'] = 'Datos incompletos';
    echo json_encode($response);
    exit;
}

$id_clase = intval($data['id_clase']);
$dia = $data['dia'];

try {
    $conn = conectarDB();
    
    // 1. Obtener información de la clase y sus días
    $sqlClase = "SELECT c.*, GROUP_CONCAT(cd.dia) as dias_clase 
                FROM clases_grupales c
                JOIN clase_dias cd ON c.id_clase = cd.id_clase
                WHERE c.id_clase = ?
                GROUP BY c.id_clase";
    $stmtClase = mysqli_prepare($conn, $sqlClase);
    mysqli_stmt_bind_param($stmtClase, 'i', $id_clase);
    mysqli_stmt_execute($stmtClase);
    $resultClase = mysqli_stmt_get_result($stmtClase);
    $clase = mysqli_fetch_assoc($resultClase);
    
    if (!$clase) {
        throw new Exception("Clase no encontrada");
    }
    
    $dias_clase = explode(',', $clase['dias_clase']);
    
    // 2. Determinar días a inscribir
    $dias_a_inscribir = [];
    if ($dia === 'todos') {
        $dias_a_inscribir = $dias_clase;
    } elseif (in_array($dia, $dias_clase)) {
        $dias_a_inscribir = [$dia];
    } else {
        throw new Exception("Día no válido para esta clase");
    }
    
    // 3. Verificar cupos disponibles
    $sqlCupos = "SELECT c.cupo_maximo, COUNT(r.id_reserva) as inscritos
                FROM clases_grupales c
                LEFT JOIN reservas_clases r ON c.id_clase = r.id_clase
                WHERE c.id_clase = ?
                GROUP BY c.id_clase";
    $stmtCupos = mysqli_prepare($conn, $sqlCupos);
    mysqli_stmt_bind_param($stmtCupos, 'i', $id_clase);
    mysqli_stmt_execute($stmtCupos);
    $resultCupos = mysqli_stmt_get_result($stmtCupos);
    $cupos = mysqli_fetch_assoc($resultCupos);
    
    if (!$cupos || $cupos['inscritos'] >= $cupos['cupo_maximo']) {
        throw new Exception("La clase no tiene cupos disponibles");
    }
    
    // 4. Realizar las inscripciones
    $inscripciones_exitosas = 0;
    $conn->begin_transaction();
    
    foreach ($dias_a_inscribir as $dia_inscripcion) {
        // Verificar si ya está inscrito en este día
        $sqlCheck = "SELECT COUNT(*) FROM reservas_clases 
                    WHERE id_usuario = ? AND id_clase = ? AND dia = ?";
        $stmtCheck = mysqli_prepare($conn, $sqlCheck);
        mysqli_stmt_bind_param($stmtCheck, 'iis', $usuario_id, $id_clase, $dia_inscripcion);
        mysqli_stmt_execute($stmtCheck);
        $resultCheck = mysqli_stmt_get_result($stmtCheck);
        $count = mysqli_fetch_row($resultCheck)[0];
        
        if ($count === 0) {
            // Insertar la reserva
            $sqlInsert = "INSERT INTO reservas_clases 
                         (id_usuario, id_clase, dia, fecha_reserva) 
                         VALUES (?, ?, ?, NOW())";
            $stmtInsert = mysqli_prepare($conn, $sqlInsert);
            mysqli_stmt_bind_param($stmtInsert, 'iis', $usuario_id, $id_clase, $dia_inscripcion);
            if (mysqli_stmt_execute($stmtInsert)) {
                $inscripciones_exitosas++;
            }
        }
    }
    
    if ($inscripciones_exitosas > 0) {
        // Actualizar estado de la clase si se llena
        $sqlUpdateEstado = "UPDATE clases_grupales 
                           SET estado = 'llena' 
                           WHERE id_clase = ? 
                           AND (SELECT COUNT(*) FROM reservas_clases WHERE id_clase = ?) >= cupo_maximo";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdateEstado);
        mysqli_stmt_bind_param($stmtUpdate, 'ii', $id_clase, $id_clase);
        mysqli_stmt_execute($stmtUpdate);
        
        $conn->commit();
        $response['success'] = true;
        if ($dia === 'todos') {
            $response['message'] = "✓ Inscripción exitosa para todos los días";
        } else {
            $response['message'] = "✓ Inscripción exitosa para el " . ucfirst($dia);
        }
    } else {
        $conn->rollback();
        $response['message'] = "ℹ Ya estabas inscrito en los días seleccionados";
    }
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    $response['message'] = '✗ ' . $e->getMessage();
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}

echo json_encode($response);
?>