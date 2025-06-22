<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idInstructor = $_POST['idInstructor'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $apellidoPaterno = $_POST['apellidoPaterno'] ?? '';
    $apellidoMaterno = $_POST['apellidoMaterno'] ?? '';
    $edad = $_POST['edad'] ?? null;

    if ($idInstructor && $nombre && $apellidoPaterno && is_numeric($edad)) {
        try {
            $stmt = $pdo->prepare("UPDATE Instructor SET nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, edad = ? WHERE idInstructor = ?");
            $stmt->execute([$nombre, $apellidoPaterno, $apellidoMaterno, $edad, $idInstructor]);

            header("Location: /GestiFit/public/admin/adminInstructores.php?actualizado=1");
            exit;
        } catch (PDOException $e) {
            error_log("Error al actualizar instructor: " . $e->getMessage());
            header("Location: /GestiFit/public/admin/adminInstructores.php?error=1");
            exit;
        }
    } else {
        header("Location: /GestiFit/public/admin/adminInstructores.php?invalido=1");
        exit;
    }
} else {
    header("Location: /GestiFit/public/admin/adminInstructores.php");
    exit;
}
