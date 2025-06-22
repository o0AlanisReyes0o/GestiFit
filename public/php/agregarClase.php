<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreClase = $_POST['nombreClase'] ?? '';
    $horario = $_POST['horario'] ?? '';
    $cuposDisponibles = $_POST['cuposDisponibles'] ?? 0;
    $cuposOcupados = $_POST['cuposOcupados'] ?? 0;
    $idInstructor = $_POST['idInstructor'] ?? null;
    $dias = $_POST['dias'] ?? [];

    if ($nombreClase && $horario && is_numeric($cuposDisponibles) && is_numeric($cuposOcupados) && $idInstructor) {
        try {
            $pdo->beginTransaction();

            // Insertar clase
            $stmt = $pdo->prepare("INSERT INTO clase (nombreClase, horario, cuposDisponibles, cuposOcupados, idInstructor) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombreClase, $horario, $cuposDisponibles, $cuposOcupados, $idInstructor]);

            $idClase = $pdo->lastInsertId(); // Obtener ID de la nueva clase

            // Insertar días
            $stmtDia = $pdo->prepare("INSERT INTO clasedias (idClase, dia) VALUES (?, ?)");
            foreach ($dias as $dia) {
                $stmtDia->execute([$idClase, $dia]);
            }

            $pdo->commit();
            header("Location: adminClases.php?mensaje=clase_agregada");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error al agregar clase: " . $e->getMessage());
            header("Location: adminClases.php?error=agregado_fallido");
            exit;
        }
    } else {
        header("Location: adminClases.php?error=datos_invalidos");
        exit;
    }
} else {
    header("Location: adminClases.php");
    exit;
}
