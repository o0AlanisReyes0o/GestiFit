<?php
// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'gestifitbd';

// Función para conectar
function conectarDB() {
    global $db_host, $db_user, $db_pass, $db_name;
    
    $conexion = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conexion, "utf8mb4");
    
    return $conexion;
}

// Función para consultas seguras simplificada
function consultaDB($conexion, $sql, $params = []) {
    $stmt = mysqli_prepare($conexion, $sql);
    
    if (!$stmt) {
        return false;
    }
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    return $stmt;
}
?>