<?php
// Usar rutas absolutas para mayor seguridad
require_once __DIR__ . '/../../src/conexion.php';

// Iniciar sesión antes de cualquier verificación
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: /GestiFit/public/index.html");
    exit;
}

// Obtener información del usuario
$userId = $_SESSION['idUsuario'];
$query = "SELECT * FROM usuario WHERE idUsuario = ?";
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

// Obtener lista de instructores
$queryInstructores = "SELECT 
  idUsuario AS idInstructor, 
  CONCAT(nombre, ' ', apellidoPaterno, IFNULL(CONCAT(' ', apellidoMaterno), '')) AS nombreCompleto, 
  edad
FROM usuario 
WHERE tipo = 'instructor'";
$resultInstructores = mysqli_query($conexion, $queryInstructores);
$instructores = mysqli_fetch_all($resultInstructores, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>GestiFit - Admin Instructores</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Teko:wght@300..700&display=swap"
        rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="/GestiFit/public/img/logo_gestifit_cuadrado-nofondo.png" />

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="/GestiFit/lib/animate/animate.min.css" />
    <link href="/GestiFit/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="/GestiFit/public/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="/GestiFit/public/css/stylesAdmin.css" rel="stylesheet" />
    <link href="/GestiFit/public/css/style.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
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
                                <div class="pe-4">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center text-lg-end">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex pe-3">
                                    <a class="btn p-0 text-primary me-3"
                                        href="https://www.instagram.com/elmanicomiogym?igsh=MXB3eHBkdjFjYXJleQ=="><i
                                            class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nav-bar px-0 py-lg-0" style="height: 80px;">
                    <nav class="navbar navbar-expand-lg navbar-light d-flex justify-content-lg-end">
                        <a href="#" class="navbar-brand-2">
                            <h1 class="text-primary mb-0"><i class="fas fa-hand-rock me-2"></i>GestiFit</h1>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="fa fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarCollapse">
                            <div class="navbar-nav mx-0 mx-lg-auto">
                                <a href="/GestiFit/public/admin/admin.php" class="nav-item nav-link active fa fa-home"> Principal</a>
                                <a href="/GestiFit/public/admin/adminClientes.php" class="nav-item nav-link fa fa-users"> Clientes</a>
                                <a href="/GestiFit/public/admin/adminMembresias.php" class="nav-item nav-link fa fa-user-tie"> Membresias</a>
                                <a href="/GestiFit/public/admin/adminInstructores.php" class="nav-item nav-link fa fa-dumbbell"> Instructores</a>
                                <a href="/GestiFit/public/admin/adminClases.php" class="nav-item nav-link fa fa-id-card"> Clases</a>

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

    <div class="align-items-center" style="margin-top: 50px;">
        <h2 class="mb-4 text-center mt-5">¿Deseas hacer cambios en los Instructores?</h2>
    </div>

    <div class="container-fluid mt-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="text-primary mb-4">Instructores Activos</h2>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#agregarInstructorModal">Añadir Instructor</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Edad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instructores as $i): ?>
                            <tr>
                                <td><?= htmlspecialchars($i['idInstructor']) ?></td>
                                <td><?= htmlspecialchars($i['nombreCompleto']) ?></td>
                                <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                <td><?= htmlspecialchars($user['telefono'] ?? '') ?></td>
                                <td><?= htmlspecialchars($i['edad']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary btnEditarInstructor"
                                        data-id="<?= $i['idInstructor'] ?>"
                                        data-nombre="<?= htmlspecialchars(explode(' ', $i['nombreCompleto'])[0]) ?>"
                                        data-apellidop="<?= htmlspecialchars(explode(' ', $i['nombreCompleto'])[1] ?? '') ?>"
                                        data-apellidom="<?= htmlspecialchars(explode(' ', $i['nombreCompleto'])[2] ?? '') ?>"
                                        data-edad="<?= htmlspecialchars($i['edad']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarInstructor">Editar</button>

                                    <form action="/GestiFit/src/php/eliminarInstructor.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este instructor?');">
                                        <input type="hidden" name="idUsuario" value="<?= $i['idInstructor'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="copyright py-3 text-center text-white bg-dark mt-auto">
        <div class="container">
            <small>&copy; 2025 GestiFit. Todos los derechos reservados.</small>
        </div>
    </div>

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
    <script async src="//www.instagram.com/embed.js"></script>
    <script src="/GestiFit/public/js/bootstrap.bundle.min.js"></script>
    <script src="/GestiFit/public/js/main.js"></script>
    <script src="/GestiFit/public/js/admin.js"></script>

    <!-- Modal: Agregar Instructor -->
    <div class="modal fade" id="agregarInstructorModal" tabindex="-1" aria-labelledby="agregarInstructorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="/GestiFit/src/php/agregarInstructor.php" method="POST">
                    <input type="hidden" name="tipo" value="instructor">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="agregarInstructorModalLabel">Agregar Instructor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="mb-3 col-md-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="apellidoPaterno" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="apellidoMaterno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="edad" class="form-label">Edad</label>
                            <input type="number" class="form-control" id="edad" name="edad" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="usuario" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Instructor -->
    <div class="modal fade" id="modalEditarInstructor" tabindex="-1" aria-labelledby="modalEditarInstructorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="/GestiFit/src/php/editarInstructor.php" method="POST" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Editar Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <input type="hidden" name="idUsuario" id="edit_idInstructor">
                    <div class="mb-3 col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control mb-2" placeholder="Nombre" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="apellidoPaterno" class="form-label">Apellido Paterno</label>
                        <input type="text" name="apellidoPaterno" id="edit_apellidoPaterno" class="form-control mb-2" placeholder="Apellido Paterno" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="apellidoMaterno" class="form-label">Apellido Materno</label>
                        <input type="text" name="apellidoMaterno" id="edit_apellidoMaterno" class="form-control mb-2" placeholder="Apellido Materno">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="edad" class="form-label">Edad</label>
                        <input type="number" name="edad" id="edit_edad" class="form-control mb-2" placeholder="Edad" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control mb-2" placeholder="Email" required>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" name="telefono" id="edit_telefono" class="form-control mb-2" placeholder="Teléfono" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btnEditarInstructor').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    const apellidoPaterno = this.dataset.apellidop;
                    const apellidoMaterno = this.dataset.apellidom;
                    const edad = this.dataset.edad;

                    document.getElementById('edit_idInstructor').value = id;
                    document.getElementById('edit_nombre').value = nombre;
                    document.getElementById('edit_apellidoPaterno').value = apellidoPaterno;
                    document.getElementById('edit_apellidoMaterno').value = apellidoMaterno;
                    document.getElementById('edit_edad').value = edad;
                    
                    // Aquí deberías hacer una petición AJAX para obtener el email y teléfono
                    // del instructor y llenar los campos correspondientes
                    fetch(`/GestiFit/src/obtenerUsuario.php?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('edit_email').value = data.email;
                            document.getElementById('edit_telefono').value = data.telefono;
                        });
                });
            });
        });
    </script>

</body>

</html>