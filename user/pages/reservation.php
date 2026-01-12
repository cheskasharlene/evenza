<?php
session_start();
require_once '../../core/connect.php';

$success_message = '';
$error_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $session_error = $_SESSION['error_message'];
    if (strpos($session_error, 'payment confirmation') === false && strpos($session_error, 'Invalid or expired') === false) {
        $error_message = $session_error;
    }
    unset($_SESSION['error_message']);
}

$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : 0;

$packages = [];
$packagesQuery = "SELECT packageId, packageName, price FROM packages ORDER BY packageId ASC";
$packagesResult = mysqli_query($conn, $packagesQuery);
if ($packagesResult) {
    while ($row = mysqli_fetch_assoc($packagesResult)) {
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

$event = null;
if ($eventId > 0) {
    $eventQuery = "SELECT eventId, title, category, venue, description FROM events WHERE eventId = ?";
    $eventStmt = mysqli_prepare($conn, $eventQuery);
    if ($eventStmt) {
        mysqli_stmt_bind_param($eventStmt, "i", $eventId);
        mysqli_stmt_execute($eventStmt);
        $eventResult = mysqli_stmt_get_result($eventStmt);
        $eventRow = mysqli_fetch_assoc($eventResult);
        mysqli_stmt_close($eventStmt);
        
        if ($eventRow) {
            $event = [
                'name' => $eventRow['title'],
                'category' => $eventRow['category'] ?? '',
                'date' => 'Date TBA',
                'time' => 'Time TBA',
                'venue' => $eventRow['venue'] ?? 'Venue TBA',
                'description' => $eventRow['description'] ?? ''
            ];
        }
    }
}

if (!$event) {
    header('Location: events.php');
    exit;
}

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

$selectedPackageId = isset($_POST['packageId']) ? intval($_POST['packageId']) : (isset($_GET['packageId']) ? intval($_GET['packageId']) : ($packages[0]['id'] ?? 1));
$selectedPackage = $packages[0] ?? ['id' => 1, 'name' => 'Bronze Package', 'tier' => 'Bronze', 'price' => 7000];
foreach ($packages as $p) {
    if ($p['id'] == $selectedPackageId) {
        $selectedPackage = $p;
        break;
    }
}

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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar navbar-expand-lg navbar-light fixed-top luxury-nav">
        <div class="container">
            <a class="navbar-brand luxury-logo" href="../index.php"><img src="../assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item nav-divider">
                        <span class="nav-separator"></span>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">My Profile</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register" href="../process/logout.php?type=user">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
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
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                    <li class="breadcrumb-item"><a href="eventDetails.php?id=<?php echo $eventId; ?>">Event Details</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reservation</li>
                </ol>
            </div>

            <div class="reservation-layout">
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
                        
                        <form id="reservationForm" method="POST" action="../process/reserve.php">
                            <input type="hidden" name="eventId" value="<?php echo $eventId; ?>">
                            <input type="hidden" name="packageId" id="packageId" value="<?php echo $selectedPackage['id']; ?>">
                            <input type="hidden" name="packageTier" id="packageTier" value="<?php echo htmlspecialchars($selectedPackage['tier']); ?>">
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
                                <input type="tel" class="form-control luxury-input" id="mobile" name="mobile" required placeholder="0921 123 4567">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="reservationDate" class="form-label">Preferred Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control luxury-input" id="reservationDate" name="reservationDate" required style="accent-color: #4A5D4E;">
                                </div>
                                <div class="col-md-4">
                                    <label for="eventStartTime" class="form-label">Event Start Time <span class="text-danger">*</span></label>
                                    <div class="custom-dropdown-wrapper">
                                        <select class="form-select luxury-input" id="eventStartTime" name="eventStartTime" required style="display: none;">
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
                                        <div class="custom-dropdown" id="customEventStartTime">
                                            <div class="custom-dropdown-selected">
                                                <span>Select start time</span>
                                                <i class="fas fa-chevron-down"></i>
                                            </div>
                                            <div class="custom-dropdown-options">
                                                <div class="custom-dropdown-option" data-value="">Select start time</div>
                                                <div class="custom-dropdown-option" data-value="08:00 AM">8:00 AM</div>
                                                <div class="custom-dropdown-option" data-value="09:00 AM">9:00 AM</div>
                                                <div class="custom-dropdown-option" data-value="10:00 AM">10:00 AM</div>
                                                <div class="custom-dropdown-option" data-value="11:00 AM">11:00 AM</div>
                                                <div class="custom-dropdown-option" data-value="12:00 PM">12:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="01:00 PM">1:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="02:00 PM">2:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="03:00 PM">3:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="04:00 PM">4:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="05:00 PM">5:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="06:00 PM">6:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="07:00 PM">7:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="08:00 PM">8:00 PM</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="eventEndTime" class="form-label">Event End Time <span class="text-danger">*</span></label>
                                    <div class="custom-dropdown-wrapper">
                                        <select class="form-select luxury-input" id="eventEndTime" name="eventEndTime" required style="display: none;">
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
                                        <div class="custom-dropdown" id="customEventEndTime">
                                            <div class="custom-dropdown-selected">
                                                <span>Select end time</span>
                                                <i class="fas fa-chevron-down"></i>
                                            </div>
                                            <div class="custom-dropdown-options">
                                                <div class="custom-dropdown-option" data-value="">Select end time</div>
                                                <div class="custom-dropdown-option" data-value="09:00 AM">9:00 AM</div>
                                                <div class="custom-dropdown-option" data-value="10:00 AM">10:00 AM</div>
                                                <div class="custom-dropdown-option" data-value="11:00 AM">11:00 AM</div>
                                                <div class="custom-dropdown-option" data-value="12:00 PM">12:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="01:00 PM">1:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="02:00 PM">2:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="03:00 PM">3:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="04:00 PM">4:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="05:00 PM">5:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="06:00 PM">6:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="07:00 PM">7:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="08:00 PM">8:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="09:00 PM">9:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="10:00 PM">10:00 PM</div>
                                                <div class="custom-dropdown-option" data-value="11:00 PM">11:00 PM</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Select Package <span class="text-danger">*</span></label>
                                <div class="package-options d-flex gap-2 mt-2 flex-wrap" id="packageOptions">
                                    <?php foreach ($packages as $p): ?>
                                        <div class="package-tile" role="button" tabindex="0" data-id="<?php echo $p['id']; ?>" data-tier="<?php echo htmlspecialchars($p['tier']); ?>" data-name="<?php echo htmlspecialchars($p['name']); ?>" data-price="<?php echo $p['price']; ?>">
                                            <div class="package-tile-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                            <div class="package-tile-price">₱ <?php echo number_format($p['price'], 2); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted d-block mt-2">Choose a package to reserve the event as a single purchase.</small>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <a href="eventDetails.php?id=<?php echo $eventId; ?>" class="btn btn-outline-luxury flex-fill">Back to Event</a>
                                <button type="submit" class="btn btn-primary-luxury flex-fill">Submit Reservation</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="reservation-summary-column">
                    <div class="luxury-card reservation-summary p-4 sticky-summary">
                        <h4 class="mb-4">Reservation Summary</h4>
                        
                        <div class="summary-item mb-3">
                            <div class="summary-label">Event Name</div>
                            <div class="summary-value"><?php echo htmlspecialchars($event['name']); ?></div>
                        </div>

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
                                Your reservation will be submitted for admin confirmation. Payment will be available once confirmed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="luxury-footer py-5">
        <div class="container">
            <div>
                <div>
                    <h5 class="footer-logo">EVENZA</h5>
                    <p class="footer-text">EVENZA is a premier event reservation platform dedicated to seamless experiences. Elevate your occasions with our curated venues and sophisticated planning tools.</p>
                </div>
                <div>
                    <h6 class="footer-heading">Contact Info</h6>
                    <p class="footer-text">
                        Email: <a href="mailto:evenzacompany@gmail.com">evenzacompany@gmail.com</a><br>
                        Phone: 09916752007<br>
                        Address: Ambulong, Tanauan City, Batangas.
                    </p>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="text-center">
                <p class="footer-copyright">&copy; 2026 EVENZA</p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reservationSuccessModal" tabindex="-1" aria-labelledby="reservationSuccessModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="success-icon-wrapper mx-auto mb-4" style="width: 80px; height: 80px; background: linear-gradient(135deg, #4A5D4A 0%, #6B8E6B 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="white" viewBox="0 0 16 16">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                        </svg>
                    </div>
                    <h3 class="mb-3" style="font-family: 'Playfair Display', serif;">Reservation Submitted!</h3>
                    <p class="text-muted mb-4">Your reservation has been successfully submitted and is now awaiting admin confirmation.</p>
                    <p class="small text-muted mb-4">Once confirmed, you will be able to proceed with payment from your profile page.</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="profile.php" class="btn btn-primary-luxury">View My Reservations</a>
                        <a href="events.php" class="btn btn-outline-luxury">Browse More Events</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        // Check if reservation was successful and show modal
        <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const successModal = new bootstrap.Modal(document.getElementById('reservationSuccessModal'));
            successModal.show();
        });
        <?php endif; ?>
    </script>
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

        // Set minimum date to today - moved to DOMContentLoaded

        // Initialize custom dropdowns for time selection
        function initCustomTimeDropdown(selectId, customDropdownId) {
            const nativeSelect = document.getElementById(selectId);
            const customDropdown = document.getElementById(customDropdownId);
            if (!nativeSelect || !customDropdown) return;
            
            const selectedText = customDropdown.querySelector('.custom-dropdown-selected span');
            const options = customDropdown.querySelectorAll('.custom-dropdown-option');
            
            // Set initial selected value
            const initialValue = nativeSelect.value;
            options.forEach(opt => {
                if (opt.getAttribute('data-value') === initialValue) {
                    opt.classList.add('selected');
                    if (initialValue) {
                        selectedText.textContent = opt.textContent;
                    }
                }
            });
            
            // Toggle dropdown
            customDropdown.querySelector('.custom-dropdown-selected').addEventListener('click', function(e) {
                e.stopPropagation();
                customDropdown.classList.toggle('open');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!customDropdown.contains(e.target)) {
                    customDropdown.classList.remove('open');
                }
            });
            
            // Handle option selection
            options.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;
                    
                    // Update native select
                    nativeSelect.value = value;
                    
                    // Update custom dropdown
                    options.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedText.textContent = text;
                    
                    // Close dropdown
                    customDropdown.classList.remove('open');
                    
                    // Trigger change event for form updates
                    const changeEvent = new Event('change', { bubbles: true });
                    nativeSelect.dispatchEvent(changeEvent);
                });
            });
        }

        // Format date for display
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString + 'T00:00:00');
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Update reservation summary in real-time
        function updateReservationSummary() {
            const dateInput = document.getElementById('reservationDate');
            const dateValue = dateInput ? dateInput.value : '';
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
            
            // Ensure no package is selected by default
            packageTiles.forEach(t => t.classList.remove('selected'));
            
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

        // Wait for DOM to be ready before initializing
        document.addEventListener('DOMContentLoaded', function() {
            // Set minimum date to today
            const dateInput = document.getElementById('reservationDate');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.setAttribute('min', today);
            }
            
            // Get time select elements and custom dropdowns
            const startTimeSelect = document.getElementById('eventStartTime');
            const endTimeSelect = document.getElementById('eventEndTime');
            const customStartTime = document.getElementById('customEventStartTime');
            const customEndTime = document.getElementById('customEventEndTime');
            
            // Initialize time dropdowns
            initCustomTimeDropdown('eventStartTime', 'customEventStartTime');
            initCustomTimeDropdown('eventEndTime', 'customEventEndTime');
            
            // Initialize package selection
            setupPackageSelectionListeners();

            // Add event listeners for real-time updates
            if (dateInput) {
                dateInput.addEventListener('change', updateReservationSummary);
            }
            if (startTimeSelect) {
                startTimeSelect.addEventListener('change', updateReservationSummary);
            }
            if (endTimeSelect) {
                endTimeSelect.addEventListener('change', updateReservationSummary);
            }

            // Validate form on submission with dynamic validation
            const reservationForm = document.getElementById('reservationForm');
            if (reservationForm) {
                reservationForm.addEventListener('submit', function(e) {
            const dateValue = dateInput.value;
            const startTimeValue = startTimeSelect ? startTimeSelect.value : '';
            const endTimeValue = endTimeSelect ? endTimeSelect.value : '';
            let hasErrors = false;

            // Remove previous error states
            dateInput.classList.remove('is-invalid');
            
            startTimeSelect.classList.remove('is-invalid');
            endTimeSelect.classList.remove('is-invalid');
            if (customStartTime) {
                customStartTime.querySelector('.custom-dropdown-selected').classList.remove('is-invalid');
            }
            if (customEndTime) {
                customEndTime.querySelector('.custom-dropdown-selected').classList.remove('is-invalid');
            }

            // Validate date
            if (!dateValue) {
                e.preventDefault();
                dateInput.classList.add('is-invalid');
                hasErrors = true;
            }

            // Validate start time
            if (!startTimeValue) {
                e.preventDefault();
                if (startTimeSelect) {
                    startTimeSelect.classList.add('is-invalid');
                }
                if (customStartTime) {
                    customStartTime.querySelector('.custom-dropdown-selected').classList.add('is-invalid');
                }
                hasErrors = true;
            }

            // Validate end time
            if (!endTimeValue) {
                e.preventDefault();
                if (endTimeSelect) {
                    endTimeSelect.classList.add('is-invalid');
                }
                if (customEndTime) {
                    customEndTime.querySelector('.custom-dropdown-selected').classList.add('is-invalid');
                }
                hasErrors = true;
            }

            if (hasErrors) {
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
                dateInput.classList.add('is-invalid');
                dateInput.focus();
                return false;
            }

            // Clear error states if validation passes
            dateInput.classList.remove('is-invalid');
            startTimeSelect.classList.remove('is-invalid');
            endTimeSelect.classList.remove('is-invalid');
            if (customStartTime) {
                customStartTime.querySelector('.custom-dropdown-selected').classList.remove('is-invalid');
            }
            if (customEndTime) {
                customEndTime.querySelector('.custom-dropdown-selected').classList.remove('is-invalid');
            }
                });
            }

            // Clear error states when user interacts with fields
            if (dateInput) {
                dateInput.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            }
            
            // Clear error states when time dropdowns are changed
            if (startTimeSelect) {
                startTimeSelect.addEventListener('change', function() {
                    this.classList.remove('is-invalid');
                    if (customStartTime) {
                        customStartTime.querySelector('.custom-dropdown-selected').classList.remove('is-invalid');
                    }
                });
            }
            
            if (endTimeSelect) {
                endTimeSelect.addEventListener('change', function() {
                    this.classList.remove('is-invalid');
                    if (customEndTime) {
                        customEndTime.querySelector('.custom-dropdown-selected').classList.remove('is-invalid');
                    }
                });
            }
        });
    </script>
    <script src="../assets/js/reservation.js"></script>
</body>
</html>

