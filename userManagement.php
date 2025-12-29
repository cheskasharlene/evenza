<?php
// Admin Authentication Guard - Must be at the very top
require_once 'adminAuth.php';

// Load users data
$usersFile = __DIR__ . '/data/users.json';
$users = [];
if (file_exists($usersFile)) {
    $users = json_decode(file_get_contents($usersFile), true) ?? [];
}

// Load admins data to check admin status
$adminsFile = __DIR__ . '/data/admins.json';
$admins = [];
if (file_exists($adminsFile)) {
    $admins = json_decode(file_get_contents($adminsFile), true) ?? [];
}

// Create a map of admin emails for quick lookup
$adminEmails = [];
foreach ($admins as $admin) {
    $adminEmails[$admin['email']] = true;
}

// Add role information to users
foreach ($users as &$user) {
    $user['role'] = isset($adminEmails[$user['email']]) ? 'Admin' : 'Client';
}
unset($user);
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
    <title>User Management - EVENZA Admin</title>
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
            background-color: #4A5D4A;
            color: #FFFFFF;
        }
        .role-client {
            background-color: #e9ecef;
            color: #495057;
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
        .form-check-input:checked {
            background-color: #4A5D4A;
            border-color: #4A5D4A;
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
                        <a href="reservationsManagement.php" class="nav-link d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-clipboard-list"></i></span> Reservations</a>
                        <a href="userManagement.php" class="nav-link active d-flex align-items-center py-2"><span class="me-2"><i class="fas fa-users"></i></span> User Management</a>
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
                    <a href="logout.php" class="btn btn-admin-primary btn-sm">Logout</a>
                </div>
            </div>

            <div class="p-4">
                <!-- Controls Section -->
                <div class="admin-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1" style="font-family: 'Playfair Display', serif;">All Users (<?php echo count($users); ?>)</h5>
                            <div class="text-muted small">Manage user profiles and administrative permissions</div>
                        </div>
                        <button type="button" class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openAddUserModal()">
                            <i class="fas fa-plus"></i> Add New User
                        </button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="admin-card p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
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

        // Save user function
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
            
            // In a real implementation, this would make an AJAX call to save the user
            const action = isEditMode ? 'updated' : 'added';
            showFeedback('User "' + fullName + '" has been ' + action + ' successfully.', 'success');
            
            // Close modal and reload after a short delay
            const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
            modal.hide();
            
            setTimeout(function() {
                location.reload();
            }, 1500);
        }

        // Delete user function
        function deleteUser(userId, userName) {
            if (confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.')) {
                // In a real implementation, this would make an AJAX call to delete the user
                showFeedback('User "' + userName + '" has been deleted successfully.', 'success');
                // Reload page after a short delay to show the feedback
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
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

