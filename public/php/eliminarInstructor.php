<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idInstructor'])) {
    $idInstructor = $_POST['idInstructor'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Instructor WHERE idInstructor = ?");
        $stmt->execute([$idInstructor]);

        // Redirigir después de la eliminación
        header("Location: /GestiFit/public/admin/adminInstructores.php");
        exit;
    } catch (PDOException $e) {
        error_log("Error al eliminar instructor: " . $e->getMessage());
        header("Location: /GestiFit/public/admin/adminInstructores.php?error=1");
        exit;
    }
} else {
    // Acceso no válido
    header("Location: /GestiFit/public/admin/adminInstructores.php");
    exit;
}
