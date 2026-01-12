<?php
require_once '../process/auth/adminAuth.php';
require_once '../../core/connect.php';
require_once '../../includes/helpers.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$unreadQuery = "SELECT COUNT(*) as unread_count FROM sms_messages WHERE is_read = 0 AND (raw_data IS NULL OR raw_data NOT LIKE ?)";
$unreadStmt = mysqli_prepare($conn, $unreadQuery);
$unreadCount = 0;
if ($unreadStmt) {
    $sentPattern = '%"type":"sent"%';
    mysqli_stmt_bind_param($unreadStmt, "s", $sentPattern);
    mysqli_stmt_execute($unreadStmt);
    $unreadResult = mysqli_stmt_get_result($unreadStmt);
    if ($unreadRow = mysqli_fetch_assoc($unreadResult)) {
        $unreadCount = $unreadRow['unread_count'] ?? 0;
    }
    mysqli_stmt_close($unreadStmt);
}

$countQuery = "SELECT COUNT(*) as total FROM sms_messages WHERE raw_data IS NULL OR raw_data NOT LIKE ?";
$countStmt = mysqli_prepare($conn, $countQuery);
$totalCount = 0;
if ($countStmt) {
    $sentPattern = '%"type":"sent"%';
    mysqli_stmt_bind_param($countStmt, "s", $sentPattern);
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    if ($countRow = mysqli_fetch_assoc($countResult)) {
        $totalCount = $countRow['total'] ?? 0;
    }
    mysqli_stmt_close($countStmt);
}

$query = "SELECT sms_id, phone_number, message_body, received_at, is_read, created_at 
          FROM sms_messages 
          WHERE raw_data IS NULL OR raw_data NOT LIKE ? 
          ORDER BY received_at DESC, created_at DESC 
          LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $query);
$smsMessages = [];

if ($stmt) {
    $sentPattern = '%"type":"sent"%';
    mysqli_stmt_bind_param($stmt, "sii", $sentPattern, $perPage, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $smsMessages[] = $row;
    }
    mysqli_stmt_close($stmt);
}

$totalPages = ceil($totalCount / $perPage);
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
    <title>SMS Inbox - EVENZA Admin</title>
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
        .sms-message-item {
            background-color: #FFFFFF;
            border: 1px solid rgba(74, 93, 74, 0.1);
            border-radius: 20px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }
        .sms-message-item.unread {
            background-color: #F0F7F0;
            border-left: 4px solid #4A5D4A;
            font-weight: 600;
        }
        .sms-phone {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: #4A5D4A;
            margin-bottom: 0.5rem;
        }
        .sms-body {
            color: #1A1A1A;
            margin-bottom: 0.75rem;
            line-height: 1.6;
        }
        .sms-date {
            color: rgba(26, 26, 26, 0.6);
            font-size: 0.875rem;
        }
        .unread-badge {
            display: inline-block;
            background-color: #4A5D4A;
            color: #FFFFFF;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        .empty-state-icon {
            font-size: 4rem;
            color: rgba(74, 93, 74, 0.3);
            margin-bottom: 1.5rem;
        }
        .empty-state-message {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #4A5D4A;
            margin-bottom: 0.5rem;
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
        }
    </style>
</head>

<body>
    <div class="d-flex admin-wrapper">
        <div class="d-flex flex-column admin-sidebar p-4" style="background: linear-gradient(180deg, #FFFFFF 0%, #F9F7F2 100%);">
            <div class="d-flex align-items-center mb-5" style="padding: 1rem 0;">
                <div class="luxury-logo">
                    <img src="../../assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img" style="max-width: 180px;">
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
                        <a href="reviewsManagement.php" class="d-flex align-items-center py-3 px-3 rounded-3" style="transition: all 0.3s ease; color: rgba(26, 26, 26, 0.7); text-decoration: none; border-left: 3px solid transparent;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-star"></i></span>
                            <span style="font-weight: 500;">Reviews & Feedback</span>
                        </a>
                        <a href="smsInbox.php" class="d-flex align-items-center py-3 px-3 rounded-3 active" style="background: linear-gradient(135deg, rgba(90, 107, 79, 0.15) 0%, rgba(90, 107, 79, 0.08) 100%); color: #5A6B4F; font-weight: 600; text-decoration: none; border-left: 3px solid #5A6B4F;">
                            <span class="me-3" style="width: 24px; text-align: center;"><i class="fas fa-sms"></i></span> 
                            <span>SMS Inbox</span>
                            <?php if ($unreadCount > 0): ?>
                                <span class="unread-badge"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
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
                        <h4 class="mb-0" style="font-family: 'Playfair Display', serif;">SMS Inbox</h4>
                        <div class="text-muted small">View and manage incoming SMS messages</div>
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

            <div class="p-4" style="padding: 2rem !important;">
                <div class="admin-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0" style="font-family: 'Playfair Display', serif;">Incoming Messages</h5>
                        <div class="text-muted small">
                            Total: <?php echo $totalCount; ?> messages
                            <?php if ($unreadCount > 0): ?>
                                | <span class="text-danger"><?php echo $unreadCount; ?> unread</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (empty($smsMessages)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="empty-state-message">No messages yet</div>
                            <div class="empty-state-subtext">Incoming SMS messages will appear here</div>
                        </div>
                    <?php else: ?>
                        <div class="sms-messages-list">
                            <?php foreach ($smsMessages as $sms): ?>
                                <div class="sms-message-item <?php echo $sms['is_read'] == 0 ? 'unread' : ''; ?>" 
                                     data-sms-id="<?php echo $sms['sms_id']; ?>"
                                     onclick="markAsRead(<?php echo $sms['sms_id']; ?>)">
                                    <div class="sms-phone">
                                        <i class="fas fa-phone me-2"></i>
                                        <?php echo htmlspecialchars(formatPhoneNumber($sms['phone_number'])); ?>
                                        <?php if ($sms['is_read'] == 0): ?>
                                            <span class="unread-badge">New</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sms-body">
                                        <?php echo nl2br(htmlspecialchars($sms['message_body'])); ?>
                                    </div>
                                    <div class="sms-date">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('F d, Y g:i A', strtotime($sms['received_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="SMS pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.js"></script>
    <script>
        const sidebarToggle = document.getElementById('adminSidebarToggle');
        const sidebar = document.querySelector('.admin-sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        function markAsRead(smsId) {
            fetch('/evenza/admin/process/sms/markSMSRead.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'smsId=' + encodeURIComponent(smsId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const messageItem = document.querySelector(`[data-sms-id="${smsId}"]`);
                    if (messageItem) {
                        messageItem.classList.remove('unread');
                        const badge = messageItem.querySelector('.unread-badge');
                        if (badge) {
                            badge.remove();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error marking as read:', error);
            });
        }
    </script>
</body>
</html>

