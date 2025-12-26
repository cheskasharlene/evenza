<?php
// Admin Authentication Guard - Must be at the very top
require_once 'adminAuth.php';
require_once 'config/database.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>EVENZA Admin Dashboard</title>
    <style>
        /* Admin Dashboard Custom Styles */
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
        .stat-number { 
            font-size: 1.8rem; 
            font-weight: 700; 
            font-family: 'Playfair Display', serif;
            color: #1A1A1A;
        }
        .stat-label { 
            color: rgba(26, 26, 26, 0.7); 
            font-size: 0.9rem; 
            font-weight: 500;
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
        .admin-section {
            display: none;
        }
        .admin-section.active {
            display: block;
        }
        .table thead th {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            color: #1A1A1A;
            border-bottom: 2px solid rgba(74, 93, 74, 0.1);
        }
        .table tbody tr:hover {
            background-color: rgba(74, 93, 74, 0.04);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-pending { background-color: #FFF3CD; color: #856404; }
        .status-confirmed { background-color: #D4EDDA; color: #155724; }
        .status-completed { background-color: #D1ECF1; color: #0C5460; }
        .status-cancelled { background-color: #F8D7DA; color: #721C24; }
        .icon-btn {
            border: none;
            background: transparent;
            color: #4A5D4A;
            padding: 0.25rem 0.5rem;
            cursor: pointer;
            border-radius: 4px;
        }
        .icon-btn:hover {
            background-color: rgba(74, 93, 74, 0.1);
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
                        <a href="#" class="nav-link active d-flex align-items-center py-2" data-section="dashboard">
                            <i class="bi bi-house-door me-2"></i> Dashboard
                        </a>
                        <a href="#" class="nav-link d-flex align-items-center py-2" data-section="events">
                            <i class="bi bi-calendar-event me-2"></i> Event Management
                        </a>
                        <a href="#" class="nav-link d-flex align-items-center py-2" data-section="reservations">
                            <i class="bi bi-clipboard-check me-2"></i> Reservations
                        </a>
                        <a href="#" class="nav-link d-flex align-items-center py-2" data-section="users">
                            <i class="bi bi-people me-2"></i> User Management
                        </a>
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
                        <h4 class="mb-0" style="font-family: 'Playfair Display', serif;" id="pageTitle">Dashboard</h4>
                        <div class="text-muted small" id="pageSubtitle">Overview of activity and performance</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-admin-primary btn-sm">Logout</a>
                </div>
            </div>

            <div class="p-4">
                <!-- Alert Container for Feedback Messages -->
                <div id="alertContainer"></div>

                <!-- Dashboard Section -->
                <div id="dashboard-section" class="admin-section active">
                    <!-- Stat Cards Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="admin-card p-4 h-100">
                                <div class="d-flex flex-column">
                                    <div class="stat-label mb-2">Total Revenue</div>
                                    <div class="stat-number">₱ <span id="totalRevenue">0</span></div>
                                    <div class="trend-indicator trend-up mt-2" style="color: #4A5D4A;">
                                        <span>↗</span> +8.3% since last month
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="admin-card p-4 h-100">
                                <div class="d-flex flex-column">
                                    <div class="stat-label mb-2">Total Tickets Sold</div>
                                    <div class="stat-number" id="ticketsSold">0</div>
                                    <div class="text-muted small mt-2">All-time</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="admin-card p-4 h-100">
                                <div class="d-flex flex-column">
                                    <div class="stat-label mb-2">Active Events</div>
                                    <div class="stat-number" id="activeEvents">0</div>
                                    <div class="text-muted small mt-2">Events accepting reservations</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="admin-card p-4 h-100">
                                <div class="d-flex flex-column">
                                    <div class="stat-label mb-2">New User Sign-ups</div>
                                    <div class="stat-number" id="newUsers">0</div>
                                    <div class="text-muted small mt-2">Last 30 days</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Panels -->
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="admin-card p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="mb-1" style="font-family: 'Playfair Display', serif;">Top Performing Events</h5>
                                        <div class="text-muted small">Top 5 events by tickets sold & capacity%</div>
                                    </div>
                                    <div class="text-muted small">Updated just now</div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Event Name</th>
                                                <th>Tickets Sold</th>
                                                <th>Capacity</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody id="topEventsBody">
                                            <!-- Populated by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="admin-card p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="mb-1" style="font-family: 'Playfair Display', serif;">Recent Activity</h5>
                                        <div class="text-muted small">Latest reservations</div>
                                    </div>
                                </div>
                                <div id="recentActivity">
                                    <!-- Populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Management Section -->
                <div id="events-section" class="admin-section">
                    <div class="admin-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-1" style="font-family: 'Playfair Display', serif;">Event Management</h5>
                                <div class="text-muted small">Manage all events in the system</div>
                            </div>
                            <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="bi bi-plus-circle me-2"></i>Add Event
                            </button>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="categoryFilter" class="form-label">Filter by Category</label>
                                <select class="form-select" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <option value="Premium">Premium</option>
                                    <option value="Business">Business</option>
                                    <option value="Wedding">Wedding</option>
                                    <option value="Workshops">Workshops</option>
                                    <option value="Socials">Socials</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Venue</th>
                                        <th>Category</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="eventsTableBody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reservations Management Section -->
                <div id="reservations-section" class="admin-section">
                    <div class="admin-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-1" style="font-family: 'Playfair Display', serif;">Reservations Management</h5>
                                <div class="text-muted small">View and manage all reservations</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reservation ID</th>
                                        <th>Event</th>
                                        <th>User</th>
                                        <th>Date & Time</th>
                                        <th>Package</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reservationsTableBody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- User Management Section -->
                <div id="users-section" class="admin-section">
                    <div class="admin-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-1" style="font-family: 'Playfair Display', serif;">User Management</h5>
                                <div class="text-muted small">Manage client and admin accounts</div>
                            </div>
                            <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-person-plus me-2"></i>Add User
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family: 'Playfair Display', serif;">Add New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addEventForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="eventTitle" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eventTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventVenue" class="form-label">Venue <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eventVenue" name="venue" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="eventDescription" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="eventCategory" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="eventCategory" name="category" required>
                                <option value="">Select category...</option>
                                <option value="Premium">Premium</option>
                                <option value="Business">Business</option>
                                <option value="Wedding">Wedding</option>
                                <option value="Workshops">Workshops</option>
                                <option value="Socials">Socials</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="eventImagePath" class="form-label">Image Path <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="eventImagePath" name="imagePath" placeholder="assets/images/event_images/imageName.jpg" required>
                            <small class="text-muted">Enter the relative path to the event image</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin-primary">Add Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family: 'Playfair Display', serif;">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editEventForm">
                    <input type="hidden" id="editEventId" name="eventId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editEventTitle" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editEventTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEventVenue" class="form-label">Venue <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editEventVenue" name="venue" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEventDescription" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editEventDescription" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editEventCategory" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="editEventCategory" name="category" required>
                                <option value="">Select category...</option>
                                <option value="Premium">Premium</option>
                                <option value="Business">Business</option>
                                <option value="Wedding">Wedding</option>
                                <option value="Workshops">Workshops</option>
                                <option value="Socials">Socials</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEventImagePath" class="form-label">Image Path <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editEventImagePath" name="imagePath" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin-primary">Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family: 'Playfair Display', serif;">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addUserForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="userFullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userFullName" name="fullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="userEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="userPassword" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="userPassword" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="roleUser" value="user" checked>
                                    <label class="form-check-label" for="roleUser">User</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin">
                                    <label class="form-check-label" for="roleAdmin">Admin</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Reservation Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family: 'Playfair Display', serif;">Update Reservation Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateStatusForm">
                    <input type="hidden" id="statusReservationId" name="reservationId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reservationStatus" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="reservationStatus" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/adminDashboard.js"></script>
</body>
</html>
