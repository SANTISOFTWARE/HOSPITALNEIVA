<?php
session_start();
// AÑADIMOS EL BLOQUE DE SEGURIDAD
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode([]); // Devolvemos un array vacío si no hay sesión
    exit;
}

require 'db_connection.php';

$sql = "SELECT id, created_at FROM personnel_reports ORDER BY created_at DESC";
$result = $conn->query($sql);

$reports = [];
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $reports[] = $row;
  }
}

echo json_encode($reports);

$conn->close();
?>