<?php
session_start();

$eventId = isset($_POST['eventId']) ? intval($_POST['eventId']) : (isset($_GET['eventId']) ? intval($_GET['eventId']) : 1);
// Package-based inputs
$packageName = isset($_POST['packageName']) ? htmlspecialchars($_POST['packageName']) : (isset($_GET['packageName']) ? htmlspecialchars($_GET['packageName']) : '');
$packagePrice = isset($_POST['packagePrice']) ? floatval($_POST['packagePrice']) : (isset($_GET['packagePrice']) ? floatval($_GET['packagePrice']) : 0.0);
$fullName = isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$mobile = isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : '';

$eventsData = [
    1 => [
        'name' => 'Business Innovation Summit 2024',
        'category' => 'Conference',
        'price' => 299,
        'priceType' => 'per person',
        'date' => 'December 25, 2024',
        'time' => '9:00 AM - 6:00 PM',
        'venue' => 'Grand Luxe Hotel - Grand Ballroom'
    ],
    2 => [
        'name' => 'Elegant Garden Wedding',
        'category' => 'Wedding',
        'price' => 5500,
        'priceType' => 'package',
        'date' => 'January 10, 2025',
        'time' => '4:00 PM - 11:00 PM',
        'venue' => 'Grand Luxe Hotel - Garden Pavilion'
    ],
    3 => [
        'name' => 'Digital Marketing Masterclass',
        'category' => 'Seminar',
        'price' => 149,
        'priceType' => 'per person',
        'date' => 'December 30, 2024',
        'time' => '10:00 AM - 5:00 PM',
        'venue' => 'Grand Luxe Hotel - Conference Hall A'
    ],
    4 => [
        'name' => 'New Year\'s Eve Gala Dinner',
        'category' => 'Hotel-Hosted Events',
        'price' => 450,
        'priceType' => 'per person',
        'date' => 'December 31, 2024',
        'time' => '7:00 PM - 1:00 AM',
        'venue' => 'Grand Luxe Hotel - Crystal Ballroom'
    ]
];

$event = isset($eventsData[$eventId]) ? $eventsData[$eventId] : $eventsData[1];

// Use package price as the total amount (flat rate)
$totalAmount = $packagePrice;

$paymentStatus = isset($_GET['status']) ? $_GET['status'] : 'pending';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - EVENZA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
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

    <div class="payment-page-section py-5 mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                            <li class="breadcrumb-item"><a href="event-details.php?id=<?php echo $eventId; ?>">Event Details</a></li>
                            <li class="breadcrumb-item"><a href="reservation.php?eventId=<?php echo $eventId; ?>&package=<?php echo urlencode($packageName); ?>">Reservation</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Payment</li>
                        </ol>
                    </div>

                    <div class="luxury-card payment-summary-card p-5 mb-4">
                        <h2 class="page-title mb-4 text-center">Payment Summary</h2>
                        
                        <div class="payment-summary-content">
                            <div class="payment-summary-item mb-4">
                                <div class="payment-label">Event Name</div>
                                <div class="payment-value"><?php echo htmlspecialchars($event['name']); ?></div>
                            </div>

                            <!-- category removed -->

                            <div class="payment-summary-item mb-4">
                                <div class="payment-label">Package</div>
                                <div class="payment-value"><?php echo htmlspecialchars($packageName); ?> - ₱ <?php echo number_format($packagePrice, 2); ?></div>
                            </div>

                            <hr class="my-4">

                            <div class="payment-total">
                                <div class="payment-total-label">Total Amount</div>
                                <div class="payment-total-value">₱ <?php echo number_format($totalAmount, 2); ?></div>
                            </div>
                        </div>

                        <?php if ($paymentStatus === 'pending'): ?>
                            <div class="payment-button-section mt-5">
                                <button type="button" class="btn btn-paypal w-100 btn-lg" onclick="processPayment()">
                                    Pay with PayPal
                                </button>
                            </div>
                        <?php endif; ?>

                        <div id="statusMessages" class="mt-4">
                            <?php if ($paymentStatus === 'processing'): ?>
                                <div class="status-message status-processing">
                                    <div class="status-icon">
                                    </div>
                                    <div class="status-content">
                                        <h5>Payment Processing</h5>
                                        <p>Your payment is being processed. Please wait...</p>
                                    </div>
                                </div>
                            <?php elseif ($paymentStatus === 'success'): ?>
                                <div class="status-message status-success">
                                    <div class="status-icon">
                                    </div>
                                    <div class="status-content">
                                        <h5>Payment Successful</h5>
                                        <p>Your payment has been processed successfully. Redirecting to confirmation page...</p>
                                        <div class="mt-3">
                                            <a href="confirmation.php?eventId=<?php echo $eventId; ?>&packageName=<?php echo urlencode($packageName); ?>&packagePrice=<?php echo $packagePrice; ?>&fullName=<?php echo urlencode($fullName); ?>&email=<?php echo urlencode($email); ?>" class="btn btn-primary-luxury">View Confirmation</a>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    setTimeout(function() {
                                        window.location.href = 'confirmation.php?eventId=<?php echo $eventId; ?>&packageName=<?php echo urlencode($packageName); ?>&packagePrice=<?php echo $packagePrice; ?>&fullName=<?php echo urlencode($fullName); ?>&email=<?php echo urlencode($email); ?>';
                                    }, 2000);
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="luxury-card p-4">
                        <h5 class="mb-3">Payment Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Reservation Details:</strong>
                                <ul class="list-unstyled mt-2">
                                    <li><small>Name: <?php echo htmlspecialchars($fullName); ?></small></li>
                                    <li><small>Email: <?php echo htmlspecialchars($email); ?></small></li>
                                    <li><small>Mobile: <?php echo htmlspecialchars($mobile); ?></small></li>
                                </ul>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Event Details:</strong>
                                <ul class="list-unstyled mt-2">
                                    <li><small>Date: <?php echo htmlspecialchars($event['date']); ?></small></li>
                                    <li><small>Time: <?php echo htmlspecialchars($event['time']); ?></small></li>
                                    <li><small>Venue: <?php echo htmlspecialchars($event['venue']); ?></small></li>
                                </ul>
                            </div>
                        </div>
                        <div class="alert alert-info mb-0">
                            <small>
                                <strong>Secure Payment:</strong> Your payment information is encrypted and secure. 
                                We use PayPal's secure payment processing system to ensure your financial data is protected.
                            </small>
                        </div>
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
    <script src="assets/js/main.js"></script>
    <script src="assets/js/payment.js"></script>
</body>
</html>

