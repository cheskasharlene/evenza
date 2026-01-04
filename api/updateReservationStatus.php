<?php
// Suppress error output to prevent breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header first
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin authentication without redirect (same logic as adminAuth.php)
$isAdmin = isset($_SESSION['admin_id']) || (isset($_SESSION['user_id']) && isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin');
if (!$isAdmin) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Include database connection
require_once '../connect.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get parameters
$reservationId = isset($_POST['reservationId']) ? intval($_POST['reservationId']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

// Validate inputs
if ($reservationId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid reservation ID']);
    exit;
}

if (!in_array(strtolower($status), ['pending', 'confirmed', 'cancelled', 'completed'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Normalize status to lowercase
$status = strtolower($status);

// Check if connection is valid
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Update reservation status using MySQLi prepared statement
$query = "UPDATE reservations SET status = ? WHERE reservationId = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "si", $status, $reservationId);
    
    if (mysqli_stmt_execute($stmt)) {
        $affectedRows = mysqli_stmt_affected_rows($stmt);
        if ($affectedRows > 0) {
            echo json_encode(['success' => true, 'message' => 'Reservation status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reservation not found or status unchanged']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating status']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Error preparing query']);
}

mysqli_close($conn);
?>
