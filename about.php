<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About EVENZA - Premium Event Reservation Platform</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Serif + Sans-serif pairing -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
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
                        <a class="nav-link active" href="about.php">About</a>
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

    <!-- About Section -->
    <section class="about-page-section py-5 mt-5">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header text-center mb-5">
                <h1 class="page-title">About EVENZA</h1>
                <p class="page-subtitle">Your trusted partner for premium event reservations</p>
            </div>

            <!-- What is EVENZA -->
            <div class="luxury-card p-5 mb-5">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h2 class="section-title mb-4">What is EVENZA</h2>
                        <p class="lead">EVENZA is a premium event reservation and ticketing platform designed to connect discerning guests with exclusive hotel-hosted events.</p>
                        <p>We specialize in curating exceptional experiences, from elegant conferences and intimate seminars to luxurious weddings and exclusive gala dinners. Our platform brings together the finest venues, world-class hospitality, and seamless reservation technology to create unforgettable moments.</p>
                        <p>At EVENZA, we believe that every event should be extraordinary. That's why we partner exclusively with luxury hotels and premium venues to offer you access to the most sought-after gatherings in the industry.</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-image-placeholder">
                            <div class="image-placeholder-about">
                                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.3;">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How the System Works -->
            <div class="luxury-card p-5 mb-5">
                <h2 class="section-title text-center mb-5">How the System Works</h2>
                <p class="text-center mb-5 lead">Reserving your perfect event is simple with our streamlined 3-step process</p>
                
                <div class="row g-4">
                    <!-- Step 1 -->
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <div class="step-number">1</div>
                            <div class="step-icon mb-4">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <h4 class="step-title mb-3">Browse & Select</h4>
                            <p class="step-description">Explore our curated collection of premium events. Filter by category, date, or venue to find the perfect experience that matches your preferences.</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <div class="step-number">2</div>
                            <div class="step-icon mb-4">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </div>
                            <h4 class="step-title mb-3">Reserve & Pay</h4>
                            <p class="step-description">Complete your reservation with ease. Enter your details, select ticket quantity, and securely process payment through our integrated PayPal system.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="col-md-4">
                        <div class="process-step text-center">
                            <div class="step-number">3</div>
                            <div class="step-icon mb-4">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <h4 class="step-title mb-3">Receive Confirmation</h4>
                            <p class="step-description">Get instant confirmation with your unique ticket ID and QR code. You'll receive email and SMS notifications with all event details and important information.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Benefits Section -->
            <div class="benefits-section">
                <div class="row g-4">
                    <!-- Benefits for Guests -->
                    <div class="col-lg-6">
                        <div class="luxury-card p-5 h-100">
                            <div class="benefits-header mb-4">
                                <div class="benefits-icon mb-3">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <h3 class="benefits-title">Benefits for Guests</h3>
                            </div>
                            <ul class="benefits-list">
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Exclusive Access:</strong> Discover and reserve premium events at luxury hotels that aren't available elsewhere.</span>
                                </li>
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Seamless Booking:</strong> Simple 3-step reservation process with secure payment and instant confirmation.</span>
                                </li>
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Digital Tickets:</strong> Receive QR code tickets instantly via email and SMS for easy event entry.</span>
                                </li>
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>24/7 Support:</strong> Access to AI assistant and customer support whenever you need assistance.</span>
                                </li>
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Event Management:</strong> View all your reservations in one place with easy access to tickets and details.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Benefits for Hotels -->
                    <div class="col-lg-6">
                        <div class="luxury-card p-5 h-100">
                            <div class="benefits-header mb-4">
                                <div class="benefits-icon mb-3">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        <polyline points="9 22 9 12 15 12 15 22"/>
                                    </svg>
                                </div>
                                <h3 class="benefits-title">Benefits for Hotels</h3>
                            </div>
                            <ul class="benefits-list">
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Increased Visibility:</strong> Showcase your events to a targeted audience of premium event seekers.</span>
                                </li>
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Streamlined Reservations:</strong> Automated booking system reduces administrative workload and errors.</span>
                                </li>
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Secure Payments:</strong> Integrated PayPal processing ensures reliable and secure payment collection.</span>
                                </li>
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Real-time Analytics:</strong> Track reservations, attendance, and revenue through comprehensive dashboard insights.</span>
                                </li>
                                <li>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <span><strong>Brand Partnership:</strong> Join an exclusive network of luxury hotels and enhance your brand reputation.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="cta-section text-center mt-5">
                <div class="luxury-card cta-card p-5">
                    <h2 class="cta-title mb-3">Ready to Experience EVENZA?</h2>
                    <p class="cta-subtitle mb-4">Join our community and discover extraordinary events at luxury hotels worldwide.</p>
                    <div class="cta-buttons">
                        <a href="events.php" class="btn btn-primary-luxury btn-lg me-3">Browse Events</a>
                        <a href="register.php" class="btn btn-outline-luxury btn-lg">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>

