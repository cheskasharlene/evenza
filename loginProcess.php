<?php
session_start();
require_once 'connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        try {
            // Query users table by email
            $stmt = $pdo->prepare("SELECT userId, firstName, lastName, fullName, email, phoneNumber, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Verify password using password_verify()
            if ($user && password_verify($password, $user['password'])) {
                // Start session with userId, firstName, and role
                $_SESSION['userId'] = $user['userId'];
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['role'] = $user['role'];
                
                // Also set additional session variables for compatibility
                $_SESSION['user_id'] = $user['userId'];
                $_SESSION['user_name'] = $user['fullName'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_mobile'] = $user['phoneNumber'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin' || $user['role'] === 'Admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                // Generic error message for security
                $error = 'Invalid email or password';
            }
        } catch(PDOException $e) {
            // Generic error message for security
            $error = 'Invalid email or password';
        }
    }
    
    // If there's an error, redirect back to login with error message
    if (!empty($error)) {
        $_SESSION['login_error'] = $error;
        header('Location: login.php');
        exit;
    }
} else {
    // If not a POST request, redirect to login page
    header('Location: login.php');
    exit;
}
?>

