<?php
require_once __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_config'];
    $valor = $_POST['valor'];
    $descripcion = $_POST['descripcion'];
    $editable = $_POST['editable'];

    try {
        $stmt = $pdo->prepare("UPDATE configuraciones SET valor = ?, descripcion = ?, editable = ? WHERE id_config = ?");
        $stmt->execute([$valor, $descripcion, $editable, $id]);
        
        header("Location: /GestiFit/public/admin/adminAvisos.php?success=1");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar el aviso: " . $e->getMessage());
    }
}
?>