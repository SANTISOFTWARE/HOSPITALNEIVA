<?php
require 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

if ($id) {
    $stmt = $conn->prepare("DELETE FROM computer_inventory WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registro eliminado.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó ID.']);
}

$conn->close();
?>