<?php
// Configuración de la base de datos
$servername = "localhost"; // El servidor de la base de datos, generalmente localhost
$username = "root";      // Tu nombre de usuario de MySQL para XAMPP (por defecto es 'root')
$password = "";          // Tu contraseña de MySQL para XAMPP (por defecto está vacía)
$dbname = "hospital_dashboard_db"; // El nombre de la base de datos que creamos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  // Si hay un error, detenemos la ejecución y mostramos el error.
  die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8 para soportar tildes y caracteres especiales
$conn->set_charset("utf8mb4");

// Establecemos la cabecera para que la respuesta siempre sea de tipo JSON
header('Content-Type: application/json');
?>