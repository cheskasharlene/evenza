<?php
// Suppress any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
require_once '../connect.php';

// Set JSON header early and prevent any output
header('Content-Type: application/json');
ob_start(); // Start output buffering to catch any accidental output

// Check admin authentication manually to avoid redirect
if (!isset($_SESSION['admin_id']) && !(isset($_SESSION['user_id']) && isset($_SESSION['role']) && (strtolower($_SESSION['role']) === 'admin'))) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
$fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
// Handle isAdmin checkbox - FormData sends boolean as string 'true' or 'false', or may not be set if unchecked
$isAdminValue = isset($_POST['isAdmin']) ? $_POST['isAdmin'] : 'false';
$role = ($isAdminValue === 'true' || $isAdminValue === true) ? 'admin' : 'user';

if (empty($fullName) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Full name and email are required']);
    exit;
}

if ($userId > 0) {
    // Update existing user
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET fullName = ?, email = ?, phone = ?, password = ?, role = ? WHERE userid = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssi", $fullName, $email, $phone, $hashedPassword, $role, $userId);
        }
    } else {
        $query = "UPDATE users SET fullName = ?, email = ?, phone = ?, role = ? WHERE userid = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssi", $fullName, $email, $phone, $role, $userId);
        }
    }
} else {
    // Insert new user
    if (empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Password is required for new users']);
        exit;
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Split fullName into firstName and lastName
    $nameParts = explode(' ', $fullName, 2);
    $firstName = $nameParts[0];
    $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
    
    $query = "INSERT INTO users (firstName, lastName, fullName, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssss", $firstName, $lastName, $fullName, $email, $phone, $hashedPassword, $role);
    }
}

// Clear any output that might have been generated
ob_clean();

if ($stmt && mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => $userId > 0 ? 'User updated successfully' : 'User added successfully']);
} else {
    $errorMsg = mysqli_error($conn);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $errorMsg]);
}

if ($stmt) {
    mysqli_stmt_close($stmt);
}

// End output buffering and send response
ob_end_flush();
exit;
?>

