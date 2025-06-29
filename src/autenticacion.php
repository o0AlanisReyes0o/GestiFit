<?php
session_start();

// if (!isset($_SESSION['usuario_id'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Usuario no autenticado']);
//     exit;
// }

$usuario_id = $_SESSION['idUsuario'];
//$usuario_id = 1; // Para pruebas, eliminar en producción

?>