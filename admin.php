<?php
// Admin Authentication Guard - Must be at the very top
require_once 'adminAuth.php';
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
        .table-sm td, .table-sm th { 
            padding: 0.75rem; 
        }
        .activity-item { 
            border-left: 3px solid rgba(74, 93, 74, 0.2); 
            padding-left: 0.75rem; 
            margin-bottom: 0.75rem;
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
        .trend-indicator {
            font-size: 0.85rem;
            font-weight: 500;
        }
        .trend-up {
            color: #4A5D4A;
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
                        <a href="admin.php" class="nav-link active d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-home"></i></span> Dashboard</a>
                        <a href="eventManagement.php" class="nav-link d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-calendar-alt"></i></span> Event Management</a>
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
                        <h4 class="mb-0" style="font-family: 'Playfair Display', serif;">Dashboard</h4>
                        <div class="text-muted small">Overview of activity and performance</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                            </svg>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-admin-primary btn-sm">Logout</a>
                </div>
            </div>

            <div class="p-4">

                <!-- Stat Cards Row -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="admin-card p-4 h-100">
                            <div class="d-flex flex-column">
                                <div class="stat-label mb-2">Total Revenue</div>
                                <div class="stat-number">₱ <span id="totalRevenue">0</span></div>
                                <div class="trend-indicator trend-up mt-2">
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
                                        <tr style="border-bottom: 2px solid rgba(74, 93, 74, 0.1);">
                                            <th style="font-weight: 600; color: #1A1A1A;">Event Name</th>
                                            <th style="font-weight: 600; color: #1A1A1A;">Tickets Sold</th>
                                            <th style="font-weight: 600; color: #1A1A1A;">Capacity</th>
                                            <th style="font-weight: 600; color: #1A1A1A;">Revenue</th>
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
                                <div class="text-muted small"><a href="#" style="color: #4A5D4A; text-decoration: none;">View all</a></div>
                            </div>
                            <div id="recentActivity">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
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
    </script>
</body>

</html>