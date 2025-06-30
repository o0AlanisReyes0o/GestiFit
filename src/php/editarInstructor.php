<?php
// editarInstructor.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

// Iniciar sesión para manejar mensajes de feedback
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $idUsuario = $_POST['idUsuario'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $apellidoPaterno = $_POST['apellidoPaterno'] ?? '';
    $apellidoMaterno = $_POST['apellidoMaterno'] ?? '';
    $edad = $_POST['edad'] ?? null;
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    // Validar campos obligatorios
    if (empty($idUsuario) || empty($nombre) || empty($apellidoPaterno) || 
        empty($edad) || empty($email) || empty($telefono)) {
        $_SESSION['error'] = "Todos los campos obligatorios deben ser completados";
        header("Location: /GestiFit/public/admin/adminInstructores.php");
        exit;
    }

    try {
        // Verificar si el instructor existe
        $check_stmt = $pdo->prepare("SELECT idUsuario FROM usuario WHERE idUsuario = ? AND tipo = 'instructor'");
        $check_stmt->execute([$idUsuario]);
        
        if ($check_stmt->rowCount() === 0) {
            $_SESSION['error'] = "El instructor no existe";
            header("Location: /GestiFit/public/admin/adminInstructores.php");
            exit;
        }

        // Verificar si el email ya está en uso por otro usuario
        $check_email = $pdo->prepare("SELECT idUsuario FROM usuario WHERE email = ? AND idUsuario != ?");
        $check_email->execute([$email, $idUsuario]);
        
        if ($check_email->rowCount() > 0) {
            $_SESSION['error'] = "El correo electrónico ya está registrado por otro usuario";
            header("Location: /GestiFit/public/admin/adminInstructores.php");
            exit;
        }

        // Actualizar los datos del instructor
        $update_stmt = $pdo->prepare(
            "UPDATE usuario SET 
                nombre = ?, 
                apellidoPaterno = ?, 
                apellidoMaterno = ?, 
                edad = ?, 
                email = ?, 
                telefono = ? 
            WHERE idUsuario = ?"
        );
        
        $success = $update_stmt->execute([
            $nombre, 
            $apellidoPaterno, 
            $apellidoMaterno, 
            $edad, 
            $email, 
            $telefono, 
            $idUsuario
        ]);

        if ($success) {
            $_SESSION['success'] = "Instructor actualizado correctamente";
        } else {
            $_SESSION['error'] = "No se realizaron cambios en el instructor";
        }

    } catch (PDOException $e) {
        error_log("Error al actualizar instructor: " . $e->getMessage());
        $_SESSION['error'] = "Error al actualizar instructor: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Acceso no válido";
}

header("Location: /GestiFit/public/admin/adminInstructores.php");
exit;
?>