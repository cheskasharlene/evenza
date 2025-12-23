<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>EVENZA Admin Dashboard</title>
    <style>
        /* Minor inline overrides for dashboard layout */
        .admin-wrapper { min-height: 100vh; }
        .admin-sidebar { width: 260px; }
        .stat-number { font-size: 1.6rem; font-weight: 700; }
        .stat-label { color: rgba(26, 26, 26, 0.7); font-size: 0.95rem; }
        .table-sm td, .table-sm th { padding: 0.65rem; }
        .activity-item { border-left: 3px solid rgba(107,127,90,0.12); padding-left: 0.75rem; }
        @media (max-width: 991px) { .admin-sidebar { width: 100%; } }
    </style>
</head>

<body>
    <div class="d-flex admin-wrapper bg-light-luxury">
        <!-- Sidebar -->
        <div class="d-flex flex-column admin-sidebar p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="luxury-logo"><img src="assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img"></div>
            </div>
            <div class="mb-4">
                <div class="luxury-card p-3">
                    <div class="d-flex flex-column">
                        <a href="#" class="nav-link active d-flex align-items-center py-2"><span class="me-2">🏠</span> Dashboard</a>
                        <a href="#" class="nav-link d-flex align-items-center py-2"><span class="me-2">🎟️</span> Event Management</a>
                        <a href="#" class="nav-link d-flex align-items-center py-2"><span class="me-2">👥</span> User Management</a>
                        <a href="#" class="nav-link d-flex align-items-center py-2"><span class="me-2">📊</span> Reports</a>
                        <a href="#" class="nav-link d-flex align-items-center py-2"><span class="me-2">⚙️</span> Settings</a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Content -->
        <div class="flex-fill p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="me-3 d-lg-none">
                        <div id="adminSidebarToggle" class="btn btn-outline-luxury btn-sm">☰</div>
                    </div>
                    <div>
                        <div class="h4 mb-0">Dashboard</div>
                        <div class="text-muted">Overview of activity and performance</div>
                    </div>
                </div>

            </div>

            <!-- Stat Cards Row -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="luxury-card p-3 h-100">
                        <div class="d-flex flex-column">
                            <div class="text-muted stat-label">Total Revenue</div>
                            <div class="stat-number mt-2">₱ <span id="totalRevenue">0</span></div>
                            <div class="text-success small mt-2">+8.3% since last month</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="luxury-card p-3 h-100">
                        <div class="d-flex flex-column">
                            <div class="text-muted stat-label">Total Tickets Sold</div>
                            <div class="stat-number mt-2" id="ticketsSold">0</div>
                            <div class="text-muted small mt-2">All-time</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="luxury-card p-3 h-100">
                        <div class="d-flex flex-column">
                            <div class="text-muted stat-label">Active Events</div>
                            <div class="stat-number mt-2" id="activeEvents">0</div>
                            <div class="text-muted small mt-2">Events accepting reservations</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="luxury-card p-3 h-100">
                        <div class="d-flex flex-column">
                            <div class="text-muted stat-label">New User Sign-ups</div>
                            <div class="stat-number mt-2" id="newUsers">0</div>
                            <div class="text-muted small mt-2">Last 30 days</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Panels -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="luxury-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-semibold">Top Performing Events</div>
                                <div class="text-muted small">Top 5 events by tickets sold & capacity%</div>
                            </div>
                            <div class="text-muted small">Updated just now</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Event</th>
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
                    <div class="luxury-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-semibold">Recent Activity</div>
                                <div class="text-muted small">Latest 5 reservations</div>
                            </div>
                            <div class="text-muted small"><a href="#">View all</a></div>
                        </div>
                        <div id="recentActivity">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="assets/js/admin.js"></script>
</body>

</html>