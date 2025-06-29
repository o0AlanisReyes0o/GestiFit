<?php
require_once '../../conexion.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT c.*, u.nombre AS instructor_nombre, u.apellidoPaterno AS instructor_apellido
        FROM clases_grupales c
        JOIN Usuario u ON c.id_instructor = u.idUsuario");
    $stmt->execute();
    $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($clases as &$clase) {
        // Obtener los días de la clase
        $diasStmt = $pdo->prepare("SELECT dia FROM ClaseDias WHERE idClase = ?");
        $diasStmt->execute([$clase['id_clase']]);
        $clase['dias'] = array_column($diasStmt->fetchAll(PDO::FETCH_ASSOC), 'dia');
        $clase['requisitos'] = $clase['requisitos'] ? explode(',', $clase['requisitos']) : [];

        // Calcular cupos disponibles
        $ocupados = $pdo->prepare("SELECT COUNT(*) FROM reservas_clases WHERE id_clase = ?");
        $ocupados->execute([$clase['id_clase']]);
        $inscritos = $ocupados->fetchColumn();
        $clase['cupos_disponibles'] = $clase['cupo_maximo'] - $inscritos;
    }

    // Construir horario agrupado por hora
    $horario = [];
    foreach ($clases as $clase) {
        $hora = substr($clase['hora_inicio'], 0, 5);
        if (!isset($horario[$hora])) {
            $horario[$hora] = [
                'hora' => $hora,
                'lunes' => null, 'martes' => null, 'miércoles' => null,
                'jueves' => null, 'viernes' => null, 'sábado' => null
            ];
        }
        foreach ($clase['dias'] as $dia) {
            $lc = strtolower($dia);
            $horario[$hora][$lc] = [
                'nombre' => $clase['nombre'],
                'instructor' => $clase['instructor_nombre']
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'clases' => $clases,
        'horario' => $horario
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}