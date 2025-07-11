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
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>GestiFit - Inicio</title>
        
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
        <link href="/GestiFit/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="/GestiFit/public/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="/GestiFit/public/css/style.css" rel="stylesheet">
        <link href="/GestiFit/public/css/stylesUsuario.css" rel="stylesheet">
    
            <!-- Agregar estilos personalizados -->
    <style>
        
        .announcement-item {
            position: relative;
            height: 100%;
            transition: transform 0.3s;
        }
        .announcement-item:hover {
            transform: translateY(-5px);
        }
        .announcement-badge {
            position: absolute;
            top: -10px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quick-access-card {
            transition: all 0.3s;
            color: inherit;
            text-decoration: none;
        }
        .quick-access-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .recommendation-card {
            transition: all 0.3s;
        }
        .recommendation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
                                    <span class="text-white">
                                    <i class="fa fa-user text-primary me-2"></i>
                                    Bienvenido, <strong><?= htmlspecialchars($user['nombre' ]) ?></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center text-lg-end">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex align-items-center small">
                                    <a href="#" class="text-body me-3 pe-3" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-cog me-2"></i>Mi cuenta</a>
                                    <form id="logoutForm" action="/GestiFit/src/cerrar_sesion.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                        <button type="submit" class="btn btn-link text-body me-3" 
                                                onclick="return confirm('¿Seguro que deseas cerrar sesión?')">
                                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                                        </button>
                                    </form>

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
                            <h1 class="text-primary mb-0"><i class="fas fa-hand-rock me-2"></i> Fitness</h1>
                        </a> 
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="fa fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarCollapse">
                            <div class="navbar-nav mx-0 mx-lg-auto">
                                    <a href="index_usuario.php" class="nav-item nav-link active">
                                        <i class="fas fa-home me-2"></i>Inicio
                                    </a>
                                    <a href="membresia.php" class="nav-item nav-link">
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


<div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
    <!-- Indicadores -->
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#header-carousel" data-bs-slide-to="1"></button>
        <!-- Los indicadores adicionales se generarán dinámicamente -->
    </div>
    
    <!-- Slides del carrusel -->
    <div class="carousel-inner">
        <!-- Slides base (puedes mantenerlas o eliminarlas) -->
        <div class="carousel-item active">
            <img class="d-block w-100" src="/GestiFit/public/img/banner1.jpg" alt="Bienvenida">
            <div class="carousel-caption d-none d-md-block">
                <h3 class="text-white animate__animated animate__fadeInDown">Bienvenido a GestiFit</h3>
                <p class="animate__animated animate__fadeInUp">Tu sistema de gestión fitness favorito</p>
                <a href="#" class="btn btn-primary animate__animated animate__zoomIn">Conoce más</a>
            </div>
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="/GestiFit/public/img/banner2.jpg" alt="Ofertas">
            <div class="carousel-caption d-none d-md-block">
                <h3 class="animate__animated animate__fadeInDown text-white">Nuestras Clases</h3>
                <p class="animate__animated animate__fadeInUp">Descubre la variedad de actividades que ofrecemos</p>
                <a href="clases.html" class="btn btn-primary animate__animated animate__zoomIn">Ver clases</a>
            </div>
        </div>
        <!-- Los avisos se agregarán aquí dinámicamente -->
    </div>
    
    <!-- Controles -->
    <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>
    <!-- Sección Motivacional Simple -->
    <div class="container-fluid bg-primary py-5">
        <div class="container text-center text-white py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h3 class="mb-4"><i class="fas fa-quote-left me-2"></i> Cada entrenamiento cuenta <i class="fas fa-quote-right ms-2"></i></h3>
                    <p class="lead mb-0">"La disciplina es el puente entre tus metas y tus logros. ¡Tú puedes!"</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen del Usuario -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <h2 class="text-center mb-5">Tu resumen</h2>
            
            <div class="row g-4">
                <!-- Tarjeta de Membresía -->
                <div class="col-md-6 col-lg-6" id="mi-membresia-card">
                    <div class="card border-0 shadow h-100">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-white"><i class="fas fa-id-card me-2"></i> Mi Membresía</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary p-3 rounded me-3">
                                    <i class="fas fa-crown fa-2x text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Premium Plus</h5>
                                    <small class="text-muted">Válida hasta: 15/08/2025</small>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="mb-3">Días restantes: 45</p>
                            <a href="membresia.html" class="btn btn-outline-primary w-100">Renovar</a>
                        </div>
                    </div>
                </div>
                
                <!-- Próximas Clases -->
                <div class="col-md-6 col-lg-6" id="proximas-clases-card">
                    <div class="card border-0 shadow h-100">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-white"><i class="fas fa-calendar-alt me-2"></i> Mis Próximas Clases</h4>
                        </div>
                        <div class="card-body">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Planes/Membresías -->
    <div class="container-fluid py-5 bg-light" id="membership">
        <div class="container py-5">
            <h2 class="text-center mb-5"><i class="fas fa-id-card-alt text-primary me-2"></i> Planes de Membresía</h2>
            <div class="row g-4" id="planes-container">
                <!-- Aquí se insertarán dinámicamente los planes desde JavaScript -->
                <div class="col-12 text-center text-muted" id="planes-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando planes...</span>
                    </div>
                    <p class="mt-2">Cargando planes disponibles...</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Accesos Rápidos -->
    <div class="container-fluid bg-white py-5">
        <div class="container py-5">
            <h2 class="text-center mb-5"><i class="fas fa-bolt text-primary me-2"></i> Accesos Rápidos</h2>
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <a href="/GestiFit/public/public/logout.html" class="quick-access-card text-center p-4 bg-white rounded shadow d-block">
                        <div class="icon-circle bg-primary text-white mx-auto mb-3">
                            <i class="fas fa-sign-out-alt fa-2x"></i>
                        </div>
                        <h5>Cerrar Sesion</h5>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="Clases.php" class="quick-access-card text-center p-4 bg-white rounded shadow d-block">
                        <div class="icon-circle bg-primary text-white mx-auto mb-3">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h5>Reservar Clase</h5>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="Entrenadores.php" class="quick-access-card text-center p-4 bg-white rounded shadow d-block">
                        <div class="icon-circle bg-primary text-white mx-auto mb-3">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <h5>Entrenadores</h5>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="membresia.php" class="quick-access-card text-center p-4 bg-white rounded shadow d-block">
                        <div class="icon-circle bg-primary text-white mx-auto mb-3">
                            <i class="fas fa-credit-card fa-2x"></i>
                        </div>
                        <h5>Pagos</h5>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="rutinas.html" class="quick-access-card text-center p-4 bg-white rounded shadow d-block">
                        <div class="icon-circle bg-primary text-white mx-auto mb-3">
                            <i class="fas fa-lock fa-2x"></i>
                        </div>
                        <h5>Rutinas</h5>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recomendaciones Personalizadas -->
    <div class="container-fluid py-5 bg-light">
        <div class="container py-5">
            <h2 class="text-center mb-5"><i class="fas fa-magic text-primary me-2"></i> Recomendaciones para ti</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="recommendation-card p-4 bg-white rounded shadow h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary p-2 rounded me-3">
                                <i class="fas fa-dumbbell text-white"></i>
                            </div>
                            <h5 class="mb-0">Rutina de Fuerza</h5>
                        </div>
                        <p class="mb-3">Basado en tus últimos entrenamientos, te recomendamos esta rutina para mejorar tu fuerza.</p>
                        <a href="#" class="btn btn-sm btn-primary">Ver Rutina</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="recommendation-card p-4 bg-white rounded shadow h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary p-2 rounded me-3">
                                <i class="fas fa-utensils text-white"></i>
                            </div>
                            <h5 class="mb-0">Plan Nutricional</h5>
                        </div>
                        <p class="mb-3">Consulta con nuestros nutriólogos para un plan personalizado según tus objetivos.</p>
                        <a href="#" class="btn btn-sm btn-primary">Proximamente</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="recommendation-card p-4 bg-white rounded shadow h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary p-2 rounded me-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <h5 class="mb-0">Clase Grupal</h5>
                        </div>
                        <p class="mb-3">Te recomendamos probar nuestra nueva clase de HIIT los martes y jueves.</p>
                        <a href="clases.html" class="btn btn-sm btn-primary">Reservar</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="recommendation-card p-4 bg-white rounded shadow h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary p-2 rounded me-3">
                                <i class="fas fa-spa text-white"></i>
                            </div>
                            <h5 class="mb-0">Spa y Relajación</h5>
                        </div>
                        <p class="mb-3">Tu membresía incluye 1 sesión mensual de spa. ¡Programa la tuya!</p>
                        <a href="#" class="btn btn-sm btn-primary">Proximamente</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips de Entrenamiento Simple -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <h2 class="text-center mb-5"><i class="fas fa-lightbulb text-primary me-2"></i> Tips de Entrenamiento</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="icon-circle bg-primary text-white mx-auto mb-3">
                                <i class="fas fa-tint fa-2x"></i>
                            </div>
                            <h5>Hidratación</h5>
                            <p>Bebe al menos 500ml de agua 2 horas antes de entrenar y pequeños sorbos durante el ejercicio.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="icon-circle bg-primary text-white mx-auto mb-3">
                                <i class="fas fa-fire fa-2x"></i>
                            </div>
                            <h5>Calentamiento</h5>
                            <p>Dedica 10-15 minutos a calentar. Reduce el riesgo de lesiones y mejora tu rendimiento.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="icon-circle bg-primary text-white mx-auto mb-3">
                                <i class="fas fa-moon fa-2x"></i>
                            </div>
                            <h5>Descanso</h5>
                            <p>Duerme al menos 7 horas. El 70% de la recuperación muscular ocurre durante el sueño.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Perfil (Original) -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="profileModalLabel"><i class="fas fa-user-cog me-2"></i> Mi Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img src="/GestiFit/public/img/icon-1.png" class="img-fluid rounded-circle mb-3" width="150" alt="Avatar">
                            <button class="btn btn-sm btn-outline-primary mb-4">Cambiar Foto</button>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-danger">Cambiar Contraseña</button>
                                <button class="btn btn-outline-secondary">Preferencias</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control" value="Juan Pérez">
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="juan.perez@example.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" value="+52 55 1234 5678">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" value="1985-05-15">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Género</label>
                                        <select class="form-select">
                                            <option>Masculino</option>
                                            <option>Femenino</option>
                                            <option>Otro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Objetivos Fitness</label>
                                    <textarea class="form-control" rows="3">Aumentar masa muscular y mejorar resistencia cardiovascular</textarea>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
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

    <!-- Copyright Start -->
    <div class="copyright py-3 text-center text-white">
        <div class="container">
            <small>&copy; 2025 GestiFit. Todos los derechos reservados.</small>
        </div>
    </div>

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
        <script src="/GestiFit/public/js/usuarioIndex.js"></script>
    </body>

</html>