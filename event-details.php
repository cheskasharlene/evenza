<?php

$eventId = isset($_GET['id']) ? intval($_GET['id']) : 1;

$eventsData = [
    1 => [
        'name' => 'Business Innovation Summit 2024',
        'category' => 'Conference',
        'description' => 'Join industry leaders and innovators for a comprehensive exploration of cutting-edge business strategies, emerging technologies, and transformative ideas. This exclusive summit brings together thought leaders, entrepreneurs, and executives for a day of inspiring keynotes, interactive workshops, and networking opportunities.',
        'date' => 'December 25, 2024',
        'time' => '9:00 AM - 6:00 PM',
        'venue' => 'Grand Luxe Hotel - Grand Ballroom',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 299,
        'priceType' => 'per person',
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
        'imageClass' => 'wedding-bg'
    ],
    3 => [
        'name' => 'Digital Marketing Masterclass',
        'category' => 'Seminar',
        'description' => 'Master the art and science of digital marketing in this intensive one-day workshop. Learn from industry experts about SEO, social media strategy, content marketing, email campaigns, and analytics. Includes hands-on exercises, case studies, and actionable insights you can implement immediately.',
        'date' => 'December 30, 2024',
        'time' => '10:00 AM - 5:00 PM',
        'venue' => 'Grand Luxe Hotel - Conference Hall A',
        'venueAddress' => '123 Luxury Avenue, Suite 100, City, State 12345',
        'price' => 149,
        'priceType' => 'per person',
        'slots' => 78,
        'totalCapacity' => 100,
        'imageClass' => 'seminar-bg'
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
        'imageClass' => 'hotel-bg'
    ]
];

$event = isset($eventsData[$eventId]) ? $eventsData[$eventId] : $eventsData[1];
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
    </nav>

    <section class="event-details-section py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($event['name']); ?></li>
                        </ol>
                    </nav>

                    <div class="event-detail-image mb-4">
                        <div class="image-placeholder-detail <?php echo htmlspecialchars($event['imageClass']); ?>">
                            <span class="event-category-badge"><?php echo htmlspecialchars($event['category']); ?></span>
                        </div>
                    </div>

                    <div class="luxury-card p-4 mb-4">
                        <h1 class="event-detail-name mb-3"><?php echo htmlspecialchars($event['name']); ?></h1>
                        <div class="event-detail-category mb-4">
                            <span class="event-category"><?php echo htmlspecialchars($event['category']); ?></span>
                        </div>
                        
                        <div class="event-detail-description mb-4">
                            <p><?php echo htmlspecialchars($event['description']); ?></p>
                        </div>

                        <hr class="my-4">

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                    </div>
                                    <div class="detail-content">
                                        <h6 class="detail-label">Date & Time</h6>
                                        <p class="detail-value"><?php echo htmlspecialchars($event['date']); ?></p>
                                        <p class="detail-value text-muted"><?php echo htmlspecialchars($event['time']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                    </div>
                                    <div class="detail-content">
                                        <h6 class="detail-label">Venue</h6>
                                        <p class="detail-value"><?php echo htmlspecialchars($event['venue']); ?></p>
                                        <p class="detail-value text-muted small"><?php echo htmlspecialchars($event['venueAddress']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="1" x2="12" y2="23"/>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                    </div>
                                    <div class="detail-content">
                                        <h6 class="detail-label">Ticket Price</h6>
                                        <p class="detail-value price-large">$<?php echo number_format($event['price']); ?></p>
                                        <p class="detail-value text-muted"><?php echo htmlspecialchars($event['priceType']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                    <div class="detail-content">
                                        <h6 class="detail-label">Available Slots</h6>
                                        <p class="detail-value"><?php echo htmlspecialchars($event['slots']); ?> available</p>
                                        <p class="detail-value text-muted small">Out of <?php echo htmlspecialchars($event['totalCapacity']); ?> total capacity</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="reservation-section luxury-card p-4">
                            <h4 class="mb-4">Reserve Your Tickets</h4>
                            <div class="row align-items-end">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="ticketQuantity" class="form-label">Number of Tickets</label>
                                    <div class="quantity-selector">
                                        <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                                        <input type="number" class="form-control luxury-input quantity-input" id="ticketQuantity" value="1" min="1" max="<?php echo htmlspecialchars($event['slots']); ?>">
                                        <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="total-price mb-3">
                                        <span class="total-label">Total:</span>
                                        <span class="total-amount" id="totalPrice">$<?php echo number_format($event['price']); ?></span>
                                    </div>
                                    <button type="button" class="btn btn-primary-luxury w-100 btn-lg" onclick="reserveTickets()">Reserve Ticket</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="luxury-card p-4 mb-4">
                        <div class="ai-assistant-header mb-3">
                            <div class="ai-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
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
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="luxury-card p-4 mb-4">
                        <h5 class="mb-4">Frequently Asked Questions</h5>
                        <div class="faq-list">
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    What is included in the ticket price?
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </button>
                                <div class="collapse" id="faq4">
                                    <div class="faq-answer">
                                        Please bring a valid ID, your confirmation email or ticket, and any materials specified in the event details. Notepads and pens will be provided.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="luxury-card p-4">
                        <h5 class="mb-4">You Might Also Like</h5>
                        <div class="recommended-events">
                            <div class="recommended-event-item mb-3">
                                <div class="recommended-event-image">
                                    <div class="image-placeholder-small seminar-bg"></div>
                                </div>
                                <div class="recommended-event-content">
                                    <h6 class="recommended-event-name">Digital Marketing Masterclass</h6>
                                    <p class="recommended-event-date small text-muted">Dec 30, 2024</p>
                                    <a href="event-details.php?id=3" class="btn btn-sm btn-outline-luxury">View Details</a>
                                </div>
                            </div>
                            <div class="recommended-event-item mb-3">
                                <div class="recommended-event-image">
                                    <div class="image-placeholder-small"></div>
                                </div>
                                <div class="recommended-event-content">
                                    <h6 class="recommended-event-name">Tech Leaders Forum 2025</h6>
                                    <p class="recommended-event-date small text-muted">Jan 15, 2025</p>
                                    <a href="event-details.php?id=5" class="btn btn-sm btn-outline-luxury">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/event-details.js"></script>
</body>
</html>

