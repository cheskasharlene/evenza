<?php
session_start();
require_once '../connect.php';
require_once '../adminAuth.php';

header('Content-Type: application/json');

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

if (!in_array(strtolower($status), ['pending', 'confirmed', 'cancelled'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Normalize status to lowercase
$status = strtolower($status);

// Update reservation status using MySQLi prepared statement
$query = "UPDATE reservations SET status = ? WHERE reservationId = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "si", $status, $reservationId);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Reservation status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating status: ' . mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Error preparing query: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>

