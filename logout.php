<?php
session_start();

// Check if this is an admin logout
$isAdmin = isset($_SESSION['admin_id']);

// Destroy the session
session_destroy();

// Redirect based on user type
if ($isAdmin) {
    header('Location: admin_login.php');
} else {
    header('Location: login.php');
}
exit;
