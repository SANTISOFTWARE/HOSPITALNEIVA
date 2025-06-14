<?php
session_start();
// AÑADIMOS EL BLOQUE DE SEGURIDAD
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

require 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];

$has_antivirus = ($data['licencias']['antivirus'] === 'si');
$has_office = ($data['licencias']['office'] === 'si');
$has_windows = ($data['licencias']['windows'] === 'si');

$compliance_due_date = null;
if (!$has_antivirus || !$has_office || !$has_windows) {
    $compliance_due_date = (new DateTime())->modify('+2 months')->format('Y-m-d');
}

$stmt = $conn->prepare("UPDATE computer_inventory SET employee_name = ?, employee_role = ?, employee_phone = ?, employee_email = ?, equipment_type = ?, brand = ?, ip_address = ?, mac_address = ?, has_antivirus_license = ?, has_office_license = ?, has_windows_license = ?, compliance_due_date = ? WHERE id = ?");

$stmt->bind_param("ssssssssiissi", 
    $data['funcionario'], 
    $data['cargo'],
    $data['celular'], 
    $data['correo'], 
    $data['tipoEquipo'], 
    $data['marca'], 
    $data['ip'], 
    $data['mac'],
    $has_antivirus,
    $has_office,
    $has_windows,
    $compliance_due_date,
    $id
);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Registro actualizado con éxito.']);
} else {
  echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro.']);
}

$stmt->close();
$conn->close();
?>