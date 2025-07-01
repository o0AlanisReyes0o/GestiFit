<?php
require_once __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clave = $_POST['clave'];
    $valor = $_POST['valor'];
    $descripcion = $_POST['descripcion'];
    $editable = $_POST['editable'];

    // Validar que la clave comience con 'aviso_'
    if (strpos($clave, 'aviso_') !== 0) {
        die("La clave debe comenzar con 'aviso_'");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO configuraciones (clave, valor, descripcion, editable) VALUES (?, ?, ?, ?)");
        $stmt->execute([$clave, $valor, $descripcion, $editable]);
        
        header("Location: /GestiFit/public/admin/adminAvisos.php?success=1");
        exit;
    } catch (PDOException $e) {
        die("Error al agregar el aviso: " . $e->getMessage());
    }
}
?>