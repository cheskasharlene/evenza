<?php

$eventId = isset($_POST['eventId']) ? intval($_POST['eventId']) : (isset($_GET['eventId']) ? intval($_GET['eventId']) : 1);
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : (isset($_GET['quantity']) ? intval($_GET['quantity']) : 1);
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
$totalAmount = $event['price'] * $quantity;

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
            <a class="navbar-brand luxury-logo" href="index.php">EVENZA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link btn-login" href="login.php">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn-register" href="register.php">Register</a>
                    </li>
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
                            <li class="breadcrumb-item"><a href="reservation.php?eventId=<?php echo $eventId; ?>&quantity=<?php echo $quantity; ?>">Reservation</a></li>
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

                            <div class="payment-summary-item mb-4">
                                <div class="payment-label">Category</div>
                                <div class="payment-value">
                                    <span class="event-category"><?php echo htmlspecialchars($event['category']); ?></span>
                                </div>
                            </div>

                            <div class="payment-summary-item mb-4">
                                <div class="payment-label">Ticket Quantity</div>
                                <div class="payment-value"><?php echo $quantity; ?> ticket(s)</div>
                            </div>

                            <hr class="my-4">

                            <div class="payment-total">
                                <div class="payment-total-label">Total Amount</div>
                                <div class="payment-total-value">$<?php echo number_format($totalAmount); ?></div>
                            </div>
                        </div>

                        <?php if ($paymentStatus === 'pending'): ?>
                            <div class="payment-button-section mt-5">
                                <button type="button" class="btn btn-paypal w-100 btn-lg" onclick="processPayment()">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 10px; vertical-align: middle;">
                                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.174 1.27 1.68 2.962 1.495 4.957-.24 2.618-1.647 4.36-4.266 5.24-.36.12-.738.21-1.133.27v.06c.03.01.06.02.09.03 1.236.396 2.2 1.06 2.864 1.96.664.9.99 1.99.99 3.24 0 .87-.18 1.67-.54 2.4-.36.73-.87 1.36-1.53 1.89-.66.53-1.44.96-2.34 1.29-.9.33-1.89.5-2.97.5H9.23c-.6 0-1.08-.45-1.14-1.04L7.076 21.336zm.66-12.3c.12 1.17.78 1.95 1.98 2.34 1.2.39 2.76.39 4.68 0 1.32-.3 2.28-.87 2.88-1.71.6-.84.78-1.89.54-3.15-.18-1.02-.66-1.77-1.44-2.25-.78-.48-1.86-.72-3.24-.72H8.76c-.3 0-.54.21-.57.51l-1.47 8.98z"/>
                                    </svg>
                                    Pay with PayPal
                                </button>
                            </div>
                        <?php endif; ?>

                        <div id="statusMessages" class="mt-4">
                            <?php if ($paymentStatus === 'processing'): ?>
                                <div class="status-message status-processing">
                                    <div class="status-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                    </div>
                                    <div class="status-content">
                                        <h5>Payment Processing</h5>
                                        <p>Your payment is being processed. Please wait...</p>
                                    </div>
                                </div>
                            <?php elseif ($paymentStatus === 'success'): ?>
                                <div class="status-message status-success">
                                    <div class="status-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                    </div>
                                    <div class="status-content">
                                        <h5>Payment Successful</h5>
                                        <p>Your payment has been processed successfully. Redirecting to confirmation page...</p>
                                        <div class="mt-3">
                                            <a href="confirmation.php?eventId=<?php echo $eventId; ?>&quantity=<?php echo $quantity; ?>&fullName=<?php echo urlencode($fullName); ?>&email=<?php echo urlencode($email); ?>" class="btn btn-primary-luxury">View Confirmation</a>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    setTimeout(function() {
                                        window.location.href = 'confirmation.php?eventId=<?php echo $eventId; ?>&quantity=<?php echo $quantity; ?>&fullName=<?php echo urlencode($fullName); ?>&email=<?php echo urlencode($email); ?>';
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

