<?php
require_once __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_rutina'];
    $nombre = $_POST['nombre_rutina'];
    $nivel = $_POST['nivel_rutina'];
    $descripcion = $_POST['descripcion'];
    $duracion = $_POST['duracion_semanas'];
    $dias = $_POST['dias_por_semana'];
    $objetivo = $_POST['objetivo'];
    $equipamiento = $_POST['equipamiento_necesario'];
    $instrucciones = $_POST['instrucciones'];
    $video = $_POST['video_url'];
    $imagen = $_POST['imagen_url'];
    $activa = isset($_POST['activa']) ? 1 : 0;

    try {
        $sql = "UPDATE rutinas SET 
            nombre_rutina = ?, nivel_rutina = ?, descripcion = ?, 
            duracion_semanas = ?, dias_por_semana = ?, objetivo = ?, 
            equipamiento_necesario = ?, instrucciones = ?, video_url = ?, 
            imagen_url = ?, activa = ?
            WHERE id_rutina = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nombre, $nivel, $descripcion, $duracion, $dias, $objetivo, 
            $equipamiento, $instrucciones, $video, $imagen, $activa, $id
        ]);
        
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