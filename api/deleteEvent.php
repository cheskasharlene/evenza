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

$eventId = isset($_POST['eventId']) ? intval($_POST['eventId']) : 0;

if ($eventId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
    exit;
}

$query = "DELETE FROM events WHERE eventId = ?";
$stmt = mysqli_prepare($conn, $query);

// Clear any output that might have been generated
ob_clean();

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $eventId);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
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

