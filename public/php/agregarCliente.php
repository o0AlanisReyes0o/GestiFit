<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if (
    isset($_POST['nombre']) &&
    isset($_POST['apellidoPaterno']) &&
    isset($_POST['apellidoMaterno']) &&
    isset($_POST['edad']) &&
    isset($_POST['usuario']) &&
    isset($_POST['direccion']) &&
    isset($_POST['email']) &&
    isset($_POST['telefono']) &&
    isset($_POST['contrasena'])
) {
    try {
        // 1. Validar que el usuario no exista
        $stmtCheckUser = $pdo->prepare("SELECT COUNT(*) FROM Usuario WHERE usuario = :usuario");
        $stmtCheckUser->execute([':usuario' => $_POST['usuario']]);
        if ($stmtCheckUser->fetchColumn() > 0) {
            header("Location: /GestiFit/public/admin/adminClientes.php?error=usuario");
            exit();
        }

        // 2. Validar que el email no exista
        $stmtCheckEmail = $pdo->prepare("SELECT COUNT(*) FROM Usuario WHERE email = :email");
        $stmtCheckEmail->execute([':email' => $_POST['email']]);
        if ($stmtCheckEmail->fetchColumn() > 0) {
            header("Location: /GestiFit/public/admin/adminClientes.php?error=email");
            exit();
        }

        $pdo->beginTransaction();

        // 3. Insertar nuevo cliente
        $stmt = $pdo->prepare("
            INSERT INTO Usuario (
                nombre, apellidoPaterno, apellidoMaterno, edad,
                tipo, usuario, contrasena, direccion, email, telefono
            ) VALUES (
                :nombre, :apellidoPaterno, :apellidoMaterno, :edad,
                'cliente', :usuario, :contrasena, :direccion, :email, :telefono
            )
        ");

        $stmt->execute([
            ':nombre' => $_POST['nombre'],
            ':apellidoPaterno' => $_POST['apellidoPaterno'],
            ':apellidoMaterno' => $_POST['apellidoMaterno'],
            ':edad' => intval($_POST['edad']),
            ':usuario' => $_POST['usuario'],
            ':contrasena' => password_hash($_POST['contrasena'], PASSWORD_DEFAULT),
            ':direccion' => $_POST['direccion'],
            ':email' => $_POST['email'],
            ':telefono' => $_POST['telefono']
        ]);

        $idUsuario = $pdo->lastInsertId();

        // 4. Insertar en UsuarioMembresia si aplica
        if (!empty($_POST['membresia'])) {
            $stmtM = $pdo->prepare("
                INSERT INTO UsuarioMembresia (idUsuario, idMembresia, fechaInicio)
                VALUES (:idUsuario, :idMembresia, CURDATE())
            ");
            $stmtM->execute([
                ':idUsuario' => $idUsuario,
                ':idMembresia' => $_POST['membresia']
            ]);
        }

        $pdo->commit();
        header("Location: /GestiFit/public/admin/adminClientes.php?registro=exito");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "❌ Error al registrar cliente: " . $e->getMessage();
    }
} else {
    echo "⚠️ Faltan campos obligatorios.";
}