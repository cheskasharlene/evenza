<?php 
session_start();
require_once 'connect.php';

// Helper function to validate and fix image paths with fallback to placeholder
function getEventImagePath($imagePath) {
    $imageDir = 'assets/images/event_images/';
    $placeholder = $imageDir . 'placeholder.jpg';
    
    // If imagePath is empty or null, use placeholder
    if (empty($imagePath)) {
        return $placeholder;
    }
    
    // If imagePath doesn't start with the directory, prepend it
    if (strpos($imagePath, $imageDir) !== 0) {
        // Extract just the filename if full path is provided
        $filename = basename($imagePath);
        $imagePath = $imageDir . $filename;
    }
    
    // Check if file exists, otherwise use placeholder
    if (file_exists($imagePath)) {
        return $imagePath;
    }
    
    // Return placeholder as fallback
    return $placeholder;
}

// Fetch all active events from database
$events = [];
$errorMessage = '';

try {
    // First, check if status column exists by trying to select it
    // If it fails, we'll select without it
    try {
        $stmt = $pdo->query("SELECT eventId, title, venue, category, imagePath, status, description FROM events ORDER BY eventId");
        $allEvents = $stmt->fetchAll();
        
        // Filter events by status (case-insensitive) - show active events, or all if none are marked active
        $activeEvents = array_filter($allEvents, function($event) {
            $status = isset($event['status']) ? strtolower(trim($event['status'])) : '';
            return $status === 'active' || empty($status);
        });
        
        // If we have active events, use them; otherwise show all events
        if (!empty($activeEvents)) {
            $events = array_values($activeEvents);
        } else {
            $events = $allEvents;
        }
    } catch(PDOException $e) {
        // If status column doesn't exist, select without it
        $stmt = $pdo->query("SELECT eventId, title, venue, category, imagePath, description FROM events ORDER BY eventId");
        $events = $stmt->fetchAll();
    }
    
    // Process image paths for each event
    foreach ($events as &$event) {
        $event['imagePath'] = getEventImagePath($event['imagePath'] ?? '');
        // Ensure status is set for filtering (default to 'active' if column doesn't exist)
        if (!isset($event['status']) || empty($event['status'])) {
            $event['status'] = 'active';
        }
    }
    unset($event); // Break reference
} catch(PDOException $e) {
    $events = [];
    $errorMessage = $e->getMessage();
    // Log error in production: error_log($e->getMessage());
}

function getCategoryFilter($category) {
    $categoryMap = [
        'Premium' => 'premium',
        'Conference' => 'business',
        'Business' => 'business',
        'Wedding' => 'weddings',
        'Seminar' => 'workshops',
        'Workshop' => 'workshops',
        'Social' => 'socials',
        'Hotel-Hosted Events' => 'socials'
    ];
    return isset($categoryMap[$category]) ? $categoryMap[$category] : 'all';
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
                    <?php foreach ($events as $event): 
                        $categoryFilter = getCategoryFilter($event['category']);
                        $isPremium = ($event['category'] === 'Premium');
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4 event-card-wrapper" 
                             data-category="<?php echo htmlspecialchars($categoryFilter); ?>" 
                             data-name="<?php echo htmlspecialchars($event['title']); ?>">
                            <div class="card event-card h-100">
                                <div class="event-card-image <?php echo $isPremium ? 'position-relative' : ''; ?>">
                                    <img src="<?php echo htmlspecialchars($event['imagePath']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($event['title']); ?>"
                                         onerror="this.src='assets/images/event_images/placeholder.jpg'">
                                    <?php if ($isPremium): ?>
                                        <span class="badge rounded-pill position-absolute top-0 end-0 m-3">Premium</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p class="card-text event-venue-text"><?php echo htmlspecialchars($event['venue']); ?></p>
                                    <a href="eventDetails.php?id=<?php echo $event['eventId']; ?>" class="btn btn-event-view w-100">View Details</a>
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

