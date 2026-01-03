<?php
session_start();

// Check if user is admin (check both admin_id and role)
$isAdmin = isset($_SESSION['admin_id']) || (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'Admin'));

session_destroy();

if ($isAdmin) {
    header('Location: adminLogin.php');
} else {
    header('Location: login.php');
}
exit;
