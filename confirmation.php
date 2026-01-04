<?php
session_start();
require_once 'connect.php';

// RESTRICTED ACCESS: Only allow access if success token is present
$successToken = isset($_GET['success']) ? trim($_GET['success']) : '';
$transactionId = isset($_GET['tx']) ? trim($_GET['tx']) : '';

// Verify success token from session
$isAuthorized = false;
if (!empty($successToken) && isset($_SESSION['payment_success_token'])) {
    if ($successToken === $_SESSION['payment_success_token']) {
        // Check if token is not expired (5 minutes)
        if (isset($_SESSION['payment_success_time']) && (time() - $_SESSION['payment_success_time']) < 300) {
            $isAuthorized = true;
        }
    }
}

// If not authorized, redirect to home page with message
if (!$isAuthorized) {
    $_SESSION['error_message'] = 'Invalid or expired payment confirmation link. Please complete your payment to view confirmation.';
    header('Location: index.php');
    exit;
}

// Get payment data from session
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Get reservation data from session (stored before payment)
$reservationData = isset($_SESSION['pending_reservation_data']) ? $_SESSION['pending_reservation_data'] : null;

// Fallback to individual session variables for compatibility
$reservationId = isset($_SESSION['payment_reservation_id']) ? intval($_SESSION['payment_reservation_id']) : 0;
$eventId = isset($_SESSION['payment_event_id']) ? intval($_SESSION['payment_event_id']) : ($reservationData ? $reservationData['eventId'] : 0);
$packageId = isset($_SESSION['payment_package_id']) ? intval($_SESSION['payment_package_id']) : ($reservationData ? $reservationData['packageId'] : 0);
$amount = isset($_SESSION['payment_amount']) ? floatval($_SESSION['payment_amount']) : ($reservationData ? $reservationData['totalAmount'] : 0);

// If transactionId is in URL, use it; otherwise use session
if (empty($transactionId) && isset($_SESSION['payment_transaction_id'])) {
    $transactionId = $_SESSION['payment_transaction_id'];
}

// Initialize variables
$errorOccurred = false;
$errorMessage = '';
$transactionReference = '';
$reservationSaved = false;
$paymentSaved = false;

// Fetch event and package details
$event = null;
$package = null;

if ($eventId > 0) {
    $eventQuery = "SELECT eventId, title, venue, description FROM events WHERE eventId = ?";
    $eventStmt = mysqli_prepare($conn, $eventQuery);
    if ($eventStmt) {
        mysqli_stmt_bind_param($eventStmt, "i", $eventId);
        mysqli_stmt_execute($eventStmt);
        $eventResult = mysqli_stmt_get_result($eventStmt);
        $event = mysqli_fetch_assoc($eventResult);
        mysqli_stmt_close($eventStmt);
    }
}

if ($packageId > 0) {
    $packageQuery = "SELECT packageId, packageName, price FROM packages WHERE packageId = ?";
    $packageStmt = mysqli_prepare($conn, $packageQuery);
    if ($packageStmt) {
        mysqli_stmt_bind_param($packageStmt, "i", $packageId);
        mysqli_stmt_execute($packageStmt);
        $packageResult = mysqli_stmt_get_result($packageStmt);
        $package = mysqli_fetch_assoc($packageResult);
        mysqli_stmt_close($packageStmt);
    }
}

// Extract package tier from package name
$packageTier = 'Unknown';
if ($package && !empty($package['packageName'])) {
    $packageTier = str_replace(' Package', '', $package['packageName']);
    $packageName = $package['packageName'];
} else {
    $packageName = 'Package';
}

// Save reservation to database ONLY after successful payment
// This is the first time the reservation is saved - it was NOT saved before payment
if ($reservationId <= 0 && $userId > 0 && $eventId > 0 && $packageId > 0 && $reservationData) {
    // Use data from session that was stored before payment
    $reservationDate = $reservationData['reservationDate'] ?? date('Y-m-d');
    $startTime = $reservationData['startTime'] ?? null;
    $endTime = $reservationData['endTime'] ?? null;
    $totalAmount = $reservationData['totalAmount'] ?? $amount;
    
    // Insert reservation using MySQLi prepared statement
    // Status is 'confirmed' because payment was successful
    $reservationQuery = "INSERT INTO reservations (userId, eventId, packageId, reservationDate, startTime, endTime, totalAmount, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')";
    
    $reservationStmt = mysqli_prepare($conn, $reservationQuery);
    
    if ($reservationStmt) {
        mysqli_stmt_bind_param($reservationStmt, "iissssd", $userId, $eventId, $packageId, $reservationDate, $startTime, $endTime, $totalAmount);
        
        if (mysqli_stmt_execute($reservationStmt)) {
            $reservationId = mysqli_insert_id($conn);
            $reservationSaved = true;
        } else {
            $errorOccurred = true;
            $errorMessage = 'Failed to save reservation details.';
            $transactionReference = $transactionId;
        }
        
        mysqli_stmt_close($reservationStmt);
    } else {
        $errorOccurred = true;
        $errorMessage = 'Database error while saving reservation.';
        $transactionReference = $transactionId;
    }
} elseif ($reservationId > 0) {
    // If reservation ID exists (shouldn't happen in new flow, but handle it)
    // Update status to confirmed
    $updateQuery = "UPDATE reservations SET status = 'confirmed' WHERE reservationId = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    if ($updateStmt) {
        mysqli_stmt_bind_param($updateStmt, "i", $reservationId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    }
    $reservationSaved = true;
} else {
    // No reservation data available
    $errorOccurred = true;
    $errorMessage = 'Reservation data not found. Please contact support.';
    $transactionReference = $transactionId;
}

