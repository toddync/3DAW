<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    logoutUser();
    echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso.']);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
?>