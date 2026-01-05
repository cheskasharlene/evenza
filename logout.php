<?php
session_start();

// Check if user is admin - only check admin_id, not user_role
// This ensures regular users with role='admin' but no admin_id don't get redirected to admin login
$isAdmin = isset($_SESSION['admin_id']);

// Destroy all session data
session_destroy();

// Redirect based on admin status
if ($isAdmin) {
    header('Location: adminLogin.php');
} else {
    // Always redirect regular users to user login page
    header('Location: login.php');
}
exit;
