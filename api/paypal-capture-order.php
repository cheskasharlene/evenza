<?php
/**
 * PayPal Capture Order API
 * This endpoint captures (completes) the PayPal payment after user approval
 */

session_start();
header('Content-Type: application/json');

require_once '../config/paypal.php';
require_once '../connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$orderId = isset($input['orderId']) ? trim($input['orderId']) : '';

if (empty($orderId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

// Verify order ID matches session
if (!isset($_SESSION['paypal_order_id']) || $_SESSION['paypal_order_id'] !== $orderId) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID mismatch']);
    exit;
}

// Get PayPal access token
$accessToken = getPayPalAccessToken();
if (!$accessToken) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to authenticate with PayPal']);
    exit;
}

// Capture the order
$ch = curl_init(getPayPalBaseUrl() . '/v2/checkout/orders/' . $orderId . '/capture');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken,
    'PayPal-Request-Id: ' . uniqid('evenza-capture-', true)
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$captureResult = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300 && isset($captureResult['status']) && $captureResult['status'] === 'COMPLETED') {
    // Extract transaction details
    $transactionId = '';
    $payerId = $captureResult['payer']['payer_id'] ?? '';
    $payerEmail = $captureResult['payer']['email_address'] ?? '';
    
    // Get capture ID from purchase units
    if (isset($captureResult['purchase_units'][0]['payments']['captures'][0])) {
        $capture = $captureResult['purchase_units'][0]['payments']['captures'][0];
        $transactionId = $capture['id'];
    }
    
    // Get stored data from session
    $eventId = $_SESSION['paypal_order_event_id'] ?? 0;
    $packageId = $_SESSION['paypal_order_package_id'] ?? 0;
    $amount = $_SESSION['paypal_order_amount'] ?? 0;
    
    // Generate success token for confirmation page
    $successToken = bin2hex(random_bytes(32));
    
    // Store payment data in session
    $_SESSION['payment_success_token'] = $successToken;
    $_SESSION['payment_success_time'] = time();
    $_SESSION['payment_transaction_id'] = $transactionId;
    $_SESSION['payment_order_id'] = $orderId;
    $_SESSION['payment_payer_id'] = $payerId;
    $_SESSION['payment_payer_email'] = $payerEmail;
    $_SESSION['payment_reservation_id'] = 0; // Will be created in confirmation.php
    $_SESSION['payment_event_id'] = $eventId;
    $_SESSION['payment_package_id'] = $packageId;
    $_SESSION['payment_amount'] = $amount;
    
    // Clear PayPal order session data
    unset($_SESSION['paypal_order_id']);
    unset($_SESSION['paypal_order_amount']);
    unset($_SESSION['paypal_order_event_id']);
    unset($_SESSION['paypal_order_package_id']);
    
    // Return success with redirect URL
    echo json_encode([
        'status' => 'COMPLETED',
        'transactionId' => $transactionId,
        'redirectUrl' => 'confirmation.php?success=' . urlencode($successToken) . '&tx=' . urlencode($transactionId)
    ]);
} else {
    error_log('PayPal Capture Error: ' . $response);
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to capture PayPal payment',
        'status' => $captureResult['status'] ?? 'UNKNOWN',
        'details' => $captureResult['details'] ?? null
    ]);
}

/**
 * Get PayPal Access Token
 */
function getPayPalAccessToken() {
    $clientId = getPayPalClientId();
    $secret = getPayPalSecret();
    
    $ch = curl_init(getPayPalBaseUrl() . '/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $secret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }
    
    error_log('PayPal Auth Error: ' . $response);
    return null;
}
?>

