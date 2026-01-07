<?php
session_start();
require_once 'connect.php';
require_once 'includes/helpers.php';
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

// Function to get category-specific package features (each tier builds on previous)
function getPackageFeatures($category, $tier) {
    $category = strtolower(trim($category ?? ''));
    
    // Normalize category
    if (stripos($category, 'business') !== false || stripos($category, 'conference') !== false) {
        $category = 'business';
    } elseif (stripos($category, 'wedding') !== false) {
        $category = 'wedding';
    } elseif (stripos($category, 'art') !== false || stripos($category, 'exhibition') !== false) {
        $category = 'art';
    } elseif (stripos($category, 'social') !== false || stripos($category, 'gala') !== false) {
        $category = 'social';
    } else {
        $category = 'business'; // Default
    }
    
    $tier = strtolower($tier);
    
    $features = [
        'business' => [
            'bronze' => [
                'Basic seating',
                'Digital handouts',
                'Event access'
            ],
            'silver' => [
                'Basic seating',
                'Digital handouts',
                'Event access',
                'Networking lunch',
                'Physical workbooks'
            ],
            'gold' => [
                'Basic seating',
                'Digital handouts',
                'Event access',
                'Networking lunch',
                'Physical workbooks',
                'VIP lounge access',
                'Private speaker Q&A'
            ]
        ],
        'wedding' => [
            'bronze' => [
                'Venue rental',
                'Basic seating',
                'Standard decorations'
            ],
            'silver' => [
                'Venue rental',
                'Basic seating',
                'Standard decorations',
                'Standard catering',
                'Floral arrangements'
            ],
            'gold' => [
                'Venue rental',
                'Basic seating',
                'Standard decorations',
                'Standard catering',
                'Floral arrangements',
                'Premium seating',
                'Premium open bar',
                '5-course meal',
                'Professional photography'
            ]
        ],
        'art' => [
            'bronze' => [
                'Gallery Entry',
                'Digital Catalog'
            ],
            'silver' => [
                'Gallery Entry',
                'Digital Catalog',
                '1 Welcome Drink',
                'Physical Brochure'
            ],
            'gold' => [
                'Gallery Entry',
                'Digital Catalog',
                '1 Welcome Drink',
                'Physical Brochure',
                'Private Auction Preview',
                'Open Bar'
            ]
        ],
        'social' => [
            'bronze' => [
                'Event access',
                'Basic seating',
                'Standard refreshments'
            ],
            'silver' => [
                'Event access',
                'Basic seating',
                'Standard refreshments',
                'Premium seating',
                'Networking opportunities',
                'Event program'
            ],
            'gold' => [
                'Event access',
                'Basic seating',
                'Standard refreshments',
                'Premium seating',
                'Networking opportunities',
                'Event program',
                'VIP event access',
                'Premium open bar',
                'Gourmet catering',
                'Exclusive networking',
                'VIP lounge access'
            ]
        ]
    ];
    
    return $features[$category][$tier] ?? $features['business'][$tier] ?? [];
}

