<?php
session_start();

// Check if admin is logged in (either through admin login or regular login with admin role)
if (!isset($_SESSION['admin_id']) && !(isset($_SESSION['user_id']) && isset($_SESSION['role']) && (strtolower($_SESSION['role']) === 'admin'))) {
    // Redirect to admin login page
    header('Location: adminLogin.php');
    exit;
}