// Save payment details if reservation was saved successfully
if ($reservationSaved && !$errorOccurred && $reservationId > 0) {
    // Check if payment already exists for this transaction
    $checkPaymentQuery = "SELECT paymentId FROM payments WHERE transactionId = ?";
    $checkStmt = mysqli_prepare($conn, $checkPaymentQuery);
    $paymentExists = false;
    
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "s", $transactionId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        if (mysqli_num_rows($checkResult) > 0) {
            $paymentExists = true;
        }
        mysqli_stmt_close($checkStmt);
    }
    
    // Insert payment if it doesn't exist
    if (!$paymentExists) {
        $paymentQuery = "INSERT INTO payments (reservationId, userId, transactionId, amount, packageName, paymentStatus) 
                         VALUES (?, ?, ?, ?, ?, 'completed')";
        
        $paymentStmt = mysqli_prepare($conn, $paymentQuery);
        
        if ($paymentStmt) {
            mysqli_stmt_bind_param($paymentStmt, "iisds", $reservationId, $userId, $transactionId, $amount, $packageName);
            
            if (mysqli_stmt_execute($paymentStmt)) {
                $paymentSaved = true;
            } else {
                $errorOccurred = true;
                $errorMessage = 'Failed to save payment details.';
                $transactionReference = $transactionId;
            }
            
            mysqli_stmt_close($paymentStmt);
        } else {
            $errorOccurred = true;
            $errorMessage = 'Database error while saving payment.';
            $transactionReference = $transactionId;
        }
    } else {
        $paymentSaved = true; // Payment already exists
    }
}

// Clear session data after processing
unset($_SESSION['payment_success_token']);
unset($_SESSION['payment_success_time']);
unset($_SESSION['payment_transaction_id']);
unset($_SESSION['payment_reservation_id']);
unset($_SESSION['payment_event_id']);
unset($_SESSION['payment_package_id']);
unset($_SESSION['payment_amount']);
unset($_SESSION['pending_reservation_id']);
unset($_SESSION['pending_event_id']);
unset($_SESSION['pending_package_id']);
unset($_SESSION['pending_amount']);
unset($_SESSION['pending_reservation_data']); // Clear reservation data after saving

// Fallback data if event/package not found
if (!$event) {
    $event = [
        'title' => 'Event Not Found',
        'venue' => 'N/A'
    ];
}

