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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    ob_end_flush();
    exit;
}

$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 0;

if ($eventId <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
    ob_end_flush();
    exit;
}

$query = "SELECT * FROM events WHERE eventId = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $eventId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Clear any output that might have been generated
    ob_clean();
    
    if ($event) {
        echo json_encode(['success' => true, 'data' => $event]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Event not found']);
    }
} else {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

// End output buffering and send response
ob_end_flush();
exit;
