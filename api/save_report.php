<?php
session_start();
require 'db_connection.php';

// VERIFICACIÓN DE SEGURIDAD: Asegurarnos de que el usuario ha iniciado sesión.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Error: Debes iniciar sesión para guardar un informe.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$analysis = $data['analysis'] ?? '';
$personnelData = $data['data'] ?? [];
$userId = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    // 1. Insertar el informe principal
    $stmt = $conn->prepare("INSERT INTO personnel_reports (user_id, analysis_text) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $analysis);
    $stmt->execute();

    // 2. Obtener el ID del informe que acabamos de crear
    $report_id = $conn->insert_id;
    if ($report_id === 0 && !empty($personnelData)) { // Solo es un problema si hay datos que insertar
        throw new Exception("No se pudo obtener el ID del nuevo informe.");
    }
    $stmt->close();

    // 3. Insertar cada una de las entradas de personal, si existen
    if (!empty($personnelData)) {
       $stmt_entries = $conn->prepare("INSERT INTO personnel_entries (report_id, personnel_type, quantity, university, residency_duration_months, hospital_area, service_type, eps_origin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($personnelData as $item) {
    $type = $item['tipo'] ?? null;
    $quantity = $item['cantidad'] ?? 0;
    $university = $item['universidad'] ?? null;
    $duration = $item['duracion'] ?? null;
    $area = $item['area'] ?? null;
    $service = $item['servicio'] ?? null;
    $eps = $item['eps'] ?? null;

    $stmt_entries->bind_param("isisssss", $report_id, $type, $quantity, $university, $duration, $area, $service, $eps);
    $stmt_entries->execute();
}
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Informe guardado correctamente en la base de datos.']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al guardar el informe: ' . $e->getMessage()]);
}

$conn->close();
?>