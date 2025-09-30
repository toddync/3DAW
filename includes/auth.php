<?php
session_start();

define('USERS_FILE', __DIR__ . '/../data/users.txt');
define('QUESTIONS_MC_FILE', __DIR__ . '/../data/questions_multiple_choice.txt');
define('QUESTIONS_TEXT_FILE', __DIR__ . '/../data/questions_text.txt');
define('ANSWERS_FILE', __DIR__ . '/../data/answers.txt');

function generateUniqueId($file) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (empty($lines)) {
        return 1;
    }
    $ids = [];
    foreach ($lines as $line) {
        $data = str_getcsv($line);
        $ids[] = (int)$data[0];
    }
    return max($ids) + 1;
}

function getUsers() {
    $users = [];
    if (!file_exists(USERS_FILE)) {
        return $users;
    }
    $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = str_getcsv($line);
        if (count($data) === 4) {
            $users[] = [
                'id' => $data[0],
                'username' => $data[1],
                'password' => $data[2], // Hashed password
                'role' => $data[3]
            ];
        }
    }
    return $users;
}

function getUserByUsername($username) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

function getUserById($id) {
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['id'] === $id) {
            return $user;
        }
    }
    return null;
}

function createUser($username, $password, $role) {
    if (getUserByUsername($username)) {
        return false; // User already exists
    }

    $id = generateUniqueId(USERS_FILE);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $newUser = [$id, $username, $hashedPassword, $role];

    $file = fopen(USERS_FILE, 'a');
    if ($file) {
        fputcsv($file, $newUser);
        fclose($file);
        return true;
    }
    return false;
}