// Fetch packages from database
$packages = [];
$packagesQuery = "SELECT packageId, packageName, price FROM packages ORDER BY packageId ASC";
$packagesResult = mysqli_query($conn, $packagesQuery);
if ($packagesResult) {
    while ($row = mysqli_fetch_assoc($packagesResult)) {
        $tier = strtolower(str_replace(' Package', '', $row['packageName']));
        $packages[] = [
            'id' => $row['packageId'],
            'name' => $row['packageName'],
            'tier' => $tier,
            'price' => floatval($row['price'])
        ];
    }
    mysqli_free_result($packagesResult);
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
    <style>
        .pricing-section {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .packages-container {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }
        .package-card {
            flex: 1;
            min-width: 200px;
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(74, 93, 74, 0.15);
            border-radius: 20px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .package-card:hover {
            background-color: #FFFFFF;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .package-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .package-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1A1A1A;
            margin: 0;
        }
        .package-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #4A5D4A;
            font-family: 'Playfair Display', serif;
        }
        /* Modal Styles */
        .package-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .package-modal-overlay.show {
            display: flex;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .package-modal {
            background-color: #FFFFFF;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        @keyframes popIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        .package-modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(74, 93, 74, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        .package-modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1A1A1A;
            margin: 0;
        }
        .package-modal-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4A5D4A;
            font-family: 'Playfair Display', serif;
        }
        .package-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 2rem;
            color: #4A5D4A;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            line-height: 1;
            padding: 0;
        }
        .package-modal-close:hover {
            background-color: rgba(74, 93, 74, 0.1);
            color: #1A1A1A;
            transform: rotate(90deg);
        }
        .package-modal-body {
            padding: 1.5rem;
        }
        .package-modal-features {
            margin-bottom: 2rem;
        }
        .package-modal-features h6 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 600;
            color: #1A1A1A;
            margin-bottom: 1rem;
        }
        .modal-feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .modal-feature-list li {
            padding: 0.75rem 0;
            color: rgba(26, 26, 26, 0.8);
            font-size: 0.95rem;
            position: relative;
            padding-left: 2rem;
            border-bottom: 1px solid rgba(74, 93, 74, 0.08);
        }
        .modal-feature-list li:last-child {
            border-bottom: none;
        }
        .modal-feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4A5D4A;
            font-weight: 600;
            font-size: 1rem;
        }
        @media (max-width: 768px) {
            .packages-container {
                flex-direction: column;
            }
            .package-card {
                min-width: 100%;
            }
        }
    </style>
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
                    <li class="nav-item nav-divider">
                        <span class="nav-separator"></span>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">My Profile</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register" href="logout.php">Logout</a>
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
                                            <p class="detail-value text-muted small"><?php echo htmlspecialchars(formatTime12Hour($event['eventTime'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Dynamic Tiered Pricing Section -->
                        <div class="pricing-section mt-4 mb-4">
                            <h5 class="detail-label mb-4">Package Options</h5>
                            <div class="packages-container">
                                <?php 
                                $eventCategory = $event['category'] ?? 'Business';
                                foreach ($packages as $package): 
                                    $features = getPackageFeatures($eventCategory, $package['tier']);
                                ?>
                                    <div class="package-card" 
                                         data-package-id="<?php echo $package['id']; ?>"
                                         data-package-name="<?php echo htmlspecialchars($package['name']); ?>"
                                         data-package-price="<?php echo $package['price']; ?>"
                                         data-package-features='<?php echo json_encode($features); ?>'>
                                        <div class="package-header">
                                            <h6 class="package-name"><?php echo htmlspecialchars($package['name']); ?></h6>
                                            <div class="package-price">₱ <?php echo number_format($package['price'], 2); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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

                <div class="event-sidebar">
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

                    <div class="luxury-card p-4">
                        <h5 class="mb-4">Frequently Asked Questions</h5>
                        <div class="faq-list">
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Can I request additional customizations for my chosen package?
                                </button>
                                <div class="collapse" id="faq1">
                                    <div class="faq-answer">
                                        Absolutely. While our Bronze, Silver, and Gold tiers provide a comprehensive foundation, our team is happy to discuss bespoke add-ons such as floral upgrades or specific technical requirements during your consultation.
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What is the timeframe for modifying or canceling a reservation?
                                </button>
                                <div class="collapse" id="faq2">
                                    <div class="faq-answer">
                                        Reservations can be modified up to 14 days before the event date. Cancellations made within this window are subject to our standard refund policy as outlined in your confirmation email.
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Are parking and accessibility services included at the venue?
                                </button>
                                <div class="collapse" id="faq3">
                                    <div class="faq-answer">
                                        Yes, all our partner venues offer complimentary valet parking for Gold package holders and accessible entry points for all guests to ensure a seamless experience.
                                    </div>
                                </div>
                            </div>
                            <div class="faq-item mb-3">
                                <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    What technical equipment is provided for business or gala events?
                                </button>
                                <div class="collapse" id="faq4">
                                    <div class="faq-answer">
                                        Our venues are equipped with high-speed WiFi and standard AV setups. Silver and Gold packages include dedicated technical support and advanced projection systems if required.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Modal -->
    <div id="packageModal" class="package-modal-overlay">
        <div class="package-modal">
            <div class="package-modal-header">
                <div>
                    <h5 class="package-modal-title" id="modalPackageName">Package Name</h5>
                    <div class="package-modal-price" id="modalPackagePrice">₱ 0.00</div>
                </div>
                <button type="button" class="package-modal-close" onclick="closePackageModal()" aria-label="Close">
                    ×
                </button>
            </div>
            <div class="package-modal-body">
                <div class="package-modal-features">
                    <h6>What's Included</h6>
                    <ul class="modal-feature-list" id="modalFeatureList">
                        <!-- Features will be populated by JavaScript -->
                    </ul>
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
                "question": "Can I request additional customizations for my chosen package?",
                "answer": "Absolutely. While our Bronze, Silver, and Gold tiers provide a comprehensive foundation, our team is happy to discuss bespoke add-ons such as floral upgrades or specific technical requirements during your consultation."
            },
            {
                "question": "What is the timeframe for modifying or canceling a reservation?",
                "answer": "Reservations can be modified up to 14 days before the event date. Cancellations made within this window are subject to our standard refund policy as outlined in your confirmation email."
            },
            {
                "question": "Are parking and accessibility services included at the venue?",
                "answer": "Yes, all our partner venues offer complimentary valet parking for Gold package holders and accessible entry points for all guests to ensure a seamless experience."
            },
            {
                "question": "What technical equipment is provided for business or gala events?",
                "answer": "Our venues are equipped with high-speed WiFi and standard AV setups. Silver and Gold packages include dedicated technical support and advanced projection systems if required."
            }
        ]
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/event-details.js"></script>
    <script>
        function openPackageModal(packageId, packageName, packagePrice, features) {
            const modal = document.getElementById('packageModal');
            if (!modal) {
                console.error('Modal element not found!');
                return;
            }
            
            const modalTitle = document.getElementById('modalPackageName');
            const modalPrice = document.getElementById('modalPackagePrice');
            const modalFeatures = document.getElementById('modalFeatureList');
            
            if (!modalTitle || !modalPrice || !modalFeatures) {
                console.error('Modal elements not found!');
                return;
            }
            
            // Set modal content
            modalTitle.textContent = packageName;
            modalPrice.textContent = '₱ ' + parseFloat(packagePrice).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Clear and populate features
            modalFeatures.innerHTML = '';
            if (Array.isArray(features) && features.length > 0) {
                features.forEach(feature => {
                    const li = document.createElement('li');
                    li.textContent = feature;
                    modalFeatures.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'No features available';
                modalFeatures.appendChild(li);
            }
            
            // Show modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function closePackageModal() {
            const modal = document.getElementById('packageModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
        
        // Initialize event listeners when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers to package cards
            const packageCards = document.querySelectorAll('.package-card');
            packageCards.forEach(card => {
                card.addEventListener('click', function() {
                    const packageId = this.getAttribute('data-package-id');
                    const packageName = this.getAttribute('data-package-name');
                    const packagePrice = parseFloat(this.getAttribute('data-package-price'));
                    const featuresJson = this.getAttribute('data-package-features');
                    
                    let features = [];
                    try {
                        features = JSON.parse(featuresJson);
                    } catch(e) {
                        console.error('Error parsing features:', e);
                    }
                    
                    openPackageModal(packageId, packageName, packagePrice, features);
                });
            });
            
            const modal = document.getElementById('packageModal');
            if (modal) {
                // Close modal when clicking overlay
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closePackageModal();
                    }
                });
            }
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closePackageModal();
                }
            });
        });
    </script>
</body>
</html>

