<?php
require_once 'conexion.php';

// Establecer headers primero para evitar salidas no deseadas
header('Content-Type: application/json');

try {
    $conexion = conectarDB();
    
    // Consulta para obtener avisos para el carrusel
    $sql = "SELECT clave, valor, descripcion 
            FROM configuraciones 
            ORDER BY clave 
            LIMIT 5";

    $stmt = consultaDB($conexion, $sql, []);
    $result = mysqli_stmt_get_result($stmt);

    $avisos = [];

    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['valor'])) {
            $avisos[] = [
                'title' => formatTitle($row['clave']),
                'description' => $row['valor'],
                'image' => 'bannerNegro.jpg',
                'button' => 'Mas información', // Puedes ajustar esto según tus necesidades)
            ];
        }
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $avisos ?: getDefaultAnnouncements()
    ]);

} catch (Exception $e) {
    // Manejo de errores
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar avisos: ' . $e->getMessage()
    ]);
}

// Funciones auxiliares...

function formatTitle($clave) {
    $titles = [
        'aviso_mantenimiento' => 'Mantenimiento Programado',
        'aviso_evento_especial' => 'Evento Especial',
        // ... otros títulos personalizados
    ];
    
    return $titles[$clave] ?? ucwords(str_replace(['aviso_', '_'], ['', ' '], $clave));
}

function getDefaultAnnouncements() {
    return [
        [
            'title' => 'Bienvenido a GestiFit',
            'description' => 'Descubre todo lo que tenemos para ofrecerte',
            'image' => 'bannerNegro.jpg',
            'button' => ['text' => 'Conoce más', 'link' => 'nosotros.html']
        ]
    ];
}
?>