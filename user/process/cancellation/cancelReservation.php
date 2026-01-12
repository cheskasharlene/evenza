<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to cancel a reservation']);
    exit;
}

require_once '../../core/connect.php';
require_once '../../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$reservationId = isset($_POST['reservationId']) ? intval($_POST['reservationId']) : 0;
$userId = $_SESSION['user_id'];

if ($reservationId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid reservation ID']);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Verify that the reservation belongs to the logged-in user
$verifyQuery = "SELECT reservationId, status, userCancelled FROM reservations WHERE reservationId = ? AND userId = ?";
$verifyStmt = mysqli_prepare($conn, $verifyQuery);

if (!$verifyStmt) {
    echo json_encode(['success' => false, 'message' => 'Error verifying reservation']);
    exit;
}

mysqli_stmt_bind_param($verifyStmt, "ii", $reservationId, $userId);
mysqli_stmt_execute($verifyStmt);
$verifyResult = mysqli_stmt_get_result($verifyStmt);
$reservation = mysqli_fetch_assoc($verifyResult);
mysqli_stmt_close($verifyStmt);

if (!$reservation) {
    echo json_encode(['success' => false, 'message' => 'Reservation not found or you do not have permission to cancel it']);
    exit;
}

// Check if already cancelled by user
if ($reservation['userCancelled']) {
    echo json_encode(['success' => false, 'message' => 'This reservation has already been cancelled']);
    exit;
}

// Update reservation: set status to cancelled and mark as user-cancelled
$updateQuery = "UPDATE reservations SET status = 'cancelled', userCancelled = 1 WHERE reservationId = ? AND userId = ?";
$updateStmt = mysqli_prepare($conn, $updateQuery);

if (!$updateStmt) {
    echo json_encode(['success' => false, 'message' => 'Error preparing update query']);
    exit;
}

mysqli_stmt_bind_param($updateStmt, "ii", $reservationId, $userId);

if (mysqli_stmt_execute($updateStmt)) {
    $affectedRows = mysqli_stmt_affected_rows($updateStmt);
    if ($affectedRows > 0) {
        // Send SMS notification about cancellation (user-initiated)
        // Pass 'user' as cancellation type - the function will detect userCancelled flag
        sendReservationStatusSMS($conn, $reservationId, 'cancelled', 'user');
        
        echo json_encode(['success' => true, 'message' => 'Reservation cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel reservation']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error cancelling reservation']);
}

mysqli_stmt_close($updateStmt);
mysqli_close($conn);
?>
