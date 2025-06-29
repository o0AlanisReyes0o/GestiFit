<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_POST['idUsuario'] ?? null;

    if ($idUsuario) {
        $stmt = $pdo->prepare("DELETE FROM Usuario WHERE idUsuario = ?");
        $stmt->execute([$idUsuario]);
        header('Location: /GestiFit/public/admin/adminClientes.php');
        exit;
    } else {
        echo "ID de usuario no proporcionado.";
    }
} else {
    echo "Método HTTP no permitido.";
}