if (!$package) {
    $package = [
        'packageName' => $packageName,
        'price' => $amount
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - EVENZA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .confirmation-page-section {
            background: linear-gradient(135deg, #F9F7F2 0%, #FFFFFF 100%);
            min-height: 100vh;
        }
        .confirmation-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(74, 93, 74, 0.1);
        }
        .thank-you-message {
            padding: 3rem 0 2rem;
        }
        .success-icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, #4A5D4A 0%, #6B8E6B 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(74, 93, 74, 0.3);
        }
        .success-icon-wrapper i {
            font-size: 2.5rem;
            color: #FFFFFF;
        }
        .thank-you-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }
        .thank-you-subtitle {
            font-family: 'Inter', sans-serif;
            font-size: 1.2rem;
            color: #666666;
            font-weight: 400;
        }
        .confirmation-details {
            background: #F9F7F2;
            border-radius: 15px;
            padding: 2.5rem;
            margin: 2rem 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(74, 93, 74, 0.1);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: #666666;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: #1A1A1A;
            font-weight: 600;
            text-align: right;
        }
        .transaction-id {
            font-family: 'Courier New', monospace;
            font-size: 0.95rem;
            color: #4A5D4A;
            background: #F0F0F0;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
        }
        .package-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .package-badge.bronze {
            background: linear-gradient(135deg, #CD7F32 0%, #B87333 100%);
            color: #FFFFFF;
        }
        .package-badge.silver {
            background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%);
            color: #1A1A1A;
        }
        .package-badge.gold {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #1A1A1A;
        }
        .error-message-box {
            background: #FFF5F5;
            border: 2px solid #FEB2B2;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }
        .error-message-box i {
            font-size: 3rem;
            color: #E53E3E;
            margin-bottom: 1rem;
        }
        .error-message-box h3 {
            font-family: 'Playfair Display', serif;
            color: #1A1A1A;
            margin-bottom: 1rem;
        }
        .error-message-box p {
            color: #666666;
            margin-bottom: 1.5rem;
        }
        .transaction-ref-box {
            background: #F9F7F2;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
        .transaction-ref-box strong {
            color: #4A5D4A;
        }
        .confirmation-actions {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(74, 93, 74, 0.1);
        }
        .btn-view-tickets {
            background: linear-gradient(135deg, #4A5D4A 0%, #6B8E6B 100%);
            border: none;
            color: #FFFFFF;
            padding: 1rem 2.5rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74, 93, 74, 0.2);
        }
        .btn-view-tickets:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 93, 74, 0.3);
            color: #FFFFFF;
        }
        @media (max-width: 768px) {
            .thank-you-title {
                font-size: 2rem;
            }
            .confirmation-details {
                padding: 1.5rem;
            }
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .detail-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="navbar navbar-expand-lg navbar-light fixed-top luxury-nav">
        <div class="container">
            <a class="navbar-brand luxury-logo" href="index.php"><img src="assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item ms-3">
                            <a class="nav-link" href="profile.php">My Profile</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-3">
                            <a class="nav-link btn-login" href="login.php">Login</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="confirmation-page-section py-5 mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if ($errorOccurred): ?>
                        <!-- Error Message -->
                        <div class="confirmation-card p-5">
                            <div class="error-message-box">
                                <i class="fas fa-exclamation-triangle"></i>
                                <h3>We're Sorry</h3>
                                <p><?php echo htmlspecialchars($errorMessage); ?></p>
                                <p class="small">Don't worry, your payment was successful. Our team has been notified and will process your reservation manually.</p>
                                
                                <?php if (!empty($transactionReference)): ?>
                                    <div class="transaction-ref-box">
                                        <strong>Transaction Reference:</strong><br>
                                        <span class="transaction-id"><?php echo htmlspecialchars($transactionReference); ?></span>
                                        <p class="small mt-2 mb-0">Please save this reference number and contact our support team at <a href="mailto:support@evenza.com">support@evenza.com</a> if you need assistance.</p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-4">
                                    <a href="profile.php" class="btn btn-primary-luxury">View My Profile</a>
                                    <a href="index.php" class="btn btn-outline-luxury ms-2">Return Home</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Success Confirmation -->
                        <div class="confirmation-card p-5">
                            <div class="thank-you-message text-center">
                                <div class="success-icon-wrapper">
                                    <i class="fas fa-check"></i>
                                </div>
                                <h1 class="thank-you-title">Thank You!</h1>
                                <p class="thank-you-subtitle">Your payment has been confirmed successfully</p>
                            </div>

                            <div class="confirmation-details">
                                <div class="detail-row">
                                    <span class="detail-label">Package Tier</span>
                                    <span class="detail-value">
                                        <span class="package-badge <?php echo strtolower($packageTier); ?>">
                                            <?php echo htmlspecialchars($packageTier); ?> Package
                                        </span>
                                    </span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Transaction ID</span>
                                    <span class="detail-value">
                                        <span class="transaction-id"><?php echo htmlspecialchars($transactionId); ?></span>
                                    </span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Event</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($event['title']); ?></span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Amount Paid</span>
                                    <span class="detail-value">₱ <?php echo number_format($amount, 2); ?></span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Reservation ID</span>
                                    <span class="detail-value">#<?php echo $reservationId; ?></span>
                                </div>
                            </div>

                            <div class="confirmation-actions text-center">
                                <a href="profile.php" class="btn btn-view-tickets">
                                    <i class="fas fa-ticket-alt me-2"></i>View My Tickets
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="luxury-card p-4 mt-4">
                        <h5 class="mb-3 text-center">What's Next?</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                You will receive a confirmation email shortly
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Your tickets are available in your profile
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                For any questions, contact us at <a href="mailto:info@evenza.com">info@evenza.com</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="luxury-footer py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="footer-logo mb-3">EVENZA</h5>
                    <p class="footer-text">Premium event reservation and ticketing platform. Experience elegance, reserve with confidence.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="footer-heading mb-3">Contact Info</h6>
                    <p class="footer-text">
                        Email: info@evenza.com<br>
                        Phone: +1 (555) 123-4567<br>
                        Address: 123 Luxury Avenue, Suite 100<br>
                        City, State 12345
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="footer-heading mb-3">Hotel Partner</h6>
                    <p class="footer-text">
                        <strong>Grand Luxe Hotels</strong><br>
                        Your trusted partner for premium event hosting
                    </p>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="footer-copyright">&copy; <?php echo date('Y'); ?> EVENZA. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
