<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idClase = $_POST['idClase'] ?? '';

    if ($idClase) {
        try {
            $pdo->beginTransaction();

            // Borrar días relacionados
            $stmtDelDias = $pdo->prepare("DELETE FROM clasedias WHERE idClase = ?");
            $stmtDelDias->execute([$idClase]);

            // Borrar clase
            $stmtDelClase = $pdo->prepare("DELETE FROM clase WHERE idClase = ?");
            $stmtDelClase->execute([$idClase]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Error al eliminar clase: " . $e->getMessage());
        }
    }
}

header("Location: adminClases.php");
exit;
