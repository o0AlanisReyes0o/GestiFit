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
    <title>Clases | GestiFit</title>
    
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Teko:wght@300..700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png">

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="/GestiFit/lib/animate/animate.min.css"/>
    <link href="/Gestifit/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="/GestiFit/public/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="/GestiFit/public/css/style.css" rel="stylesheet">
    <link href="/GestiFit/public/css/stylesUsuario.css" rel="stylesheet">

    <style>
        /* Estilos específicos para la página de clases */
        .class-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            background: var(--bs-white);
        }

        .class-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(12, 24, 68, 0.2);
        }

        .class-img {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .class-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .class-card:hover .class-img img {
            transform: scale(1.1);
        }

        .class-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(12, 24, 68, 0.7);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .class-card:hover .class-overlay {
            opacity: 1;
        }

        .class-content {
            padding: 20px;
        }

        .bg-breadcrumb {
            background: linear-gradient(rgba(12, 24, 68, 0.9), rgba(12, 24, 68, 0.9)), url('/GestiFit/public/img/feature-2.jpg');
            background-size: cover;
            background-position: center;
            padding: 80px 0;
        }

        /* Estilos para la tabla de horarios */
        .schedule-table {
            background: var(--bs-white);
            border-radius: 10px;
            overflow: hidden;
        }
        /* Notificaciones estilo Toast */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s, fadeOut 0.5s 2.5s forwards;
            display: flex;
            align-items: center;
        }

        .notification.success {
            background-color: #28a745;
        }

        .notification.error {
            background-color: #dc3545;
        }

        .notification.info {
            background-color: #17a2b8;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .notification i {
            margin-right: 10px;
            font-size: 1.2em;
        }
    </style>
</head>

