<?php
// Start output buffering FIRST to catch any output
ob_start();

// Suppress any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header early
header('Content-Type: application/json');

session_start();

// Try to include connect.php and catch any die() output
$bufferBeforeConnect = ob_get_contents();
ob_clean();

// Include connect.php - if it dies, we'll catch the output
require_once '../connect.php';

// Check if connect.php output anything (from die())
$bufferAfterConnect = ob_get_contents();
if (!empty(trim($bufferAfterConnect))) {
    // connect.php called die() - clear it and return JSON error
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please check your database configuration.']);
    exit;
}

// Verify connection exists
if (!isset($conn) || !$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

// Check for connection errors (this should not happen if connect.php worked, but just in case)
if (mysqli_connect_errno()) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection error: ' . mysqli_connect_error()]);
    exit;
}

// Check admin authentication manually to avoid redirect
if (!isset($_SESSION['admin_id']) && !(isset($_SESSION['user_id']) && isset($_SESSION['role']) && (strtolower($_SESSION['role']) === 'admin'))) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$eventId = isset($_POST['eventId']) ? intval($_POST['eventId']) : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$venue = isset($_POST['venue']) ? trim($_POST['venue']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$imagePath = isset($_POST['imagePath']) ? trim($_POST['imagePath']) : '';

// Validate required fields
if (empty($title)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Title is required']);
    exit;
}

// Ensure category is not empty - if it is, set a default
if (empty($category)) {
    $category = 'Uncategorized';
}

// Log for debugging
error_log("Updating event ID: $eventId, Category: $category");

try {
    if ($eventId > 0) {
        // Verify event exists before updating
        $checkQuery = "SELECT eventId FROM events WHERE eventId = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, "i", $eventId);
            mysqli_stmt_execute($checkStmt);
            $checkResult = mysqli_stmt_get_result($checkStmt);
            $eventExists = mysqli_fetch_assoc($checkResult);
            mysqli_stmt_close($checkStmt);
            
            if (!$eventExists) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Event not found']);
                exit;
            }
        }
        
        // Update existing event
        $query = "UPDATE events SET title = ?, category = ?, venue = ?, description = ?, imagePath = ? WHERE eventId = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssi", $title, $category, $venue, $description, $imagePath, $eventId);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to prepare update statement: ' . mysqli_error($conn)]);
            exit;
        }
    } else {
        // Insert new event
        $query = "INSERT INTO events (title, category, venue, description, imagePath) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $title, $category, $venue, $description, $imagePath);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to prepare insert statement: ' . mysqli_error($conn)]);
            exit;
        }
    }
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}

// Check if there's any unwanted output in the buffer
$bufferContent = ob_get_contents();
if (!empty(trim($bufferContent))) {
    // There's unwanted output - log it and clear it
    error_log('Unexpected output in buffer before JSON: ' . substr($bufferContent, 0, 200));
    ob_clean();
}

// Prepare response
$response = null;
if ($stmt) {
    if (mysqli_stmt_execute($stmt)) {
        // For UPDATE queries, affected_rows can be 0 if data is unchanged, but query still succeeds
        // For INSERT queries, affected_rows should be 1
        $affectedRows = mysqli_stmt_affected_rows($stmt);
        
        if ($eventId > 0) {
            // Verify the update by fetching the updated record to confirm category was saved
            $verifyQuery = "SELECT category FROM events WHERE eventId = ?";
            $verifyStmt = mysqli_prepare($conn, $verifyQuery);
            $verifiedCategory = $category;
            if ($verifyStmt) {
                mysqli_stmt_bind_param($verifyStmt, "i", $eventId);
                mysqli_stmt_execute($verifyStmt);
                $verifyResult = mysqli_stmt_get_result($verifyStmt);
                $updatedEvent = mysqli_fetch_assoc($verifyResult);
                mysqli_stmt_close($verifyStmt);
                if ($updatedEvent && isset($updatedEvent['category'])) {
                    $verifiedCategory = trim($updatedEvent['category']);
                    error_log("Verified category for event $eventId: $verifiedCategory");
                }
            }
            
            // For updates, success if execute succeeded (even if 0 rows affected means data was already the same)
            $response = json_encode([
                'success' => true, 
                'message' => 'Event updated successfully',
                'affectedRows' => $affectedRows,
                'category' => $verifiedCategory, // Return the verified category from database
                'eventId' => $eventId
            ]);
        } else {
            // For inserts, we need at least 1 affected row
            if ($affectedRows > 0) {
                $response = json_encode([
                    'success' => true, 
                    'message' => 'Event added successfully',
                    'affectedRows' => $affectedRows
                ]);
            } else {
                $response = json_encode(['success' => false, 'message' => 'Failed to insert event. No rows were added.']);
            }
        }
    } else {
        $errorMsg = mysqli_error($conn);
        $response = json_encode(['success' => false, 'message' => 'Database error: ' . $errorMsg]);
    }
    mysqli_stmt_close($stmt);
} else {
    $errorMsg = mysqli_error($conn);
    $response = json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $errorMsg]);
}

// Ensure we have a valid response
if ($response === null || $response === false) {
    $response = json_encode(['success' => false, 'message' => 'Unknown error occurred']);
}

// Final check - ensure response is valid JSON
$testParse = json_decode($response);
if ($testParse === null && json_last_error() !== JSON_ERROR_NONE) {
    $response = json_encode(['success' => false, 'message' => 'Error generating response']);
}

// End output buffering completely and output only JSON
ob_end_clean();

// Ensure headers are sent
if (!headers_sent()) {
    header('Content-Type: application/json', true);
}

// Output the response
echo $response;

// Flush output to ensure it's sent
if (ob_get_level() > 0) {
    ob_end_flush();
}

exit;
