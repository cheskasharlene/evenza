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

if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

// Prevent deleting the current admin user
$currentUserId = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0);
if ($userId == $currentUserId) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit;
}

$query = "DELETE FROM users WHERE userid = ?";
$stmt = mysqli_prepare($conn, $query);

// Clear any output that might have been generated
ob_clean();

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        $errorMsg = mysqli_error($conn);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $errorMsg]);
    }
    mysqli_stmt_close($stmt);
} else {
    $errorMsg = mysqli_error($conn);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $errorMsg]);
}

// End output buffering and send response
ob_end_flush();
exit;
?>

