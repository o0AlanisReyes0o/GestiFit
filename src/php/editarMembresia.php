<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar sesión y permisos de administrador
    session_start();
    if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] !== 'administrador') {
        header('HTTP/1.1 403 Forbidden');
        exit('Acceso denegado');
    }

    // Sanitizar y validar datos de entrada
    $id = filter_var($_POST['idMembresia'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $nombre = trim($_POST['nombre'] ?? '');
    $costo = filter_var($_POST['costo'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $duracion = filter_var($_POST['duracionMeses'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $beneficios = trim($_POST['beneficios'] ?? '');

    // Validaciones completas
    if ($id <= 0) {
        header('Location: /GestiFit/public/admin/adminMembresias.php?error=id_invalido');
        exit;
    }

    if (empty($nombre) || strlen($nombre) > 50) {
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
        $pdo->beginTransaction();

        // Verificar si la membresía existe
        $stmtCheck = $pdo->prepare("SELECT idMembresia FROM membresia WHERE idMembresia = ?");
        $stmtCheck->execute([$id]);
        
        if ($stmtCheck->rowCount() === 0) {
            $pdo->rollBack();
            header('Location: /GestiFit/public/admin/adminMembresias.php?error=no_existe');
            exit;
        }

        // Actualizar todos los campos según la nueva estructura
        $stmtUpdate = $pdo->prepare("
            UPDATE membresia SET 
                nombre = :nombre,
                costo = :costo,
                duracionMeses = :duracion,
                descripcion = :descripcion,
                beneficios = :beneficios
            WHERE idMembresia = :id
        ");

        $stmtUpdate->execute([
            ':nombre' => $nombre,
            ':costo' => $costo,
            ':duracion' => $duracion,
            ':descripcion' => $descripcion,
            ':beneficios' => $beneficios,
            ':id' => $id
        ]);

        // Verificar si realmente se actualizó algún registro
        if ($stmtUpdate->rowCount() === 0) {
            $pdo->rollBack();
            header('Location: /GestiFit/public/admin/adminMembresias.php?error=sin_cambios');
            exit;
        }

        $pdo->commit();
        header('Location: /GestiFit/public/admin/adminMembresias.php?success=1');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al actualizar membresía ID $id: " . $e->getMessage());
        header('Location: /GestiFit/public/admin/adminMembresias.php?error=db');
        exit;
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Método no permitido');
}
?>