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
    <link href="/Gestifit/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

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
        <h2 class="mb-4">¿Deseas hacer cambios en Clases?</h2>
    </div>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Clases Disponibles</h2>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarClase">Nueva Clase</button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Clase</th>
                        <th>Instructor</th>
                        <th>Horario</th>
                        <th>Días</th>
                        <th>Cupos Disponibles</th>
                        <th>Cupos Ocupados</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clases)): ?>
                        <?php foreach ($clases as $clase): ?>
                            <tr>
                                <td><?= htmlspecialchars($clase['nombreClase']) ?></td>
                                <td><?= htmlspecialchars($clase['nombreInstructor']) ?></td>
                                <td><?= htmlspecialchars($clase['horario']) ?></td>
                                <td><?= htmlspecialchars(implode(', ', $diasPorClase[$clase['idClase']] ?? [])) ?></td>
                                <td><?= htmlspecialchars($clase['cuposDisponibles']) ?></td>
                                <td><?= htmlspecialchars($clase['cuposOcupados']) ?></td>
                                <td>
                                    <?php
                                    $diasString = isset($diasPorClase[$clase['idClase']]) ? implode(',', $diasPorClase[$clase['idClase']]) : '';
                                    ?>

                                    <button class="btn btn-sm btn-secondary btn-editar"
                                        data-id="<?= $clase['idClase'] ?>"
                                        data-nombre="<?= htmlspecialchars($clase['nombreClase']) ?>"
                                        data-horario="<?= htmlspecialchars($clase['horario']) ?>"
                                        data-cuposdisp="<?= $clase['cuposDisponibles'] ?>"
                                        data-cuposocu="<?= $clase['cuposOcupados'] ?>"
                                        data-idinstructor="<?= $clase['idInstructor'] ?>"
                                        data-dias="<?= implode(',', $diasPorClase[$clase['idClase']] ?? []) ?>">
                                        Editar
                                    </button>

                                    <form method="POST" action="/GestiFit/public/php/eliminarClase.php" style="display:inline-block" onsubmit="return confirm('¿Seguro que quieres eliminar esta clase?');">
                                        <input type="hidden" name="idClase" value="<?= $clase['idClase'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay clases registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
    <!-- Modal Agregar -->
    <div class="modal fade" id="modalAgregarClase" tabindex="-1" aria-labelledby="modalAgregarClaseLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formAgregarClase" method="POST" action="/GestiFit/public/php/agregarClase.php">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalAgregarClaseLabel">Agregar Nueva Clase</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="mb-3 col-md-6">
                            <label for="nombreClaseAgregar" class="form-label">Nombre de la clase</label>
                            <input type="text" class="form-control" id="nombreClaseAgregar" name="nombreClase" required>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="horarioAgregar" class="form-label">Horario</label>
                            <select class="form-select" id="horarioAgregar" name="horario" required>
                                <option value="" disabled selected>Seleccione un horario</option>
                                <?php
                                for ($hora = 8; $hora <= 19; $hora++) {
                                    $horaFormatted = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                                    echo "<option value=\"$horaFormatted\">$horaFormatted</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="cuposDisponiblesAgregar" class="form-label">Cupos Disponibles</label>
                            <input type="number" class="form-control" id="cuposDisponiblesAgregar" name="cuposDisponibles" min="0" required>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="cuposOcupadosAgregar" class="form-label">Cupos Ocupados</label>
                            <input type="number" class="form-control" id="cuposOcupadosAgregar" name="cuposOcupados" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label for="instructorAgregar" class="form-label">Instructor</label>
                        <select class="form-select" id="instructorAgregar" name="idInstructor" required>
                            <option value="" disabled selected>Seleccione un instructor</option>
                            <?php foreach ($instructores as $inst): ?>
                                <option value="<?= $inst['idInstructor'] ?>">
                                    <?= htmlspecialchars($inst['nombre'] . ' ' . $inst['apellidoPaterno'] . ' ' . $inst['apellidoMaterno']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label">Días</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                            foreach ($diasSemana as $dia): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias[]" value="<?= $dia ?>" id="diaAgregar<?= $dia ?>">
                                    <label class="form-check-label" for="diaAgregar<?= $dia ?>"><?= $dia ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Agregar Clase</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- Modal Editar Clase -->
    <div class="modal fade" id="modalEditarClase" tabindex="-1" aria-labelledby="modalEditarClaseLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="/GestiFit/public/php/editarClase.php" method="POST" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditarClaseLabel">Editar Clase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body row">
                    <input type="hidden" name="idClase" id="idClaseEditar">

                    <div class="mb-3 col-md-6">
                        <label for="nombreClaseEditar" class="form-label">Nombre de la clase</label>
                        <input type="text" class="form-control" name="nombreClase" id="nombreClaseEditar" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="horarioEditar" class="form-label">Horario</label>
                        <select class="form-select" name="horario" id="horarioEditar" required>
                            <option value="" disabled selected>Seleccione un horario</option>
                            <?php
                            for ($hora = 8; $hora <= 19; $hora++) {
                                $horaFormatted = str_pad($hora, 2, '0', STR_PAD_LEFT) . ':00';
                                echo "<option value=\"$horaFormatted\">$horaFormatted</option>";
                            }
                            ?>
                        </select>
                    </div>


                    <div class="mb-3 col-md-6">
                        <label for="cuposDisponiblesEditar" class="form-label">Cupos disponibles</label>
                        <input type="number" class="form-control" name="cuposDisponibles" id="cuposDisponiblesEditar" min="0" required>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="cuposOcupadosEditar" class="form-label">Cupos ocupados</label>
                        <input type="number" class="form-control" name="cuposOcupados" id="cuposOcupadosEditar" min="0" required>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label for="instructorEditar" class="form-label">Instructor</label>
                        <select class="form-select" name="idInstructor" id="instructorEditar" required>
                            <option value="" disabled selected>Selecciona un instructor</option>
                            <?php
                            try {
                                $stmtInstructores = $pdo->query("SELECT idInstructor, CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) AS nombreCompleto FROM instructor");
                                while ($instructor = $stmtInstructores->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $instructor['idInstructor'] . '">' . htmlspecialchars($instructor['nombreCompleto']) . '</option>';
                                }
                            } catch (PDOException $e) {
                                echo '<option value="">Error al cargar instructores</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label">Días de la clase</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                            foreach ($diasSemana as $dia) {
                                echo '
                <div class="form-check form-check-inline">
                  <input class="form-check-input diasEditar" type="checkbox" name="dias[]" value="' . $dia . '" id="diaEditar_' . $dia . '">
                  <label class="form-check-label" for="diaEditar_' . $dia . '">' . $dia . '</label>
                </div>
              ';
                            }
                            ?>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const nombre = btn.getAttribute('data-nombre');
                const horario = btn.getAttribute('data-horario')?.substring(0, 5); // recorta a "HH:MM"
                const cuposDisponibles = btn.getAttribute('data-cuposdisp');
                const cuposOcupados = btn.getAttribute('data-cuposocu');
                const idInstructor = btn.getAttribute('data-idinstructor');
                const dias = (btn.getAttribute('data-dias') || '').split(',').map(d => d.trim());

                document.getElementById('idClaseEditar').value = id;
                document.getElementById('nombreClaseEditar').value = nombre;
                document.getElementById('horarioEditar').value = horario;
                document.getElementById('cuposDisponiblesEditar').value = cuposDisponibles;
                document.getElementById('cuposOcupadosEditar').value = cuposOcupados;
                document.getElementById('instructorEditar').value = idInstructor;

                document.querySelectorAll('.diasEditar').forEach(chk => {
                    chk.checked = dias.includes(chk.value);
                });

                const modal = new bootstrap.Modal(document.getElementById('modalEditarClase'));
                modal.show();
            });
        });
    </script>
</body>

</html>