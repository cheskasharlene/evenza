<?php
session_start();

$eventId = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Mapping of event IDs to image filenames
$eventImageMap = [
    101 => 'galaEvening.jpg',
    102 => 'wineCellar.jpg',
    103 => 'artExhibition.jpg',
    1 => 'businessInnovation.jpg',
    2 => 'gardenWedding.jpg',
    3 => 'marketingClass.jpg',
    4 => 'nyGala.jpg',
    5 => 'techForum.jpg',
    6 => 'beachWedding.jpg',
    7 => 'corporateTbuilding.jpg',
    8 => 'springWedding.jpg',
    9 => 'pdWorkshop.jpg',
    10 => 'exclusiveGala.jpg',
    11 => 'leadershipSummit.jpg',
    12 => 'skillsTraining.jpg'
];

// Function to get event image path with fallback
function getEventImagePath($eventId, $imageMap) {
    $imageDir = 'assets/images/event_images/';
    $defaultImage = 'placeholder.jpg';
    
    // Get the image filename for this event ID
    $imageFile = isset($imageMap[$eventId]) ? $imageMap[$eventId] : null;
    
    if ($imageFile) {
        $imagePath = $imageDir . $imageFile;
        // Check if file exists, otherwise use default
        if (file_exists($imagePath)) {
            return $imagePath;
        }
    }
    
    // Fallback to default placeholder
    $defaultPath = $imageDir . $defaultImage;
    // If placeholder doesn't exist, still return the path (browser will handle 404 gracefully)
    return file_exists($defaultPath) ? $defaultPath : $imageDir . $defaultImage;
}

