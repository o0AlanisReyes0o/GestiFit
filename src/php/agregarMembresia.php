<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'] ?? '';
  $costo = $_POST['costo'] ?? '';
  $duracion = $_POST['duracionMeses'] ?? '';

  if ($nombre && $costo && $duracion) {
    $stmt = $pdo->prepare("INSERT INTO Membresia (nombre, costo, duracionMeses) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $costo, $duracion]);
  }

  header('Location: /GestiFit/public/admin/adminMembresias.php');
  exit;
}
?>
