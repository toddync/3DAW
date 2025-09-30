<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

    if (!isAuthenticated() || !isAdmin()) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Somente administradores.']);
    exit();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $user = getUserById($_GET['id']);
            if ($user) {
                unset($user['password']); // Don't expose hashed password
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
            }
        } else {
            $users = getUsers();
            // Remove passwords from the list
            $usersWithoutPasswords = array_map(function($user) {
                unset($user['password']);
                return $user;
            }, $users);
            echo json_encode(['success' => true, 'users' => $usersWithoutPasswords]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        $role = $input['role'] ?? 'user'; // Default role

        if (empty($username) || empty($password)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Nome de usuário e senha são obrigatórios.']);
            break;
        }

        if (createUser($username, $password, $role)) {
            $newUser = getUserByUsername($username);
            unset($newUser['password']);
            echo json_encode(['success' => true, 'message' => 'Usuário criado com sucesso.', 'user' => $newUser]);
        } else {
            http_response_code(409); // Conflict
            echo json_encode(['success' => false, 'message' => 'Nome de usuário já existe ou falha ao criar usuário.']);
        }
        break;

    case 'PUT':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'ID de usuário é obrigatório.']);
            break;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? null; // Password is optional for update
        $role = $input['role'] ?? '';

        $existingUser = getUserById($id);
        if (!$existingUser) {
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
            break;
        }

        // Use existing values if not provided in the update request
        $username = empty($username) ? $existingUser['username'] : $username;
        $role = empty($role) ? $existingUser['role'] : $role;

        if (updateUser($id, $username, $password, $role)) {
            $updatedUser = getUserById($id);
            unset($updatedUser['password']);
            echo json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso.', 'user' => $updatedUser]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Falha ao atualizar usuário.']);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'ID de usuário é obrigatório.']);
            break;
        }

        if (deleteUser($id)) {
            echo json_encode(['success' => true, 'message' => 'Usuário deletado com sucesso.']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Falha ao deletar usuário.']);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        break;
}
?>