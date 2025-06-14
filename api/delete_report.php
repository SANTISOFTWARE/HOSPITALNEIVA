<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? 0;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM personnel_reports WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Informe eliminado.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó ID.']);
}

$conn->close();
?>