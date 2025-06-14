<?php
session_start();
require_once 'admin-config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  $stmt = $pdo->prepare("SELECT id, nombre, password FROM admins WHERE email = ?");
  $stmt->execute([$email]);
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($admin && password_verify($password, $admin['password'])) {
    $_SESSION['admin_name'] = $admin['nombre'];
    header("Location: ../admin-dashboard.htm");
    exit();
  } else {
    echo "<script>alert('Credenciales incorrectas'); window.history.back();</script>";
  }
}
?>