<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userData = [
    'name' => $_SESSION['user_name'] ?? 'User',
    'email' => $_SESSION['user_email'] ?? '',
    'mobile' => $_SESSION['user_mobile'] ?? ''
];

$reservations = [];
$userId = $_SESSION['user_id'];
$query = "
    SELECT r.*, 
           e.title as eventName, 
           e.venue, 
           p.packageName,
           r.reservationDate as date,
           CONCAT(COALESCE(r.startTime, ''), ' - ', COALESCE(r.endTime, '')) as time
    FROM reservations r
    LEFT JOIN events e ON r.eventId = e.eventId
    LEFT JOIN packages p ON r.packageId = p.packageId
    WHERE r.userId = ?
    ORDER BY r.reservationDate DESC, r.createdAt DESC
";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
    
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - EVENZA</title>
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
                            <a class="nav-link active" href="profile.php">My Profile</a>
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

    <div class="profile-page-section py-5 mt-5">
        <div class="container">
            <div class="page-header mb-5">
                <h1 class="page-title">My Profile</h1>
                <p class="page-subtitle">Manage your account and view your reservations</p>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="luxury-card p-4">
                        <h3 class="mb-4">Profile Information</h3>

                        <div class="profile-info-item mb-4">
                            <div class="profile-info-label">
                                Name
                            </div>
                            <div class="profile-info-value"><?php echo htmlspecialchars($userData['name']); ?></div>
                        </div>

                        <div class="profile-info-item mb-4">
                            <div class="profile-info-label">
                                Email
                            </div>
                            <div class="profile-info-value"><?php echo htmlspecialchars($userData['email']); ?></div>
                        </div>

                        <div class="profile-info-item mb-4">
                            <div class="profile-info-label">
                                Mobile Number
                            </div>
                            <div class="profile-info-value"><?php echo htmlspecialchars($userData['mobile']); ?></div>
                        </div>

                        <button type="button" class="btn btn-outline-luxury w-100 mt-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            Edit Profile
                        </button>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="luxury-card p-4">
                        <h3 class="mb-4">My Reservations</h3>
                        
                        <?php if (empty($reservations)): ?>
                            <div class="text-center py-5">
                                <p class="text-muted">You don't have any reservations yet.</p>
                                <a href="events.php" class="btn btn-primary-luxury mt-3">Browse Events</a>
                            </div>
                        <?php else: ?>
                            <div class="reservations-list">
                                <?php foreach ($reservations as $reservation): ?>
                                    <div class="reservation-item luxury-card p-4 mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <h5 class="reservation-event-name mb-2"><?php echo htmlspecialchars($reservation['eventName']); ?></h5>
                                                
                                                <!-- category removed -->
                                                
                                                <div class="reservation-date mb-2">
                                                    <?php if (!empty($reservation['date'])): ?>
                                                        <span><?php echo date('F j, Y', strtotime($reservation['date'])); ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($reservation['time']) && $reservation['time'] !== ' - '): ?>
                                                        <span class="text-muted ms-2"><?php echo htmlspecialchars($reservation['time']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="reservation-venue text-muted small">
                                                    <?php echo htmlspecialchars($reservation['venue']); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                                <div class="ticket-status mb-2">
                                                    <?php if ($reservation['status'] === 'confirmed'): ?>
                                                        <span class="status-badge status-confirmed">
                                                            Confirmed
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-pending">
                                                            Pending
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="ticket-details small text-muted">
                                                    <?php if (isset($reservation['packageName'])): ?>
                                                        <div>Package: <?php echo htmlspecialchars($reservation['packageName']); ?></div>
                                                    <?php else: ?>
                                                        <div>Qty: <?php echo htmlspecialchars($reservation['quantity'] ?? 1); ?></div>
                                                    <?php endif; ?>
                                                    <div>Total: ₱ <?php echo number_format($reservation['totalAmount'] ?? 0, 2); ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 text-center">
                                                <a href="profile.php?viewReservation=<?php echo $reservation['reservationId']; ?>" class="btn btn-primary-luxury w-100">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content luxury-card">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control luxury-input" id="editName" value="<?php echo htmlspecialchars($userData['name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control luxury-input" id="editEmail" value="<?php echo htmlspecialchars($userData['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editMobile" class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control luxury-input" id="editMobile" value="<?php echo htmlspecialchars($userData['mobile']); ?>">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-luxury" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-luxury" onclick="saveProfile()">Save Changes</button>
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
    <script src="assets/js/profile.js"></script>
</body>
</html>

