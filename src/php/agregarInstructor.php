<?php
// agregarInstructor.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellidoPaterno = $_POST['apellidoPaterno'] ?? '';
    $apellidoMaterno = $_POST['apellidoMaterno'] ?? '';
    $edad = $_POST['edad'] ?? null;

    try {
        $stmt = $pdo->prepare("INSERT INTO Instructor (nombre, apellidoPaterno, apellidoMaterno, edad) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $apellidoPaterno, $apellidoMaterno, $edad]);
    } catch (PDOException $e) {
        error_log("Error al agregar instructor: " . $e->getMessage());
    }
    header('Location: /GestiFit/public/admin/adminInstructores.php');
    exit;
}
