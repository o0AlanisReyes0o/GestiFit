<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

if (
    isset($_POST['nombre']) &&
    isset($_POST['apellidoPaterno']) &&
    isset($_POST['apellidoMaterno']) &&
    isset($_POST['edad']) &&
    isset($_POST['usuario']) &&
    isset($_POST['direccion']) &&
    isset($_POST['email']) &&
    isset($_POST['telefono']) &&
    isset($_POST['contrasena'])
) {
    try {
        // 1. Validar que el usuario no exista
        $stmtCheckUser = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE usuario = :usuario");
        $stmtCheckUser->execute([':usuario' => $_POST['usuario']]);
        if ($stmtCheckUser->fetchColumn() > 0) {
            header("Location: /GestiFit/public/admin/adminClientes.php?error=usuario");
            exit();
        }

        // 2. Validar que el email no exista
        $stmtCheckEmail = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email");
        $stmtCheckEmail->execute([':email' => $_POST['email']]);
        if ($stmtCheckEmail->fetchColumn() > 0) {
            header("Location: /GestiFit/public/admin/adminClientes.php?error=email");
            exit();
        }

        $pdo->beginTransaction();

        // 3. Insertar nuevo cliente
        $stmt = $pdo->prepare("
            INSERT INTO usuario (
                nombre, apellidoPaterno, apellidoMaterno, edad,
                tipo, usuario, contrasena, direccion, email, telefono, fechaRegistro
            ) VALUES (
                :nombre, :apellidoPaterno, :apellidoMaterno, :edad,
                'cliente', :usuario, :contrasena, :direccion, :email, :telefono, NOW()
            )
        ");

        $stmt->execute([
            ':nombre' => $_POST['nombre'],
            ':apellidoPaterno' => $_POST['apellidoPaterno'],
            ':apellidoMaterno' => $_POST['apellidoMaterno'],
            ':edad' => intval($_POST['edad']),
            ':usuario' => $_POST['usuario'],
            ':contrasena' => password_hash($_POST['contrasena'], PASSWORD_DEFAULT),
            ':direccion' => $_POST['direccion'],
            ':email' => $_POST['email'],
            ':telefono' => $_POST['telefono']
        ]);

        $idUsuario = $pdo->lastInsertId();

        // 4. Insertar en usuariomembresia si aplica
        if (!empty($_POST['membresia'])) {
            try {
                // Obtener detalles de la membresía seleccionada
                $stmtMembresia = $pdo->prepare("
                    SELECT duracionMeses, costo 
                    FROM membresia 
                    WHERE idMembresia = :idMembresia
                ");
                $stmtMembresia->execute([':idMembresia' => $_POST['membresia']]);
                $membresia = $stmtMembresia->fetch(PDO::FETCH_ASSOC);

                if (!$membresia) {
                    throw new Exception("La membresía seleccionada no existe");
                }

                // Calcular fecha de fin basada en la duración
                $fechaInicio = new DateTime();
                $fechaFin = clone $fechaInicio;
                $fechaFin->add(new DateInterval('P' . $membresia['duracionMeses'] . 'M'));

                // Insertar en usuariomembresia
                $stmtM = $pdo->prepare("
                    INSERT INTO usuariomembresia (
                        idUsuario, idMembresia, fechaInicio, fechaFin, estado
                    ) VALUES (
                        :idUsuario, :idMembresia, :fechaInicio, :fechaFin, 'activa'
                    )
                ");
                $stmtM->execute([
                    ':idUsuario' => $idUsuario,
                    ':idMembresia' => $_POST['membresia'],
                    ':fechaInicio' => $fechaInicio->format('Y-m-d'),
                    ':fechaFin' => $fechaFin->format('Y-m-d')
                ]);

                // Registrar el pago
                $stmtPago = $pdo->prepare("
                    INSERT INTO pagos (
                        id_usuario, id_membresia, monto, estado_pago, referencia_pago, id_metodo_pago, fecha_pago
                    ) VALUES (
                        :idUsuario, :idMembresia, :monto, 'completado', :referencia, 2,NOW()
                    )
                ");
                $stmtPago->execute([
                    ':idUsuario' => $idUsuario,
                    ':idMembresia' => $_POST['membresia'],
                    ':monto' => $membresia['costo'],
                    ':referencia' => 'GESTI-' . strtoupper(uniqid())
                ]);

            } catch (Exception $e) {
                $pdo->rollBack();
                header("Location: /GestiFit/public/admin/adminClientes.php?error=membresia");
                exit();
            }
        }

        $pdo->commit();
        header("Location: /GestiFit/public/admin/adminClientes.php?registro=exito");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error al registrar cliente: " . $e->getMessage();
    }
} else {
    echo "Faltan campos obligatorios.";
}