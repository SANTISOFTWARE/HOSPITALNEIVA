<?php
// Iniciamos la sesión para poder guardar información del usuario si el login es exitoso
session_start();

// Incluimos la conexión a la base de datos
require 'db_connection.php';

// Obtenemos los datos que el JavaScript nos envía en formato JSON
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// Preparamos una consulta para buscar el usuario y evitar inyección SQL
$stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$response = ['success' => false, 'message' => 'Usuario o contraseña incorrectos.'];

// Verificamos si se encontró un usuario
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // ¡MUY IMPORTANTE! Usamos password_verify para comparar la contraseña enviada
    // con el hash seguro que guardamos en la base de datos.
    if (password_verify($password, $user['password_hash'])) {
        
        // Si la contraseña es correcta, la respuesta es exitosa
        $response['success'] = true;
        unset($response['message']); // No necesitamos mensaje de error

        // Guardamos información en la sesión para futuro uso
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }
}

$stmt->close();
$conn->close();

// Devolvemos la respuesta (exitosa o no) en formato JSON
echo json_encode($response);
?>