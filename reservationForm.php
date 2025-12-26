<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode('reservationForm.php?eventId=' . (isset($_GET['eventId']) ? $_GET['eventId'] : '')));
    exit;
}

require_once 'config/database.php';

$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 0;
$error = '';
$event = null;
$packages = [];

if ($eventId > 0) {
    $conn = getDBConnection();
    
    // Fetch event details (without pricing)
    $stmt = $conn->prepare("SELECT eventId, title, description, venue, category, imagePath, eventDate, totalCapacity, availableSlots FROM events WHERE eventId = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();
    
    // Fetch all packages
    $packagesResult = $conn->query("SELECT packageId, packageName, price, description FROM packages ORDER BY price ASC");
    while ($row = $packagesResult->fetch_assoc()) {
        $packages[] = $row;
    }
    
    $conn->close();
    
    if (!$event) {
        $error = 'Event not found.';
    }
} else {
    $error = 'Invalid event ID.';
}

// Set default selected package (first one)
$selectedPackage = !empty($packages) ? $packages[0] : null;
$totalAmount = $selectedPackage ? $selectedPackage['price'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Form - EVENZA</title>
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
            <?php if ($error || !$event): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error ? $error : 'Event not found.'); ?>
                    <a href="events.php" class="btn btn-sm btn-outline-danger ms-3">Back to Events</a>
                </div>
            <?php else: ?>
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
                            
                            <form id="reservationForm" method="POST" action="processReservation.php">
                                <input type="hidden" name="eventId" value="<?php echo $eventId; ?>">
                                <input type="hidden" name="userId" value="<?php echo $_SESSION['user_id']; ?>">
                                <input type="hidden" name="packageId" id="packageId" value="<?php echo $selectedPackage ? $selectedPackage['packageId'] : ''; ?>">
                                <input type="hidden" name="totalAmount" id="totalAmount" value="<?php echo $totalAmount; ?>">
                                
                                <div class="mb-4">
                                    <label for="reservationDate" class="form-label">Preferred Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control luxury-input" id="reservationDate" name="reservationDate" required>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="eventStartTime" class="form-label">Event Start Time <span class="text-danger">*</span></label>
                                        <select class="form-select luxury-input" id="eventStartTime" name="eventStartTime" required>
                                            <option value="">Select start time</option>
                                            <option value="08:00">8:00 AM</option>
                                            <option value="09:00">9:00 AM</option>
                                            <option value="10:00">10:00 AM</option>
                                            <option value="11:00">11:00 AM</option>
                                            <option value="12:00">12:00 PM</option>
                                            <option value="13:00">1:00 PM</option>
                                            <option value="14:00">2:00 PM</option>
                                            <option value="15:00">3:00 PM</option>
                                            <option value="16:00">4:00 PM</option>
                                            <option value="17:00">5:00 PM</option>
                                            <option value="18:00">6:00 PM</option>
                                            <option value="19:00">7:00 PM</option>
                                            <option value="20:00">8:00 PM</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="eventEndTime" class="form-label">Event End Time <span class="text-danger">*</span></label>
                                        <select class="form-select luxury-input" id="eventEndTime" name="eventEndTime" required>
                                            <option value="">Select end time</option>
                                            <option value="09:00">9:00 AM</option>
                                            <option value="10:00">10:00 AM</option>
                                            <option value="11:00">11:00 AM</option>
                                            <option value="12:00">12:00 PM</option>
                                            <option value="13:00">1:00 PM</option>
                                            <option value="14:00">2:00 PM</option>
                                            <option value="15:00">3:00 PM</option>
                                            <option value="16:00">4:00 PM</option>
                                            <option value="17:00">5:00 PM</option>
                                            <option value="18:00">6:00 PM</option>
                                            <option value="19:00">7:00 PM</option>
                                            <option value="20:00">8:00 PM</option>
                                            <option value="21:00">9:00 PM</option>
                                            <option value="22:00">10:00 PM</option>
                                            <option value="23:00">11:00 PM</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Select Package <span class="text-danger">*</span></label>
                                    <select class="form-select luxury-input" id="packageSelect" name="packageSelect" required>
                                        <option value="">Choose a package...</option>
                                        <?php foreach ($packages as $pkg): ?>
                                            <option value="<?php echo $pkg['packageId']; ?>" 
                                                    data-price="<?php echo $pkg['price']; ?>"
                                                    data-name="<?php echo htmlspecialchars($pkg['packageName']); ?>"
                                                    <?php echo ($selectedPackage && $selectedPackage['packageId'] == $pkg['packageId']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($pkg['packageName']); ?> - ₱<?php echo number_format($pkg['price'], 2); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted d-block mt-2">Select a package to see the total amount.</small>
                                </div>

                                <div class="d-flex gap-3 mt-4">
                                    <a href="eventDetails.php?id=<?php echo $eventId; ?>" class="btn btn-outline-luxury flex-fill">Back to Event</a>
                                    <button type="submit" class="btn btn-primary-luxury flex-fill">Submit Reservation</button>
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
                                <div class="summary-value"><?php echo htmlspecialchars($event['title']); ?></div>
                            </div>

                            <div class="summary-item mb-3">
                                <div class="summary-label">Venue</div>
                                <div class="summary-value small"><?php echo htmlspecialchars($event['venue']); ?></div>
                            </div>

                            <div class="summary-item mb-3">
                                <div class="summary-label">Date & Time</div>
                                <div class="summary-value">
                                    <div id="summaryDate">Select a date</div>
                                    <div class="text-muted small" id="summaryTimeRange">Select times</div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="summary-item mb-2">
                                <div class="summary-label">Package</div>
                                <div class="summary-value" id="summaryPackage"><?php echo $selectedPackage ? htmlspecialchars($selectedPackage['packageName']) : 'Not selected'; ?></div>
                            </div>

                            <hr class="my-4">
                            <div class="summary-total">
                                <div class="summary-total-label">Total Amount</div>
                                <div class="summary-total-value" id="summaryTotal">₱ <?php echo number_format($totalAmount, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
    <script>
        // Set minimum date to today
        const dateInput = document.getElementById('reservationDate');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        }

        // Package selection handler - Update total amount and summary
        const packageSelect = document.getElementById('packageSelect');
        if (packageSelect) {
            packageSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const packageId = this.value;
                const packagePrice = selectedOption.getAttribute('data-price');
                const packageName = selectedOption.getAttribute('data-name');
                
                if (packageId && packagePrice) {
                    // Update hidden fields
                    document.getElementById('packageId').value = packageId;
                    document.getElementById('totalAmount').value = packagePrice;
                    
                    // Update summary
                    document.getElementById('summaryPackage').textContent = packageName;
                    document.getElementById('summaryTotal').textContent = '₱ ' + parseFloat(packagePrice).toLocaleString('en-PH', { 
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2 
                    });
                }
            });
        }

        // Update summary when date/time changes
        function updateSummary() {
            const dateValue = dateInput ? dateInput.value : '';
            const startTime = document.getElementById('eventStartTime') ? document.getElementById('eventStartTime').value : '';
            const endTime = document.getElementById('eventEndTime') ? document.getElementById('eventEndTime').value : '';
            
            if (dateValue) {
                const date = new Date(dateValue);
                const formattedDate = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                document.getElementById('summaryDate').textContent = formattedDate;
            }
            
            if (startTime && endTime) {
                const formatTime = (time) => {
                    const [hours, minutes] = time.split(':');
                    const h = parseInt(hours);
                    const ampm = h >= 12 ? 'PM' : 'AM';
                    const displayHour = h > 12 ? h - 12 : (h === 0 ? 12 : h);
                    return `${displayHour}:${minutes} ${ampm}`;
                };
                document.getElementById('summaryTimeRange').textContent = `${formatTime(startTime)} - ${formatTime(endTime)}`;
            }
        }

        if (dateInput) dateInput.addEventListener('change', updateSummary);
        const startTimeSelect = document.getElementById('eventStartTime');
        const endTimeSelect = document.getElementById('eventEndTime');
        if (startTimeSelect) startTimeSelect.addEventListener('change', updateSummary);
        if (endTimeSelect) endTimeSelect.addEventListener('change', updateSummary);
    </script>
</body>
</html>

