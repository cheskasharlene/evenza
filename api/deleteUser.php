<?php
// Start output buffering FIRST to catch any output
ob_start();

// Suppress any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header early
header('Content-Type: application/json');

session_start();
require_once '../connect.php';

// Check admin authentication manually to avoid redirect
if (!isset($_SESSION['admin_id']) && !(isset($_SESSION['user_id']) && isset($_SESSION['role']) && (strtolower($_SESSION['role']) === 'admin'))) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    ob_end_flush();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    ob_end_flush();
    exit;
}

$userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;

if ($userId <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    ob_end_flush();
    exit;
}

// Prevent deleting the current admin user
$currentUserId = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0);
if ($userId == $currentUserId) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    ob_end_flush();
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
