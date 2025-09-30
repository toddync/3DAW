<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isAuthenticated() || !isUser()) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Somente usuários autenticados.']);
    exit();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Admins see all answers, users see only their own
        if (isAdmin()) {
            $answers = getAnswers();
            echo json_encode(['success' => true, 'answers' => $answers]);
        } else {
            $user = getLoggedInUser();
            $answers = getAnswers($user['id']);
            echo json_encode(['success' => true, 'answers' => $answers]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $question_id = $input['question_id'] ?? '';
        $submitted_answer = $input['answer'] ?? '';
        $user_id = getLoggedInUser()['id'];

        if (empty($question_id) || $submitted_answer === '') {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'ID da questão e resposta são obrigatórios.']);
            exit();
        }

        $result = submitAnswer($question_id, $user_id, $submitted_answer);

        if ($result['success']) {
            echo json_encode($result);
        } else {
            http_response_code(500); // Internal Server Error or other specific error
            echo json_encode($result);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
        break;
}
?>