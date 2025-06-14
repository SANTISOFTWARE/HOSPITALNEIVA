<?php
session_start();
// AÑADIMOS EL BLOQUE DE SEGURIDAD
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode([]); // Devolvemos un array vacío si no hay sesión
    exit;
}

require 'db_connection.php';

// ... el resto del código de este archivo no cambia
$sql = "SELECT id, employee_name, employee_role, employee_phone, employee_email, equipment_type, brand, ip_address, mac_address, has_antivirus_license, has_office_license, has_windows_license, compliance_due_date FROM computer_inventory ORDER BY id DESC";
$result = $conn->query($sql);

$records = [];
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $row['licencias'] = [
        'windows' => $row['has_windows_license'] ? 'si' : 'no',
        'office' => $row['has_office_license'] ? 'si' : 'no',
        'antivirus' => $row['has_antivirus_license'] ? 'si' : 'no'
    ];
    unset($row['has_windows_license'], $row['has_office_license'], $row['has_antivirus_license']);
    $records[] = $row;
  }
}

echo json_encode($records);

$conn->close();
?>