<?php
// Iniciar la sesión para poder acceder a ella.
session_start();

// Destruir todas las variables de sesión.
$_SESSION = array();

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir al usuario a la página de inicio de sesión.
// Usamos ../ para salir de la carpeta 'api' y llegar a 'index.html'.
header("location: ../index.html");
exit;
?>