$eventsData = [
    // Premium Events
    101 => [
        'name' => 'Gala Evening',
        'category' => 'Premium',
        'description' => 'An exquisite night of sophistication featuring a gourmet multi-course dinner, live orchestral performances, and elite networking in our most prestigious ballroom.',
        'date' => 'February 14, 2025',
        'time' => '7:00 PM - 11:00 PM',
        'venue' => 'Grand Luxe Hotel - Crystal Ballroom',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 500,
        'priceType' => 'per person',
        'slots' => 80,
        'totalCapacity' => 150,
        'imageClass' => ''
    ],
    102 => [
        'name' => 'Wine Tasting Experience',
        'category' => 'Premium',
        'description' => 'Embark on a sensory journey through world-class vineyards. Sample rare vintages guided by master sommelier-led insights in an intimate, cellar-inspired atmosphere.',
        'date' => 'March 20, 2025',
        'time' => '6:00 PM - 9:00 PM',
        'venue' => 'Grand Luxe Hotel - Wine Cellar',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 350,
        'priceType' => 'per person',
        'slots' => 45,
        'totalCapacity' => 60,
        'imageClass' => ''
    ],
    103 => [
        'name' => 'Art Exhibition Opening',
        'category' => 'Premium',
        'description' => 'Experience a curated showcase of contemporary masterpieces. This exclusive gallery opening features artist talks and a private viewing of groundbreaking visual narratives.',
        'date' => 'April 15, 2025',
        'time' => '5:00 PM - 9:00 PM',
        'venue' => 'Grand Luxe Hotel - Art Gallery',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 200,
        'priceType' => 'per person',
        'slots' => 120,
        'totalCapacity' => 200,
        'imageClass' => ''
    ],
    // Regular Events
    1 => [
        'name' => 'Business Innovation Summit',
        'category' => 'Conference',
        'description' => 'Join industry leaders and innovators for a comprehensive exploration of cutting-edge business strategies, emerging technologies, and transformative ideas. This exclusive summit brings together thought leaders, entrepreneurs, and executives for a day of inspiring keynotes, interactive workshops, and networking opportunities.',
        'date' => 'December 25, 2024',
        'time' => '9:00 AM - 6:00 PM',
        'venue' => 'Grand Luxe Hotel - Grand Ballroom',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 4000,
        'priceType' => 'package',
        'slots' => 45,
        'totalCapacity' => 200,
        'imageClass' => ''
    ],
    2 => [
        'name' => 'Elegant Garden Wedding',
        'category' => 'Wedding',
        'description' => 'Experience the perfect blend of elegance and natural beauty in our stunning garden pavilion. This intimate wedding package includes full venue access, professional catering, floral arrangements, and dedicated event coordination. Create unforgettable memories in a setting designed for romance and sophistication.',
        'date' => 'January 10, 2025',
        'time' => '4:00 PM - 11:00 PM',
        'venue' => 'Grand Luxe Hotel - Garden Pavilion',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 5500,
        'priceType' => 'package',
        'slots' => 12,
        'totalCapacity' => 150,
        'imageClass' => ''
    ],
    3 => [
        'name' => 'Digital Marketing Masterclass',
        'category' => 'Seminar',
        'description' => 'Master the art of modern branding and digital growth. Learn data-driven strategies from top industry experts to elevate your brand\'s market presence.',
        'date' => 'December 30, 2024',
        'time' => '10:00 AM - 5:00 PM',
        'venue' => 'Grand Luxe Hotel - Conference Hall A',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 149,
        'priceType' => 'per person',
        'slots' => 78,
        'totalCapacity' => 100,
        'imageClass' => ''
    ],
    4 => [
        'name' => 'New Year\'s Eve Gala Dinner',
        'category' => 'Hotel-Hosted Events',
        'description' => 'Ring in the new year in style with our exclusive gala dinner. Enjoy a multi-course gourmet meal prepared by our award-winning chefs, premium bar service, live entertainment, and a spectacular midnight celebration. This elegant evening promises to be an unforgettable start to the new year.',
        'date' => 'December 31, 2024',
        'time' => '7:00 PM - 1:00 AM',
        'venue' => 'Grand Luxe Hotel - Crystal Ballroom',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 450,
        'priceType' => 'per person',
        'slots' => 23,
        'totalCapacity' => 120,
        'imageClass' => ''
    ],
    5 => [
        'name' => 'Tech Leaders Forum',
        'category' => 'Conference',
        'description' => 'Join industry titans and visionaries at the Innovation Center for high-level discussions on emerging tech trends, AI integration, and the future of digital transformation.',
        'date' => 'January 25, 2025',
        'time' => '9:00 AM - 5:00 PM',
        'venue' => 'Grand Luxe Hotel - Innovation Center',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 299,
        'priceType' => 'per person',
        'slots' => 95,
        'totalCapacity' => 150,
        'imageClass' => ''
    ],
    6 => [
        'name' => 'Luxury Beach Wedding',
        'category' => 'Wedding',
        'description' => 'Exchange vows against the backdrop of a sunset-kissed ocean. An ultra-premium seaside celebration featuring floral elegance and a private reception on the Oceanview Terrace.',
        'date' => 'February 20, 2025',
        'time' => '4:00 PM - 11:00 PM',
        'venue' => 'Grand Luxe Hotel - Oceanview Terrace',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 7500,
        'priceType' => 'package',
        'slots' => 8,
        'totalCapacity' => 100,
        'imageClass' => ''
    ],
    7 => [
        'name' => 'Corporate Team Building Retreat',
        'category' => 'Business',
        'description' => 'Rejuvenate your team\'s spirit at our Mountain Resort. A strategic blend of outdoor adventure and collaborative workshops designed to strengthen leadership and communication.',
        'date' => 'March 5, 2025',
        'time' => '8:00 AM - 6:00 PM',
        'venue' => 'Grand Luxe Hotel - Mountain Resort Wing',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 3500,
        'priceType' => 'package',
        'slots' => 25,
        'totalCapacity' => 80,
        'imageClass' => ''
    ],
    8 => [
        'name' => 'Spring Wedding Collection',
        'category' => 'Wedding',
        'description' => 'Discover the season\'s most breathtaking bridal trends. A grand showcase in the Ballroom featuring runway shows and consultations with premier wedding artisans.',
        'date' => 'April 10, 2025',
        'time' => '2:00 PM - 6:00 PM',
        'venue' => 'Grand Luxe Hotel - Grand Ballroom',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 150,
        'priceType' => 'per person',
        'slots' => 180,
        'totalCapacity' => 250,
        'imageClass' => ''
    ],
    9 => [
        'name' => 'Professional Development Workshop',
        'category' => 'Workshop',
        'description' => 'Enhance your career trajectory with hands-on training in Conference Hall B. This workshop focuses on high-impact soft skills and modern management methodologies.',
        'date' => 'February 8, 2025',
        'time' => '9:00 AM - 4:00 PM',
        'venue' => 'Grand Luxe Hotel - Conference Hall B',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 199,
        'priceType' => 'per person',
        'slots' => 65,
        'totalCapacity' => 90,
        'imageClass' => ''
    ],
    10 => [
        'name' => 'Exclusive Members Gala',
        'category' => 'Social',
        'description' => 'A night of unparalleled luxury reserved for our VIP members. Enjoy private lounge access, bespoke cocktails, and a first-hand look at Evenza\'s upcoming flagship events.',
        'date' => 'March 15, 2025',
        'time' => '7:00 PM - 11:00 PM',
        'venue' => 'Grand Luxe Hotel - VIP Lounge',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 400,
        'priceType' => 'per person',
        'slots' => 50,
        'totalCapacity' => 75,
        'imageClass' => ''
    ],
    11 => [
        'name' => 'Leadership Summit',
        'category' => 'Conference',
        'description' => 'An elite gathering for executive-level strategy. Focus on visionary leadership, organizational resilience, and global market navigation in the Executive Center.',
        'date' => 'April 5, 2025',
        'time' => '8:00 AM - 6:00 PM',
        'venue' => 'Grand Luxe Hotel - Executive Center',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 450,
        'priceType' => 'per person',
        'slots' => 70,
        'totalCapacity' => 120,
        'imageClass' => ''
    ],
    12 => [
        'name' => 'Advanced Skills Training',
        'category' => 'Workshop',
        'description' => 'Deep-dive into technical excellence at our state-of-the-art Training Center. Intensive modules designed for professionals seeking to master complex industry tools.',
        'date' => 'May 12, 2025',
        'time' => '9:00 AM - 5:00 PM',
        'venue' => 'Grand Luxe Hotel - Training Center',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 249,
        'priceType' => 'per person',
        'slots' => 55,
        'totalCapacity' => 80,
        'imageClass' => ''
    ]
];

