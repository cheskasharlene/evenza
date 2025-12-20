<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Events - EVENZA</title>
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
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="events.php">Events</a>
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
    </div>

    <div class="page-header py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="page-title mb-4">Available Events</h1>

                    <div class="search-filter-section">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <div class="search-box">
                                    <input type="text" class="form-control luxury-input" id="searchInput" placeholder="Search by event name or date...">
                                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select luxury-input" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <option value="conference">Conference</option>
                                    <option value="wedding">Wedding</option>
                                    <option value="seminar">Seminar</option>
                                    <option value="hotel-hosted">Hotel-Hosted Events</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary-luxury w-100" onclick="filterEvents()">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="events-grid-section py-5">
        <div class="container">
            <div class="row g-4" id="eventsGrid">
                <div class="col-lg-4 col-md-6" data-category="conference">
                    <div class="luxury-card event-card-grid">
                        <div class="event-image-grid">
                            <div class="image-placeholder-grid">
                                <span class="event-category-badge">Conference</span>
                            </div>
                        </div>
                        <div class="event-content-grid p-4">
                            <h3 class="event-name">Business Innovation Summit 2024</h3>
                            <div class="event-meta mb-3">
                                <span class="event-category">Conference</span>
                                <span class="event-date-time">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Dec 25, 2024 • 9:00 AM
                                </span>
                            </div>
                            <div class="event-venue mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Grand Luxe Hotel - Grand Ballroom
                            </div>
                            <div class="event-price-slot d-flex justify-content-between align-items-center mb-3">
                                <div class="event-price">
                                    <strong>$299</strong> <span class="text-muted">per person</span>
                                </div>
                                <div class="event-slots">
                                    <span class="slots-available">45 slots available</span>
                                </div>
                            </div>
                            <a href="event-details.php?id=1" class="btn btn-primary-luxury w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-category="wedding">
                    <div class="luxury-card event-card-grid">
                        <div class="event-image-grid">
                            <div class="image-placeholder-grid wedding-bg">
                                <span class="event-category-badge">Wedding</span>
                            </div>
                        </div>
                        <div class="event-content-grid p-4">
                            <h3 class="event-name">Elegant Garden Wedding</h3>
                            <div class="event-meta mb-3">
                                <span class="event-category">Wedding</span>
                                <span class="event-date-time">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Jan 10, 2025 • 4:00 PM
                                </span>
                            </div>
                            <div class="event-venue mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Grand Luxe Hotel - Garden Pavilion
                            </div>
                            <div class="event-price-slot d-flex justify-content-between align-items-center mb-3">
                                <div class="event-price">
                                    <strong>$5,500</strong> <span class="text-muted">package</span>
                                </div>
                                <div class="event-slots">
                                    <span class="slots-available">12 slots available</span>
                                </div>
                            </div>
                            <a href="event-details.php?id=2" class="btn btn-primary-luxury w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-category="seminar">
                    <div class="luxury-card event-card-grid">
                        <div class="event-image-grid">
                            <div class="image-placeholder-grid seminar-bg">
                                <span class="event-category-badge">Seminar</span>
                            </div>
                        </div>
                        <div class="event-content-grid p-4">
                            <h3 class="event-name">Digital Marketing Masterclass</h3>
                            <div class="event-meta mb-3">
                                <span class="event-category">Seminar</span>
                                <span class="event-date-time">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Dec 30, 2024 • 10:00 AM
                                </span>
                            </div>
                            <div class="event-venue mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Grand Luxe Hotel - Conference Hall A
                            </div>
                            <div class="event-price-slot d-flex justify-content-between align-items-center mb-3">
                                <div class="event-price">
                                    <strong>$149</strong> <span class="text-muted">per person</span>
                                </div>
                                <div class="event-slots">
                                    <span class="slots-available">78 slots available</span>
                                </div>
                            </div>
                            <a href="event-details.php?id=3" class="btn btn-primary-luxury w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-category="hotel-hosted">
                    <div class="luxury-card event-card-grid">
                        <div class="event-image-grid">
                            <div class="image-placeholder-grid hotel-bg">
                                <span class="event-category-badge">Hotel-Hosted</span>
                            </div>
                        </div>
                        <div class="event-content-grid p-4">
                            <h3 class="event-name">New Year's Eve Gala Dinner</h3>
                            <div class="event-meta mb-3">
                                <span class="event-category">Hotel-Hosted Events</span>
                                <span class="event-date-time">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Dec 31, 2024 • 7:00 PM
                                </span>
                            </div>
                            <div class="event-venue mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Grand Luxe Hotel - Crystal Ballroom
                            </div>
                            <div class="event-price-slot d-flex justify-content-between align-items-center mb-3">
                                <div class="event-price">
                                    <strong>$450</strong> <span class="text-muted">per person</span>
                                </div>
                                <div class="event-slots">
                                    <span class="slots-available">23 slots available</span>
                                </div>
                            </div>
                            <a href="event-details.php?id=4" class="btn btn-primary-luxury w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-category="conference">
                    <div class="luxury-card event-card-grid">
                        <div class="event-image-grid">
                            <div class="image-placeholder-grid">
                                <span class="event-category-badge">Conference</span>
                            </div>
                        </div>
                        <div class="event-content-grid p-4">
                            <h3 class="event-name">Tech Leaders Forum 2025</h3>
                            <div class="event-meta mb-3">
                                <span class="event-category">Conference</span>
                                <span class="event-date-time">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Jan 15, 2025 • 8:30 AM
                                </span>
                            </div>
                            <div class="event-venue mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Grand Luxe Hotel - Innovation Center
                            </div>
                            <div class="event-price-slot d-flex justify-content-between align-items-center mb-3">
                                <div class="event-price">
                                    <strong>$399</strong> <span class="text-muted">per person</span>
                                </div>
                                <div class="event-slots">
                                    <span class="slots-available">120 slots available</span>
                                </div>
                            </div>
                            <a href="event-details.php?id=5" class="btn btn-primary-luxury w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-category="wedding">
                    <div class="luxury-card event-card-grid">
                        <div class="event-image-grid">
                            <div class="image-placeholder-grid wedding-bg">
                                <span class="event-category-badge">Wedding</span>
                            </div>
                        </div>
                        <div class="event-content-grid p-4">
                            <h3 class="event-name">Luxury Beach Wedding</h3>
                            <div class="event-meta mb-3">
                                <span class="event-category">Wedding</span>
                                <span class="event-date-time">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    Feb 14, 2025 • 5:00 PM
                                </span>
                            </div>
                            <div class="event-venue mb-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Grand Luxe Hotel - Oceanview Terrace
                            </div>
                            <div class="event-price-slot d-flex justify-content-between align-items-center mb-3">
                                <div class="event-price">
                                    <strong>$8,500</strong> <span class="text-muted">package</span>
                                </div>
                                <div class="event-slots">
                                    <span class="slots-available">8 slots available</span>
                                </div>
                            </div>
                            <a href="event-details.php?id=6" class="btn btn-primary-luxury w-100">View Details</a>
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
    <script src="assets/js/events.js"></script>
</body>
</html>

