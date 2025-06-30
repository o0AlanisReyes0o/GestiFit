<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/src/php/conexiondb.php';

header('Content-Type: application/json');

try {
    // Clientes por mes (usando fechaRegistro)
    $stmtClientes = $pdo->query("SELECT MONTH(fechaRegistro) AS mes, COUNT(*) AS total FROM usuario WHERE tipo = 'cliente' GROUP BY mes ORDER BY mes");
    $clientesData = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
    $clientes = ["labels" => [], "data" => []];
    foreach ($clientesData as $row) {
        $clientes['labels'][] = date('M', mktime(0, 0, 0, $row['mes'], 10));
        $clientes['data'][] = (int)$row['total'];
    }

    // Instructores (cantidad de clases que imparte cada uno)
    $stmtInstructores = $pdo->query("SELECT u.idUsuario, CONCAT(u.nombre, ' ', u.apellidoPaterno) AS nombre, COUNT(c.id_clase) AS totalClases 
                                    FROM usuario u 
                                    LEFT JOIN clases_grupales c ON u.idUsuario = c.id_instructor 
                                    WHERE u.tipo = 'instructor' 
                                    GROUP BY u.idUsuario");
    $instructoresData = $stmtInstructores->fetchAll(PDO::FETCH_ASSOC);
    $instructores = ["labels" => [], "data" => []];
    foreach ($instructoresData as $inst) {
        $instructores['labels'][] = $inst['nombre'];
        $instructores['data'][] = (int)$inst['totalClases'];
    }

    // Clases (preferencia por cantidad de usuarios inscritos)
    $stmtClases = $pdo->query("SELECT c.nombre, COUNT(rc.id_usuario) AS inscritos 
                              FROM clases_grupales c 
                              LEFT JOIN reservas_clases rc ON c.id_clase = rc.id_clase 
                              GROUP BY c.id_clase");
    $clasesData = $stmtClases->fetchAll(PDO::FETCH_ASSOC);
    $clases = ["labels" => [], "data" => []];
    foreach ($clasesData as $clase) {
        $clases['labels'][] = $clase['nombre'];
        $clases['data'][] = (int)$clase['inscritos'];
    }

    // Membresías (distribución por cantidad de usuarios)
    $stmtMembresias = $pdo->query("SELECT m.nombre, COUNT(um.idUsuario) AS total 
                                  FROM usuariomembresia um 
                                  JOIN membresia m ON um.idMembresia = m.idMembresia 
                                  GROUP BY m.idMembresia");
    $membresiasData = $stmtMembresias->fetchAll(PDO::FETCH_ASSOC);
    $membresias = ["labels" => [], "data" => []];
    foreach ($membresiasData as $mem) {
        $membresias['labels'][] = $mem['nombre'];
        $membresias['data'][] = (int)$mem['total'];
    }

    // Respuesta final
    echo json_encode([
        "clientes" => $clientes,
        "instructores" => $instructores,
        "clases" => $clases,
        "membresias" => $membresias
    ]);

} catch (PDOException $e) {
    error_log("Error en datosGraficas.php: " . $e->getMessage());
    echo json_encode(["error" => "No se pudieron obtener los datos"]);
}