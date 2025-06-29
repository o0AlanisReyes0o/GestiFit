<?php
// seguridad.php - funciones comunes de seguridad

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Función para cerrar sesión
function cerrarSesion($redirect = null) {
    session_unset();
    session_destroy();
    if ($redirect) {
        header("Location: $redirect");
        exit;
    }
}

// Función opcional de verificación de token CSRF
function verificarCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit("Token CSRF inválido.");
    }
}

// Genera token CSRF (por si lo quieres usar en formularios)
function generarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
?>
