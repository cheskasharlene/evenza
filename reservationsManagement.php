<?php
// Admin Authentication Guard - Must be at the very top
require_once 'adminAuth.php';

// Load reservations data
$reservationsFile = __DIR__ . '/data/reservations.json';
$reservations = [];
if (file_exists($reservationsFile)) {
    $reservations = json_decode(file_get_contents($reservationsFile), true) ?? [];
}

// Get filter parameters
$packageFilter = isset($_GET['package']) ? $_GET['package'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';

// Filter reservations
$filteredReservations = $reservations;
if (!empty($packageFilter)) {
    $filteredReservations = array_filter($filteredReservations, function($r) use ($packageFilter) {
        $packageName = isset($r['packageName']) ? strtolower($r['packageName']) : '';
        return stripos($packageName, strtolower($packageFilter)) !== false;
    });
}
if (!empty($dateFilter)) {
    $filteredReservations = array_filter($filteredReservations, function($r) use ($dateFilter) {
        $reservationDate = isset($r['date']) ? $r['date'] : '';
        // Convert date format for comparison
        $resDate = date('Y-m-d', strtotime($reservationDate));
        return $resDate === $dateFilter;
    });
}

// Group reservations by date
$groupedReservations = [];
foreach ($filteredReservations as $reservation) {
    $date = isset($reservation['date']) ? $reservation['date'] : 'Unknown Date';
    if (!isset($groupedReservations[$date])) {
        $groupedReservations[$date] = [];
    }
    $groupedReservations[$date][] = $reservation;
}

// Sort dates
uksort($groupedReservations, function($a, $b) {
    return strtotime($a) - strtotime($b);
});
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

                <!-- Reservations List -->
                <div class="admin-card p-4">
                    <h5 class="mb-4" style="font-family: 'Playfair Display', serif;">
                        Reservations (<?php echo count($filteredReservations); ?>)
                    </h5>
                    
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
                                        <?php echo htmlspecialchars($reservation['eventName'] ?? 'Unknown Event'); ?>
                                    </h6>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo htmlspecialchars($reservation['time'] ?? 'N/A'); ?>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?php echo htmlspecialchars($reservation['venue'] ?? 'N/A'); ?>
                                    </div>
                                    <?php if (isset($reservation['packageName'])): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-box me-1"></i>
                                            <?php echo htmlspecialchars($reservation['packageName']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="text-muted small">
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                        ₱<?php echo number_format($reservation['totalAmount'] ?? 0, 2); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Reservation Status</label>
                                        <div class="status-toggle-group">
                                            <button type="button" 
                                                    class="status-toggle-btn status-pending <?php echo (isset($reservation['status']) && strtolower($reservation['status']) === 'pending') ? 'active' : ''; ?>"
                                                    onclick="updateReservationStatus('<?php echo htmlspecialchars($reservation['id'], ENT_QUOTES); ?>', 'pending')">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </button>
                                            <button type="button" 
                                                    class="status-toggle-btn status-confirmed <?php echo (isset($reservation['status']) && strtolower($reservation['status']) === 'confirmed') ? 'active' : ''; ?>"
                                                    onclick="updateReservationStatus('<?php echo htmlspecialchars($reservation['id'], ENT_QUOTES); ?>', 'confirmed')">
                                                <i class="fas fa-check-circle me-1"></i> Confirmed
                                            </button>
                                            <button type="button" 
                                                    class="status-toggle-btn status-cancelled <?php echo (isset($reservation['status']) && strtolower($reservation['status']) === 'cancelled') ? 'active' : ''; ?>"
                                                    onclick="updateReservationStatus('<?php echo htmlspecialchars($reservation['id'], ENT_QUOTES); ?>', 'cancelled')">
                                                <i class="fas fa-times-circle me-1"></i> Cancelled
                                            </button>
                                        </div>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-id-badge me-1"></i>
                                        Reservation ID: <?php echo htmlspecialchars($reservation['id'] ?? 'N/A'); ?>
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
            // In a real implementation, this would make an AJAX call to update the status
            // For now, we'll show feedback and update the UI
            
            const statusLabels = {
                'pending': 'Pending',
                'confirmed': 'Confirmed',
                'cancelled': 'Cancelled'
            };
            
            showFeedback('Reservation status updated to ' + statusLabels[newStatus] + '.', 'success');
            
            // Update button states
            const buttons = document.querySelectorAll(`[onclick*="${reservationId}"]`);
            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.textContent.toLowerCase().includes(newStatus)) {
                    btn.classList.add('active');
                }
            });
            
            // In production, reload after a short delay to reflect server-side changes
            setTimeout(function() {
                location.reload();
            }, 1500);
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

