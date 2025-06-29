<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ['success' => null, 'error' => null];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $required = ['username', 'name', 'apellidopat', 'email', 'password'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("El campo $field es obligatorio");
        }
    }

    $username = trim($_POST['username']);

    // Detectar tipo de usuario
    if (preg_match('/instructor/i', $username)) {
        $tipoUsuario = 'instructor';
    } elseif (preg_match('/^\d/', $username)) {
        $tipoUsuario = 'administrador';
    } else {
        $tipoUsuario = 'cliente';
    }

    $userData = [
        'nombre' => trim($_POST['name']),
        'apellidoPaterno' => trim($_POST['apellidopat']),
        'apellidoMaterno' => trim($_POST['apellidomat'] ?? ''),
        'edad' => isset($_POST['age']) ? (int)$_POST['age'] : null,
        'tipo' => $tipoUsuario,
        'usuario' => $username,
        'contrasena' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
        'telefono' => $_POST['phone'] ?? null,
        'direccion' => $_POST['address'] ?? null
    ];

    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El email no es válido");
    }

    $query = "INSERT INTO Usuario (
        nombre, apellidoPaterno, apellidoMaterno, edad, tipo, usuario, 
        contrasena, email, telefono, direccion, fechaRegistro
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        $userData['nombre'],
        $userData['apellidoPaterno'],
        $userData['apellidoMaterno'],
        $userData['edad'],
        $userData['tipo'],
        $userData['usuario'],
        $userData['contrasena'],
        $userData['email'],
        $userData['telefono'],
        $userData['direccion']
    ]);

    $response['success'] = "¡Registro exitoso! Bienvenido/a " . $userData['nombre'];

} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        $response['error'] = strpos($e->getMessage(), 'usuario') !== false 
            ? "El nombre de usuario ya existe" 
            : "El email ya está registrado";
    } else {
        $response['error'] = "Error en la base de datos: " . $e->getMessage();
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
