<?php
require 'db_connection.php';

$report_id = $_GET['id'] ?? 0;

if (!$report_id) {
    echo json_encode(['success' => false, 'message' => 'ID de informe no proporcionado.']);
    exit;
}

$response = ['success' => false];

// Obtener el análisis del informe
$stmt_report = $conn->prepare("SELECT analysis_text FROM personnel_reports WHERE id = ?");
$stmt_report->bind_param("i", $report_id);
<?php
session_start();
// AÑADIMOS EL BLOQUE DE SEGURIDAD
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

require 'db_connection.php';

// ... el resto del código de este archivo no cambia
$report_id = $_GET['id'] ?? 0;

if (!$report_id) {
    echo json_encode(['success' => false, 'message' => 'ID de informe no proporcionado.']);
    exit;
}

$response = ['success' => false];

$stmt_report = $conn->prepare("SELECT analysis_text FROM personnel_reports WHERE id = ?");
$stmt_report->bind_param("i", $report_id);
$stmt_report->execute();
$result_report = $stmt_report->get_result();
if ($report_data = $result_report->fetch_assoc()) {
    $response['analysis'] = $report_data['analysis_text'];
}
$stmt_report->close();


$stmt_entries = $conn->prepare("SELECT personnel_type as tipo, quantity as cantidad, university as universidad, residency_duration_months as duracion, hospital_area as area, service_type as servicio, eps_origin as eps FROM personnel_entries WHERE report_id = ?");
$stmt_entries->bind_param("i", $report_id);
$stmt_entries->execute();
$result_entries = $stmt_entries->get_result();

$entries = [];
while ($row = $result_entries->fetch_assoc()) {
    $entries[] = array_filter($row, function($value) { return $value !== null; });
}
$stmt_entries->close();

$response['data'] = $entries;
$response['success'] = true;

echo json_encode($response);
$conn->close();
?>