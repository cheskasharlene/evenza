<?php
session_start();
require_once 'connect.php'; 
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

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

$event = null;
$eventImagePath = 'assets/images/event_images/placeholder.jpg';

if ($eventId > 0) {
    $query = "SELECT * FROM events WHERE eventId = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $eventId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $event = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if ($event) {
            $eventImagePath = getEventImagePath($event['imagePath']);
            
            if (stripos($event['title'], 'wine') !== false || stripos($event['title'], 'tasting') !== false) {
                if (file_exists('assets/images/event_images/wineCellar.jpg')) {
                    $eventImagePath = 'assets/images/event_images/wineCellar.jpg';
                }
            }
            
            if (isset($event['eventDate']) && !empty($event['eventDate'])) {
                $eventDate = new DateTime($event['eventDate']);
                $event['formattedDate'] = $eventDate->format('F j, Y');
            } else {
                $event['formattedDate'] = 'Date TBA';
            }
            
            if (empty($event['venueAddress'])) {
                $event['venueAddress'] = '123 Luxury Avenue, Suite 100, City, State 12345';
            }
        }
    } else {
        $event = null;
    }
}

if (!$event) {
    header('Location: events.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - EVENZA</title>
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
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($event['title']); ?></li>
                </ol>
            </div>

            <div class="event-details-layout">
                <!-- Main Content Column -->
                <div class="event-main-content">
                    <div class="event-detail-image mb-4">
                        <img src="<?php echo htmlspecialchars($eventImagePath); ?>" 
                             alt="<?php echo htmlspecialchars($event['title']); ?>" 
                             class="event-hero-image rounded"
                             onerror="this.src='assets/images/event_images/placeholder.jpg'">
                    </div>

                    <div class="luxury-card p-4 mb-4">
                        <h1 class="event-detail-name mb-3"><?php echo htmlspecialchars($event['title']); ?></h1>
                        
                        <div class="event-detail-description mb-4">
                            <p><?php echo !empty($event['description']) ? nl2br(htmlspecialchars($event['description'])) : 'No description available for this event.'; ?></p>
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
                            <?php if (isset($event['eventDate']) && !empty($event['eventDate'])): ?>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                    </div>
                                    <div class="detail-content">
                                        <h6 class="detail-label">Date</h6>
                                        <p class="detail-value"><?php echo htmlspecialchars($event['formattedDate']); ?></p>
                                        <?php if (isset($event['eventTime']) && !empty($event['eventTime'])): ?>
                                            <p class="detail-value text-muted small"><?php echo htmlspecialchars($event['eventTime']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
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
                            <button class="btn btn-primary-luxury" type="button" id="aiSendButton" onclick="askAI()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm-1.138-1.138L13.229 1.5 4.577 8.932l.921.001Z"/>
                                </svg>
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

    <!-- Pass event data to JavaScript -->
    <script type="application/json" id="eventData">
    {
        "title": <?php echo json_encode($event['title']); ?>,
        "description": <?php echo json_encode($event['description'] ?? ''); ?>,
        "venue": <?php echo json_encode($event['venue'] ?? ''); ?>,
        "venueAddress": <?php echo json_encode($event['venueAddress'] ?? ''); ?>,
        "formattedDate": <?php echo json_encode($event['formattedDate'] ?? ''); ?>,
        "eventTime": <?php echo json_encode($event['eventTime'] ?? ''); ?>,
        "packages": [
            {"name": "Bronze Package", "price": 7000},
            {"name": "Silver Package", "price": 10000},
            {"name": "Gold Package", "price": 15000}
        ],
        "faqs": [
            {
                "question": "What is included in the ticket price?",
                "answer": "The ticket price includes full access to the event, all sessions and workshops, refreshments, and networking opportunities. Additional services may be available at extra cost."
            },
            {
                "question": "Can I cancel or refund my reservation?",
                "answer": "Cancellations made 48 hours before the event will receive a full refund. Cancellations made within 48 hours are non-refundable but may be transferable."
            },
            {
                "question": "Is parking available at the venue?",
                "answer": "Yes, complimentary valet parking is available for all event attendees. Please arrive 15 minutes early to allow time for parking."
            },
            {
                "question": "What should I bring to the event?",
                "answer": "Please bring a valid ID, your confirmation email or ticket, and any materials specified in the event details. Notepads and pens will be provided."
            }
        ]
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/event-details.js"></script>
</body>
</html>

