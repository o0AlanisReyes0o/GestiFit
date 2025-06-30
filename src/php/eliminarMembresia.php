<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

// Verificar que sea una solicitud POST y que el usuario sea administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    // Validar permisos
    if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] !== 'administrador') {
        header('HTTP/1.1 403 Forbidden');
        exit('Acceso denegado');
    }

    $id = filter_var($_POST['idMembresia'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

    if ($id <= 0) {
        header('Location: /GestiFit/public/admin/adminMembresias.php?error=id_invalido');
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Verificar si hay usuarios con esta membresía activa
        $stmtCheck = $pdo->prepare("
            SELECT COUNT(*) 
            FROM usuariomembresia 
            WHERE idMembresia = ? 
            AND estado = 'activa'
            AND (fechaFin IS NULL OR fechaFin >= CURDATE())
        ");
        $stmtCheck->execute([$id]);
        $usuariosActivos = $stmtCheck->fetchColumn();

        if ($usuariosActivos > 0) {
            $pdo->rollBack();
            header('Location: /GestiFit/public/admin/adminMembresias.php?error=membresia_en_uso');
            exit;
        }

        // 2. Eliminar la membresía
        $stmtDelete = $pdo->prepare("DELETE FROM membresia WHERE idMembresia = ?");
        $stmtDelete->execute([$id]);

        // 3. Eliminar relaciones históricas (opcional, según requisitos)
        $stmtDeleteRel = $pdo->prepare("DELETE FROM usuariomembresia WHERE idMembresia = ?");
        $stmtDeleteRel->execute([$id]);

        $pdo->commit();
        header('Location: /GestiFit/public/admin/adminMembresias.php?success=1');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al eliminar membresía ID $id: " . $e->getMessage());
        header('Location: /GestiFit/public/admin/adminMembresias.php?error=db');
        exit;
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Método no permitido');
}
?>