<?php
header('Content-Type: application/json');
require_once 'conexion.php';
session_start();

$response = ['success' => null, 'error' => null, 'redirect' => null];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    if (empty($_POST['usuario']) || empty($_POST['contrasena'])) {
        throw new Exception("Usuario y contraseña son obligatorios");
    }

    $usuario = trim($_POST['usuario']);
    $contrasena = $_POST['contrasena'];

    $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($contrasena, $user['contrasena'])) {
        throw new Exception("Credenciales incorrectas");
    }

    // Guardar datos en la sesión
    $_SESSION['idUsuario'] = $user['idUsuario'];
    $_SESSION['tipo'] = $user['tipo'];
    $_SESSION['nombre'] = $user['nombre'];

    $response['success'] = "¡Bienvenido " . $user['nombre'] . "!";
    $response['redirect'] = ($user['tipo'] === 'administrador')
        ? "/GestiFit/public/admin/admin.php"
        : "/GestiFit/public/usuario/index_usuario.php";

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
