<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['idMembresia'] ?? null;

    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM Membresia WHERE idMembresia = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar membresía: " . $e->getMessage());
        }
    }
}

// Redirige de nuevo a la lista
header('Location: /GestiFit/public/admin/adminMembresias.php');
exit;
?>
