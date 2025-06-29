<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../conexion.php';

// Función para sanitizar datos
function sanitizeData($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Función para procesar equipamiento e instrucciones
function processTextData($text) {
    return array_filter(array_map('trim', explode("\n", $text)));
}

try {
    $conexion = conectarDB();
    
    // Consulta SQL con parámetros preparados
    $sql = "SELECT id_rutina, nombre_rutina, nivel_rutina, descripcion, duracion_semanas, dias_por_semana, 
                   objetivo, equipamiento_necesario, instrucciones, video_url, imagen_url 
            FROM rutinas 
            WHERE activa = 1
            ORDER BY FIELD(nivel_rutina, 'Principiante', 'Intermedio', 'Avanzado'), nombre_rutina;";
    
    $stmt = consultaDB($conexion, $sql);
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $rutinas = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $video_url = $row['video_url'];
        
        // Procesar URL de YouTube si es necesario
        if (strpos($video_url, 'youtube.com/watch?v=') !== false) {
            parse_str(parse_url($video_url, PHP_URL_QUERY), $params);
            $video_url = isset($params['v']) ? "https://www.youtube.com/embed/{$params['v']}" : $video_url;
        }
        
        $rutinas[] = [
            'id' => (int)$row['id_rutina'],
            'nombre' => sanitizeData($row['nombre_rutina']),
            'nivel' => strtolower($row['nivel_rutina']),
            'nivel_display' => sanitizeData($row['nivel_rutina']),
            'descripcion' => sanitizeData($row['descripcion']),
            'duracion_semanas' => (int)$row['duracion_semanas'],
            'dias_por_semana' => (int)$row['dias_por_semana'],
            'objetivo' => sanitizeData($row['objetivo']),
            'objetivo_key' => strtolower(str_replace(' ', '-', $row['objetivo'])),
            'equipamiento' => array_map(
                'sanitizeData',
                array_filter(array_map('trim', explode(',', $row['equipamiento_necesario'])))
            ),
            'instrucciones' => array_map('sanitizeData', processTextData($row['instrucciones'])),
            'video_url' => sanitizeData($video_url),
            'imagen_url' => sanitizeData($row['imagen_url'] ?: '/GestiFit/public/img/rutina-default.jpg')
        ];
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    
    echo json_encode([
        'success' => true,
        'count' => count($rutinas),
        'rutinas' => $rutinas
    ], JSON_PRETTY_PRINT); // Agregado JSON_PRETTY_PRINT para mejor legibilidad
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>