<body>
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

    <!-- Navbar modificado para usuario logueado -->
    <div class="container-fluid header-top">
        <div class="nav-shaps-2"></div>
        <div class="container d-flex align-items-center">
            <div class="d-flex align-items-center h-100">
                <a href="#" class="navbar-brand" style="height: 125px;">
                    <h1 class="text-primary mb-0">
                        <img src="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png" class="img-fluid" width="70" height="70"> GestiFit </h1>
                </a>
            </div>
            <div class="w-100 h-100">
                <div class="topbar px-0 py-2 d-none d-lg-block" style="height: 45px;">
                    <div class="row gx-0 align-items-center">
                        <div class="col-lg-8 text-center text-lg-center mb-lg-0">
                            <div class="d-flex flex-wrap">
                                <div class="pe-4">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center text-lg-end">
                            <div class="d-flex justify-content-end">
                                
                                <div class="d-flex align-items-center small">
                                    <a href="/GestiFit/src/cerrar_sesion.php" 
                                        class="text-body me-3"
                                        onclick="return confirm('¿Seguro que deseas cerrar sesión?')">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                                    </a>
                                </div>
                                <div class="d-flex pe-3">
                                    <a class="btn p-0 text-primary me-3" href="https://www.instagram.com/elmanicomiogym?igsh=MXB3eHBkdjFjYXJleQ=="><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nav-bar px-0 py-lg-0" style="height: 80px;">
                    <nav class="navbar navbar-expand-lg navbar-light d-flex justify-content-lg-end">
                        <a href="#" class="navbar-brand-2">
                            <h1 class="text-primary mb-0"><i class="fas fa-hand-rock me-2"></i> GestiFit</h1>
                        </a> 
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="fa fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarCollapse">
                            <div class="navbar-nav mx-0 mx-lg-auto">
                                    <a href="index_usuario.php" class="nav-item nav-link ">
                                        <i class="fas fa-home me-2"></i>Inicio
                                    </a>
                                    <a href="membresia.php" class="nav-item nav-link">
                                        <i class="fas fa-id-card me-2"></i>Mi Membresía
                                    </a>
                                    <a href="Clases.php" class="nav-item nav-link active">
                                        <i class="fas fa-calendar-alt me-2"></i>Clases
                                    </a>
                                    <a href="rutinas.html" class="nav-item nav-link">
                                        <i class="fas fa-running me-2"></i>Rutinas
                                    </a>
                                    <a href="Entrenadores.php" class="nav-item nav-link">
                                        <i class="fas fa-dumbbell me-2"></i>Entrenadores
                                    </a> 
                                
                                <div class="nav-btn ps-3">
                                    <button class="btn-search btn btn-primary btn-md-square mt-2 mt-lg-0 mb-4 mb-lg-0 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search"></i></button>
                                    <a href="clases.html" class="btn btn-primary py-2 px-4 ms-0 ms-lg-3"> <span>Reservar Clase</span></a>
                                </div>
                                <div class="nav-shaps-1"></div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar End -->

    <!-- Hero Section -->
    <section class="bg-breadcrumb py-5">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-8">
                    <h1 class="display-4 text-white mb-4">Nuestras Clases Grupales</h1>
                    <p class="text-white mb-0">Descubre la variedad de clases que ofrecemos para todos los niveles y objetivos fitness.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Clases Section -->
    <section class="container-fluid py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 700px;">
                <h4 class="text-primary mb-2">Programa de Clases</h4>
                <h2 class="display-5 mb-3">Elige tu clase ideal</h2>
                <p class="mb-0">Todas nuestras clases son impartidas por instructores certificados y están diseñadas para ayudarte a alcanzar tus objetivos.</p>
            </div>

            <!-- Filtros de Clases -->
            <div class="row mb-5 wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-md-8 mx-auto">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select class="form-select" id="filter-day">
                                        <option value="">Todos los días</option>
                                        <option value="lunes">Lunes</option>
                                        <option value="martes">Martes</option>
                                        <option value="miércoles">Miércoles</option>
                                        <option value="jueves">Jueves</option>
                                        <option value="viernes">Viernes</option>
                                        <option value="sábado">Sábado</option>
                                        <option value="domingo">Domingo</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="filter-time">
                                        <option value="">Cualquier horario</option>
                                        <option value="morning">Mañana (6am-12pm)</option>
                                        <option value="afternoon">Tarde (12pm-6pm)</option>
                                        <option value="evening">Noche (6pm-9pm)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="filter-level">
                                        <option value="">Todos los niveles</option>
                                        <option value="principiante">Principiante</option>
                                        <option value="intermedio">Intermedio</option>
                                        <option value="avanzado">Avanzado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenedor dinámico para las clases -->
            <div class="row g-4" id="clases-container">
                <!-- Las clases se cargarán aquí dinámicamente -->
            </div>
        </div>
    </section>

    <!-- Horario Semanal -->
    <section class="container-fluid bg-light py-5">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 700px;">
                <h4 class="text-primary mb-2">Horario</h4>
                <h2 class="display-5 mb-3">Horario Semanal de Clases</h2>
                <p class="mb-0">Consulta los horarios de todas nuestras clases grupales.</p>
            </div>

            <div class="row wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="schedule-table">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Hora</th>
                                    <th>Lunes</th>
                                    <th>Martes</th>
                                    <th>Miércoles</th>
                                    <th>Jueves</th>
                                    <th>Viernes</th>
                                    <th>Sábado</th>
                                </tr>
                            </thead>
                            <tbody id="schedule-body">
                                <!-- El horario se cargará aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenedor para modales dinámicos -->
    <div id="modals-container">
        <!-- Los modales se generarán aquí dinámicamente -->
    </div>

    <div class="modal fade" id="modal-clase" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <!-- Contenido del modal -->
    </div>
    <!-- Footer Start -->
    <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
        <div class="container py-5">
            <div class="row g-5 mb-5 align-items-center">
                <div class="col-lg-7">
                    <div class="position-relative d-flex" style="transform: skew(18deg);">
                        <input class="form-control border-0 w-100 py-3 pe-5" type="text" placeholder="Email para suscribirse">
                        <button type="button" class="btn-primary py-2 px-4 ms-3"> <span>Suscribirse</span></button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex align-items-center justify-content-center justify-content-lg-end">
                        <a class="btn btn-primary btn-md-square me-3" href="https://www.instagram.com/elmanicomiogym?igsh=MXB3eHBkdjFjYXJleQ=="><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="row g-5">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item">
                        <h4 class="text-white mb-4"><img src="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png" class="img-fluid" width="70" height="70"> GestiFit</h4>
                        <p class="mb-0">Manicomio Gym es más que un gimnasio, es un estilo de vida que motiva. Únete a nuestra comunidad fitness.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item">
                        <h4 class="text-white mb-4">Enlaces Rápidos</h4>
                        <a href="index.html"> Inicio</a>
                        <a href="#membership"> Membresías</a>
                        <a href="Clases.html"> Clases</a>
                        <a href="Instructores.html"> Instructores</a>
                        <a href="#"> Blog</a>
                        <a href="#"> Testimonios</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item">
                        <h4 class="text-white mb-4"> Información de Contacto</h4>
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="d-flex">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <div>
                                        <h5 class="text-white mb-2">Dirección</h5>
                                        <p class="mb-0">Av. Principal 123, Ciudad de México</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <div>
                                        <h5 class="text-white mb-2">Correo</h5>
                                        <p class="mb-0">info@manicomiogym.com</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex">
                                    <i class="fa fa-phone-alt text-primary me-2"></i>
                                    <div>
                                        <h5 class="text-white mb-2">Teléfono</h5>
                                        <p class="mb-0">+52 55 4916 4529</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="copyright py-3 text-center text-white">
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

    <!-- Template Javascript -->
    <script src="/GestiFit/public/js/main.js"></script>

    <!-- Script para manejo de clases -->
    <script src="/GestiFit/public/js/clases.js"></script>
</body>
</html>