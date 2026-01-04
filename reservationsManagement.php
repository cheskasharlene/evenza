<?php
// Admin Authentication Guard - Must be at the very top
require_once 'adminAuth.php';
require_once 'connect.php';

// Get filter parameters
$packageFilter = isset($_GET['package']) ? $_GET['package'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';

// Build SQL query with JOINs to fetch customer name, event title, and package details
$query = "SELECT 
            r.reservationId,
            r.userId,
            r.eventId,
            r.packageId,
            r.reservationDate,
            r.startTime,
            r.endTime,
            r.totalAmount,
            r.status,
            r.createdAt,
            u.fullName AS customerName,
            u.email AS customerEmail,
            e.title AS eventTitle,
            e.venue AS eventVenue,
            p.packageName,
            p.price AS packagePrice
          FROM reservations r
          INNER JOIN users u ON r.userId = u.userid
          INNER JOIN events e ON r.eventId = e.eventId
          INNER JOIN packages p ON r.packageId = p.packageId
          WHERE 1=1";

$params = [];
$types = '';

// Apply filters - extract tier from package name (Bronze Package -> Bronze)
if (!empty($packageFilter)) {
    $query .= " AND p.packageName LIKE ?";
    $params[] = $packageFilter . '%';
    $types .= 's';
}

if (!empty($dateFilter)) {
    $query .= " AND DATE(r.reservationDate) = ?";
    $params[] = $dateFilter;
    $types .= 's';
}

$query .= " ORDER BY r.reservationDate DESC, r.createdAt DESC";

// Execute query with prepared statement
$reservations = [];
$totalRevenue = 0;

$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            // Extract package tier from package name (e.g., "Bronze Package" -> "Bronze")
            $packageTier = 'Unknown';
            if (!empty($row['packageName'])) {
                $packageTier = str_replace(' Package', '', $row['packageName']);
            }
            $row['packageTier'] = $packageTier;
            
            $reservations[] = $row;
            $totalRevenue += floatval($row['totalAmount']);
        }
        
        mysqli_free_result($result);
    } else {
        $error_message = 'Error fetching reservations: ' . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
} else {
    $error_message = 'Error preparing query: ' . mysqli_error($conn);
}

// Group reservations by date
$groupedReservations = [];
foreach ($reservations as $reservation) {
    $reservationDate = isset($reservation['reservationDate']) ? date('Y-m-d', strtotime($reservation['reservationDate'])) : 'Unknown Date';
    $dateFormatted = date('F j, Y', strtotime($reservation['reservationDate']));
    
    if (!isset($groupedReservations[$dateFormatted])) {
        $groupedReservations[$dateFormatted] = [];
    }
    $groupedReservations[$dateFormatted][] = $reservation;
}

// Sort dates
uksort($groupedReservations, function($a, $b) {
    return strtotime($a) - strtotime($b);
});

// Calculate revenue by package tier
$revenueByPackage = [
    'Bronze' => 0,
    'Silver' => 0,
    'Gold' => 0
];

