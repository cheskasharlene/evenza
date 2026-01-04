<?php
session_start();
require_once 'connect.php';

// Display success/error messages
$success_message = '';
$error_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 1;

// Load packages from database
$packages = [];
$packagesQuery = "SELECT packageId, packageName, price FROM packages ORDER BY packageId ASC";
$packagesResult = mysqli_query($conn, $packagesQuery);
if ($packagesResult) {
    while ($row = mysqli_fetch_assoc($packagesResult)) {
        // Extract tier from package name (e.g., "Bronze Package" -> "Bronze")
        $tier = str_replace(' Package', '', $row['packageName']);
        $packages[] = [
            'id' => $row['packageId'],
            'name' => $row['packageName'],
            'tier' => $tier,
            'price' => floatval($row['price'])
        ];
    }
    mysqli_free_result($packagesResult);
}

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

// Price calculation function using switch statement (for reference, but we'll use DB prices)
function calculatePackagePrice($packageTier) {
    switch(strtolower($packageTier)) {
        case 'bronze':
            return 7000;
        case 'silver':
            return 10000;
        case 'gold':
            return 15000;
        default:
            return 0;
    }
}

// Alternative using if-else (commented out, using switch above)
/*
function calculatePackagePrice($packageTier) {
    if (strtolower($packageTier) === 'bronze') {
        return 7000;
    } elseif (strtolower($packageTier) === 'silver') {
        return 10000;
    } elseif (strtolower($packageTier) === 'gold') {
        return 15000;
    } else {
        return 0;
    }
}
*/

// Get selected package from POST/GET or default to first package
$selectedPackageId = isset($_POST['packageId']) ? intval($_POST['packageId']) : (isset($_GET['packageId']) ? intval($_GET['packageId']) : ($packages[0]['id'] ?? 1));
$selectedPackage = $packages[0] ?? ['id' => 1, 'name' => 'Bronze Package', 'tier' => 'Bronze', 'price' => 7000];
foreach ($packages as $p) {
    if ($p['id'] == $selectedPackageId) {
        $selectedPackage = $p;
        break;
    }
}

