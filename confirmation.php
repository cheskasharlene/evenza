<?php

$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 1;
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
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

$event = isset($eventsData[$eventId]) ? $eventsData[$eventId] : $eventsData[1];
$totalAmount = $event['price'] * $quantity;
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
    <nav class="navbar navbar-expand-lg navbar-light fixed-top luxury-nav">
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
    </nav>

    <section class="confirmation-page-section py-5 mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="luxury-card confirmation-card p-5">
                        <div class="thank-you-message text-center mb-5">
                            <div class="success-icon mb-3">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
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

                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">Category</div>
                                <div class="confirmation-value">
                                    <span class="event-category"><?php echo htmlspecialchars($event['category']); ?></span>
                                </div>
                            </div>

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
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px; color: var(--accent-olive);">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <strong><?php echo htmlspecialchars($event['date']); ?></strong>
                                            <span class="text-muted ms-2"><?php echo htmlspecialchars($event['time']); ?></span>
                                        </div>
                                        <div>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px; color: var(--accent-olive);">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                <circle cx="12" cy="10" r="3"/>
                                            </svg>
                                            <?php echo htmlspecialchars($event['venue']); ?>
                                            <div class="text-muted small ms-6"><?php echo htmlspecialchars($event['venueAddress']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">Number of Tickets</div>
                                <div class="confirmation-value"><?php echo $quantity; ?> ticket(s)</div>
                            </div>

                            <div class="confirmation-item mb-4">
                                <div class="confirmation-label">Total Amount Paid</div>
                                <div class="confirmation-value price-amount">$<?php echo number_format($totalAmount); ?></div>
                            </div>

                            <hr class="my-4">

                            <div class="confirmation-note">
                                <div class="note-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
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
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                            <polyline points="6 9 6 2 18 2 18 9"/>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                            <rect x="6" y="14" width="12" height="8"/>
                                        </svg>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px; color: var(--accent-olive);">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                Please arrive 15 minutes before the event start time.
                            </li>
                            <li class="mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px; color: var(--accent-olive);">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                Bring a valid ID and this confirmation for entry.
                            </li>
                            <li class="mb-0">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px; color: var(--accent-olive);">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                For any questions, contact us at info@evenza.com
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="luxury-footer py-5">
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
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/confirmation.js"></script>
</body>
</html>

