<?php
/**
 * Admin Authentication Guard
 * 
 * Place this file at the very top of all admin pages (before any HTML output).
 * It checks if an admin session exists and redirects to adminLogin.php if not.
 * 
 * Usage: require_once 'adminAuth.php';
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to admin login page
    header('Location: adminLogin.php');
    exit;
}

// Optional: You can add additional security checks here
// For example, check if admin account is still active, etc.

