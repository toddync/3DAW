<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (authenticateUser($username, $password)) {
        echo json_encode([
            'success' => true,
            'message' => 'Login realizado com sucesso.',
            'user' => getLoggedInUser()
        ]);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Usuário ou senha inválidos.']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
?>