<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que el usuario sea administrador
    session_start();
    if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] !== 'administrador') {
        header('HTTP/1.1 403 Forbidden');
        exit('Acceso denegado');
    }

    // Obtener y sanitizar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $costo = filter_var($_POST['costo'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $duracion = filter_var($_POST['duracionMeses'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $beneficios = trim($_POST['beneficios'] ?? '');

    // Validaciones básicas
    if (empty($nombre)) {
        header('Location: /GestiFit/public/admin/adminMembresias.php?error=nombre');
        exit;
    }

    if ($costo <= 0) {
        header('Location: /GestiFit/public/admin/adminMembresias.php?error=costo');
        exit;
    }

    if ($duracion <= 0) {
        header('Location: /GestiFit/public/admin/adminMembresias.php?error=duracion');
        exit;
    }

    try {
        // Insertar en la base de datos con transacción
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("
            INSERT INTO membresia (
                nombre, costo, duracionMeses, descripcion, beneficios
            ) VALUES (
                :nombre, :costo, :duracion, :descripcion, :beneficios
            )
        ");
        
        $stmt->execute([
            ':nombre' => $nombre,
            ':costo' => $costo,
            ':duracion' => $duracion,
            ':descripcion' => $descripcion,
            ':beneficios' => $beneficios
        ]);

        $pdo->commit();
        header('Location: /GestiFit/public/admin/adminMembresias.php?success=1');
        exit;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al agregar membresía: " . $e->getMessage());
        header('Location: /GestiFit/public/admin/adminMembresias.php?error=db');
        exit;
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Método no permitido');
}
?>