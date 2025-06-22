<?php
$host = 'localhost';
$db   = 'gimnasio';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Puedes usar error_log para confirmar conexión exitosa si quieres
    // error_log('✅ Conexión establecida correctamente.');
} catch (PDOException $e) {
    error_log("❌ Error de conexión a la base de datos: " . $e->getMessage());
    die("Error en la conexión. Intenta más tarde.");
}
?>
