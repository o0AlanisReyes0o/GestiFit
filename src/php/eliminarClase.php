<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] !== 'administrador') {
    $_SESSION['error'] = "No tienes permisos para esta acción";
    header("Location: /GestiFit/public/admin/adminClases.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_clase = filter_input(INPUT_POST, 'id_clase', FILTER_VALIDATE_INT);

    if (!$id_clase) {
        $_SESSION['error'] = "ID de clase no válido";
        header("Location: /GestiFit/public/admin/adminClases.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Eliminar reservas (si las hay)
        $stmtDelReservas = $pdo->prepare("DELETE FROM reservas_clases WHERE id_clase = ?");
        $stmtDelReservas->execute([$id_clase]);

        // 2. Eliminar días relacionados
        $stmtDelDias = $pdo->prepare("DELETE FROM clasedias WHERE idClase = ?");
        $stmtDelDias->execute([$id_clase]);

        // 3. Eliminar la clase principal
        $stmtDelClase = $pdo->prepare("DELETE FROM clases_grupales WHERE id_clase = ?");
        $stmtDelClase->execute([$id_clase]);

        $affectedRows = $stmtDelClase->rowCount();

        $pdo->commit();
        
        if ($affectedRows > 0) {
            $_SESSION['success'] = "Clase eliminada correctamente";
        } else {
            $_SESSION['warning'] = "No se encontró la clase con ID $id_clase";
        }
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al eliminar clase: " . $e->getMessage());
        $_SESSION['error'] = "Error al eliminar la clase: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Método no permitido";
}

header("Location: /GestiFit/public/admin/adminClases.php");
exit;