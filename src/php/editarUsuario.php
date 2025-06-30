<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar sesión y permisos de administrador
        session_start();
        if (!isset($_SESSION['idUsuario']) || $_SESSION['tipo'] !== 'administrador') {
            header('HTTP/1.1 403 Forbidden');
            exit('Acceso denegado');
        }

        // Sanitizar y validar datos de entrada
        $idUsuario = filter_var($_POST['idUsuario'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidoPaterno = trim($_POST['apellidoPaterno'] ?? '');
        $apellidoMaterno = trim($_POST['apellidoMaterno'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $telefono = trim($_POST['telefono'] ?? '');
        $idMembresia = isset($_POST['membresia']) ? filter_var($_POST['membresia'], FILTER_SANITIZE_NUMBER_INT) : null;

        // Validaciones básicas
        if ($idUsuario <= 0) {
            header('Location: /GestiFit/public/admin/adminClientes.php?error=id_invalido');
            exit;
        }

        if (empty($nombre) || empty($apellidoPaterno) || empty($email)) {
            header('Location: /GestiFit/public/admin/adminClientes.php?error=campos_requeridos');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /GestiFit/public/admin/adminClientes.php?error=email_invalido');
            exit;
        }

        $pdo->beginTransaction();

        // 1. Verificar que el usuario existe
        $stmtCheck = $pdo->prepare("SELECT idUsuario FROM usuario WHERE idUsuario = ?");
        $stmtCheck->execute([$idUsuario]);
        
        if ($stmtCheck->rowCount() === 0) {
            $pdo->rollBack();
            header('Location: /GestiFit/public/admin/adminClientes.php?error=usuario_no_existe');
            exit;
        }

        // 2. Verificar unicidad del email (excepto para el mismo usuario)
        $stmtEmail = $pdo->prepare("SELECT idUsuario FROM usuario WHERE email = ? AND idUsuario != ?");
        $stmtEmail->execute([$email, $idUsuario]);
        
        if ($stmtEmail->rowCount() > 0) {
            $pdo->rollBack();
            header('Location: /GestiFit/public/admin/adminClientes.php?error=email_existente');
            exit;
        }

        // 3. Actualizar datos del usuario
        $stmtUsuario = $pdo->prepare("
            UPDATE usuario SET 
                nombre = :nombre,
                apellidoPaterno = :apellidoPaterno,
                apellidoMaterno = :apellidoMaterno,
                email = :email,
                telefono = :telefono
            WHERE idUsuario = :idUsuario
        ");

        $stmtUsuario->execute([
            ':nombre' => $nombre,
            ':apellidoPaterno' => $apellidoPaterno,
            ':apellidoMaterno' => $apellidoMaterno,
            ':email' => $email,
            ':telefono' => $telefono,
            ':idUsuario' => $idUsuario
        ]);

        // 4. Manejo de membresías
        // Obtener membresía actual del usuario
        $stmtMembActual = $pdo->prepare("
            SELECT idMembresia 
            FROM usuariomembresia 
            WHERE idUsuario = ? 
            AND estado = 'activa'
            ORDER BY fechaInicio DESC 
            LIMIT 1
        ");
        $stmtMembActual->execute([$idUsuario]);
        $membresiaActual = $stmtMembActual->fetch(PDO::FETCH_ASSOC);
        $idMembresiaActual = $membresiaActual['idMembresia'] ?? null;

        // Verificar si hay cambios en la membresía
        if ($idMembresia != $idMembresiaActual) {
            // Si se seleccionó una membresía diferente
            if ($idMembresia) {
                // Verificar que la membresía existe
                $stmtMembresia = $pdo->prepare("
                    SELECT duracionMeses, costo 
                    FROM membresia 
                    WHERE idMembresia = ?
                ");
                $stmtMembresia->execute([$idMembresia]);
                $membresia = $stmtMembresia->fetch(PDO::FETCH_ASSOC);
                
                if (!$membresia) {
                    $pdo->rollBack();
                    header('Location: /GestiFit/public/admin/adminClientes.php?error=membresia_no_existe');
                    exit;
                }

                // Cancelar membresía actual si existe
                if ($idMembresiaActual) {
                    $stmtCancelMemb = $pdo->prepare("
                        UPDATE usuariomembresia SET 
                            estado = 'cancelada',
                            fechaFin = CURDATE()
                        WHERE idUsuario = ?
                        AND estado = 'activa'
                    ");
                    $stmtCancelMemb->execute([$idUsuario]);
                }

                // Asignar nueva membresía
                $fechaInicio = new DateTime();
                $fechaFin = clone $fechaInicio;
                $fechaFin->add(new DateInterval('P' . $membresia['duracionMeses'] . 'M'));

                $stmtInsertMemb = $pdo->prepare("
                    INSERT INTO usuariomembresia (
                        idUsuario, idMembresia, fechaInicio, fechaFin, estado
                    ) VALUES (
                        :idUsuario, :idMembresia, :fechaInicio, :fechaFin, 'activa'
                    )
                ");
                $stmtInsertMemb->execute([
                    ':idUsuario' => $idUsuario,
                    ':idMembresia' => $idMembresia,
                    ':fechaInicio' => $fechaInicio->format('Y-m-d'),
                    ':fechaFin' => $fechaFin->format('Y-m-d')
                ]);

                // Registrar el pago (método por defecto: efectivo)
                $referencia = 'MEM-' . strtoupper(uniqid());
                $stmtPago = $pdo->prepare("
                    INSERT INTO pagos (
                        id_usuario, id_membresia, id_metodo_pago, monto, 
                        estado_pago, referencia_pago, fecha_pago
                    ) VALUES (
                        :idUsuario, :idMembresia, 2, :monto, 
                        'completado', :referencia, NOW()
                    )
                ");
                $stmtPago->execute([
                    ':idUsuario' => $idUsuario,
                    ':idMembresia' => $idMembresia,
                    ':monto' => $membresia['costo'],
                    ':referencia' => $referencia
                ]);
            } else {
                // Si se quitó la membresía (se seleccionó "Sin membresía")
                if ($idMembresiaActual) {
                    $stmtCancelMemb = $pdo->prepare("
                        UPDATE usuariomembresia SET 
                            estado = 'cancelada',
                            fechaFin = CURDATE()
                        WHERE idUsuario = ?
                        AND estado = 'activa'
                    ");
                    $stmtCancelMemb->execute([$idUsuario]);
                }
            }
        }

        $pdo->commit();
        header('Location: /GestiFit/public/admin/adminClientes.php?success=1');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error al actualizar cliente ID $idUsuario: " . $e->getMessage());
        header('Location: /GestiFit/public/admin/adminClientes.php?error=db');
        exit;
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Método no permitido');
}