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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <title>Mi Membresía - GestiFit</title>

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Teko:wght@300..700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="/GestiFit/lib/animate/animate.min.css"/>
    <link href="/GestiFit/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="/GestiFit/public/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="/GestiFit/public/css/style.css" rel="stylesheet">
    <link href="/GestiFit/public/css/stylesUsuario.css" rel="stylesheet">

    <style>
        /* Estilos adicionales para la página de membresía */
        .membership-section {
            background: linear-gradient(rgba(255, 255, 255, 0.95), rgba(245, 245, 245, 0.95)), url(/GestiFit/public/img/header-2.jpg);
            background-size: cover;
        }
        
        .membership-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(12, 24, 68, 0.1);
            height: 100%;
            margin-bottom: 25px;
        }
        
        .membership-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(12, 24, 68, 0.2);
        }
        
        .membership-card .card-header {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-dark) 100%);
            color: white;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .membership-card .card-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        }
        
        .membership-card .card-body {
            padding: 25px;
        }
        
        .progress-thin {
            height: 8px;
            border-radius: 4px;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .quick-access-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 20px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--bs-dark);
        }
        
        .quick-access-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(12, 24, 68, 0.15);
            color: var(--bs-primary);
        }
        
        .quick-access-card .icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            background: rgba(13, 110, 253, 0.1);
            color: var(--bs-primary);
            font-size: 24px;
        }
        
        .payment-history {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .payment-history::-webkit-scrollbar {
            width: 8px;
        }
        
        .payment-history::-webkit-scrollbar-thumb {
            background-color: var(--bs-primary);
            border-radius: 4px;
        }
        
        .payment-history::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--bs-primary);
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--bs-white);
            border: 3px solid var(--bs-primary);
        }
        
        .timeline-item.active::before {
            background: var(--bs-primary);
        }
        
        @media (max-width: 768px) {
            .membership-card {
                margin-bottom: 20px;
            }
            
            .quick-access-card {
                margin-bottom: 15px;
            }
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
                                    <a href="membresia.php" class="nav-item nav-link active">
                                        <i class="fas fa-id-card me-2"></i>Mi Membresía
                                    </a>
                                    <a href="Clases.php" class="nav-item nav-link">
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

    
    <div class="container-fluid membership-section py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12">
                    <h1 class="display-5 fw-bold text-primary">
                        <i class="fas fa-id-card me-2"></i> Mi Membresía
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index_usuario.php">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mi Membresía</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Primera fila: Resumen de Membresía y Accesos Rápidos -->
            <div class="row mb-4">
                <!-- Tarjeta de Membresía -->
                <div class="col-lg-6 mb-4">
                    <div class="card membership-card">
                        <div class="card-header">
                            <h4 class="mb-0 text-white"><i class="fas fa-crown me-2"></i> Membresía Actual</h4>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center mb-3 mb-md-0">
                                    <div id="membership-icon" class="display-3 text-primary">
                                        <i class="fas fa-dumbbell"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div id="membership-info">
                                        <h3 class="mb-3" id="membership-name">Cargando...</h3>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span id="membership-period">Vigencia:</span>
                                                <span id="membership-days-left"></span>
                                            </div>
                                            <div class="progress progress-thin">
                                                <div id="membership-progress" class="progress-bar bg-primary" role="progressbar"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted">Fecha de inicio:</small>
                                                <div id="membership-start" class="fw-bold">--/--/----</div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted">Fecha de vencimiento:</small>
                                                <div id="membership-end" class="fw-bold">--/--/----</div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Estado:</small>
                                                <div><span id="membership-status" class="status-badge bg-primary">Cargando...</span></div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Próximo pago (antes de):</small>
                                                <div id="next-payment" class="fw-bold">--/--/----</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-center">
                                <button id="renew-upgrade-btn" class="btn btn-primary">
                                    <i class="fas fa-sync-alt me-2"></i>Renovar o Mejorar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Accesos Rápidos -->
                <div class="col-lg-6 mb-4">
                    <div class="card membership-card h-100">
                        <div class="card-header">
                            <h4 class="mb-0 text-white"><i class="fas fa-bolt me-2"></i> Accesos Rápidos</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="Clases.php" class="quick-access-card">
                                        <div class="icon-wrapper">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <h5 class="text-center mb-0">Reservar Clase</h5>
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="Entrenadores.php" class="quick-access-card">
                                        <div class="icon-wrapper">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <h5 class="text-center mb-0">Entrenadores</h5>
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="/GestiFit/public/public/logout.html" class="quick-access-card">
                                        <div class="icon-wrapper">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </div>
                                        <h5 class="text-center mb-0">Cerrar Sesion</h5>
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="rutinas.html" class="quick-access-card">
                                        <div class="icon-wrapper">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <h5 class="text-center mb-0">Rutinas</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Segunda fila: Beneficios y Resumen de Pagos -->
            <div class="row mb-4">
                <!-- Beneficios de la Membresía -->
                <div class="col-lg-6 mb-4">
                    <div class="card membership-card h-100">
                        <div class="card-header">
                            <h4 class="mb-0 text-white"><i class="fas fa-star me-2"></i> Beneficios de tu Membresía</h4>
                        </div>
                        <div class="card-body">
                            <div id="benefits-container">

                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen de Pagos -->
                <div class="col-lg-6 mb-4">
                    <div class="card membership-card h-100">
                        <div class="card-header">
                            <h4 class="mb-0 text-white"><i class="fas fa-wallet me-2"></i> Resumen de Pagos</h4>
                        </div>
                        <div class="card-body">
                            <div id="payment-summary">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Total pagado este mes:</h5>
                                    <span class="fw-bold text-primary" id="current-month-payment">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Próximo pago:</h5>
                                    <span class="fw-bold" id="next-payment-amount2">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Método de pago:</h5>
                                    <span class="badge bg-light text-dark" id="payment-method2">No registrado</span>
                                </div>
                                <div class="progress progress-thin mb-4">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                </div>
                                <a href="#historial">
                                    <button  class="btn btn-primary w-100" id="view-payments-btn">
                                        <i class="fas fa-list me-2"></i>Ver historial completo
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tercera fila: Historial de Pagos -->
            <div class="row">
                <div class="col-12">
                    <div id="historial" class="card membership-card">
                        <div class="card-header">
                            <h4 class="mb-0 text-white"><i class="fas fa-history me-2"></i> Historial de Pagos Recientes</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive payment-history">
                                <table class="table table-hover" id="payments-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Concepto</th>
                                            <th>Método</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payments-body">
                                        <!-- Los datos se cargarán dinámicamente -->
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando historial...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button id="make-payment-btn" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Realizar Pago
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal para realizar pagos -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Realizar Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="payment-modal-content">
                    <!-- Contenido se cargará dinámicamente -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando formulario...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        <a href="index_usuario.php"> Inicio</a>
                        <a href="#membership"> Membresías</a>
                        <a href="Clases.php"> Clases</a>
                        <a href="Entrenadores.php"> Instructores</a>
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
    <script src="/GestiFit/public/js/membresia.js"></script>

</body>
</html>