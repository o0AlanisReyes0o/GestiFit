<?php
require_once __DIR__ . '/../../src/conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$accion = $_GET['action'] ?? '';

if ($accion === 'listar') {
    header('Content-Type: application/json');
    $sql = "SELECT id_casillero, numero, estado FROM casillero ORDER BY numero";
    $result = mysqli_query($conexion, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

if ($accion === 'reservar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    // Verificar disponibilidad
    $query = "SELECT estado FROM casillero WHERE id_casillero = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $casillero = mysqli_fetch_assoc($res);

    if (!$casillero || $casillero['estado'] !== 'disponible') {
        echo json_encode(['success' => false, 'message' => 'No disponible']);
        exit;
    }

    // Actualizar estado
    $update = mysqli_prepare($conexion, "UPDATE casillero SET estado = 'reservado' WHERE id_casillero = ?");
    mysqli_stmt_bind_param($update, "i", $id);
    mysqli_stmt_execute($update);

    echo json_encode(['success' => true]);
    exit;
}

// Si la acción no es válida
http_response_code(400);
echo json_encode(['error' => 'Acción no válida']);
