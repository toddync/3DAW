<?php
class AlunoCRUD {
    private $conn;
    private $table_name = "alunos";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new aluno
    public function create($matricula, $cpf, $nome) {
        // Check if matricula or CPF already exists
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE matricula = ? OR cpf = ?";
        $stmt = $this->conn->prepare($check_query);
        $stmt->bind_param("ss", $matricula, $cpf);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return false; // Matricula or CPF already exists
        }

        // Insert new record
        $query = "INSERT INTO " . $this->table_name . " (matricula, cpf, nome) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $matricula, $cpf, $nome);
        
        return $stmt->execute();
    }

    // Read all alunos - FIXED: Return array instead of mysqli_result
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $result = $this->conn->query($query);
        
        $alunos = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $alunos[] = $row;
            }
        }
        return $alunos;
    }

    // Read single aluno
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update aluno
    public function update($id, $matricula, $cpf, $nome) {
        // Check if matricula or CPF already exists in other records
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE (matricula = ? OR cpf = ?) AND id != ?";
        $stmt = $this->conn->prepare($check_query);
        $stmt->bind_param("ssi", $matricula, $cpf, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return false; // Matricula or CPF exists in another record
        }

        $query = "UPDATE " . $this->table_name . " SET matricula = ?, cpf = ?, nome = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $matricula, $cpf, $nome, $id);
        
        return $stmt->execute();
    }

    // Delete aluno
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alunos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$alunoCRUD = new AlunoCRUD($conn);

// Set content type to JSON for API responses
header('Content-Type: application/json');

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests (read operations)
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'read':
            $alunos = $alunoCRUD->read();
            echo json_encode($alunos);
            break;
            
        case 'readOne':
            $id = $_GET['id'] ?? 0;
            $aluno = $alunoCRUD->readOne($id);
            echo json_encode($aluno);
            break;
            
        default:
            echo json_encode(['error' => 'Ação não reconhecida']);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST requests (create, update, delete)
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $success = $alunoCRUD->create($_POST['matricula'], $_POST['cpf'], $_POST['nome']);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Aluno criado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro: Matrícula ou CPF já existe!']);
            }
            break;
            
        case 'update':
            $success = $alunoCRUD->update($_POST['id'], $_POST['matricula'], $_POST['cpf'], $_POST['nome']);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Aluno atualizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro: Matrícula ou CPF já existe!']);
            }
            break;
            
        case 'delete':
            $success = $alunoCRUD->delete($_POST['id']);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Aluno deletado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao deletar aluno!']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
            break;
    }
}

$conn->close();
?>