// Use price from database
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
                    <li class="breadcrumb-item"><a href="eventDetails.php?id=<?php echo $eventId; ?>">Event Details</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reservation</li>
                </ol>
            </div>

            <div class="reservation-layout">
                <!-- Left Column: Form -->
                <div class="reservation-form-column">
                    <div class="luxury-card p-4">
                        <h2 class="page-title mb-4">Reservation Form</h2>
                        
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error_message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form id="reservationForm" method="POST" action="reserve.php">
                            <input type="hidden" name="eventId" value="<?php echo $eventId; ?>">
                            <!-- Package selection hidden fields -->
                            <input type="hidden" name="packageId" id="packageId" value="<?php echo $selectedPackage['id']; ?>">
                            <input type="hidden" name="packageTier" id="packageTier" value="<?php echo htmlspecialchars($selectedPackage['tier']); ?>">
                            <input type="hidden" name="packageName" id="packageName" value="<?php echo htmlspecialchars($selectedPackage['name']); ?>">
                            <input type="hidden" name="packagePrice" id="packagePrice" value="<?php echo $selectedPackage['price']; ?>">
                            <input type="hidden" name="reservationDate" id="reservationDate" value="<?php echo date('Y-m-d'); ?>">
                            
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
                                <input type="tel" class="form-control luxury-input" id="mobile" name="mobile" required placeholder="+63-9123-456-7890">
                            </div>

                            <div class="mb-4">
                                <label for="reservationDate" class="form-label">Preferred Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control luxury-input" id="reservationDate" name="reservationDate" required>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="eventStartTime" class="form-label">Event Start Time <span class="text-danger">*</span></label>
                                    <select class="form-select luxury-input" id="eventStartTime" name="eventStartTime" required>
                                        <option value="">Select start time</option>
                                        <option value="08:00 AM">8:00 AM</option>
                                        <option value="09:00 AM">9:00 AM</option>
                                        <option value="10:00 AM">10:00 AM</option>
                                        <option value="11:00 AM">11:00 AM</option>
                                        <option value="12:00 PM">12:00 PM</option>
                                        <option value="01:00 PM">1:00 PM</option>
                                        <option value="02:00 PM">2:00 PM</option>
                                        <option value="03:00 PM">3:00 PM</option>
                                        <option value="04:00 PM">4:00 PM</option>
                                        <option value="05:00 PM">5:00 PM</option>
                                        <option value="06:00 PM">6:00 PM</option>
                                        <option value="07:00 PM">7:00 PM</option>
                                        <option value="08:00 PM">8:00 PM</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="eventEndTime" class="form-label">Event End Time <span class="text-danger">*</span></label>
                                    <select class="form-select luxury-input" id="eventEndTime" name="eventEndTime" required>
                                        <option value="">Select end time</option>
                                        <option value="09:00 AM">9:00 AM</option>
                                        <option value="10:00 AM">10:00 AM</option>
                                        <option value="11:00 AM">11:00 AM</option>
                                        <option value="12:00 PM">12:00 PM</option>
                                        <option value="01:00 PM">1:00 PM</option>
                                        <option value="02:00 PM">2:00 PM</option>
                                        <option value="03:00 PM">3:00 PM</option>
                                        <option value="04:00 PM">4:00 PM</option>
                                        <option value="05:00 PM">5:00 PM</option>
                                        <option value="06:00 PM">6:00 PM</option>
                                        <option value="07:00 PM">7:00 PM</option>
                                        <option value="08:00 PM">8:00 PM</option>
                                        <option value="09:00 PM">9:00 PM</option>
                                        <option value="10:00 PM">10:00 PM</option>
                                        <option value="11:00 PM">11:00 PM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Select Package <span class="text-danger">*</span></label>
                                <div class="package-options d-flex gap-2 mt-2 flex-wrap" id="packageOptions">
                                    <?php foreach ($packages as $p): ?>
                                        <div class="package-tile <?php echo ($p['id'] == $selectedPackageId) ? 'selected' : ''; ?>" role="button" tabindex="0" data-id="<?php echo $p['id']; ?>" data-tier="<?php echo htmlspecialchars($p['tier']); ?>" data-name="<?php echo htmlspecialchars($p['name']); ?>" data-price="<?php echo $p['price']; ?>">
                                            <div class="package-tile-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                            <div class="package-tile-rate">Flat rate</div>
                                            <div class="package-tile-price">₱ <?php echo number_format($p['price'], 2); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted d-block mt-2">Choose a package to reserve the event as a single purchase.</small>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <a href="eventDetails.php?id=<?php echo $eventId; ?>" class="btn btn-outline-luxury flex-fill">Back to Event</a>
                                <button type="submit" class="btn btn-primary-luxury flex-fill">Proceed to Payment</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Sticky Summary -->
                <div class="reservation-summary-column">
                    <div class="luxury-card reservation-summary p-4 sticky-summary">
                        <h4 class="mb-4">Reservation Summary</h4>
                        
                        <div class="summary-item mb-3">
                            <div class="summary-label">Event Name</div>
                            <div class="summary-value"><?php echo htmlspecialchars($event['name']); ?></div>
                        </div>

                        <!-- category removed -->

                        <div class="summary-item mb-3">
                            <div class="summary-label">Date & Time</div>
                            <div class="summary-value">
                                <div id="summaryDate"><?php echo htmlspecialchars($event['date']); ?></div>
                                <div class="text-muted small" id="summaryTimeRange"><?php echo htmlspecialchars($event['time']); ?></div>
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
            eventId: <?php echo $eventId; ?>,
            defaultEvent: {
                date: "<?php echo htmlspecialchars($event['date']); ?>",
                time: "<?php echo htmlspecialchars($event['time']); ?>"
            }
        };

        // Set minimum date to today
        const dateInput = document.getElementById('reservationDate');
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        // Format date for display
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString + 'T00:00:00');
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Update reservation summary in real-time
        function updateReservationSummary() {
            const dateValue = dateInput.value;
            const startTimeValue = document.getElementById('eventStartTime').value;
            const endTimeValue = document.getElementById('eventEndTime').value;

            // Update date
            if (dateValue) {
                const formattedDate = formatDate(dateValue);
                document.getElementById('summaryDate').textContent = formattedDate;
            } else {
                document.getElementById('summaryDate').textContent = reservationData.defaultEvent.date;
            }

            // Update time range
            if (startTimeValue && endTimeValue) {
                document.getElementById('summaryTimeRange').textContent = `${startTimeValue} - ${endTimeValue}`;
            } else if (startTimeValue) {
                document.getElementById('summaryTimeRange').textContent = startTimeValue;
            } else if (endTimeValue) {
                document.getElementById('summaryTimeRange').textContent = endTimeValue;
            } else {
                document.getElementById('summaryTimeRange').textContent = reservationData.defaultEvent.time;
            }
        }

        // Handle package selection and update summary
        function setupPackageSelectionListeners() {
            const packageTiles = document.querySelectorAll('.package-tile');
            
            packageTiles.forEach(tile => {
                tile.addEventListener('click', function() {
                    // Remove selected class from all tiles
                    packageTiles.forEach(t => t.classList.remove('selected'));
                    
                    // Add selected class to clicked tile
                    this.classList.add('selected');
                    
                    // Update hidden fields
                    const packageId = this.getAttribute('data-id');
                    const packageTier = this.getAttribute('data-tier');
                    const packageName = this.getAttribute('data-name');
                    const packagePrice = this.getAttribute('data-price');
                    
                    document.getElementById('packageId').value = packageId;
                    document.getElementById('packageTier').value = packageTier;
                    document.getElementById('packageName').value = packageName;
                    document.getElementById('packagePrice').value = packagePrice;
                    
                    // Update summary
                    document.getElementById('summaryPackage').textContent = packageName;
                    document.getElementById('summaryTotal').textContent = '₱ ' + parseFloat(packagePrice).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                });

                // Allow keyboard navigation
                tile.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        }

        // Add event listeners for real-time updates
        dateInput.addEventListener('change', updateReservationSummary);
        document.getElementById('eventStartTime').addEventListener('change', updateReservationSummary);
        document.getElementById('eventEndTime').addEventListener('change', updateReservationSummary);

        // Validate form on submission
        document.getElementById('reservationForm').addEventListener('submit', function(e) {
            const dateValue = dateInput.value;
            const startTimeValue = document.getElementById('eventStartTime').value;
            const endTimeValue = document.getElementById('eventEndTime').value;

            if (!dateValue) {
                e.preventDefault();
                alert('Please select a preferred date.');
                return false;
            }

            if (!startTimeValue) {
                e.preventDefault();
                alert('Please select an event start time.');
                return false;
            }

            if (!endTimeValue) {
                e.preventDefault();
                alert('Please select an event end time.');
                return false;
            }

            // Validate that end time is after start time
            const timeOrder = ['08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM', '09:00 PM', '10:00 PM', '11:00 PM'];
            const startIndex = timeOrder.indexOf(startTimeValue);
            const endIndex = timeOrder.indexOf(endTimeValue);

            if (startIndex >= endIndex) {
                e.preventDefault();
                alert('Event end time must be after start time.');
                return false;
            }

            // Validate date is not in past
            const selectedDate = new Date(dateValue + 'T00:00:00');
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate < today) {
                e.preventDefault();
                alert('Please select a future date.');
                dateInput.focus();
                return false;
            }
        });

        // Initialize package selection on page load
        setupPackageSelectionListeners();
    </script>
    <script src="assets/js/reservation.js"></script>
</body>
</html>

