<?php
session_start();
require_once 'connect.php';

// PayPal callback handler - processes payment and redirects to confirmation
// This simulates PayPal IPN/callback processing

// Debug: Log that callback was reached
error_log('PayPal Callback reached');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log('User not logged in, redirecting to login');
    header('Location: login.php');
    exit;
}

// Get payment parameters from PayPal return or POST
$paymentStatus = isset($_GET['payment_status']) ? $_GET['payment_status'] : (isset($_POST['payment_status']) ? $_POST['payment_status'] : '');
$transactionId = isset($_GET['tx']) ? $_GET['tx'] : (isset($_POST['tx']) ? $_POST['tx'] : '');
$payerId = isset($_GET['PayerID']) ? $_GET['PayerID'] : (isset($_POST['PayerID']) ? $_POST['PayerID'] : '');

// Get reservation data from session (stored before payment, NOT from database yet)
$reservationData = isset($_SESSION['pending_reservation_data']) ? $_SESSION['pending_reservation_data'] : null;

// Get data from session or URL (for compatibility)
$reservationId = 0; // Will be created after payment confirmation
$eventId = isset($_SESSION['pending_event_id']) ? $_SESSION['pending_event_id'] : (isset($_GET['eventId']) ? intval($_GET['eventId']) : ($reservationData ? $reservationData['eventId'] : 0));
$packageId = isset($_SESSION['pending_package_id']) ? $_SESSION['pending_package_id'] : (isset($_GET['packageId']) ? intval($_GET['packageId']) : ($reservationData ? $reservationData['packageId'] : 0));
$amount = isset($_SESSION['pending_amount']) ? $_SESSION['pending_amount'] : (isset($_GET['amount']) ? floatval($_GET['amount']) : ($reservationData ? $reservationData['totalAmount'] : 0));

// Debug logging
error_log('PayPal Callback - reservationId: ' . $reservationId . ', eventId: ' . $eventId . ', packageId: ' . $packageId . ', amount: ' . $amount);
error_log('Session data: ' . print_r([
    'pending_reservation_id' => $_SESSION['pending_reservation_id'] ?? 'not set',
    'pending_event_id' => $_SESSION['pending_event_id'] ?? 'not set',
    'pending_package_id' => $_SESSION['pending_package_id'] ?? 'not set',
    'pending_amount' => $_SESSION['pending_amount'] ?? 'not set'
], true));

// Generate transaction ID if not provided (for testing/simulation)
if (empty($transactionId)) {
    $transactionId = 'PAYPAL-' . strtoupper(substr(md5(time() . $_SESSION['user_id'] . $reservationId), 0, 12));
}

// If payment is successful, generate success token and redirect
if ($paymentStatus === 'Completed' || $paymentStatus === 'success' || !empty($payerId)) {
    // Generate secure success token
    $successToken = bin2hex(random_bytes(32));
    
    // Store token in session temporarily (expires in 5 minutes)
    $_SESSION['payment_success_token'] = $successToken;
    $_SESSION['payment_success_time'] = time();
    $_SESSION['payment_transaction_id'] = $transactionId;
    $_SESSION['payment_reservation_id'] = 0; // Will be created in confirmation.php
    $_SESSION['payment_event_id'] = $eventId;
    $_SESSION['payment_package_id'] = $packageId;
    $_SESSION['payment_amount'] = $amount;
    
    // Ensure reservation data is available for confirmation.php
    if ($reservationData) {
        $_SESSION['pending_reservation_data'] = $reservationData;
    }
    
    // Redirect to confirmation with success token
    header('Location: confirmation.php?success=' . urlencode($successToken) . '&tx=' . urlencode($transactionId));
    exit;
} else {
    // Payment failed or cancelled
    $_SESSION['error_message'] = 'Payment was not completed. Please try again.';
    header('Location: payment.php?eventId=' . $eventId . '&reservationId=' . $reservationId);
    exit;
}
?>

