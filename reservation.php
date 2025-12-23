<?php
session_start();

$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 1;

// Package-based reservation: define packages for the event
$eventsData = [
    1 => [
        'name' => 'Business Innovation Summit 2024',
        'category' => 'Conference',
        'price' => 299,
        'priceType' => 'per person',
        'date' => 'December 25, 2024',
        'time' => '9:00 AM - 6:00 PM',
        'venue' => 'Grand Luxe Hotel - Grand Ballroom',
        'slots' => 45
    ],
    2 => [
        'name' => 'Elegant Garden Wedding',
        'category' => 'Wedding',
        'price' => 5500,
        'priceType' => 'package',
        'date' => 'January 10, 2025',
        'time' => '4:00 PM - 11:00 PM',
        'venue' => 'Grand Luxe Hotel - Garden Pavilion',
        'slots' => 12
    ],
    3 => [
        'name' => 'Digital Marketing Masterclass',
        'category' => 'Seminar',
        'price' => 149,
        'priceType' => 'per person',
        'date' => 'December 30, 2024',
        'time' => '10:00 AM - 5:00 PM',
        'venue' => 'Grand Luxe Hotel - Conference Hall A',
        'slots' => 78
    ],
    4 => [
        'name' => 'New Year\'s Eve Gala Dinner',
        'category' => 'Hotel-Hosted Events',
        'price' => 450,
        'priceType' => 'per person',
        'date' => 'December 31, 2024',
        'time' => '7:00 PM - 1:00 AM',
        'venue' => 'Grand Luxe Hotel - Crystal Ballroom',
        'slots' => 23
    ]
];

$event = isset($eventsData[$eventId]) ? $eventsData[$eventId] : $eventsData[1];

// Define package options (example packages) — replace with event-specific packages as needed
$packages = [
    ['id' => 'gold', 'name' => 'Gold Package', 'price' => 15000],
    ['id' => 'silver', 'name' => 'Silver Package', 'price' => 10000],
    ['id' => 'bronze', 'name' => 'Bronze Package', 'price' => 7000]
];

$selectedPackage = $packages[0];
$totalAmount = $selectedPackage['price'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation - EVENZA</title>
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

    <div class="reservation-page-section py-5 mt-5">
        <div class="container">
            <div aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                    <li class="breadcrumb-item"><a href="event-details.php?id=<?php echo $eventId; ?>">Event Details</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reservation</li>
                </ol>
            </div>

            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="luxury-card p-4">
                        <h2 class="page-title mb-4">Reservation Form</h2>
                        
                        <form id="reservationForm" method="POST" action="payment.php">
                            <input type="hidden" name="eventId" value="<?php echo $eventId; ?>">
                            <!-- Package selection hidden fields -->
                            <input type="hidden" name="packageId" id="packageId" value="<?php echo $selectedPackage['id']; ?>">
                            <input type="hidden" name="packageName" id="packageName" value="<?php echo htmlspecialchars($selectedPackage['name']); ?>">
                            <input type="hidden" name="packagePrice" id="packagePrice" value="<?php echo $selectedPackage['price']; ?>">
                            
                            <div class="mb-4">
                                <label for="fullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control luxury-input" id="fullName" name="fullName" required placeholder="Enter your full name">
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control luxury-input" id="email" name="email" required placeholder="your.email@example.com">
                            </div>

                            <div class="mb-4">
                                <label for="mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control luxury-input" id="mobile" name="mobile" required placeholder="+1 (555) 123-4567">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Select Package <span class="text-danger">*</span></label>
                                <div class="package-options d-flex flex-column flex-md-row gap-3 mt-2" id="packageOptions">
                                    <?php foreach ($packages as $p): ?>
                                        <div class="package-card luxury-card p-3" role="button" tabindex="0" data-id="<?php echo $p['id']; ?>" data-name="<?php echo htmlspecialchars($p['name']); ?>" data-price="<?php echo $p['price']; ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($p['name']); ?></div>
                                                    <div class="text-muted small">Flat rate</div>
                                                </div>
                                                <div class="package-price">₱ <?php echo number_format($p['price'], 2); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted d-block mt-2">Choose a package to reserve the event as a single purchase.</small>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <a href="event-details.php?id=<?php echo $eventId; ?>" class="btn btn-outline-luxury flex-fill">Back to Event</a>
                                <button type="submit" class="btn btn-primary-luxury flex-fill">Proceed to Payment</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="luxury-card reservation-summary p-4 sticky-top" style="top: 100px;">
                        <h4 class="mb-4">Reservation Summary</h4>
                        
                        <div class="summary-item mb-3">
                            <div class="summary-label">Event Name</div>
                            <div class="summary-value"><?php echo htmlspecialchars($event['name']); ?></div>
                        </div>

                        <!-- category removed -->

                        <div class="summary-item mb-3">
                            <div class="summary-label">Date & Time</div>
                            <div class="summary-value">
                                <div><?php echo htmlspecialchars($event['date']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($event['time']); ?></div>
                            </div>
                        </div>

                        <div class="summary-item mb-3">
                            <div class="summary-label">Venue</div>
                            <div class="summary-value small"><?php echo htmlspecialchars($event['venue']); ?></div>
                        </div>

                        <hr class="my-4">
                        <div class="summary-item mb-2">
                            <div class="summary-label">Package</div>
                            <div class="summary-value" id="summaryPackage"><?php echo htmlspecialchars($selectedPackage['name']); ?></div>
                        </div>

                        <hr class="my-4">
                        <div class="summary-total">
                            <div class="summary-total-label">Total Amount</div>
                            <div class="summary-total-value" id="summaryTotal">₱ <?php echo number_format($totalAmount, 2); ?></div>
                        </div>

                        <div class="summary-note mt-4">
                            <p class="small text-muted mb-0">
                                You will be redirected to the payment page after submitting this form.
                            </p>
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
    <script>
        const reservationData = {
            packages: <?php echo json_encode($packages); ?>,
            selectedPackageId: "<?php echo $selectedPackage['id']; ?>",
            eventId: <?php echo $eventId; ?>
        };
    </script>
    <script src="assets/js/reservation.js"></script>
</body>
</html>

