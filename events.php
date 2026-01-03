<?php 
session_start();
require_once 'connect.php';

function getEventImagePath($imagePath) {
    $imageDir = 'assets/images/event_images/';
    $placeholder = $imageDir . 'placeholder.jpg';
    
    if (empty($imagePath)) {
        return $placeholder;
    }
    
    $imagePath = ltrim($imagePath, '/\\');
    
    if (strpos($imagePath, $imageDir) !== 0) {
        $filename = basename($imagePath);
        $filename = str_replace(['/', '\\'], '', $filename);
        $imagePath = $imageDir . $filename;
    }
    
    if (file_exists($imagePath)) {
        return $imagePath;
    }
    
    return $placeholder;
}

$events = [];
$errorMessage = '';

$query = "SELECT eventId, title, venue, category, imagePath, description FROM events ORDER BY eventId DESC";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $eventId = isset($row['eventId']) ? $row['eventId'] : 0;
        
        $event = [
            'eventId' => $eventId,
            'id' => $eventId,
            'title' => isset($row['title']) ? $row['title'] : '',
            'venue' => isset($row['venue']) ? $row['venue'] : '',
            'category' => isset($row['category']) ? $row['category'] : '',
            'imagePath' => isset($row['imagePath']) ? $row['imagePath'] : '',
            'status' => 'Active',
            'description' => isset($row['description']) ? $row['description'] : ''
        ];
        
        $originalImagePath = $event['imagePath'];
        $event['imagePath'] = getEventImagePath($event['imagePath']);
        
        if (stripos($event['title'], 'wine') !== false && !file_exists($event['imagePath']) && $event['imagePath'] === 'assets/images/event_images/placeholder.jpg') {
            // Try wineCellar.jpg if the original path doesn't work
            if (file_exists('assets/images/event_images/wineCellar.jpg')) {
                $event['imagePath'] = 'assets/images/event_images/wineCellar.jpg';
            }
        }
        
        $events[] = $event;
    }
    mysqli_free_result($result);
} else {
    $events = [];
    $errorMessage = mysqli_error($conn);
}

function getCategoryFilter($category) {
    $category = trim($category);
    $categoryLower = strtolower($category);
    
    $categoryMap = [
        'premium' => 'premium',
        'conference' => 'business',
        'business' => 'business',
        'wedding' => 'weddings',
        'weddings' => 'weddings',
        'seminar' => 'workshops',
        'workshop' => 'workshops',
        'workshops' => 'workshops',
        'social' => 'socials',
        'socials' => 'socials',
        'hotel-hosted events' => 'socials',
        'hotel-hosted' => 'socials'
    ];
    
    if (isset($categoryMap[$categoryLower])) {
        return $categoryMap[$categoryLower];
    }
    
    if (stripos($category, 'wedding') !== false) {
        return 'weddings';
    }
    if (stripos($category, 'workshop') !== false || stripos($category, 'seminar') !== false || 
        stripos($category, 'training') !== false || stripos($category, 'masterclass') !== false) {
        return 'workshops';
    }
    if (stripos($category, 'social') !== false || stripos($category, 'gala') !== false) {
        return 'socials';
    }
    if (stripos($category, 'premium') !== false || stripos($category, 'exhibition') !== false || 
        stripos($category, 'tasting') !== false) {
        return 'premium';
    }
    if (stripos($category, 'business') !== false || stripos($category, 'conference') !== false) {
        return 'business';
    }
    
    return 'all';
}
?>
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
                <?php if (!empty($errorMessage)): ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <p><strong>Database Error:</strong> <?php echo htmlspecialchars($errorMessage); ?></p>
                            <p class="small">Please check your database connection and ensure the events table exists.</p>
                        </div>
                    </div>
                <?php elseif (empty($events)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <p>No events available at this time.</p>
                            <p class="small">Please add events through the admin dashboard.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php 
                    // Debug output in HTML comments (view page source to see)
                    echo "<!-- DEBUG: Total events fetched: " . count($events) . " -->\n";
                    foreach ($events as $debugEvent) {
                        echo "<!-- Event: " . htmlspecialchars($debugEvent['title']) . " | Category: " . htmlspecialchars($debugEvent['category'] ?? 'N/A') . " | Status: " . (isset($debugEvent['status']) ? htmlspecialchars($debugEvent['status']) : 'N/A') . " | Filter Category: " . getCategoryFilter($debugEvent['category'] ?? '') . " -->\n";
                    }
                    ?>
                    <?php foreach ($events as $event): 
                        $categoryFilter = getCategoryFilter($event['category']);
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" 
                             data-category="<?php echo htmlspecialchars($categoryFilter); ?>" 
                             data-name="<?php echo htmlspecialchars($event['title']); ?>">
                            <div class="card event-card h-100">
                                <div class="event-card-image">
                                    <img src="<?php echo htmlspecialchars($event['imagePath']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($event['title']); ?>"
                                         onerror="this.src='assets/images/event_images/placeholder.jpg'">
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p class="card-text event-venue-text"><?php echo htmlspecialchars($event['venue']); ?></p>
                                    <a href="eventDetails.php?id=<?php echo $event['id'] ?? $event['eventId']; ?>" class="btn btn-event-view w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const selectedCategory = categoryFilter ? categoryFilter.value : 'all';

                eventCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    const cardName = card.getAttribute('data-name') ? card.getAttribute('data-name').toLowerCase() : '';

                    const matchesSearch = !searchTerm || cardName.includes(searchTerm);
                    const matchesCategory = selectedCategory === 'all' || cardCategory === selectedCategory;

                    if (matchesSearch && matchesCategory) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Initialize: show all events on page load
            if (eventCards.length > 0) {
                eventCards.forEach(card => {
                    card.style.display = '';
                });
            }

            // Event listeners for real-time filtering
            if (searchInput) {
                searchInput.addEventListener('input', filterEvents);
            }
            if (categoryFilter) {
                categoryFilter.addEventListener('change', filterEvents);
            }
        });
    </script>
</body>
</html>

