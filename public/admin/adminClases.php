<?php
// Usar rutas absolutas para mayor seguridad
require_once __DIR__ . '/../../src/conexion.php';

// Iniciar sesión antes de cualquier verificación
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['idUsuario'])) {
    header("Location: /GestiFit/public/index.html");
    exit;
}

// Obtener información del usuario
$userId = $_SESSION['idUsuario'];
$query = "SELECT * FROM usuario WHERE idUsuario = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: /GestiFit/public/login.html");
    exit;
}

// Obtener lista de clases con información del instructor
$queryClases = "SELECT 
    c.id_clase, 
    c.nombre AS nombreClase, 
    CONCAT(u.nombre, ' ', u.apellidoPaterno) AS nombreInstructor,
    CONCAT(c.hora_inicio, ' - ', c.hora_fin) AS horario,
    c.cupo_maximo AS cuposDisponibles,
    (SELECT COUNT(*) FROM reservas_clases WHERE id_clase = c.id_clase) AS cuposOcupados,
    c.id_instructor
FROM clases_grupales c
JOIN usuario u ON c.id_instructor = u.idUsuario";
$clases = $pdo->query($queryClases)->fetchAll(PDO::FETCH_ASSOC);

// Obtener días para cada clase
$diasPorClase = [];
$queryDias = "SELECT idClase, GROUP_CONCAT(dia SEPARATOR ',') AS dias 
              FROM clasedias 
              GROUP BY idClase";
$resultDias = $pdo->query($queryDias);
while ($row = $resultDias->fetch(PDO::FETCH_ASSOC)) {
    $diasPorClase[$row['idClase']] = explode(',', $row['dias']);
}

// Obtener lista de instructores para los select
$queryInstructores = "SELECT idUsuario AS idInstructor, nombre, apellidoPaterno, apellidoMaterno 
                      FROM usuario 
                      WHERE tipo = 'instructor'";
