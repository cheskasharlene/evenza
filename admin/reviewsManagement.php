<?php
require_once 'adminAuth.php';
require_once '../core/connect.php';
require_once '../includes/helpers.php';

// Filtering and pagination
$ratingFilter = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

// Build query
$query = "SELECT 
            r.reviewId,
            r.reservationId,
            r.userId,
            r.eventId,
            r.rating,
            r.comment,
            r.createdAt,
            u.fullName AS userName,
            u.email AS userEmail,
            res.reservationCode,
            res.reservationDate,
            e.title AS eventTitle,
            e.venue AS eventVenue
          FROM reviews r
          INNER JOIN users u ON r.userId = u.userid
          INNER JOIN reservations res ON r.reservationId = res.reservationId
          LEFT JOIN events e ON r.eventId = e.eventId
          WHERE 1=1";

$params = [];
$types = '';

if ($ratingFilter > 0 && $ratingFilter <= 5) {
    $query .= " AND r.rating = ?";
    $params[] = $ratingFilter;
    $types .= 'i';
}

if (!empty($searchQuery)) {
    $query .= " AND (u.fullName LIKE ? OR u.email LIKE ? OR res.reservationCode LIKE ? OR r.comment LIKE ?)";
    $searchParam = '%' . $searchQuery . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ssss';
}

$query .= " ORDER BY r.createdAt DESC";

// Count query for pagination
$countQuery = "SELECT COUNT(*) as total
          FROM reviews r
          INNER JOIN users u ON r.userId = u.userid
          INNER JOIN reservations res ON r.reservationId = res.reservationId
          LEFT JOIN events e ON r.eventId = e.eventId
          WHERE 1=1";

if ($ratingFilter > 0 && $ratingFilter <= 5) {
    $countQuery .= " AND r.rating = ?";
}

if (!empty($searchQuery)) {
    $countQuery .= " AND (u.fullName LIKE ? OR u.email LIKE ? OR res.reservationCode LIKE ? OR r.comment LIKE ?)";
}

$totalCount = 0;
$countParams = [];
$countTypes = '';

if ($ratingFilter > 0 && $ratingFilter <= 5) {
    $countParams[] = $ratingFilter;
    $countTypes .= 'i';
}

if (!empty($searchQuery)) {
    $searchParam = '%' . $searchQuery . '%';
    $countParams[] = $searchParam;
    $countParams[] = $searchParam;
    $countParams[] = $searchParam;
    $countParams[] = $searchParam;
    $countTypes .= 'ssss';
}

if (!empty($countParams)) {
    $countStmt = mysqli_prepare($conn, $countQuery);
    if ($countStmt) {
        mysqli_stmt_bind_param($countStmt, $countTypes, ...$countParams);
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        if ($countRow = mysqli_fetch_assoc($countResult)) {
            $totalCount = intval($countRow['total']);
        }
        mysqli_stmt_close($countStmt);
    }
} else {
    $countResult = mysqli_query($conn, $countQuery);
    if ($countResult) {
        $countRow = mysqli_fetch_assoc($countResult);
        $totalCount = intval($countRow['total']);
        mysqli_free_result($countResult);
    }
}

$totalPages = ceil($totalCount / $perPage);

// Fetch reviews
$query .= " LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

$reviews = [];
if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $reviews[] = $row;
            }
            mysqli_free_result($result);
        }
        mysqli_stmt_close($stmt);
    }
}

// Get statistics
$statsQuery = "SELECT 
                COUNT(*) as totalReviews,
                AVG(rating) as averageRating
              FROM reviews";