$event = isset($eventsData[$eventId]) ? $eventsData[$eventId] : $eventsData[1];
$eventImagePath = getEventImagePath($eventId, $eventImageMap);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['name']); ?> - EVENZA</title>
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

    <div class="event-details-section py-5 mt-5">
        <div class="container">
            <div aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($event['name']); ?></li>
                </ol>
            </div>

            <div class="event-details-layout">
                <!-- Main Content Column -->
                <div class="event-main-content">
                    <div class="event-detail-image mb-4">
                        <img src="<?php echo htmlspecialchars($eventImagePath); ?>" 
                             alt="<?php echo htmlspecialchars($event['name']); ?>" 
                             class="event-hero-image rounded">
                    </div>

                    <div class="luxury-card p-4 mb-4">
                        <h1 class="event-detail-name mb-3"><?php echo htmlspecialchars($event['name']); ?></h1>
                        <!-- category removed -->
                        
                        <div class="event-detail-description mb-4">
                            <p><?php echo htmlspecialchars($event['description']); ?></p>
                        </div>

                        <hr class="my-4">

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                    </div>
                                    <div class="detail-content">
                                        <h6 class="detail-label">Venue</h6>
                                        <p class="detail-value"><?php echo htmlspecialchars($event['venue']); ?></p>
                                        <p class="detail-value text-muted small"><?php echo htmlspecialchars($event['venueAddress']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="reservation-section inquiry-section p-4 text-center mt-4">
                            <?php $link = isset($_SESSION['user_id']) ? 'reservation.php?eventId=' . $eventId : 'login.php?redirect=' . urlencode('reservation.php?eventId=' . $eventId); ?>
                            <div class="d-flex justify-content-center">
                                <a href="<?php echo $link; ?>" class="btn btn-primary-luxury w-100">Inquire Reservation</a>
                            </div>
                        </div>
                    </div> <!-- end .luxury-card -->
                </div>

                <!-- Sidebar Column -->
                <div class="event-sidebar">
                    <!-- AI Assistant Card -->
                    <div class="luxury-card p-4 mb-4">
                        <div class="ai-assistant-header mb-3">
                            <div class="ai-icon">
                            </div>
                            <h5 class="mb-0">AI Assistant</h5>
                        </div>
                        <p class="text-muted mb-3">Need help? Ask me anything about this event!</p>
                        <div class="ai-chat-box mb-3">
                            <div class="ai-message">
                                <p class="mb-0">Hello! I'm here to help you with any questions about this event. What would you like to know?</p>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control luxury-input" id="aiQuestion" placeholder="Ask a question...">
                            <button class="btn btn-primary-luxury" type="button" onclick="askAI()">
                            </button>
                        </div>
                    </div>

                    <!-- FAQ Card -->
                    <div class="luxury-card p-4">
                        <h5 class="mb-4">Frequently Asked Questions</h5>
                        <div class="faq-list">
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    What is included in the ticket price?
                                </button>
                                <div class="collapse" id="faq1">
                                    <div class="faq-answer">
                                        The ticket price includes full access to the event, all sessions and workshops, refreshments, and networking opportunities. Additional services may be available at extra cost.
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Can I cancel or refund my reservation?
                                </button>
                                <div class="collapse" id="faq2">
                                    <div class="faq-answer">
                                        Cancellations made 48 hours before the event will receive a full refund. Cancellations made within 48 hours are non-refundable but may be transferable.
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Is parking available at the venue?
                                </button>
                                <div class="collapse" id="faq3">
                                    <div class="faq-answer">
                                        Yes, complimentary valet parking is available for all event attendees. Please arrive 15 minutes early to allow time for parking.
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    What should I bring to the event?
                                </button>
                                <div class="collapse" id="faq4">
                                    <div class="faq-answer">
                                        Please bring a valid ID, your confirmation email or ticket, and any materials specified in the event details. Notepads and pens will be provided.
                                    </div>
                                </div>
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/event-details.js"></script>
</body>
</html>

