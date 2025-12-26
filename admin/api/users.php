<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
require_once '../../config/database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn = getDBConnection();
    
    if ($method === 'GET') {
        // Get all users
        $result = $conn->query("
            SELECT userId, fullName, email, role, createdAt
            FROM users
            ORDER BY createdAt DESC
        ");
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        echo json_encode(['success' => true, 'users' => $users]);
    }
    elseif ($method === 'POST') {
        // Add new user
        $fullName = trim($_POST['fullName']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        // Validate role
        if (!in_array($role, ['user', 'admin'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid role']);
            exit;
        }
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT userId FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            $stmt->close();
            exit;
        }
        $stmt->close();
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (fullName, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullName, $email, $hashedPassword, $role);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add user']);
        }
        $stmt->close();
    }
    elseif ($method === 'DELETE') {
        // Delete user
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = intval($data['userId']);
        
        // Prevent deleting own account
        if ($userId == $_SESSION['admin_id']) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM users WHERE userId = ?");
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
        $stmt->close();
    }
    
    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>

