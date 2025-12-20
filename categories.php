<?php

$category = isset($_GET['category']) ? $_GET['category'] : '';

$categoryNames = [
    'conference' => 'Conference',
    'wedding' => 'Wedding',
    'seminar' => 'Seminar',
    'hotel-hosted' => 'Hotel-Hosted Events'
];

$categoryDisplayName = isset($categoryNames[$category]) ? $categoryNames[$category] : 'All Categories';
$pageTitle = $categoryDisplayName . ' Events';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - EVENZA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="navbar navbar-expand-lg navbar-light fixed-top luxury-nav">
        <div class="container">
            <a class="navbar-brand luxury-logo" href="index.php">EVENZA"
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
                        <a class="nav-link active" href="categories.php">Categories</a>
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

    <div class="category-header py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($categoryDisplayName); ?></li>
                        </ol>
                    </div>
                    <h1 class="category-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <p class="category-subtitle">Discover our curated selection of <?php echo strtolower(htmlspecialchars($categoryDisplayName)); ?> events</p>
                </div>
            </div>
        </div>
    </div>

    <div class="category-events-section py-5">
        <div class="container">
            <div class="row g-4" id="categoryEventsGrid">
                <?php

                $allEvents = [
                    [
                        'id' => 1,
                        'name' => 'Business Innovation Summit 2024',
                        'category' => 'conference',
                        'date' => 'Dec 25, 2024',
                        'time' => '9:00 AM',
                        'venue' => 'Grand Luxe Hotel - Grand Ballroom',
                        'price' => 299,
                        'priceType' => 'per person',
                        'slots' => 45,
                        'imageClass' => ''
                    ],
                    [
                        'id' => 5,
                        'name' => 'Tech Leaders Forum 2025',
                        'category' => 'conference',
                        'date' => 'Jan 15, 2025',
                        'time' => '8:30 AM',
                        'venue' => 'Grand Luxe Hotel - Innovation Center',
                        'price' => 399,
                        'priceType' => 'per person',
                        'slots' => 120,
                        'imageClass' => ''
                    ],
                    [
                        'id' => 7,
                        'name' => 'Global Finance Conference',
                        'category' => 'conference',
                        'date' => 'Feb 5, 2025',
                        'time' => '10:00 AM',
                        'venue' => 'Grand Luxe Hotel - Executive Hall',
                        'price' => 349,
                        'priceType' => 'per person',
                        'slots' => 200,
                        'imageClass' => ''
                    ],
                    [
                        'id' => 2,
                        'name' => 'Elegant Garden Wedding',
                        'category' => 'wedding',
                        'date' => 'Jan 10, 2025',
                        'time' => '4:00 PM',
                        'venue' => 'Grand Luxe Hotel - Garden Pavilion',
                        'price' => 5500,
                        'priceType' => 'package',
                        'slots' => 12,
                        'imageClass' => 'wedding-bg'
                    ],
                    [
                        'id' => 6,
                        'name' => 'Luxury Beach Wedding',
                        'category' => 'wedding',
                        'date' => 'Feb 14, 2025',
                        'time' => '5:00 PM',
                        'venue' => 'Grand Luxe Hotel - Oceanview Terrace',
                        'price' => 8500,
                        'priceType' => 'package',
                        'slots' => 8,
                        'imageClass' => 'wedding-bg'
                    ],
                    [
                        'id' => 8,
                        'name' => 'Classic Ballroom Wedding',
                        'category' => 'wedding',
                        'date' => 'Mar 20, 2025',
                        'time' => '6:00 PM',
                        'venue' => 'Grand Luxe Hotel - Crystal Ballroom',
                        'price' => 6500,
                        'priceType' => 'package',
                        'slots' => 15,
                        'imageClass' => 'wedding-bg'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Digital Marketing Masterclass',
                        'category' => 'seminar',
                        'date' => 'Dec 30, 2024',
                        'time' => '10:00 AM',
                        'venue' => 'Grand Luxe Hotel - Conference Hall A',
                        'price' => 149,
                        'priceType' => 'per person',
                        'slots' => 78,
                        'imageClass' => 'seminar-bg'
                    ],
                    [
                        'id' => 9,
                        'name' => 'Leadership Development Workshop',
                        'category' => 'seminar',
                        'date' => 'Jan 25, 2025',
                        'time' => '9:00 AM',
                        'venue' => 'Grand Luxe Hotel - Training Center',
                        'price' => 199,
                        'priceType' => 'per person',
                        'slots' => 50,
                        'imageClass' => 'seminar-bg'
                    ],
                    [
                        'id' => 10,
                        'name' => 'Entrepreneurship Bootcamp',
                        'category' => 'seminar',
                        'date' => 'Feb 18, 2025',
                        'time' => '8:30 AM',
                        'venue' => 'Grand Luxe Hotel - Business Center',
                        'price' => 249,
                        'priceType' => 'per person',
                        'slots' => 60,
                        'imageClass' => 'seminar-bg'
                    ],
                    [
                        'id' => 4,
                        'name' => 'New Year\'s Eve Gala Dinner',
                        'category' => 'hotel-hosted',
                        'date' => 'Dec 31, 2024',
                        'time' => '7:00 PM',
                        'venue' => 'Grand Luxe Hotel - Crystal Ballroom',
                        'price' => 450,
                        'priceType' => 'per person',
                        'slots' => 23,
                        'imageClass' => 'hotel-bg'
                    ],
                    [
                        'id' => 11,
                        'name' => 'Valentine\'s Day Special Dinner',
                        'category' => 'hotel-hosted',
                        'date' => 'Feb 14, 2025',
                        'time' => '7:30 PM',
                        'venue' => 'Grand Luxe Hotel - Rooftop Restaurant',
                        'price' => 350,
                        'priceType' => 'per person',
                        'slots' => 30,
                        'imageClass' => 'hotel-bg'
                    ],
                    [
                        'id' => 12,
                        'name' => 'Spring Garden Party',
                        'category' => 'hotel-hosted',
                        'date' => 'Mar 15, 2025',
                        'time' => '3:00 PM',
                        'venue' => 'Grand Luxe Hotel - Garden Pavilion',
                        'price' => 275,
                        'priceType' => 'per person',
                        'slots' => 40,
                        'imageClass' => 'hotel-bg'
                    ]
                ];

                $filteredEvents = array_filter($allEvents, function($event) use ($category) {
                    return $event['category'] === $category;
                });

                if (empty($filteredEvents)) {
                    echo '<div class="col-12"><div class="luxury-card p-5 text-center"><h3>No events found in this category</h3><p class="text-muted">Please check back later or browse our <a href="events.php">all events</a> page.</p></div></div>';
                } else {
                    foreach ($filteredEvents as $event) {
                        $categoryBadge = $categoryNames[$event['category']];
                        ?>
                        <div class="col-lg-4 col-md-6" data-category="<?php echo htmlspecialchars($event['category']); ?>">
                            <div class="luxury-card event-card-grid">
                                <div class="event-image-grid">
                                    <div class="image-placeholder-grid <?php echo htmlspecialchars($event['imageClass']); ?>">
                                        <span class="event-category-badge"><?php echo htmlspecialchars($categoryBadge); ?></span>
                                    </div>
                                </div>
                                <div class="event-content-grid p-4">
                                    <h3 class="event-name"><?php echo htmlspecialchars($event['name']); ?></h3>
                                    <div class="event-meta mb-3">
                                        <span class="event-category"><?php echo htmlspecialchars($categoryBadge); ?></span>
                                        <span class="event-date-time">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <?php echo htmlspecialchars($event['date']); ?> • <?php echo htmlspecialchars($event['time']); ?>
                                        </span>
                                    </div>
                                    <div class="event-venue mb-2">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        <?php echo htmlspecialchars($event['venue']); ?>
                                    </div>
                                    <div class="event-price-slot d-flex justify-content-between align-items-center mb-3">
                                        <div class="event-price">
                                            <strong>$<?php echo number_format($event['price']); ?></strong> 
                                            <span class="text-muted"><?php echo htmlspecialchars($event['priceType']); ?></span>
                                        </div>
                                        <div class="event-slots">
                                            <span class="slots-available"><?php echo htmlspecialchars($event['slots']); ?> slots available</span>
                                        </div>
                                    </div>
                                    <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-primary-luxury w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
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

