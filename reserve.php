<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Please login to make a reservation.';
    header('Location: login.php');
    exit;
}

$success = false;
$error_message = '';
$reservationId = null;

// Process reservation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $eventId = isset($_POST['eventId']) ? intval($_POST['eventId']) : 0;
    $packageId = isset($_POST['packageId']) ? intval($_POST['packageId']) : 0;
    $packageTier = isset($_POST['packageTier']) ? trim($_POST['packageTier']) : '';
    $reservationDate = isset($_POST['reservationDate']) ? $_POST['reservationDate'] : date('Y-m-d');
    $startTime = isset($_POST['eventStartTime']) ? $_POST['eventStartTime'] : null;
    $endTime = isset($_POST['eventEndTime']) ? $_POST['eventEndTime'] : null;
    
    // Validate inputs
    if ($userId <= 0) {
        $error_message = 'Invalid user session. Please login again.';
    } elseif ($eventId <= 0) {
        $error_message = 'Invalid event selected.';
    } elseif ($packageId <= 0) {
        $error_message = 'Please select a valid package.';
    } else {
        // Get package details from database to verify and get price
        $packageQuery = "SELECT packageId, packageName, price FROM packages WHERE packageId = ?";
        $packageStmt = mysqli_prepare($conn, $packageQuery);
        
        if ($packageStmt) {
            mysqli_stmt_bind_param($packageStmt, "i", $packageId);
            mysqli_stmt_execute($packageStmt);
            $packageResult = mysqli_stmt_get_result($packageStmt);
            $package = mysqli_fetch_assoc($packageResult);
            mysqli_stmt_close($packageStmt);
            
            if ($package) {
                $totalAmount = floatval($package['price']);
                $packageName = $package['packageName'];
                
                // Convert time format if needed (from "9:00 AM" to "09:00:00")
                $startTimeFormatted = null;
                $endTimeFormatted = null;
                if ($startTime) {
                    $startTimeFormatted = date('H:i:s', strtotime($startTime));
                }
                if ($endTime) {
                    $endTimeFormatted = date('H:i:s', strtotime($endTime));
                }
                
                // DO NOT save reservation to database yet - only store in session
                // Reservation will be saved only after successful PayPal payment in confirmation.php
                
                // Store reservation data in session for payment processing
                $_SESSION['pending_reservation_data'] = [
                    'userId' => $userId,
                    'eventId' => $eventId,
                    'packageId' => $packageId,
                    'packageName' => $packageName,
                    'packageTier' => $packageTier,
                    'reservationDate' => $reservationDate,
                    'startTime' => $startTimeFormatted,
                    'endTime' => $endTimeFormatted,
                    'totalAmount' => $totalAmount
                ];
                
                // Also store for PayPal callback compatibility
                $_SESSION['pending_reservation_id'] = 0; // Will be set after payment
                $_SESSION['pending_event_id'] = $eventId;
                $_SESSION['pending_package_id'] = $packageId;
                $_SESSION['pending_amount'] = $totalAmount;
                
                // Redirect to payment page - NO database save yet
                header('Location: payment.php?eventId=' . $eventId . '&packageId=' . $packageId . '&packageName=' . urlencode($packageName) . '&packagePrice=' . $totalAmount);
                exit;
            } else {
                $error_message = 'Package not found in database.';
            }
        } else {
            $error_message = 'Database error: ' . mysqli_error($conn);
        }
    }
    
    // If there's an error, store it in session and redirect back
    if (!empty($error_message)) {
        $_SESSION['error_message'] = $error_message;
        header('Location: reservation.php?eventId=' . $eventId);
        exit;
    }
} else {
    // If not POST, redirect to reservation page
    $eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 0;
    header('Location: reservation.php?eventId=' . $eventId);
    exit;
}
?>

