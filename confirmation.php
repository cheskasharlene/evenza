<?php

$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 1;
// Package-based confirmation
$packageName = isset($_GET['packageName']) ? htmlspecialchars($_GET['packageName']) : 'Package';
$packagePrice = isset($_GET['packagePrice']) ? floatval($_GET['packagePrice']) : 0.0;
$fullName = isset($_GET['fullName']) ? htmlspecialchars($_GET['fullName']) : 'Guest User';
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$ticketId = 'EVZ-' . strtoupper(substr(md5($eventId . $fullName . time()), 0, 8));

$eventsData = [
    1 => [
        'name' => 'Business Innovation Summit 2024',
        'category' => 'Conference',
        'date' => 'December 25, 2024',
        'time' => '9:00 AM - 6:00 PM',
        'venue' => 'Grand Luxe Hotel - Grand Ballroom',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 299,
        'priceType' => 'per person'
    ],
    2 => [
        'name' => 'Elegant Garden Wedding',
        'category' => 'Wedding',
        'date' => 'January 10, 2025',
        'time' => '4:00 PM - 11:00 PM',
        'venue' => 'Grand Luxe Hotel - Garden Pavilion',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 5500,
        'priceType' => 'package'
    ],
    3 => [
        'name' => 'Digital Marketing Masterclass',
        'category' => 'Seminar',
        'date' => 'December 30, 2024',
        'time' => '10:00 AM - 5:00 PM',
        'venue' => 'Grand Luxe Hotel - Conference Hall A',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 149,
        'priceType' => 'per person'
    ],
    4 => [
        'name' => 'New Year\'s Eve Gala Dinner',
        'category' => 'Hotel-Hosted Events',
        'date' => 'December 31, 2024',
        'time' => '7:00 PM - 1:00 AM',
        'venue' => 'Grand Luxe Hotel - Crystal Ballroom',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 450,
        'priceType' => 'per person'
    ]
];

session_start();

$event = isset($eventsData[$eventId]) ? $eventsData[$eventId] : $eventsData[1];
// Use package price if provided
$totalAmount = $packagePrice;

// Persist reservation for logged-in users
$reservationsFile = __DIR__ . '/data/reservations.json';
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

$reservationRecord = [
    'id' => uniqid('res_'),
    'userId' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
    'eventId' => $eventId,
    'eventName' => $event['name'],
    'date' => $event['date'],
    'time' => $event['time'],
    'venue' => $event['venue'],
    'ticketId' => $ticketId,
    'packageName' => $packageName,
    'totalAmount' => $totalAmount,
    'status' => 'confirmed',
    'createdAt' => date('c')
];

// Only save if we have a logged-in user and this ticket doesn't already exist
if (!empty($reservationRecord['userId'])) {
    $existing = [];
    if (file_exists($reservationsFile)) {
        $existing = json_decode(file_get_contents($reservationsFile), true) ?? [];
    }

    $found = false;
    foreach ($existing as $r) {
        if (isset($r['ticketId']) && $r['ticketId'] === $ticketId) {
            $found = true;
            break;
        }
    }

    if (!$found) {
        $existing[] = $reservationRecord;
        file_put_contents($reservationsFile, json_encode($existing, JSON_PRETTY_PRINT));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - EVENZA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
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

    <div class="confirmation-page-section py-5 mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="luxury-card confirmation-card p-5">
                        <div class="thank-you-message text-center mb-5">
                            <div class="success-icon mb-3">

                            </div>
                            <h1 class="thank-you-title">Thank You!</h1>
                            <p class="thank-you-subtitle">Your reservation has been confirmed successfully.</p>
                        </div>

                        <hr class="my-5">

                        <div class="confirmation-content">
                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">Event Name</div>
                                <div class="confirmation-value"><?php echo htmlspecialchars($event['name']); ?></div>
                            </div>

                            <!-- category removed -->

                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">Ticket ID</div>
                                <div class="confirmation-value ticket-id"><?php echo htmlspecialchars($ticketId); ?></div>
                            </div>

                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">QR Code</div>
                                <div class="confirmation-value">
                                    <div id="qrcode" class="qr-code-container"></div>
                                    <small class="text-muted d-block mt-2">Present this QR code at the event entrance</small>
                                </div>
                            </div>

                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">Event Date & Venue</div>
                                <div class="confirmation-value">
                                    <div class="event-date-venue">
                                        <div class="mb-2">
                                            <strong><?php echo htmlspecialchars($event['date']); ?></strong>
                                            <span class="text-muted ms-2"><?php echo htmlspecialchars($event['time']); ?></span>
                                        </div>
                                        <div>
                                            <?php echo htmlspecialchars($event['venue']); ?>
                                            <div class="text-muted small ms-6"><?php echo htmlspecialchars($event['venueAddress']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">Package</div>
                                <div class="confirmation-value"><?php echo htmlspecialchars($packageName); ?></div>
                            </div>

                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">Total Amount Paid</div>
                                <div class="confirmation-value price-amount">₱ <?php echo number_format($totalAmount, 2); ?></div>
                            </div>

                            <hr class="my-4">

                            <div class="confirmation-note">
                                <div class="note-icon">
                                </div>
                                <div class="note-content">
                                    <p class="mb-0"><strong>Note:</strong> You will receive an SMS confirmation shortly.</p>
                                </div>
                            </div>
                        </div>

                        <div class="confirmation-actions mt-5">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary-luxury w-100" onclick="window.print()">
                                        Print Ticket
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <a href="index.php" class="btn btn-outline-luxury w-100">
                                        Return to Home
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="luxury-card p-4 mt-4">
                        <h5 class="mb-3">Important Information</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                Please arrive 15 minutes before the event start time.
                            </li>
                            <li class="mb-2">
                                Bring a valid ID and this confirmation for entry.
                            </li>
                            <li class="mb-0">
                                For any questions, contact us at info@evenza.com
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
    <script src="assets/js/main.js"></script>
    <script src="assets/js/confirmation.js"></script>
</body>
</html>

