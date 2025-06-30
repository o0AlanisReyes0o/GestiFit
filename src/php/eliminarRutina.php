<?php
require_once __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_rutina'];

    try {
        $sql = "DELETE FROM rutinas WHERE id_rutina = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        header("Location: /GestiFit/public/admin/adminRutinas.php?success=1");
        exit();
    } catch (PDOException $e) {
        header("Location: /GestiFit/public/admin/adminRutinas.php?error=1");
        exit();
    }
} else {
    header("Location: /GestiFit/public/admin/adminRutinas.php");
    exit();
}
?>