foreach ($reservations as $reservation) {
    $tier = $reservation['packageTier'];
    if (isset($revenueByPackage[$tier])) {
        $revenueByPackage[$tier] += floatval($reservation['totalAmount']);
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Reservations Management - EVENZA Admin</title>
    <style>
        .admin-wrapper { 
            min-height: 100vh; 
            background-color: #F9F7F2;
        }
        .admin-sidebar { 
            width: 260px; 
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .admin-content {
            margin-left: 260px;
            width: calc(100% - 260px);
        }
        .admin-top-nav {
            background-color: #FFFFFF;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(74, 93, 74, 0.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }
        .admin-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: none;
        }
        .btn-admin-primary {
            background-color: #4A5D4A;
            border-color: #4A5D4A;
            color: #FFFFFF;
        }
        .btn-admin-primary:hover {
            background-color: #3a4a3a;
            border-color: #3a4a3a;
            color: #FFFFFF;
        }
        .date-group-header {
            background-color: #F9F7F2;
            padding: 1rem 1.5rem;
            border-left: 4px solid #4A5D4A;
            margin-bottom: 1rem;
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1A1A1A;
        }
        .reservation-item {
            background-color: #FFFFFF;
            border: 1px solid rgba(74, 93, 74, 0.1);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }
        .reservation-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .status-toggle-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .status-toggle-btn {
            padding: 0.5rem 1rem;
            border: 2px solid #4A5D4A;
            background-color: #FFFFFF;
            color: #4A5D4A;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .status-toggle-btn:hover {
            background-color: #F9F7F2;
        }
        .status-toggle-btn.active {
            background-color: #4A5D4A;
            color: #FFFFFF;
        }
        .status-pending {
            border-color: #ffc107;
            color: #856404;
        }
        .status-pending.active {
            background-color: #ffc107;
            color: #000;
        }
        .status-confirmed {
            border-color: #28a745;
            color: #155724;
        }
        .status-confirmed.active {
            background-color: #28a745;
            color: #FFFFFF;
        }
        .status-cancelled {
            border-color: #dc3545;
            color: #721c24;
        }
        .status-cancelled.active {
            background-color: #dc3545;
            color: #FFFFFF;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        @media (max-width: 991px) { 
            .admin-sidebar { 
                width: 100%; 
                position: relative;
                height: auto;
            }
            .admin-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex admin-wrapper">
        <!-- Sidebar -->
        <div class="d-flex flex-column admin-sidebar p-4" style="background-color: #F9F7F2;">
            <div class="d-flex align-items-center mb-4">
                <div class="luxury-logo"><img src="assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img"></div>
            </div>
            <div class="mb-4">
                <div class="admin-card p-3">
                    <div class="d-flex flex-column">
                        <a href="admin.php" class="nav-link d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-home"></i></span> Dashboard</a>
                        <a href="eventManagement.php" class="nav-link d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-calendar-alt"></i></span> Event Management</a>
                        <a href="reservationsManagement.php" class="nav-link active d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-clipboard-list"></i></span> Reservations</a>
                        <a href="userManagement.php" class="nav-link d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-users"></i></span> User Management</a>
                        <a href="#" class="nav-link d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-cog"></i></span> Settings</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-fill admin-content">
            <!-- Top Navigation Bar -->
            <div class="admin-top-nav d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3 d-lg-none">
                        <button id="adminSidebarToggle" class="btn btn-outline-secondary btn-sm">☰</button>
                    </div>
                    <div>
                        <h4 class="mb-0" style="font-family: 'Playfair Display', serif;">Reservations Management</h4>
                        <div class="text-muted small">View and manage all event reservations</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user text-muted"></i>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-admin-primary btn-sm">Logout</a>
                </div>
            </div>

            <div class="p-4">
                <!-- Filters Section -->
                <div class="admin-card p-4 mb-4">
                    <h5 class="mb-4" style="font-family: 'Playfair Display', serif;">Filter Reservations</h5>
                    <form method="GET" action="reservationsManagement.php" class="row g-3">
                        <div class="col-md-4">
                            <label for="packageFilter" class="form-label fw-semibold">Package Tier</label>
                            <select class="form-select" id="packageFilter" name="package">
                                <option value="">All Packages</option>
                                <option value="Bronze" <?php echo $packageFilter === 'Bronze' ? 'selected' : ''; ?>>Bronze</option>
                                <option value="Silver" <?php echo $packageFilter === 'Silver' ? 'selected' : ''; ?>>Silver</option>
                                <option value="Gold" <?php echo $packageFilter === 'Gold' ? 'selected' : ''; ?>>Gold</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="dateFilter" class="form-label fw-semibold">Filter by Date</label>
                            <input type="date" class="form-control" id="dateFilter" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-admin-primary me-2">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <?php if (!empty($packageFilter) || !empty($dateFilter)): ?>
                            <a href="reservationsManagement.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Revenue Summary -->
                <div class="admin-card p-4 mb-4">
                    <h5 class="mb-4" style="font-family: 'Playfair Display', serif;">Revenue Summary</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3" style="background-color: #F9F7F2; border-radius: 8px;">
                                <div class="text-muted small mb-1">Total Revenue</div>
                                <div class="h4 mb-0" style="color: #4A5D4A; font-weight: 600;">₱<?php echo number_format($totalRevenue, 2); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3" style="background-color: #F9F7F2; border-radius: 8px;">
                                <div class="text-muted small mb-1">Bronze Package</div>
                                <div class="h5 mb-0" style="color: #4A5D4A; font-weight: 600;">₱<?php echo number_format($revenueByPackage['Bronze'], 2); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3" style="background-color: #F9F7F2; border-radius: 8px;">
                                <div class="text-muted small mb-1">Silver Package</div>
                                <div class="h5 mb-0" style="color: #4A5D4A; font-weight: 600;">₱<?php echo number_format($revenueByPackage['Silver'], 2); ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3" style="background-color: #F9F7F2; border-radius: 8px;">
                                <div class="text-muted small mb-1">Gold Package</div>
                                <div class="h5 mb-0" style="color: #4A5D4A; font-weight: 600;">₱<?php echo number_format($revenueByPackage['Gold'], 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservations List -->
                <div class="admin-card p-4">
                    <h5 class="mb-4" style="font-family: 'Playfair Display', serif;">
                        Reservations (<?php echo count($reservations); ?>)
                    </h5>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($groupedReservations)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
                        <p>No reservations found.</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($groupedReservations as $date => $dateReservations): ?>
                    <div class="mb-4">
                        <div class="date-group-header">
                            <i class="fas fa-calendar-day me-2"></i><?php echo htmlspecialchars($date); ?>
                            <span class="badge bg-secondary ms-2"><?php echo count($dateReservations); ?> reservation(s)</span>
                        </div>
                        
                        <?php foreach ($dateReservations as $reservation): ?>
                        <div class="reservation-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-2" style="font-family: 'Playfair Display', serif;">
                                        <?php echo htmlspecialchars($reservation['eventTitle'] ?? 'Unknown Event'); ?>
                                    </h6>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-user me-1"></i>
                                        <strong>Customer:</strong> <?php echo htmlspecialchars($reservation['customerName'] ?? 'N/A'); ?>
                                        <span class="ms-2 text-muted">(<?php echo htmlspecialchars($reservation['customerEmail'] ?? 'N/A'); ?>)</span>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo htmlspecialchars($reservation['eventTime'] ?? 'N/A'); ?>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($reservation['eventVenue'] ?? 'N/A'); ?>
                                    </div>
                                    <div class="mb-2">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-box me-1"></i>
                                            <?php echo htmlspecialchars($reservation['packageName'] ?? ($reservation['packageTier'] ?? 'N/A') . ' Package'); ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                        <strong>Revenue:</strong> ₱<?php echo number_format($reservation['totalAmount'] ?? 0, 2); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Reservation Status</label>
                                        <div class="status-toggle-group">
                                            <button type="button" 
                                                    class="status-toggle-btn status-pending <?php echo (isset($reservation['status']) && strtolower($reservation['status']) === 'pending') ? 'active' : ''; ?>"
                                                    onclick="updateReservationStatus('<?php echo htmlspecialchars($reservation['reservationId'], ENT_QUOTES); ?>', 'pending')">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </button>
                                            <button type="button" 
                                                    class="status-toggle-btn status-confirmed <?php echo (isset($reservation['status']) && strtolower($reservation['status']) === 'confirmed') ? 'active' : ''; ?>"
                                                    onclick="updateReservationStatus('<?php echo htmlspecialchars($reservation['reservationId'], ENT_QUOTES); ?>', 'confirmed')">
                                                <i class="fas fa-check-circle me-1"></i> Confirmed
                                            </button>
                                            <button type="button" 
                                                    class="status-toggle-btn status-cancelled <?php echo (isset($reservation['status']) && strtolower($reservation['status']) === 'cancelled') ? 'active' : ''; ?>"
                                                    onclick="updateReservationStatus('<?php echo htmlspecialchars($reservation['reservationId'], ENT_QUOTES); ?>', 'cancelled')">
                                                <i class="fas fa-times-circle me-1"></i> Cancelled
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-id-badge me-1"></i>
                                        Reservation ID: <?php echo htmlspecialchars($reservation['reservationId'] ?? 'N/A'); ?>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <i class="fas fa-calendar me-1"></i>
                                        Booked: <?php echo date('M j, Y', strtotime($reservation['reservationDate'] ?? 'now')); ?>
                                        <?php if (!empty($reservation['startTime']) && !empty($reservation['endTime'])): ?>
                                            <br><i class="fas fa-clock me-1"></i>
                                            <?php echo date('g:i A', strtotime($reservation['startTime'])); ?> - <?php echo date('g:i A', strtotime($reservation['endTime'])); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container for Feedback Messages -->
    <div class="toast-container">
        <div id="feedbackToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-info-circle me-2"></i>
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                <!-- Message will be inserted here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('adminSidebarToggle');
            const sidebar = document.querySelector('.admin-sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('d-none');
                });
            }
        });

        // Show feedback toast
        function showFeedback(message, type = 'info') {
            const toast = document.getElementById('feedbackToast');
            const toastMessage = document.getElementById('toastMessage');
            const toastHeader = toast.querySelector('.toast-header');
            
            toastMessage.textContent = message;
            
            // Update icon based on type
            const icon = toastHeader.querySelector('i');
            if (type === 'success') {
                icon.className = 'fas fa-check-circle me-2 text-success';
            } else if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle me-2 text-danger';
            } else {
                icon.className = 'fas fa-info-circle me-2';
            }
            
            const bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: 4000
            });
            bsToast.show();
        }

        // Update reservation status
        function updateReservationStatus(reservationId, newStatus) {
            // Make AJAX call to update status in database
            fetch('api/updateReservationStatus.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'reservationId=' + encodeURIComponent(reservationId) + '&status=' + encodeURIComponent(newStatus)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFeedback('Reservation status updated to ' + newStatus + '.', 'success');
                    
                    // Update button states
                    const buttons = document.querySelectorAll(`[onclick*="${reservationId}"]`);
                    buttons.forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.textContent.includes(newStatus)) {
                            btn.classList.add('active');
                        }
                    });
                    
                    // Reload after a short delay to reflect server-side changes
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showFeedback('Error updating status: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                showFeedback('Error updating status: ' + error.message, 'error');
            });
        }

        // Show feedback on page load if there's a message in URL
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const messageType = urlParams.get('type') || 'success';
        if (message) {
            showFeedback(decodeURIComponent(message), messageType);
        }
    </script>
</body>

</html>

