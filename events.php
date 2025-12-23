<?php session_start(); ?>
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
                        <a class="nav-link active" href="events.php">Events</a>
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

    <div class="page-header py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="page-title mb-4">Available Events</h1>

                    <div class="search-filter-section">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <div class="search-box">
                                    <input type="text" class="form-control luxury-input" id="searchInput" placeholder="Search by event name or venue...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select luxury-input" id="categoryFilter">
                                    <option value="all">All Categories</option>
                                    <option value="premium">Premium</option>
                                    <option value="business">Business</option>
                                    <option value="weddings">Weddings</option>
                                    <option value="socials">Socials</option>
                                    <option value="workshops">Workshops</option>
                                </select>
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
                <!-- Premium Event Card 1: Gala Evening -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="premium" data-name="Gala Evening">
                    <div class="card event-card h-100">
                                <div class="event-card-image position-relative">
                                    <img src="assets/images/event_images/galaEvening.jpg" class="card-img-top" alt="Gala Evening">
                                    <span class="badge rounded-pill position-absolute top-0 end-0 m-3">Premium</span>
                                </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Gala Evening</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Crystal Ballroom</p>
                            <a href="event-details.php?id=101" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Premium Event Card 2: Wine Tasting Experience -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="premium" data-name="Wine Tasting Experience">
                    <div class="card event-card h-100">
                        <div class="event-card-image position-relative">
                            <img src="assets/images/event_images/wineCellar.jpg" class="card-img-top" alt="Wine Tasting Experience">
                            <span class="badge rounded-pill position-absolute top-0 end-0 m-3">Premium</span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Wine Tasting Experience</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Wine Cellar</p>
                            <a href="event-details.php?id=102" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Premium Event Card 3: Art Exhibition Opening -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="premium" data-name="Art Exhibition Opening">
                    <div class="card event-card h-100">
                        <div class="event-card-image position-relative">
                            <img src="assets/images/event_images/artExhibition.jpg" class="card-img-top" alt="Art Exhibition Opening">
                            <span class="badge rounded-pill position-absolute top-0 end-0 m-3">Premium</span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Art Exhibition Opening</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Art Gallery</p>
                            <a href="event-details.php?id=103" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 1: Business Innovation Summit 2024 -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="business" data-name="Business Innovation Summit 2024">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <img src="assets/images/event_images/businessInnovation.jpg" class="card-img-top" alt="Business Innovation Summit">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Business Innovation Summit</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Grand Ballroom</p>
                            <a href="event-details.php?id=1" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 2: Elegant Garden Wedding -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="weddings" data-name="Elegant Garden Wedding">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <img src="assets/images/event_images/gardenWedding.jpg" class="card-img-top" alt="Elegant Garden Wedding">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Elegant Garden Wedding</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Garden Pavilion</p>
                            <a href="event-details.php?id=2" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 3: Digital Marketing Masterclass -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="workshops" data-name="Digital Marketing Masterclass">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder workshop-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Digital Marketing Masterclass</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Conference Hall A</p>
                            <a href="event-details.php?id=3" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 4: New Year's Eve Gala Dinner -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="socials" data-name="New Year's Eve Gala Dinner">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <img src="assets/images/event_images/nyGala.jpg" class="card-img-top" alt="New Year's Eve Gala Dinner">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">New Year's Eve Gala Dinner</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Crystal Ballroom</p>
                            <a href="event-details.php?id=4" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 5: Tech Leaders Forum 2025 -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="business" data-name="Tech Leaders Forum 2025">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder business-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Tech Leaders Forum</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Innovation Center</p>
                            <a href="event-details.php?id=5" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 6: Luxury Beach Wedding -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="weddings" data-name="Luxury Beach Wedding">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder wedding-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Luxury Beach Wedding</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Oceanview Terrace</p>
                            <a href="event-details.php?id=6" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 7: Corporate Team Building Retreat -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="business" data-name="Corporate Team Building Retreat">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder business-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Corporate Team Building Retreat</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Mountain Resort Wing</p>
                            <a href="event-details.php?id=7" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 8: Spring Wedding Collection -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="weddings" data-name="Spring Wedding Collection">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder wedding-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Spring Wedding Collection</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Grand Ballroom</p>
                            <a href="event-details.php?id=8" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 9: Professional Development Workshop -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="workshops" data-name="Professional Development Workshop">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder workshop-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Professional Development Workshop</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Conference Hall B</p>
                            <a href="event-details.php?id=9" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 10: Exclusive Members Gala -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="socials" data-name="Exclusive Members Gala">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder gala-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Exclusive Members Gala</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - VIP Lounge</p>
                            <a href="event-details.php?id=10" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 11: Leadership Summit 2025 -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="business" data-name="Leadership Summit 2025">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder business-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Leadership Summit</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Executive Center</p>
                            <a href="event-details.php?id=11" class="btn btn-event-view w-100">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 12: Advanced Skills Training -->
                <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" data-category="workshops" data-name="Advanced Skills Training">
                    <div class="card event-card h-100">
                        <div class="event-card-image">
                            <div class="image-placeholder workshop-bg"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title event-title">Advanced Skills Training</h3>
                            <p class="card-text event-venue-text">Grand Luxe Hotel - Training Center</p>
                            <a href="event-details.php?id=12" class="btn btn-event-view w-100">View Details</a>
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
                        Phone: +63-9123-456-7890<br>
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
    <script>
        // Event filtering and search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const eventCards = document.querySelectorAll('.event-card-wrapper');

            function filterEvents() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;

                eventCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    const cardName = card.getAttribute('data-name').toLowerCase();

                    const matchesSearch = cardName.includes(searchTerm);
                    const matchesCategory = selectedCategory === 'all' || cardCategory === selectedCategory;

                    if (matchesSearch && matchesCategory) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Event listeners for real-time filtering
            searchInput.addEventListener('input', filterEvents);
            categoryFilter.addEventListener('change', filterEvents);
        });
    </script>
</body>
</html>

