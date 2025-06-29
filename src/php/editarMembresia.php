<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['idMembresia'] ?? null;
  $nombre = $_POST['nombre'] ?? '';
  $costo = $_POST['costo'] ?? '';
  $duracion = $_POST['duracionMeses'] ?? '';

  if ($id && $nombre && $costo && $duracion) {
    $stmt = $pdo->prepare("UPDATE Membresia SET nombre = ?, costo = ?, duracionMeses = ? WHERE idMembresia = ?");
    $stmt->execute([$nombre, $costo, $duracion, $id]);
  }

  header('Location: /GestiFit/public/admin/adminMembresias.php');
  exit;
}
?>
