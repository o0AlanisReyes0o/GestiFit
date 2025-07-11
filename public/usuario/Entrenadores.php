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
    <title>Entrenadores - GestiFit</title>
    
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
                            <h1 class="text-primary mb-0"><i class="fas fa-hand-rock me-2"></i> Fitness</h1>
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
                                    <a href="Clases.php" class="nav-item nav-link">
                                        <i class="fas fa-calendar-alt me-2"></i>Clases
                                    </a>
                                    <a href="rutinas.html" class="nav-item nav-link">
                                        <i class="fas fa-running me-2"></i>Rutinas
                                    </a>
                                    <a href="Entrenadores.php" class="nav-item nav-link active">
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

    <!-- Breadcrumb Start -->
    <div class="bg-breadcrumb py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h1 class="display-4 text-white mb-3">Nuestros Entrenadores</h1>
                <nav aria-label="breadcrumb animated slideInDown">
                    <ol class="breadcrumb justify-content-center mb-0">
                        <li class="breadcrumb-item"><a href="index.html">Inicio</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Entrenadores</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Team Section Start -->
    <div class="container-fluid team py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 800px;">
                <h4 class="text-primary mb-2">Expertos en Fitness</h4>
                <h1 class="display-4 mb-4">Conoce a Nuestros Entrenadores</h1>
                <p class="mb-0">Nuestro equipo de profesionales certificados está listo para guiarte en tu viaje fitness. Cada uno con especialidades únicas para ayudarte a alcanzar tus objetivos personales.</p>
            </div>
            
            <div class="row gy-5 gy-lg-4 gx-4">
                <!-- Entrenador 1 -->
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="team-item">
                        <div class="team-img">
                            <img src="/GestiFit/public/img/team-1.jpg" class="img-fluid w-100" alt="Laura Gómez">
                        </div>
                        <div class="team-content">
                            <h4>Laura Gómez</h4>
                            <p class="mb-1"><strong>Especialidad:</strong> Entrenamiento Funcional</p>
                            <p class="mb-1"><strong>Horario:</strong> 6:00 am - 2:00 pm</p>
                            <p class="mb-0"><strong>Días:</strong> Lunes a Viernes</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> 52 55 2138 9232</p>
                            <a href="https://wa.me/525521389232" target="_blank" class="btn btn-success mt-2">
                                <i class="bi bi-whatsapp"></i> Contactar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Entrenador 2 -->
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="team-item">
                        <div class="team-img">
                            <img src="/GestiFit/public/img/team-2.jpg" class="img-fluid w-100" alt="Carlos Ramírez">
                        </div>
                        <div class="team-content">
                            <h4>Carlos Ramírez</h4>
                            <p class="mb-1"><strong>Especialidad:</strong> Levantamiento de Pesas</p>
                            <p class="mb-1"><strong>Horario:</strong> 2:00 pm - 10:00 pm</p>
                            <p class="mb-0"><strong>Días:</strong> Lunes a Viernes</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> 52 55 2138 9232</p>
                            <a href="https://wa.me/525521389232" target="_blank" class="btn btn-success mt-2">
                                <i class="bi bi-whatsapp"></i> Contactar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Entrenador 3 -->
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.6s">
                    <div class="team-item">
                        <div class="team-img">
                            <img src="/GestiFit/public/img/team-3.jpg" class="img-fluid w-100" alt="Andrea Silva">
                        </div>
                        <div class="team-content">
                            <h4>Andrea Silva</h4>
                            <p class="mb-1"><strong>Especialidad:</strong> Yoga y Pilates</p>
                            <p class="mb-1"><strong>Horario:</strong> 7:00 am - 10:00 am</p>
                            <p class="mb-0"><strong>Días:</strong> Sabado y Domingo</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> 52 55 2138 9232</p>
                            <a href="https://wa.me/525521389232" target="_blank" class="btn btn-success mt-2">
                                <i class="bi bi-whatsapp"></i> Contactar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Entrenador 4 -->
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.8s">
                    <div class="team-item">
                        <div class="team-img">
                            <img src="/GestiFit/public/img/team-4.jpg" class="img-fluid w-100" alt="David Torres">
                        </div>
                        <div class="team-content">
                            <h4>David Torres</h4>
                            <p class="mb-1"><strong>Especialidad:</strong> CrossFit</p>
                            <p class="mb-1"><strong>Horario:</strong> 9:00 am - 5:00 pm</p>
                            <p class="mb-0"><strong>Días:</strong> Lunes a Sabado</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> 52 55 2138 9232</p>
                            <a href="https://wa.me/525521389232" target="_blank" class="btn btn-success mt-2">
                                <i class="bi bi-whatsapp"></i> Contactar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Entrenador 5 -->
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="1.0s">
                    <div class="team-item">
                        <div class="team-img">
                            <img src="/GestiFit/public/img/icon-1.png" class="img-fluid w-100" alt="María Fernández">
                        </div>
                        <div class="team-content">
                            <h4>María Fernández</h4>
                            <p class="mb-1"><strong>Especialidad:</strong> Spinning y Cardio</p>
                            <p class="mb-1"><strong>Horario:</strong> 5:00 am - 1:00 pm</p>
                            <p class="mb-0"><strong>Días:</strong> Lunes a Viernes</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> 52 55 2138 9232</p>
                            <a href="https://wa.me/525521389232" target="_blank" class="btn btn-success mt-2">
                                <i class="bi bi-whatsapp"></i> Contactar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Entrenador 6 -->
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="1.2s">
                    <div class="team-item">
                        <div class="team-img">
                            <img src="/GestiFit/public/img/icon-1.png" class="img-fluid w-100" alt="Javier López">
                        </div>
                        <div class="team-content">
                            <h4>Javier López</h4>
                            <p class="mb-1"><strong>Especialidad:</strong> Boxeo y Artes Marciales</p>
                            <p class="mb-1"><strong>Horario:</strong> 4:00 pm - 10:00 pm</p>
                            <p class="mb-0"><strong>Días:</strong> Martes a Domingo</p>
                            <p class="mb-1"><strong>WhatsApp:</strong> 52 55 2138 9232</p>
                            <a href="https://wa.me/525521389232" target="_blank" class="btn btn-success mt-2">
                                <i class="bi bi-whatsapp"></i> Contactar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Team Section End -->
    
    <!-- Modals 4-6 seguirían el mismo patrón -->
    <!-- Trainer Modals End -->

    <!-- Testimonials Section Start -->
    <section class="container-fluid py-5 bg-white">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.2s" style="max-width: 700px;">
                <h4 class="text-primary mb-2">Testimonios</h4>
                <h2 class="display-5 mb-3">Lo que dicen nuestros clientes</h2>
                <p class="mb-0">Experiencias reales de personas que han trabajado con nuestros entrenadores.</p>
            </div>
            
            <div class="announcement-carousel owl-carousel wow fadeInUp" data-wow-delay="0.4s">
                <div class="announcement-item">
                    <span class="announcement-badge bg-primary text-white">Laura G.</span>
                    <h4>Transformación Increíble</h4>
                    <p>Carlos me ayudó a ganar 5 kg de músculo en 6 meses con un programa personalizado. Su conocimiento en levantamiento es excepcional.</p>
                </div>
                
                <div class="announcement-item">
                    <span class="announcement-badge bg-primary text-white">Miguel R.</span>
                    <h4>Recuperación de Lesión</h4>
                    <p>Después de mi lesión de rodilla, Andrea diseñó un programa de yoga terapéutico que me permitió recuperar movilidad sin dolor.</p>
                </div>
                
                <div class="announcement-item">
                    <span class="announcement-badge bg-primary text-white">Ana T.</span>
                    <h4>Pérdida de Peso</h4>
                    <p>Gracias a Laura perdí 12 kg en 4 meses. Sus entrenamientos funcionales son desafiantes pero adaptados a mi nivel.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials Section End -->

    <!-- CTA Section Start -->
    <section class="container-fluid explore py-5 wow zoomIn" data-wow-delay="0.2s">
        <div class="container py-5 text-center">
            <h1 class="display-1 text-white mb-4">¿Listo para comenzar?</h1>
            <p class="text-white mb-5">Reserva una clase grupal con uno de nuestros entrenadores y da el primer paso hacia tus objetivos fitness.</p>
            <a class="btn btn-primary py-3 px-4 px-md-5 me-2" href="clases.html"><i class="fas fa-calendar-alt me-2"></i> <span>Reservar Ahora</span></a>
        </div>
    </section>
    <!-- CTA Section End -->

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
                        <a href="clases.html"> Clases</a>
                        <a href="instructores.html"> Instructores</a>
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
    
    <script>
        // Inicializar el carrusel de testimonios
        $(document).ready(function(){
            $('.announcement-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                    992: {
                        items: 3
                    }
                }
            });
        });
    </script>
</body>
</html>