<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About EVENZA - Premium Event Reservation Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="navbar navbar-expand-lg navbar-light fixed-top luxury-nav">
        <div class="container">
            <a class="navbar-brand luxury-logo" href="../../index.php"><img src="../../assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">About</a>
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

    <div class="about-page-section py-5 mt-5">
        <div class="container" style="padding-top: 2rem;">
            <div class="luxury-card p-5 mb-5">
                <div class="what-is-evenza-content">
                    <h2 class="section-title text-center mb-5">What is EVENZA</h2>
                    <div class="what-is-evenza-text">
                        <p>EVENZA is a premium event reservation and ticketing platform designed to connect guests with exceptional, hotel-hosted experiences. We bring together elegant venues, seamless technology, and world-class hospitality to make every event easy to discover, reserve, and enjoy.</p>
                        <p>From conferences and seminars to weddings, galas, and exclusive social gatherings, EVENZA simplifies the entire reservation process—allowing users to explore curated events, select tailored packages, and secure tickets with confidence. Our platform is built to support both guests and event organizers, ensuring smooth management, accurate bookings, and memorable experiences from start to finish.</p>
                        <p>At EVENZA, we believe every event should feel effortless and extraordinary. We're here to transform how people experience events—where convenience meets luxury, and every moment is worth celebrating.</p>
                    </div>
                </div>
            </div>

            <div class="luxury-card p-5 mb-5">
                <h2 class="section-title text-center mb-5">How the System Works</h2>
                <p class="text-center mb-5 lead">Reserving your perfect event is simple with our streamlined 3-step process</p>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <div class="step-number">1</div>
                            <div class="step-icon mb-4">

                            </div>
                            <h4 class="step-title mb-3">Curate Your Experience</h4>
                            <p class="step-description">Explore our handpicked collection of premium events. Refine your search by category, date, or venue to find an experience perfectly tailored to your lifestyle.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <div class="step-number">2</div>
                            <div class="step-icon mb-4">

                            </div>
                            <h4 class="step-title mb-3">Secure Your Placement</h4>
                            <p class="step-description">Complete your reservation with ease through our secure, integrated payment gateway. Select your preferred tier and confirm your attendance in just a few clicks.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <div class="step-number">3</div>
                            <div class="step-icon mb-4">

                            </div>
                            <h4 class="step-title mb-3">Receive Exclusive Access</h4>
                            <p class="step-description">Instantly receive your digital pass and unique reservation details. Your confirmation will be sent via email and SMS, ensuring a seamless entry to your event.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cta-section text-center mt-5">
                <div class="luxury-card cta-card p-5">
                    <h2 class="cta-title mb-3">Ready to Experience EVENZA?</h2>
                    <p class="cta-subtitle mb-4">Join our community and discover extraordinary events at luxury hotels worldwide.</p>
                    <div class="cta-buttons<?php echo !isset($_SESSION['user_id']) ? '' : ' cta-buttons-single'; ?>">
                        <a href="events.php" class="btn btn-primary-luxury btn-lg<?php echo !isset($_SESSION['user_id']) ? ' me-3' : ''; ?>">Browse Events</a>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="register.php" class="btn btn-outline-luxury btn-lg">Create Account</a>
                        <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
</html>

