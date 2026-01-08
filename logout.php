<?php
session_start();

// Check which type of session is active before clearing
$isAdmin = isset($_SESSION['admin_id']);
$isUser = isset($_SESSION['user_id']);

// Only clear the relevant session variables, not the entire session
// This allows admin and user to be logged in simultaneously in the same browser
if ($isAdmin) {
    // Clear admin session variables
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
    
    // If user is also logged in, keep user session intact
    if (!$isUser) {
        // Only destroy session if user is not logged in
        session_destroy();
    }
    
    // Redirect to admin login
    header('Location: adminLogin.php');
} elseif ($isUser) {
    // Clear user session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_mobile']);
    unset($_SESSION['user_role']);
    unset($_SESSION['userId']);
    unset($_SESSION['firstName']);
    unset($_SESSION['role']);
    
    // If admin is also logged in, keep admin session intact
    if (!$isAdmin) {
        // Only destroy session if admin is not logged in
        session_destroy();
    }
    
    // Always redirect regular users to user login page
    header('Location: login.php');
} else {
    // No active session, just redirect to login
    session_destroy();
    header('Location: login.php');
}
exit;
