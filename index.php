<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVENZA - Premium Event Reservation & Ticketing</title>
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
                        <a class="nav-link active" href="index.php">Home</a>
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

    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-8 col-xl-7">
                    <div class="hero-content">
                        <h1 class="hero-title">EVENZA</h1>
                        <p class="hero-subtitle">Reserve Hotel-Hosted Events with Ease</p>
                        <div class="hero-buttons mt-4">
                            <a href="events.php" class="btn btn-primary-luxury btn-lg">Explore Events</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="featured-events py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Featured Events</h2>
                <p class="section-subtitle">Curated selections for the discerning attendee</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="luxury-card event-card">
                        <div class="event-image">
                            <div class="image-placeholder">
                                <span class="event-badge">Premium</span>
                            </div>
                        </div>
                        <div class="event-content p-4">
                            <h3 class="event-title">Gala Evening</h3>
                            <p class="event-date">December 15, 2024</p>
                            <p class="event-description">An elegant evening of fine dining and entertainment in an exclusive setting.</p>
                            <a href="#" class="btn btn-sm btn-primary-luxury mt-3">Reserve Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="luxury-card event-card">
                        <div class="event-image">
                            <div class="image-placeholder">
                                <span class="event-badge">Premium</span>
                            </div>
                        </div>
                        <div class="event-content p-4">
                            <h3 class="event-title">Wine Tasting</h3>
                            <p class="event-date">December 20, 2024</p>
                            <p class="event-description">Discover rare vintages in an intimate tasting experience.</p>
                            <a href="#" class="btn btn-sm btn-primary-luxury mt-3">Reserve Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="luxury-card event-card">
                        <div class="event-image">
                            <div class="image-placeholder">
                                <span class="event-badge">Premium</span>
                            </div>
                        </div>
                        <div class="event-content p-4">
                            <h3 class="event-title">Art Exhibition</h3>
                            <p class="event-date">January 5, 2025</p>
                            <p class="event-description">Private viewing of contemporary masterpieces.</p>
                            <a href="#" class="btn btn-sm btn-primary-luxury mt-3">Reserve Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cta-section py-5">
        <div class="container">
            <div class="luxury-card cta-card text-center p-5">
                <h2 class="cta-title mb-3">Ready to Reserve Your Experience?</h2>
                <p class="cta-subtitle mb-4">Join our exclusive community and gain access to premium events worldwide.</p>
                <a href="register.php" class="btn btn-primary-luxury btn-lg">Create Account</a>
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
</body>
</html>

