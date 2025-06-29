<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_POST['idUsuario'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $apellidoPaterno = $_POST['apellidoPaterno'] ?? '';
    $apellidoMaterno = $_POST['apellidoMaterno'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $idMembresia = $_POST['membresia'] ?? null; // Aquí el ID de la membresía

    if ($idUsuario) {
        // Actualizar datos de usuario
        $stmt = $pdo->prepare("UPDATE Usuario SET nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, email = ?, telefono = ? WHERE idUsuario = ?");
        $stmt->execute([$nombre, $apellidoPaterno, $apellidoMaterno, $email, $telefono, $idUsuario]);

        // Verificar si ya tiene membresía asignada
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM UsuarioMembresia WHERE idUsuario = ?");
        $stmtCheck->execute([$idUsuario]);
        $existe = $stmtCheck->fetchColumn();

        if ($existe) {
            // Actualizar membresía existente
            $stmt = $pdo->prepare("UPDATE UsuarioMembresia SET idMembresia = ? WHERE idUsuario = ?");
            $stmt->execute([$idMembresia ?: null, $idUsuario]);
        } else {
            // Insertar nueva membresía si se seleccionó alguna
            if ($idMembresia) {
                $stmt = $pdo->prepare("INSERT INTO UsuarioMembresia (idUsuario, idMembresia) VALUES (?, ?)");
                $stmt->execute([$idUsuario, $idMembresia]);
            }
        }
    }

    // Redirigir a la página de usuarios
    header('Location: /GestiFit/public/admin/adminClientes.php');
    exit;
}
?>
