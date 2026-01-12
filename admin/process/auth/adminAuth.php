<?php
session_start();

if (!isset($_SESSION['admin_id']) && !(isset($_SESSION['user_id']) && isset($_SESSION['role']) && (strtolower($_SESSION['role']) === 'admin'))) {
    header('Location: ../../adminPages/adminLogin.php');
    exit;
}
