<?php
require_once __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_config'];

    try {
        // Verificar si el aviso es editable antes de eliminarlo
        $stmt = $pdo->prepare("SELECT editable FROM configuraciones WHERE id_config = ?");
        $stmt->execute([$id]);
        $aviso = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$aviso || $aviso['editable'] == 0) {
            die("No se puede eliminar este aviso porque no es editable.");
        }

        $stmt = $pdo->prepare("DELETE FROM configuraciones WHERE id_config = ?");
        $stmt->execute([$id]);
        
        header("Location: /GestiFit/public/admin/adminAvisos.php?success=1");
        exit;
    } catch (PDOException $e) {
        die("Error al eliminar el aviso: " . $e->getMessage());
    }
}
?>