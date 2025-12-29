<?php
require_once 'adminAuth.php';
require_once 'connect.php';

$eventsData = [];
try {
    $stmt = $pdo->query("SELECT eventId, title, venue, category, imagePath, status FROM events ORDER BY eventId");
    $eventsData = $stmt->fetchAll();
    
    $eventsDataAssoc = [];
    foreach ($eventsData as $event) {
        $imagePath = $event['imagePath'];
        $imageName = basename($imagePath);
        
        $eventsDataAssoc[$event['eventId']] = [
            'eventId' => $event['eventId'],
            'name' => $event['title'],
            'title' => $event['title'],
            'category' => $event['category'],
            'status' => $event['status'],
            'image' => $imageName,
            'imagePath' => $imagePath,
            'venue' => $event['venue']
        ];
    }
    $eventsData = $eventsDataAssoc;
} catch(PDOException $e) {
    $eventsData = [];
}

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$filteredEvents = $eventsData;
if (!empty($searchQuery)) {
    $filteredEvents = array_filter($eventsData, function($event) use ($searchQuery) {
        return stripos($event['name'], $searchQuery) !== false || 
               stripos($event['title'], $searchQuery) !== false ||
               stripos($event['category'], $searchQuery) !== false ||
               stripos($event['venue'], $searchQuery) !== false;
    });
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
    <title>Event Management - EVENZA Admin</title>
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
        .table th {
            font-weight: 600;
            color: #1A1A1A;
            border-bottom: 2px solid rgba(74, 93, 74, 0.1);
        }
        .table td {
            vertical-align: middle;
        }
        .event-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .action-btn {
            background: none;
            border: none;
            color: #4A5D4A;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .action-btn:hover {
            color: #3a4a3a;
            transform: scale(1.1);
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
                        <a href="eventManagement.php" class="nav-link active d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-calendar-alt"></i></span> Event Management</a>
                        <a href="reservationsManagement.php" class="nav-link d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-clipboard-list"></i></span> Reservations</a>
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
                        <h4 class="mb-0" style="font-family: 'Playfair Display', serif;">Event Management</h4>
                        <div class="text-muted small">Manage all events and their details</div>
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
                <!-- Controls Section -->
                <div class="admin-card p-4 mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="searchBar" class="form-label fw-semibold">Search Events</label>
                            <form method="GET" action="eventManagement.php" class="d-flex gap-2">
                                <input type="text" 
                                       class="form-control" 
                                       id="searchBar" 
                                       name="search" 
                                       placeholder="Search by event name or category..." 
                                       value="<?php echo htmlspecialchars($searchQuery); ?>">
                                <button type="submit" class="btn btn-admin-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <?php if (!empty($searchQuery)): ?>
                                <a href="eventManagement.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button type="button" class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="fas fa-plus"></i> Add New Event
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Events Table -->
                <div class="admin-card p-4">
                    <h5 class="mb-4" style="font-family: 'Playfair Display', serif;">All Events (<?php echo count($filteredEvents); ?>)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Event Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($filteredEvents)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-search fa-2x mb-3 d-block"></i>
                                        No events found matching your search.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($filteredEvents as $id => $event): 
                                    // Handle image path - use full path if available, otherwise construct from filename
                                    $imageSrc = isset($event['imagePath']) && !empty($event['imagePath']) 
                                        ? $event['imagePath'] 
                                        : 'assets/images/event_images/' . (isset($event['image']) ? $event['image'] : 'businessInnovation.jpg');
                                ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($imageSrc); ?>" 
                                             alt="<?php echo htmlspecialchars($event['name'] ?? $event['title']); ?>" 
                                             class="event-thumbnail"
                                             onerror="this.src='assets/images/event_images/businessInnovation.jpg'">
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($event['name'] ?? $event['title']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark"><?php echo htmlspecialchars($event['category']); ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($event['status']) === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo htmlspecialchars($event['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="action-btn" onclick="editEvent(<?php echo $event['eventId']; ?>)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn text-danger" onclick="deleteEvent(<?php echo $event['eventId']; ?>, '<?php echo htmlspecialchars($event['name'] ?? $event['title'], ENT_QUOTES); ?>')" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family: 'Playfair Display', serif;" id="addEventModalLabel">Add New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addEventForm">
                        <div class="mb-3">
                            <label for="eventName" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="eventName" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventCategory" class="form-label">Category</label>
                            <select class="form-select" id="eventCategory" required>
                                <option value="">Select Category</option>
                                <option value="Conference">Conference</option>
                                <option value="Wedding">Wedding</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Business">Business</option>
                                <option value="Social">Social</option>
                                <option value="Hotel-Hosted Events">Hotel-Hosted Events</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="eventStatus" class="form-label">Status</label>
                            <select class="form-select" id="eventStatus" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-admin-primary" onclick="saveNewEvent()">Save Event</button>
                </div>
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

        // Edit event function
        function editEvent(eventId) {
            showFeedback('Edit functionality will open the event editor for Event ID: ' + eventId, 'info');
            // In a real implementation, this would open an edit modal or navigate to an edit page
        }

        // Delete event function
        function deleteEvent(eventId, eventName) {
            if (confirm('Are you sure you want to delete "' + eventName + '"? This action cannot be undone.')) {
                // In a real implementation, this would make an AJAX call to delete the event
                showFeedback('Event "' + eventName + '" has been deleted successfully.', 'success');
                // Reload page after a short delay to show the feedback
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        }

        // Save new event
        function saveNewEvent() {
            const eventName = document.getElementById('eventName').value;
            const eventCategory = document.getElementById('eventCategory').value;
            const eventStatus = document.getElementById('eventStatus').value;
            
            if (!eventName || !eventCategory) {
                showFeedback('Please fill in all required fields.', 'error');
                return;
            }
            
            // In a real implementation, this would make an AJAX call to save the event
            showFeedback('Event "' + eventName + '" has been added successfully.', 'success');
            
            // Close modal and reload after a short delay
            const modal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
            modal.hide();
            
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

