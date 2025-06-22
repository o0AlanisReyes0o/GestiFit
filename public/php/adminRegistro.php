<?php
// Incluye tu archivo de conexión PDO
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

// Verifica que todos los campos requeridos están presentes
if (
    isset($_POST['nombre']) &&
    isset($_POST['apellidoPaterno']) &&
    isset($_POST['apellidoMaterno']) &&
    isset($_POST['edad']) &&
    isset($_POST['usuario']) &&
    isset($_POST['direccion']) &&
    isset($_POST['email']) &&
    isset($_POST['telefono']) &&
    isset($_POST['contrasena']) // ✅ Nuevo campo obligatorio
) {
    try {
        // Hashear la contraseña de forma segura
        $hashContrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

        // Preparar la consulta
        $stmt = $pdo->prepare("
            INSERT INTO cliente (
                nombre, apellidoPaterno, apellidoMaterno, edad,
                usuario, contrasena, direccion, email, telefono
            ) VALUES (
                :nombre, :apellidoPaterno, :apellidoMaterno, :edad,
                :usuario, :contrasena, :direccion, :email, :telefono
            )
        ");

        // Ejecutar con los datos recibidos
        $stmt->execute([
            ':nombre' => $_POST['nombre'],
            ':apellidoPaterno' => $_POST['apellidoPaterno'],
            ':apellidoMaterno' => $_POST['apellidoMaterno'],
            ':edad' => intval($_POST['edad']),
            ':usuario' => $_POST['usuario'],
            ':contrasena' => $hashContrasena,
            ':direccion' => $_POST['direccion'],
            ':email' => $_POST['email'],
            ':telefono' => $_POST['telefono']
        ]);

        // Redirigir con éxito
        header("Location: /GestiFit/public/admin/adminClientes.html?registro=exito");
        exit();
    } catch (PDOException $e) {
        echo "❌ Error al registrar cliente: " . $e->getMessage();
    }
} else {
    echo "⚠️ Faltan campos obligatorios.";
}