$statsResult = mysqli_query($conn, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);
mysqli_free_result($statsResult);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Reviews & Feedback Management - EVENZA Admin</title>
    <style>
        .admin-wrapper { 
            min-height: 100vh; 
            background: linear-gradient(135deg, #F9F7F2 0%, #F5F3ED 100%);
        }
        .admin-sidebar { 
            width: 240px; 
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            background: linear-gradient(180deg, #FFFFFF 0%, #F9F7F2 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        }
        .admin-content {
            margin-left: 240px;
            width: calc(100% - 240px);
        }
        .admin-top-nav {
            background-color: #FFFFFF;
            padding: 1.25rem 2rem;
            border-bottom: 1px solid rgba(74, 93, 74, 0.08);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }
        .admin-card {
            background-color: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(74, 93, 74, 0.05);
        }
        .btn-admin-primary {
            background-color: #5A6B4F;
            border-color: #5A6B4F;
            color: #FFFFFF;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-admin-primary:hover {
            background-color: #8B7A6B;
            border-color: #8B7A6B;
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .btn-admin-primary.btn-sm {
            padding: 0.5rem 1.25rem;
            font-size: 0.875rem;
        }
        .review-card {
            background-color: #FFFFFF;
            border: 1px solid rgba(74, 93, 74, 0.1);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .star-rating-display {
            color: #ffc107;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1A1A1A;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <div class="d-flex admin-wrapper">
        <div class="d-flex flex-column admin-sidebar p-4">
            <div class="d-flex align-items-center mb-5" style="padding: 1rem 0;">
                <div class="luxury-logo">
                    <img src="../assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img" style="max-width: 180px;">
                </div>
            </div>
            <div class="mb-4">
                <div style="background: transparent; box-shadow: none; border: none;">
                    <div class="d-flex flex-column gap-2">
                        <a href="admin.php" class="d-flex align-items-center py-3 px-3 rounded-3" style="transition: all 0.3s ease; color: rgba(26, 26, 26, 0.7); text-decoration: none; border-left: 3px solid transparent;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-home"></i></span> 
                            <span style="font-weight: 500;">Dashboard</span>
                        </a>
                        <a href="eventManagement.php" class="d-flex align-items-center py-3 px-3 rounded-3" style="transition: all 0.3s ease; color: rgba(26, 26, 26, 0.7); text-decoration: none; border-left: 3px solid transparent;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-calendar-alt"></i></span> 
                            <span style="font-weight: 500;">Event Management</span>
                        </a>
                        <a href="reservationsManagement.php" class="d-flex align-items-center py-3 px-3 rounded-3" style="transition: all 0.3s ease; color: rgba(26, 26, 26, 0.7); text-decoration: none; border-left: 3px solid transparent;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-clipboard-list"></i></span>
                            <span style="font-weight: 500;">Reservations</span>
                        </a>
                        <a href="userManagement.php" class="d-flex align-items-center py-3 px-3 rounded-3" style="transition: all 0.3s ease; color: rgba(26, 26, 26, 0.7); text-decoration: none; border-left: 3px solid transparent;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-users"></i></span> 
                            <span style="font-weight: 500;">User Management</span>
                        </a>
                        <a href="reviewsManagement.php" class="d-flex align-items-center py-3 px-3 rounded-3 active" style="background: linear-gradient(135deg, rgba(90, 107, 79, 0.15) 0%, rgba(90, 107, 79, 0.08) 100%); color: #5A6B4F; font-weight: 600; text-decoration: none; border-left: 3px solid #5A6B4F;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-star"></i></span>
                            <span>Reviews & Feedback</span>
                        </a>
                        <a href="smsInbox.php" class="d-flex align-items-center py-3 px-3 rounded-3" style="transition: all 0.3s ease; color: rgba(26, 26, 26, 0.7); text-decoration: none; border-left: 3px solid transparent;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-sms"></i></span> 
                            <span style="font-weight: 500;">SMS Inbox</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-fill admin-content">
            <div class="admin-top-nav d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3 d-lg-none">
                        <button id="adminSidebarToggle" class="btn btn-outline-secondary btn-sm">☰</button>
                    </div>
                    <div>
                        <h4 class="mb-0" style="font-family: 'Playfair Display', serif;">Reviews & Feedback</h4>
                        <div class="text-muted small">Manage user reviews and feedback</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user text-muted"></i>
                        </div>
                    </div>
                    <a href="../process/logout.php?type=admin" class="btn btn-admin-primary btn-sm">Logout</a>
                </div>
            </div>

            <div class="p-4" style="padding: 2rem !important;">
                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-6">
                        <div class="admin-card p-4 h-100">
                            <div class="d-flex flex-column">
                                <div class="stat-label mb-2">Total Reviews</div>
                                <div class="stat-number"><?php echo number_format($stats['totalReviews'] ?? 0); ?></div>
                                <div class="text-muted small mt-2">All-time reviews</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-6">
                        <div class="admin-card p-4 h-100">
                            <div class="d-flex flex-column">
                                <div class="stat-label mb-2">Average Rating</div>
                                <div class="stat-number"><?php echo number_format($stats['averageRating'] ?? 0, 1); ?>/5</div>
                                <div class="text-muted small mt-2">Overall rating</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="admin-card mb-4">
                    <div class="p-4">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #1A1A1A;">Search</label>
                                <input type="text" class="form-control" name="search" placeholder="Name, email, or comment..." value="<?php echo htmlspecialchars($searchQuery); ?>" style="border-radius: 50px; padding: 0.6rem 1.25rem; border: 1px solid rgba(74, 93, 74, 0.2);">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="color: #1A1A1A;">Rating</label>
                                <select class="form-select" name="rating" style="border-radius: 50px; padding: 0.6rem 1.25rem; border: 1px solid rgba(74, 93, 74, 0.2);">
                                    <option value="">All Ratings</option>
                                    <option value="5" <?php echo $ratingFilter == 5 ? 'selected' : ''; ?>>5 Stars</option>
                                    <option value="4" <?php echo $ratingFilter == 4 ? 'selected' : ''; ?>>4 Stars</option>
                                    <option value="3" <?php echo $ratingFilter == 3 ? 'selected' : ''; ?>>3 Stars</option>
                                    <option value="2" <?php echo $ratingFilter == 2 ? 'selected' : ''; ?>>2 Stars</option>
                                    <option value="1" <?php echo $ratingFilter == 1 ? 'selected' : ''; ?>>1 Star</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-admin-primary w-100 me-2">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                                <a href="reviewsManagement.php" class="btn btn-outline-secondary" style="border-radius: 50px; padding: 0.6rem 1.25rem;">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="admin-card">
                    <div class="p-4">
                        <?php if (empty($reviews)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-star text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">No reviews found</p>
                            </div>
                        <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <h5 class="mb-0" style="font-family: 'Inter', sans-serif; font-weight: 600; color: #1A1A1A;"><?php echo htmlspecialchars($review['userName']); ?></h5>
                                        </div>
                                        <div class="mb-2">
                                            <?php
                                            $rating = intval($review['rating']);
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '<i class="fas fa-star star-rating-display"></i>';
                                                } else {
                                                    echo '<i class="far fa-star text-muted"></i>';
                                                }
                                            }
                                            ?>
                                            <span class="ms-2" style="color: #6c757d; font-weight: 600;"><?php echo $rating; ?>/5</span>
                                        </div>
                                        <p class="text-muted mb-2" style="font-size: 0.875rem;">
                                            <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($review['userEmail']); ?>
                                            <span class="ms-3"><i class="fas fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($review['createdAt'])); ?></span>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php if (!empty($review['comment'])): ?>
                                    <div class="mb-3 p-3" style="background: #F9F7F2; border-radius: 15px; border-left: 3px solid #5A6B4F;">
                                        <p class="mb-0" style="color: #1A1A1A; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="pt-3" style="border-top: 1px solid rgba(74, 93, 74, 0.1);">
                                    <div class="text-muted small">
                                        <span><i class="fas fa-ticket-alt me-1"></i>Reservation: <strong style="color: #5A6B4F;"><?php echo htmlspecialchars($review['reservationCode'] ?? 'N/A'); ?></strong></span>
                                        <?php if (!empty($review['eventTitle'])): ?>
                                            <span class="ms-3"><i class="fas fa-calendar-alt me-1"></i><?php echo htmlspecialchars($review['eventTitle']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Reviews pagination">
                                    <ul class="pagination justify-content-center mt-4">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&rating=<?php echo $ratingFilter; ?>&search=<?php echo urlencode($searchQuery); ?>" style="border-radius: 50px; margin: 0 2px; border: 1px solid rgba(74, 93, 74, 0.2); color: #5A6B4F; padding: 0.5rem 1rem;">Previous</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&rating=<?php echo $ratingFilter; ?>&search=<?php echo urlencode($searchQuery); ?>" style="border-radius: 50px; margin: 0 2px; border: 1px solid rgba(74, 93, 74, 0.2); color: #5A6B4F; padding: 0.5rem 1rem; <?php echo $i == $page ? 'background-color: #5A6B4F; border-color: #5A6B4F; color: white;' : ''; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&rating=<?php echo $ratingFilter; ?>&search=<?php echo urlencode($searchQuery); ?>" style="border-radius: 50px; margin: 0 2px; border: 1px solid rgba(74, 93, 74, 0.2); color: #5A6B4F; padding: 0.5rem 1rem;">Next</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


