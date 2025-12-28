<?php


session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to admin login page
    header('Location: adminLogin.php');
    exit;
}
