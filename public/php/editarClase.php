<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idClase = $_POST['idClase'] ?? null;
    $nombreClase = $_POST['nombreClase'] ?? '';
    $horario = $_POST['horario'] ?? '';
    $cuposDisponibles = $_POST['cuposDisponibles'] ?? 0;
    $cuposOcupados = $_POST['cuposOcupados'] ?? 0;
    $idInstructor = $_POST['idInstructor'] ?? null;
    $dias = $_POST['dias'] ?? [];

    if ($idClase && $nombreClase && $horario && is_numeric($cuposDisponibles) && is_numeric($cuposOcupados) && $idInstructor) {
        try {
            $pdo->beginTransaction();

            // Actualizar clase
            $stmt = $pdo->prepare("UPDATE clase SET nombreClase = ?, horario = ?, cuposDisponibles = ?, cuposOcupados = ?, idInstructor = ? WHERE idClase = ?");
            $stmt->execute([$nombreClase, $horario, $cuposDisponibles, $cuposOcupados, $idInstructor, $idClase]);

            // Eliminar días antiguos
            $stmtDel = $pdo->prepare("DELETE FROM clasedias WHERE idClase = ?");
            $stmtDel->execute([$idClase]);

            // Insertar nuevos días
            $stmtDia = $pdo->prepare("INSERT INTO clasedias (idClase, dia) VALUES (?, ?)");
            foreach ($dias as $dia) {
                $stmtDia->execute([$idClase, $dia]);
            }

            $pdo->commit();
            header("Location: /GestiFit/public/admin/adminClases.php?mensaje=clase_editada");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error al editar clase: " . $e->getMessage());
            header("Location: /GestiFit/public/admin/adminClases.php?error=edicion_fallida");
            exit;
        }
    } else {
        header("Location: /GestiFit/public/admin/adminClases.php?error=datos_invalidos");
        exit;
    }
} else {
    header("Location: /GestiFit/public/admin/adminClases.php");
    exit;
}
