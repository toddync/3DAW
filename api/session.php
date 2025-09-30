<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isAuthenticated()) {
        echo json_encode([
            'logged_in' => true,
            'user' => getLoggedInUser()
        ]);
    } else {
        echo json_encode([
            'logged_in' => false,
            'user' => null
        ]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
?>