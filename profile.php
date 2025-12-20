<?php

$userData = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'mobile' => '+1 (555) 123-4567'
];

$reservations = [
    [
        'id' => 1,
        'eventId' => 1,
        'eventName' => 'Business Innovation Summit 2024',
        'category' => 'Conference',
        'date' => 'December 25, 2024',
        'time' => '9:00 AM - 6:00 PM',
        'ticketId' => 'EVZ-A1B2C3D4',
        'quantity' => 2,
        'totalAmount' => 598,
        'status' => 'confirmed',
        'venue' => 'Grand Luxe Hotel - Grand Ballroom'
    ],
    [
        'id' => 2,
        'eventId' => 3,
        'eventName' => 'Digital Marketing Masterclass',
        'category' => 'Seminar',
        'date' => 'December 30, 2024',
        'time' => '10:00 AM - 5:00 PM',
        'ticketId' => 'EVZ-E5F6G7H8',
        'quantity' => 1,
        'totalAmount' => 149,
        'status' => 'confirmed',
        'venue' => 'Grand Luxe Hotel - Conference Hall A'
    ],
    [
        'id' => 3,
        'eventId' => 4,
        'eventName' => 'New Year\'s Eve Gala Dinner',
        'category' => 'Hotel-Hosted Events',
        'date' => 'December 31, 2024',
        'time' => '7:00 PM - 1:00 AM',
        'ticketId' => 'EVZ-I9J0K1L2',
        'quantity' => 2,
        'totalAmount' => 900,
        'status' => 'pending',
        'venue' => 'Grand Luxe Hotel - Crystal Ballroom'
    ]
];
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
                        <a class="nav-link active" href="profile.php">My Profile</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn-register" href="login.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="profile-page-section py-5 mt-5">
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
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px; color: var(--accent-olive);">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                Name
                            </div>
                            <div class="profile-info-value"><?php echo htmlspecialchars($userData['name']); ?></div>
                        </div>

                        <div class="profile-info-item mb-4">
                            <div class="profile-info-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px; color: var(--accent-olive);">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                                Email
                            </div>
                            <div class="profile-info-value"><?php echo htmlspecialchars($userData['email']); ?></div>
                        </div>

                        <div class="profile-info-item mb-4">
                            <div class="profile-info-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px; color: var(--accent-olive);">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
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
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--accent-olive); opacity: 0.5; margin-bottom: 1rem;">
                                    <path d="M9 11H1v12h8V11zM23 11H15v12h8V11z"/>
                                    <path d="M5 11V1a1 1 0 0 1 1h12a1 1 0 0 1 1v10M5 11h14"/>
                                </svg>
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
                                                
                                                <div class="mb-2">
                                                    <span class="event-category"><?php echo htmlspecialchars($reservation['category']); ?></span>
                                                </div>
                                                
                                                <div class="reservation-date mb-2">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px; color: var(--accent-olive);">
                                                        <circle cx="12" cy="12" r="10"/>
                                                        <polyline points="12 6 12 12 16 14"/>
                                                    </svg>
                                                    <span><?php echo htmlspecialchars($reservation['date']); ?></span>
                                                    <span class="text-muted ms-2"><?php echo htmlspecialchars($reservation['time']); ?></span>
                                                </div>
                                                
                                                <div class="reservation-venue text-muted small">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                        <circle cx="12" cy="10" r="3"/>
                                                    </svg>
                                                    <?php echo htmlspecialchars($reservation['venue']); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                                <div class="ticket-status mb-2">
                                                    <?php if ($reservation['status'] === 'confirmed'): ?>
                                                        <span class="status-badge status-confirmed">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                                <polyline points="22 4 12 14.01 9 11.01"/>
                                                            </svg>
                                                            Confirmed
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-pending">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <circle cx="12" cy="12" r="10"/>
                                                                <polyline points="12 6 12 12 16 14"/>
                                                            </svg>
                                                            Pending
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="ticket-details small text-muted">
                                                    <div>Qty: <?php echo $reservation['quantity']; ?></div>
                                                    <div>Total: $<?php echo number_format($reservation['totalAmount']); ?></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 text-center">
                                                <a href="confirmation.php?eventId=<?php echo $reservation['eventId']; ?>&quantity=<?php echo $reservation['quantity']; ?>&ticketId=<?php echo htmlspecialchars($reservation['ticketId']); ?>" class="btn btn-primary-luxury w-100">
                                                    View Ticket
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
    </section>

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
    <script src="assets/js/profile.js"></script>
</body>
</html>

