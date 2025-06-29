<?php
$server = "localhost";
$user = "root";
$password = "";
$database = "gestifitbd";

// Conexión mysqli
$conexion = mysqli_connect($server, $user, $password, $database);
if (!$conexion) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión mysqli']);
    exit;
}
mysqli_set_charset($conexion, "utf8mb4");

// Conexión PDO
try {
    $pdo = new PDO("mysql:host=$server;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión PDO: ' . $e->getMessage()]);
    exit;
}
?>
