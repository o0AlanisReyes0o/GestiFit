<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/GestiFit/public/php/conexiondb.php';

try {
    // Obtener todos los clientes (filtrar por tipo = 'cliente')
    $stmtClientes = $pdo->prepare("SELECT 
        idUsuario, 
        nombre, 
        apellidoPaterno, 
        apellidoMaterno, 
        email, 
        telefono 
        FROM usuario 
        WHERE tipo = 'cliente'
        ORDER BY idUsuario");
    $stmtClientes->execute();
    $clientesRaw = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener membresías activas de todos los clientes
    $stmtMembresias = $pdo->prepare("SELECT 
        um.idUsuario, 
        m.nombre AS membresia, 
        um.fechaInicio AS fecha_inicio, 
        um.fechaFin AS fecha_fin
        FROM usuariomembresia um
        LEFT JOIN membresia m ON um.idMembresia = m.idMembresia");
    $stmtMembresias->execute();
    $membresiasRaw = $stmtMembresias->fetchAll(PDO::FETCH_ASSOC);

    // Reorganizar membresías por usuario
    $membresiasPorUsuario = [];
    foreach ($membresiasRaw as $m) {
        $membresiasPorUsuario[$m['idUsuario']] = $m;
    }

    // Combinar datos
    $clientes = [];
    foreach ($clientesRaw as $c) {
        $id = $c['idUsuario'];
        $clientes[] = [
            'idUsuario' => $id,
            'nombreCompleto' => trim($c['nombre'] . ' ' . $c['apellidoPaterno'] . ' ' . $c['apellidoMaterno']),
            'email' => $c['email'],
            'telefono' => $c['telefono'],
            'membresia' => $membresiasPorUsuario[$id]['membresia'] ?? 'Sin membresía',
            'fecha_inicio' => $membresiasPorUsuario[$id]['fecha_inicio'] ?? '-',
            'fecha_fin' => $membresiasPorUsuario[$id]['fecha_fin'] ?? '-',
        ];
    }

    // Debug en consola
    echo "<script>console.log('✔️ Datos finales de clientes: " . json_encode($clientes) . "');</script>";

} catch (PDOException $e) {
    error_log("Error en adminConsultas.php: " . $e->getMessage());
    $clientes = [];
}
?>
