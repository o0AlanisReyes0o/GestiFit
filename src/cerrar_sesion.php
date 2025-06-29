<?php
// cerrar_sesion.php - Manejo seguro de cierre de sesión

require_once __DIR__ . '/seguridad.php';


// Cerrar sesión y redirigir
cerrarSesion('/GestiFit/public/public/logout.html?logout=success');

?>