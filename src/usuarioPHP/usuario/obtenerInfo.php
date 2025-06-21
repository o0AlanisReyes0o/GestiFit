<?php
require_once '../conexion.php';

header('Content-Type: application/json');


$usuario_id = 2;
$conexion = conectarDB();

// Consulta para obtener los datos del usuario
$sql = "SELECT 
            id_usuario, 
            username, 
            nombre, 
            apellido, 
            email, 
            fecha_nacimiento, 
            telefono, 
            direccion, 
            rol, 
            estado,
            fecha_registro,
            -- Último acceso (puedes almacenarlo en otra tabla si lo necesitas)
            NOW() as last_access,
            -- Avatar (asumiendo que lo tienes en otra tabla o campo)
            'default-avatar.jpg' as avatar
        FROM usuarios 
        WHERE id_usuario = ?";

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

// Formatear fecha de nacimiento si existe
$fecha_nacimiento = $usuario['fecha_nacimiento'] ? date('d/m/Y', strtotime($usuario['fecha_nacimiento'])) : null;

// Formatear respuesta
$respuesta = [
    'success' => true,
    'data' => [
        'id' => $usuario['id_usuario'],
        'username' => $usuario['username'],
        'name' => $usuario['nombre'] . ' ' . $usuario['apellido'],
        'email' => $usuario['email'],
        'birthdate' => $fecha_nacimiento,
        'phone' => $usuario['telefono'],
        'address' => $usuario['direccion'],
        'role' => $usuario['rol'],
        'status' => $usuario['estado'],
        'registration_date' => date('d/m/Y H:i', strtotime($usuario['fecha_registro'])),
        'last_access' => date('d/m/Y H:i', strtotime($usuario['last_access'])),
        'avatar' => '/GestiFit/public/img/gymProfileicon.jpg'
    ]
];

echo json_encode($respuesta);

mysqli_close($conexion);
?>