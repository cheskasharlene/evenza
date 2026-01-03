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
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$venue = isset($_POST['venue']) ? trim($_POST['venue']) : '';
$venueAddress = isset($_POST['venueAddress']) ? trim($_POST['venueAddress']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$eventDate = isset($_POST['eventDate']) ? trim($_POST['eventDate']) : '';
$eventTime = isset($_POST['eventTime']) ? trim($_POST['eventTime']) : '';
$imagePath = isset($_POST['imagePath']) ? trim($_POST['imagePath']) : '';

if (empty($title) || empty($category)) {
    echo json_encode(['success' => false, 'message' => 'Title and category are required']);
    exit;
}

if ($eventId > 0) {
    // Update existing event - preserve eventDate, eventTime, and venueAddress if empty
    if (empty($eventDate) || empty($eventTime) || empty($venueAddress)) {
        // Fetch current values to preserve them
        $fetchQuery = "SELECT eventDate, eventTime, venueAddress FROM events WHERE eventId = ?";
        $fetchStmt = mysqli_prepare($conn, $fetchQuery);
        if ($fetchStmt) {
            mysqli_stmt_bind_param($fetchStmt, "i", $eventId);
            mysqli_stmt_execute($fetchStmt);
            $fetchResult = mysqli_stmt_get_result($fetchStmt);
            $currentEvent = mysqli_fetch_assoc($fetchResult);
            mysqli_stmt_close($fetchStmt);
            
            if ($currentEvent) {
                if (empty($eventDate)) {
                    $eventDate = $currentEvent['eventDate'] ?? '';
                }
                if (empty($eventTime)) {
                    $eventTime = $currentEvent['eventTime'] ?? '';
                }
                if (empty($venueAddress)) {
                    $venueAddress = $currentEvent['venueAddress'] ?? '';
                }
            }
        }
    }
    
    $query = "UPDATE events SET title = ?, category = ?, venue = ?, venueAddress = ?, description = ?, eventDate = ?, eventTime = ?, imagePath = ? WHERE eventId = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssssi", $title, $category, $venue, $venueAddress, $description, $eventDate, $eventTime, $imagePath, $eventId);
    }
} else {
    // Insert new event
    $query = "INSERT INTO events (title, category, venue, venueAddress, description, eventDate, eventTime, imagePath) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssss", $title, $category, $venue, $venueAddress, $description, $eventDate, $eventTime, $imagePath);
    }
}

// Clear any output that might have been generated
ob_clean();

if ($stmt && mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => $eventId > 0 ? 'Event updated successfully' : 'Event added successfully']);
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

