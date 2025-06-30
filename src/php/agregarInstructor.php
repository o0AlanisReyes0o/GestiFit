<?php
// agregarInstructor.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

// Iniciar sesión para manejar mensajes de feedback
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $apellidoPaterno = $_POST['apellidoPaterno'] ?? '';
    $apellidoMaterno = $_POST['apellidoMaterno'] ?? '';
    $edad = $_POST['edad'] ?? null;
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    
    // Validar campos obligatorios
    if (empty($nombre) || empty($apellidoPaterno) || empty($edad) || 
        empty($email) || empty($telefono) || empty($usuario) || empty($contrasena)) {
        $_SESSION['error'] = "Todos los campos obligatorios deben ser completados";
        header('Location: /GestiFit/public/admin/adminInstructores.php');
        exit;
    }

    // Hashear la contraseña
    $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

    try {
        // Verificar si el usuario ya existe
        $check_user = $pdo->prepare("SELECT idUsuario FROM usuario WHERE usuario = ?");
        $check_user->execute([$usuario]);
        
        if ($check_user->rowCount() > 0) {
            $_SESSION['error'] = "El nombre de usuario ya está en uso";
            header('Location: /GestiFit/public/admin/adminInstructores.php');
            exit;
        }

        // Verificar si el email ya existe
        $check_email = $pdo->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
        $check_email->execute([$email]);
        
        if ($check_email->rowCount() > 0) {
            $_SESSION['error'] = "El correo electrónico ya está registrado";
            header('Location: /GestiFit/public/admin/adminInstructores.php');
            exit;
        }

        // Insertar nuevo instructor
        $stmt = $pdo->prepare(
            "INSERT INTO usuario (
                nombre, 
                apellidoPaterno, 
                apellidoMaterno, 
                edad, 
                tipo, 
                usuario, 
                contrasena, 
                email, 
                telefono, 
                fechaRegistro
            ) VALUES (?, ?, ?, ?, 'instructor', ?, ?, ?, ?, NOW())");
        
        $success = $stmt->execute([
            $nombre, 
            $apellidoPaterno, 
            $apellidoMaterno, 
            $edad, 
            $usuario, 
            $hashed_password, 
            $email, 
            $telefono
        ]);

        if ($success) {
            $_SESSION['success'] = "Instructor agregado correctamente";
        } else {
            $_SESSION['error'] = "Error al agregar instructor";
        }
        
    } catch (PDOException $e) {
        error_log("Error al agregar instructor: " . $e->getMessage());
        $_SESSION['error'] = "Ocurrió un error al agregar el instructor: " . $e->getMessage();
    }
    
    header('Location: /GestiFit/public/admin/adminInstructores.php');
    exit;
}