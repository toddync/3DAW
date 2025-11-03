<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Allow any authenticated user to GET questions, but require admin for creating/updating/deleting
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isAuthenticated()) {
        http_response_code(403); // Forbidden
        echo json_encode(['success' => false, 'message' => 'Acesso negado. Autenticação necessária.']);
        exit();
    }
} else {
    if (!isAuthenticated() || !isAdmin()) {
        http_response_code(403); // Forbidden
        echo json_encode(['success' => false, 'message' => 'Acesso negado. Somente administradores.']);
        exit();
    }
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $question = getQuestionById($_GET['id']);
            if ($question) {
                echo json_encode(['success' => true, 'question' => $question]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['success' => false, 'message' => 'Questão não encontrada.']);
            }
        } else {
            $questions = getQuestions();
            echo json_encode(['success' => true, 'questions' => $questions]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? '';
        $question_text = $input['question'] ?? '';
        $options = $input['options'] ?? []; // For multiple choice
        $correct_answer = $input['correct_answer'] ?? '';

        if (empty($type) || empty($question_text) || empty($correct_answer)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Tipo, enunciado e resposta correta são obrigatórios.']);
            break;
        }

        if ($type === 'multiple_choice') {
            if (count($options) !== 5) {
                http_response_code(400); // Bad Request
                echo json_encode(['success' => false, 'message' => 'Questões de múltipla escolha exigem exatamente 5 opções.']);
                break;
            }
        }

        if (createQuestion($type, $question_text, $options, $correct_answer)) {
            echo json_encode(['success' => true, 'message' => 'Questão criada com sucesso.']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Falha ao criar questão.']);
        }
        break;

    case 'PUT':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'ID da questão é obrigatório.']);
            break;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? '';
        $question_text = $input['question'] ?? '';
        $options = $input['options'] ?? [];
        $correct_answer = $input['correct_answer'] ?? '';

        if (empty($type) || empty($question_text) || empty($correct_answer)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Tipo, enunciado e resposta correta são obrigatórios.']);
            break;
        }

        if ($type === 'multiple_choice') {
            if (count($options) !== 5) {
                http_response_code(400); // Bad Request
                echo json_encode(['success' => false, 'message' => 'Questões de múltipla escolha exigem exatamente 5 opções.']);
                break;
            }
        }

        if (updateQuestion($id, $type, $question_text, $options, $correct_answer)) {
            echo json_encode(['success' => true, 'message' => 'Questão atualizada com sucesso.']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Falha ao atualizar questão.']);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? null; // Need type to know which file to delete from
        if (!$id || !$type) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'ID da questão e tipo são obrigatórios.']);
            break;
        }

        if (deleteQuestion($id, $type)) {
            echo json_encode(['success' => true, 'message' => 'Questão deletada com sucesso.']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Falha ao deletar questão.']);
        }
        break;

    default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
        break;
}
?>