<?php
require_once '../process/auth/adminAuth.php';
require_once '../../core/connect.php';
require_once '../../includes/helpers.php';

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$roleFilter = isset($_GET['role']) ? trim($_GET['role']) : '';

$query = "SELECT userid, firstName, lastName, fullName, email, phone, role FROM users WHERE 1=1";
$params = [];
$types = '';

if (!empty($searchQuery)) {
    $query .= " AND (fullName LIKE ? OR email LIKE ?)";
    $searchParam = '%' . $searchQuery . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
}

if (!empty($roleFilter) && $roleFilter !== 'all') {
    $query .= " AND role = ?";
    $params[] = $roleFilter;
    $types .= 's';
}

$query .= " ORDER BY userid ASC";

$users = [];

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = [
                    'id' => $row['userid'],
                    'firstName' => $row['firstName'],
                    'lastName' => $row['lastName'],
                    'fullName' => $row['fullName'],
                    'email' => $row['email'],
                    'mobile' => !empty($row['phone']) ? formatPhoneNumber($row['phone']) : 'N/A',
                    'role' => ucfirst(strtolower($row['role'])) 
                ];
            }
            mysqli_free_result($result);
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = [
                'id' => $row['userid'],
                'firstName' => $row['firstName'],
                'lastName' => $row['lastName'],
                'fullName' => $row['fullName'],
                'email' => $row['email'],
                'mobile' => !empty($row['phone']) ? formatPhoneNumber($row['phone']) : 'N/A',
                'role' => ucfirst(strtolower($row['role'])) 
            ];
        }
        mysqli_free_result($result);
    } else {
        $error = mysqli_error($conn);
        error_log("User Management Query Error: " . $error);
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
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>User Management - EVENZA Admin</title>
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
            overflow-x: hidden;
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
        .table {
            margin-bottom: 0;
        }
        .table thead {
            background: linear-gradient(135deg, rgba(74, 93, 74, 0.05) 0%, rgba(74, 93, 74, 0.02) 100%);
        }
        .table th {
            font-weight: 600;
            color: #1A1A1A;
            border-bottom: 2px solid rgba(74, 93, 74, 0.15);
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        .table td {
            vertical-align: middle;
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(74, 93, 74, 0.08);
            white-space: normal;
            word-wrap: break-word;
        }
        .table tbody tr {
            background-color: transparent;
        }
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4A5D4A 0%, #6B7F5A 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .role-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .role-admin {
            background-color: rgba(5, 150, 105, 0.15);
            color: #059669;
            border: 1px solid #059669;
        }
        .role-client {
            background-color: rgba(107, 114, 128, 0.15);
            color: #6b7280;
            border: 1px solid #6b7280;
        }
        .search-input {
            border: 1px solid rgba(74, 93, 74, 0.2);
            border-radius: 50px;
            padding: 0.6rem 1.25rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            border-color: #5A6B4F;
            box-shadow: 0 0 0 0.2rem rgba(90, 107, 79, 0.15);
            outline: none;
        }
        
        .btn-add-user {
            background-color: #5A6B4F;
            border-color: #5A6B4F;
            color: #FFFFFF;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .btn-add-user:hover {
            background-color: #8B7A6B;
            border-color: #8B7A6B;
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .role-badge {
            border-radius: 50px;
        }
        .action-btn {
            background: rgba(74, 93, 74, 0.08);
            border: none;
            color: #4A5D4A;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 0.25rem;
        }
        .action-btn:hover {
            background: rgba(74, 93, 74, 0.15);
            color: #3a4a3a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .action-btn.text-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        .action-btn.text-danger:hover {
            background: rgba(220, 53, 69, 0.2);
            color: #c82333;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .form-check-input:checked {
            background-color: #4A5D4A;
            border-color: #4A5D4A;
        }
        .admin-sidebar a:not(.active):hover {
            background: rgba(74, 93, 74, 0.05) !important;
            color: #4A5D4A !important;
            border-left-color: rgba(74, 93, 74, 0.3) !important;
            transform: translateX(5px);
        }
        @media (max-width: 991px) { 
            .admin-sidebar { 
                width: 100%; 
                position: relative;
                height: auto;
                display: none;
            }
            .admin-sidebar.show {
                display: flex;
            }
            .admin-content {
                margin-left: 0;
                width: 100%;
            }
            .admin-wrapper {
                flex-direction: column;
            }
            .filter-section {
                flex-direction: column;
                gap: 1rem;
            }
            .filter-section > div {
                width: 100% !important;
                min-width: 100% !important;
            }
        }
        @media (max-width: 768px) {
            .admin-top-nav {
                padding: 0.75rem 1rem;
                flex-wrap: wrap;
            }
            .admin-top-nav h4 {
                font-size: 1.25rem;
            }
            .table-responsive {
                font-size: 0.875rem;
                overflow-x: visible;
                width: 100%;
            }
            .table th,
            .table td {
                padding: 0.5rem;
            }
            .btn-admin-primary {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
        }
        @media (max-width: 576px) {
            .admin-top-nav {
                padding: 0.5rem;
            }
            .admin-top-nav > div {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .table th,
            .table td {
                font-size: 0.75rem;
                padding: 0.4rem;
            }
            .table th:nth-child(3),
            .table td:nth-child(3) {
                display: none;
            }
            .search-input,
            .role-filter {
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex admin-wrapper">
        <!-- Sidebar -->
        <div class="d-flex flex-column admin-sidebar p-4" style="background: linear-gradient(180deg, #FFFFFF 0%, #F9F7F2 100%);">
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
                        <a href="userManagement.php" class="d-flex align-items-center py-3 px-3 rounded-3 active" style="background: linear-gradient(135deg, rgba(90, 107, 79, 0.15) 0%, rgba(90, 107, 79, 0.08) 100%); color: #5A6B4F; font-weight: 600; text-decoration: none; border-left: 3px solid #5A6B4F;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-users"></i></span> 
                            <span>User Management</span>
                        </a>
                        <a href="reviewsManagement.php" class="d-flex align-items-center py-3 px-3 rounded-3" style="transition: all 0.3s ease; color: rgba(26, 26, 26, 0.7); text-decoration: none; border-left: 3px solid transparent;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-star"></i></span>
                            <span style="font-weight: 500;">Reviews & Feedback</span>
                        </a>
                        <a href="smsInbox.php" class="d-flex align-items-center py-3 px-3 rounded-3" style="transition: all 0.3s ease; color: rgba(26, 26, 26, 0.7); text-decoration: none; border-left: 3px solid transparent;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-sms"></i></span> 
                            <span style="font-weight: 500;">SMS Inbox</span>
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
                        <h4 class="mb-0" style="font-family: 'Playfair Display', serif;">User Management</h4>
                        <div class="text-muted small">Manage user accounts and permissions</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user text-muted"></i>
                        </div>
                    </div>
                    <a href="../user/process/logout.php?type=admin" class="btn btn-admin-primary btn-sm">Logout</a>
                </div>
            </div>

            <div class="p-4" style="padding: 2rem !important; width: 100%; overflow-x: hidden; box-sizing: border-box;">
                <!-- Controls Section -->
                <!-- Search & Filter Bar -->
                <div class="admin-card p-4 mb-4">
                    <div class="row g-3 align-items-end">
                        <!-- Search Input -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #1A1A1A;">Search</label>
                            <div class="position-relative">
                                <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #6c757d; z-index: 10;"></i>
                                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>" style="padding-left: 45px;">
                            </div>
                        </div>
                        
                        <!-- Role Filter -->
                        <div class="col-md-4">
                            <label for="roleFilter" class="form-label fw-semibold" style="color: #1A1A1A;">Filter by Role</label>
                            <div class="custom-dropdown-wrapper">
                                <select id="roleFilter" class="form-select" style="display: none;">
                                    <option value="all" <?php echo (empty($roleFilter) || $roleFilter === 'all') ? 'selected' : ''; ?>>All Roles</option>
                                    <option value="admin" <?php echo ($roleFilter === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="user" <?php echo ($roleFilter === 'user' || $roleFilter === 'client') ? 'selected' : ''; ?>>User</option>
                                </select>
                                <div class="custom-dropdown" id="customRoleFilter">
                                    <div class="custom-dropdown-selected">
                                        <span><?php 
                                            if (empty($roleFilter) || $roleFilter === 'all') {
                                                echo 'All Roles';
                                            } elseif ($roleFilter === 'admin') {
                                                echo 'Admin';
                                            } else {
                                                echo 'User';
                                            }
                                        ?></span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="custom-dropdown-options">
                                        <div class="custom-dropdown-option" data-value="all">All Roles</div>
                                        <div class="custom-dropdown-option" data-value="admin">Admin</div>
                                        <div class="custom-dropdown-option" data-value="user">User</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add User Button -->
                        <div class="col-md-2">
                            <button type="button" class="btn btn-add-user w-100" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openAddUserModal()">
                                <i class="fas fa-plus me-2"></i> Add New User
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="admin-card p-4">
                    <div class="table-responsive" style="overflow-x: visible; width: 100%;">
                        <table class="table align-middle" style="width: 100%; table-layout: auto;">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-users fa-2x mb-3 d-block"></i>
                                        No users found.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                <?php echo strtoupper(substr($user['fullName'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($user['fullName']); ?></div>
                                                <div class="text-muted small">ID: <?php echo htmlspecialchars($user['id']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($user['email']); ?></div>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($user['mobile'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td>
                                        <span class="role-badge <?php echo strtolower($user['role']) === 'admin' ? 'role-admin' : 'role-client'; ?>">
                                            <i class="fas <?php echo strtolower($user['role']) === 'admin' ? 'fa-shield-alt' : 'fa-user'; ?> me-1"></i>
                                            <?php echo htmlspecialchars($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="action-btn" onclick="editUser('<?php echo htmlspecialchars($user['id'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['fullName'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['mobile'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['role'], ENT_QUOTES); ?>')" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn text-danger" onclick="deleteUser('<?php echo htmlspecialchars($user['id'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['fullName'], ENT_QUOTES); ?>')" title="Delete">
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

    <!-- User Modal (Add/Edit) -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family: 'Playfair Display', serif;" id="userModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="userId" name="userId">
                        <div class="mb-3">
                            <label for="userFullName" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userFullName" name="fullName" required placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="userEmail" name="email" required placeholder="Enter email address">
                        </div>
                        <div class="mb-3">
                            <label for="userMobile" class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control" id="userMobile" name="mobile" placeholder="Enter mobile number">
                        </div>
                        <div class="mb-3" id="passwordField">
                            <label for="userPassword" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="userPassword" name="password" placeholder="Enter password">
                            <div class="form-text">Leave blank when editing to keep current password.</div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isAdmin" name="isAdmin">
                                <label class="form-check-label" for="isAdmin">
                                    <strong>Grant Administrative Permissions</strong>
                                </label>
                                <div class="form-text">Check this box to assign administrative access to this user.</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-admin-primary" onclick="saveUser()">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let isEditMode = false;
        let currentUserId = null;

        // Sidebar toggle for mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('adminSidebarToggle');
            const sidebar = document.querySelector('.admin-sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
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

        // Open add user modal
        function openAddUserModal() {
            isEditMode = false;
            currentUserId = null;
            document.getElementById('userModalLabel').textContent = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('userPassword').required = true;
        }

        // Edit user function
        function editUser(userId, fullName, email, mobile, role) {
            isEditMode = true;
            currentUserId = userId;
            document.getElementById('userModalLabel').textContent = 'Edit User';
            document.getElementById('userId').value = userId;
            document.getElementById('userFullName').value = fullName;
            document.getElementById('userEmail').value = email;
            document.getElementById('userMobile').value = mobile;
            document.getElementById('isAdmin').checked = (role === 'Admin');
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('userPassword').required = false;
            document.getElementById('userPassword').placeholder = 'Leave blank to keep current password';
            
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }

        function saveUser() {
            const fullName = document.getElementById('userFullName').value.trim();
            const email = document.getElementById('userEmail').value.trim();
            const mobile = document.getElementById('userMobile').value.trim();
            const password = document.getElementById('userPassword').value;
            const isAdmin = document.getElementById('isAdmin').checked;
            
            if (!fullName || !email) {
                showFeedback('Please fill in all required fields.', 'error');
                return;
            }
            
            if (!isEditMode && !password) {
                showFeedback('Password is required for new users.', 'error');
                return;
            }
            
            const action = isEditMode ? 'updated' : 'added';
            showFeedback('User "' + fullName + '" has been ' + action + ' successfully.', 'success');
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
            modal.hide();
            
            setTimeout(function() {
                location.reload();
            }, 1500);
        }

        function deleteUser(userId, userName) {
            if (confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.')) {
                showFeedback('User "' + userName + '" has been deleted successfully.', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        }

        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const usersTableBody = document.getElementById('usersTableBody');
        
        // Initialize custom dropdown for role filter
        function initCustomDropdown(nativeSelect, customDropdown) {
            const selectedText = customDropdown.querySelector('.custom-dropdown-selected span');
            const options = customDropdown.querySelectorAll('.custom-dropdown-option');
            
            // Set initial selected value
            const initialValue = nativeSelect.value;
            const initialText = Array.from(nativeSelect.options).find(opt => opt.value === initialValue)?.textContent || 'All Roles';
            selectedText.textContent = initialText;
            
            // Toggle dropdown on click
            customDropdown.querySelector('.custom-dropdown-selected').addEventListener('click', function(e) {
                e.stopPropagation();
                customDropdown.classList.toggle('open');
            });
            
            // Handle option selection
            options.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;
                    
                    // Update native select
                    nativeSelect.value = value;
                    
                    // Update custom dropdown display
                    selectedText.textContent = text;
                    customDropdown.classList.remove('open');
                    
                    // Trigger search
                    performSearch();
                });
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!customDropdown.contains(e.target)) {
                    customDropdown.classList.remove('open');
                }
            });
        }
        
        // Initialize role filter dropdown
        const customRoleFilter = document.getElementById('customRoleFilter');
        if (customRoleFilter && roleFilter) {
            initCustomDropdown(roleFilter, customRoleFilter);
        }

        function performSearch() {
            const searchQuery = searchInput.value.trim();
            const roleValue = roleFilter.value;
            
            const url = new URL(window.location.href);
            if (searchQuery) {
                url.searchParams.set('search', searchQuery);
            } else {
                url.searchParams.delete('search');
            }
            if (roleValue && roleValue !== 'all') {
                url.searchParams.set('role', roleValue);
            } else {
                url.searchParams.delete('role');
            }
            window.history.pushState({}, '', url);
            
            fetch(`/evenza/admin/process/search/searchUsers.php?search=${encodeURIComponent(searchQuery)}&role=${encodeURIComponent(roleValue)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateUsersTable(data.users);
                    } else {
                        showFeedback('Error searching users: ' + (data.message || 'Unknown error'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showFeedback('An error occurred while searching. Please try again.', 'error');
                });
        }

        function updateUsersTable(users) {
            if (users.length === 0) {
                usersTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-2x mb-3 d-block"></i>
                            No users found.
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            users.forEach(user => {
                const roleClass = (user.role.toLowerCase() === 'admin') ? 'role-admin' : 'role-client';
                const roleIcon = (user.role.toLowerCase() === 'admin') ? 'fa-shield-alt' : 'fa-user';
                const fullName = user.fullName || (user.firstName + ' ' + user.lastName);
                const initial = fullName.charAt(0).toUpperCase();
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">${initial}</div>
                                <div>
                                    <div class="fw-semibold">${escapeHtml(fullName)}</div>
                                    <div class="text-muted small">ID: ${escapeHtml(user.id)}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>${escapeHtml(user.email)}</div>
                        </td>
                        <td>
                            <div>${escapeHtml(user.mobile || 'N/A')}</div>
                        </td>
                        <td>
                            <span class="role-badge ${roleClass}">
                                <i class="fas ${roleIcon} me-1"></i>
                                ${escapeHtml(user.role)}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="action-btn" onclick="editUser('${escapeHtml(user.id)}', '${escapeHtml(fullName)}', '${escapeHtml(user.email)}', '${escapeHtml(user.mobile || '')}', '${escapeHtml(user.role)}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn text-danger" onclick="deleteUser('${escapeHtml(user.id)}', '${escapeHtml(fullName)}')" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            usersTableBody.innerHTML = html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const messageType = urlParams.get('type') || 'success';
        if (message) {
            showFeedback(decodeURIComponent(message), messageType);
        }
    </script>
</body>

</html>

