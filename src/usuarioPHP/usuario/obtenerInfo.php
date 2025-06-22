<?php
require_once '../conexion.php';
require_once '../../autenticacion.php';

header('Content-Type: application/json');

$conexion = conectarDB();

// Consulta modificada para nueva estructura de Usuario
$sql = "SELECT 
            idUsuario, 
            usuario,
            nombre, 
            apellidoPaterno, 
            apellidoMaterno, 
            email, 
            edad,
            telefono, 
            direccion, 
            tipo, 
            fechaRegistro,
            NOW() as last_access,
            'default-avatar.jpg' as avatar
        FROM Usuario 
        WHERE idUsuario = ?";

$stmt = consultaDB($conexion, $sql, [$usuario_id]);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no encontrado'
    ]);
    exit;
}

$usuario = mysqli_fetch_assoc($result);

// Formatear respuesta para nueva estructura
$respuesta = [
    'success' => true,
    'data' => [
        'id' => $usuario['idUsuario'],
        'username' => $usuario['usuario'],
        'name' => $usuario['nombre'] . ' ' . $usuario['apellidoPaterno'] . 
                 ($usuario['apellidoMaterno'] ? ' ' . $usuario['apellidoMaterno'] : ''),
        'email' => $usuario['email'],
        'age' => $usuario['edad'], // Cambiado de birthdate a age
        'phone' => $usuario['telefono'],
        'address' => $usuario['direccion'],
        'role' => $usuario['tipo'], // Cambiado de rol a tipo
        'registration_date' => date('d/m/Y H:i', strtotime($usuario['fechaRegistro'])),
        'last_access' => date('d/m/Y H:i', strtotime($usuario['last_access'])),
        'avatar' => '/GestiFit/public/img/gymProfileicon.jpg'
    ]
];

echo json_encode($respuesta);

mysqli_close($conexion);
?>