$instructores = $pdo->query($queryInstructores)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>GestiFit - Admin Clases</title>
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
              <a href="/GestiFit/public/admin/adminRutinas.php" class="nav-item nav-link">
                <i class="fas fa-running me-2"></i>Rutinas
              </a>
              <a href="/GestiFit/public/admin/adminAvisos.php" class="nav-item nav-link ">
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

    <div class="align-items-center" style="margin-top: 50px;">
        <h2 class="mb-4 text-center mt-5">¿Deseas hacer cambios en las Clases?</h2>
    </div>

    <div class="container-fluid mt-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="text-primary mb-4">Clases Activas</h2>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarClase">Añadir Clase</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Instructor</th>
                            <th>Horario</th>
                            <th>Días</th>
                            <th>Cupo Máximo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clases as $clase): ?>
                            <tr>
                                <td><?= htmlspecialchars($clase['id_clase']) ?></td>
                                <td><?= htmlspecialchars($clase['nombreClase']) ?></td>
                                <td><?= htmlspecialchars($clase['nombreInstructor']) ?></td>
                                <td><?= htmlspecialchars($clase['horario']) ?></td>
                                <td><?= isset($diasPorClase[$clase['id_clase']]) ? htmlspecialchars(implode(', ', $diasPorClase[$clase['id_clase']])) : '' ?></td>
                                <td><?= htmlspecialchars($clase['cuposDisponibles']) ?></td>
                                <td><?= htmlspecialchars($clase['cuposDisponibles'] - $clase['cuposOcupados'] > 0 ? 'Disponible' : 'Llena') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary btnEditarClase"
                                        data-id="<?= $clase['id_clase'] ?>"
                                        data-nombre="<?= htmlspecialchars($clase['nombreClase']) ?>"
                                        data-instructor="<?= $clase['id_instructor'] ?>"
                                        data-horainicio="<?= htmlspecialchars(explode(' - ', $clase['horario'])[0]) ?>"
                                        data-horafin="<?= htmlspecialchars(explode(' - ', $clase['horario'])[1]) ?>"
                                        data-cupomaximo="<?= $clase['cuposDisponibles'] ?>"
                                        data-dias="<?= isset($diasPorClase[$clase['id_clase']]) ? implode(',', $diasPorClase[$clase['id_clase']]) : '' ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarClase">
                                        Editar
                                    </button>

                                    <form action="/GestiFit/src/php/eliminarClase.php" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta clase?');">
                                        <input type="hidden" name="id_clase" value="<?= $clase['id_clase'] ?>">
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

    <!-- Modal Agregar Clase -->
    <div class="modal fade" id="modalAgregarClase" tabindex="-1" aria-labelledby="modalAgregarClaseLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="/GestiFit/src/php/agregarClase.php" method="POST">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalAgregarClaseLabel">Agregar Nueva Clase</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="mb-3 col-md-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="id_instructor" class="form-label">Instructor</label>
                            <select class="form-select" id="id_instructor" name="id_instructor" required>
                                <option value="">Seleccionar instructor</option>
                                <?php foreach ($instructores as $instructor): ?>
                                    <option value="<?= $instructor['idInstructor'] ?>">
                                        <?= htmlspecialchars($instructor['nombre'] . ' ' . $instructor['apellidoPaterno']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="hora_inicio" class="form-label">Hora Inicio</label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="hora_fin" class="form-label">Hora Fin</label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="cupo_maximo" class="form-label">Cupo Máximo</label>
                            <input type="number" class="form-control" id="cupo_maximo" name="cupo_maximo" min="1" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="lugar" class="form-label">Lugar</label>
                            <input type="text" class="form-control" id="lugar" name="lugar" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="dificultad" class="form-label">Dificultad</label>
                            <select class="form-select" id="dificultad" name="dificultad">
                                <option value="principiante">Principiante</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Días de la semana</label>
                            <div class="d-flex flex-wrap gap-3">
                                <?php 
                                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                                foreach ($dias as $dia): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias[]" value="<?= $dia ?>" id="dia_<?= $dia ?>">
                                        <label class="form-check-label" for="dia_<?= $dia ?>"><?= $dia ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="requisitos" class="form-label">Requisitos (separados por comas)</label>
                            <textarea class="form-control" id="requisitos" name="requisitos" rows="2"></textarea>
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

    <!-- Modal Editar Clase -->
    <div class="modal fade" id="modalEditarClase" tabindex="-1" aria-labelledby="modalEditarClaseLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="/GestiFit/src/php/editarClase.php" method="POST">
                    <input type="hidden" name="id_clase" id="edit_id_clase">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalEditarClaseLabel">Editar Clase</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="mb-3 col-md-6">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="edit_id_instructor" class="form-label">Instructor</label>
                            <select class="form-select" id="edit_id_instructor" name="id_instructor" required>
                                <option value="">Seleccionar instructor</option>
                                <?php foreach ($instructores as $instructor): ?>
                                    <option value="<?= $instructor['idInstructor'] ?>">
                                        <?= htmlspecialchars($instructor['nombre'] . ' ' . $instructor['apellidoPaterno']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="edit_hora_inicio" class="form-label">Hora Inicio</label>
                            <input type="time" class="form-control" id="edit_hora_inicio" name="hora_inicio" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="edit_hora_fin" class="form-label">Hora Fin</label>
                            <input type="time" class="form-control" id="edit_hora_fin" name="hora_fin" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="edit_cupo_maximo" class="form-label">Cupo Máximo</label>
                            <input type="number" class="form-control" id="edit_cupo_maximo" name="cupo_maximo" min="1" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="edit_lugar" class="form-label">Lugar</label>
                            <input type="text" class="form-control" id="edit_lugar" name="lugar" required>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="edit_dificultad" class="form-label">Dificultad</label>
                            <select class="form-select" id="edit_dificultad" name="dificultad">
                                <option value="principiante">Principiante</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Días de la semana</label>
                            <div class="d-flex flex-wrap gap-3">
                                <?php foreach ($dias as $dia): ?>
                                    <div class="form-check">
                                        <input class="form-check-input edit_dias" type="checkbox" name="dias[]" value="<?= $dia ?>" id="edit_dia_<?= $dia ?>">
                                        <label class="form-check-label" for="edit_dia_<?= $dia ?>"><?= $dia ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="edit_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="edit_requisitos" class="form-label">Requisitos separados por comas</label>
                            <textarea class="form-control" id="edit_requisitos" name="requisitos" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar el botón de edición
            document.querySelectorAll('.btnEditarClase').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    const idInstructor = this.dataset.instructor;
                    const horaInicio = this.dataset.horainicio;
                    const horaFin = this.dataset.horafin;
                    const cupoMaximo = this.dataset.cupomaximo;
                    const dias = this.dataset.dias ? this.dataset.dias.split(',') : [];

                    // Llenar el formulario de edición
                    document.getElementById('edit_id_clase').value = id;
                    document.getElementById('edit_nombre').value = nombre;
                    document.getElementById('edit_id_instructor').value = idInstructor;
                    document.getElementById('edit_hora_inicio').value = horaInicio;
                    document.getElementById('edit_hora_fin').value = horaFin;
                    document.getElementById('edit_cupo_maximo').value = cupoMaximo;

                    // Marcar los días correspondientes
                    document.querySelectorAll('.edit_dias').forEach(checkbox => {
                        checkbox.checked = dias.includes(checkbox.value);
                    });

                    // Aquí deberías hacer una petición AJAX para obtener los demás datos
                    // como descripción, requisitos, lugar, etc.
                    fetch(`/GestiFit/src/obtenerClase.php?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('edit_lugar').value = data.lugar || '';
                            document.getElementById('edit_dificultad').value = data.dificultad || 'principiante';
                            document.getElementById('edit_descripcion').value = data.descripcion || '';
                            document.getElementById('edit_requisitos').value = data.requisitos || '';
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</body>

</html>