function updateUser($id, $username, $password = null, $role) {
    $users = getUsers();
    $updated = false;
    $fileContent = [];

    foreach ($users as $user) {
        if ($user['id'] === $id) {
            $user['username'] = $username;
            if ($password) {
                $user['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $user['role'] = $role;
            $updated = true;
        }
        $fileContent[] = [$user['id'], $user['username'], $user['password'], $user['role']];
    }

    if ($updated) {
        $file = fopen(USERS_FILE, 'w');
        if ($file) {
            foreach ($fileContent as $line) {
                fputcsv($file, $line);
            }
            fclose($file);
            return true;
        }
    }
    return false;
}

function deleteUser($id) {
    $users = getUsers();
    $fileContent = [];
    $deleted = false;

    foreach ($users as $user) {
        if ($user['id'] !== $id) {
            $fileContent[] = [$user['id'], $user['username'], $user['password'], $user['role']];
        } else {
            $deleted = true;
        }
    }

    if ($deleted) {
        $file = fopen(USERS_FILE, 'w');
        if ($file) {
            foreach ($fileContent as $line) {
                fputcsv($file, $line);
            }
            fclose($file);
            return true;
        }
    }
    return false;
}

function authenticateUser($username, $password) {
    $user = getUserByUsername($username);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isAuthenticated() && $_SESSION['role'] === 'admin';
}

function isUser() {
    return isAuthenticated() && $_SESSION['role'] === 'user';
}

function getLoggedInUser() {
    if (isAuthenticated()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

// Generic functions for questions and answers (to be expanded)
function getQuestions($type = null) {
    $questions = [];
    $files = [];
    if ($type === 'multiple_choice' || $type === null) {
        $files[] = QUESTIONS_MC_FILE;
    }
    if ($type === 'text' || $type === null) {
        $files[] = QUESTIONS_TEXT_FILE;
    }

    foreach ($files as $file_path) {
        if (file_exists($file_path)) {
            $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $data = str_getcsv($line);
                if ($file_path === QUESTIONS_MC_FILE && count($data) === 8) { // id, question, 5 options, correct_answer
                    $questions[] = [
                        'id' => $data[0],
                        'type' => 'multiple_choice',
                        'question' => $data[1],
                        'options' => array_slice($data, 2, 5), // Exactly 5 options
                        'correct_answer' => $data[7] // The 8th element (index 7)
                    ];
                } elseif ($file_path === QUESTIONS_TEXT_FILE && count($data) === 3) {
                    $questions[] = [
                        'id' => $data[0],
                        'type' => 'text',
                        'question' => $data[1],
                        'correct_answer' => $data[2]
                    ];
                }
            }
        }
    }
    return $questions;
}

function getQuestionById($id) {
    $questions = getQuestions();
    foreach ($questions as $question) {
        if ($question['id'] === $id) {
            return $question;
        }
    }
    return null;
}

function createQuestion($type, $question_text, $options = [], $correct_answer = null) {
    $file_path = '';
    $newQuestion = [];

    if ($type === 'multiple_choice') {
        $file_path = QUESTIONS_MC_FILE;
        $id = generateUniqueId($file_path);
        // Ensure exactly 5 options
        $paddedOptions = array_pad(array_slice($options, 0, 5), 5, '');
        $newQuestion = array_merge([$id, $question_text], $paddedOptions, [$correct_answer]);
    } elseif ($type === 'text') {
        $file_path = QUESTIONS_TEXT_FILE;
        $id = generateUniqueId($file_path);
        $newQuestion = [$id, $question_text, $correct_answer];
    } else {
        return false;
    }

    $file = fopen($file_path, 'a');
    if ($file) {
        fputcsv($file, $newQuestion);
        fclose($file);
        return true;
    }
    return false;
}

function updateQuestion($id, $type, $question_text, $options = [], $correct_answer = null) {
    $questions = getQuestions($type);
    $file_path = ($type === 'multiple_choice') ? QUESTIONS_MC_FILE : QUESTIONS_TEXT_FILE;
    $updated = false;
    $fileContent = [];

    foreach ($questions as $question) {
        if ($question['id'] === $id) {
            $question['question'] = $question_text;
            if ($type === 'multiple_choice') {
                $question['options'] = $options;
            }
            $question['correct_answer'] = $correct_answer;
            $updated = true;
        }
        if ($type === 'multiple_choice') {
            // Ensure exactly 5 options when writing back
            $paddedOptions = array_pad(array_slice($question['options'], 0, 5), 5, '');
            $fileContent[] = array_merge([$question['id'], $question['question']], $paddedOptions, [$question['correct_answer']]);
        } elseif ($type === 'text') {
            $fileContent[] = [$question['id'], $question['question'], $question['correct_answer']];
        }
    }

    if ($updated) {
        $file = fopen($file_path, 'w');
        if ($file) {
            foreach ($fileContent as $line) {
                fputcsv($file, $line);
            }
            fclose($file);
            return true;
        }
    }
    return false;
}

function deleteQuestion($id, $type) {
    $questions = getQuestions($type);
    $file_path = ($type === 'multiple_choice') ? QUESTIONS_MC_FILE : QUESTIONS_TEXT_FILE;
    $fileContent = [];
    $deleted = false;

    foreach ($questions as $question) {
        if ($question['id'] !== $id) {
            if ($type === 'multiple_choice') {
                // Ensure exactly 5 options when writing back
                $paddedOptions = array_pad(array_slice($question['options'], 0, 5), 5, '');
                $fileContent[] = array_merge([$question['id'], $question['question']], $paddedOptions, [$question['correct_answer']]);
            } elseif ($type === 'text') {
                $fileContent[] = [$question['id'], $question['question'], $question['correct_answer']];
            }
        } else {
            $deleted = true;
        }
    }

    if ($deleted) {
        $file = fopen($file_path, 'w');
        if ($file) {
            foreach ($fileContent as $line) {
                fputcsv($file, $line);
            }
            fclose($file);
            return true;
        }
    }
    return false;
}

function submitAnswer($question_id, $user_id, $submitted_answer) {
    $question = getQuestionById($question_id);
    if (!$question) {
        return ['success' => false, 'message' => 'Question not found.'];
    }

    $is_correct = (string)$submitted_answer === (string)$question['correct_answer'];
    $id = generateUniqueId(ANSWERS_FILE);
    $timestamp = date('c'); // ISO 8601 format
    $newAnswer = [$id, $question_id, $user_id, $submitted_answer, $timestamp, $is_correct ? 'true' : 'false'];

    $file = fopen(ANSWERS_FILE, 'a');
    if ($file) {
        fputcsv($file, $newAnswer);
        fclose($file);
        return ['success' => true, 'message' => 'Answer submitted.', 'is_correct' => $is_correct];
    }
    return ['success' => false, 'message' => 'Falha ao enviar resposta.'];
}

function getAnswers($user_id = null) {
    $answers = [];
    if (!file_exists(ANSWERS_FILE)) return $answers;
    $lines = file(ANSWERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = str_getcsv($line);
        // id, question_id, user_id, submitted_answer, timestamp, is_correct
        if (count($data) >= 6) {
            $qid = $data[1];
            $uid = $data[2];
            if ($user_id !== null && (string)$uid !== (string)$user_id) continue;
            $question = getQuestionById($qid);
            $user = getUserById($uid);
            $answers[] = [
                'id' => $data[0],
                'question_id' => $qid,
                'question' => $question ? $question['question'] : null,
                'question_type' => $question ? $question['type'] : null,
                'user_id' => $uid,
                'username' => $user ? $user['username'] : null,
                'answer' => $data[3],
                'timestamp' => $data[4],
                'is_correct' => ($data[5] === 'true')
            ];
        }
    }
    return $answers;
}

?>