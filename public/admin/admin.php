<?php
// Usar rutas absolutas para mayor seguridad
require_once __DIR__ . '/../../src/conexion.php';

// Iniciar sesión antes de cualquier verificación
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['idUsuario'])) {  // Cambiado de 'user_id' a 'usuario_id'
    header("Location: /GestiFit/public/public/index.html");
    exit;
}

// Obtener información del usuario
$userId = $_SESSION['idUsuario'];  // Cambiado para coincidir con tu otro código
$query = "SELECT * FROM Usuario WHERE idUsuario = ?";
$stmt = mysqli_prepare($conexion, $query);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

$user = mysqli_fetch_assoc($result);

if (!$user) {
    session_destroy();
    header("Location: /GestiFit/public/login.html");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <title>GestiFit</title>

  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Teko:wght@300..700&display=swap"
  rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png">

  <!-- Libraries Stylesheet -->
  <link rel="stylesheet" href="/GestiFit/lib/animate/animate.min.css" />
  <link href="/Gestifit/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Customized Bootstrap Stylesheet -->
  <link href="/GestiFit/public/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Template Stylesheet -->
  <link href="/GestiFit/public/css/stylesAdmin.css" rel="stylesheet" />
  <link href="/GestiFit/public/css/style.css" rel="stylesheet" />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

  <!-- Spinner Start -->
  <div id="spinner"
    class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <!-- Spinner End -->
<div class="text-white bg-dark text-center py-2 fw-bold">
  <i class="fas fa-user-shield me-2"></i>Modo Administrador
</div>
<!-- Navbar & Hero Start -->
<nav class="container-fluid header-top">
  <div class="nav-shaps-2"></div>
  <div class="container d-flex align-items-center">
    <div class="d-flex align-items-center h-100">
      <a href="#" class="navbar-brand" style="height: 125px;">
        <h1 class="text-primary mb-0">
          <img src="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png" class="img-fluid" width="70" height="70">
          GestiFit
        </h1>
      </a>
    </div>
    <div class="w-100 h-100">
      <div class="topbar px-0 py-2 d-none d-lg-block" style="height: 45px;">
        <div class="row gx-0 align-items-center">
          <div class="col-lg-8 text-center text-lg-center mb-lg-0">
            <div class="d-flex flex-wrap">
              <div class="pe-4"></div>
            </div>
          </div>
          <div class="col-lg-4 text-center text-lg-end">
            <div class="d-flex justify-content-end">
              <div class="d-flex pe-3">
                <a class="btn p-0 text-primary me-3"
                  href="https://www.instagram.com/elmanicomiogym?igsh=MXB3eHBkdjFjYXJleQ==">
                  <i class="fab fa-instagram"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="nav-bar px-0 py-lg-0" style="height: 80px;">
        <nav class="navbar navbar-expand-lg navbar-light d-flex justify-content-lg-end">
          <a href="#" class="navbar-brand-2">
            <h1 class="text-primary mb-0">
              <i class="fas fa-hand-rock me-2"></i>GestiFit
            </h1>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav mx-0 mx-lg-auto">
              <a href="/GestiFit/public/admin/admin.php" class="nav-item nav-link active">
                <i class="fa fa-home me-2"></i>Principal
              </a>
              <a href="/GestiFit/public/admin/adminClientes.php" class="nav-item nav-link">
                <i class="fa fa-users me-2"></i>Clientes
              </a>
              <a href="/GestiFit/public/admin/adminMembresias.php" class="nav-item nav-link">
                <i class="fa fa-user-tie me-2"></i>Membresías
              </a>
              <a href="/GestiFit/public/admin/adminInstructores.php" class="nav-item nav-link">
                <i class="fa fa-dumbbell me-2"></i>Instructores
              </a>
              <a href="/GestiFit/public/admin/adminClases.php" class="nav-item nav-link">
                <i class="fa fa-id-card me-2"></i>Clases
              </a>
              <a href="/GestiFit/public/admin/adminRutinas.php" class="nav-item nav-link ">
                <i class="fas fa-running me-2"></i>Rutinas
              </a>
              <a href="/GestiFit/public/admin/adminAvisos.php" class="nav-item nav-link">
                <i class="fas fa-bullhorn me-2"></i>Avisos
              </a>

              <div class="nav-btn ps-3">
                <a href="/GestiFit/src/cerrar_sesion.php"
                   class="btn btn-primary py-2 px-4 ms-0 ms-lg-3"
                   onclick="return confirm('¿Seguro que deseas cerrar sesión?')">
                  <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                </a>
              </div>

              <div class="nav-shaps-1"></div>
            </div>
          </div>
        </nav>
      </div>
    </div>
  </div>
</nav>

  <!-- Navbar & Hero End -->

  <h1 class="mb-a">Bienvenido Administrador</h1>
  <h3 class="mb-4">Aquí tienes el resumen del día de hoy</h3>

  <div class="main-content">
    <section class="charts-section container my-5">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="chart-container p-3 bg-white rounded shadow-sm">
            <h4 class="text-center">Clientes Nuevos</h4>
            <canvas id="clientesChart" height="250"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="chart-container p-3 bg-white rounded shadow-sm">
            <h4 class="text-center">Evaluación Instructores</h4>
            <canvas id="instructoresChart" height="250"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="chart-container p-3 bg-white rounded shadow-sm">
            <h4 class="text-center">Preferencia de Clases</h4>
            <canvas id="clasesChart" height="250"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="chart-container p-3 bg-white rounded shadow-sm">
            <h4 class="text-center">Distribución Membresías</h4>
            <canvas id="membresiasChart" height="250"></canvas>
          </div>
        </div>
      </div>
    </section>
  </div>


  <!-- Copyright Start -->
  <div class="copyright py-3 text-center text-white bg-dark mt-auto">
    <div class="container">
      <small>&copy; 2025 GestiFit. Todos los derechos reservados.</small>
    </div>
  </div>
  <!-- Copyright End -->

  <!-- Back to Top -->
  <div class="back-to-top">
    <a href="#"><i class="fa fa-angle-up"></i></a>
  </div>

  <!-- JavaScript Libraries -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/GestiFit/lib/wow/wow.min.js"></script>
  <script src="/GestiFit/lib/easing/easing.min.js"></script>
  <script src="/GestiFit/lib/waypoints/waypoints.min.js"></script>
  <script src="/GestiFit/lib/owlcarousel/owl.carousel.min.js"></script>
  <!-- Script necesario para que se renderice el embed -->
  <script async src="//www.instagram.com/embed.js"></script>

  <!-- Template Javascript -->
  <script src="/GestiFit/public/js/main.js"></script>
  <script src="/GestiFit/public/js/admin.js"></script>

</body>

</html>
