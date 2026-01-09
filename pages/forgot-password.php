<?php
session_start();
require_once '../core/connect.php';
require_once '../includes/helpers.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    
    // Validate all fields are filled
    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (($passwordValidation = validatePassword($newPassword)) !== true) {
        $error = $passwordValidation;
    } else {
        // Verify email exists in database
        $checkQuery = "SELECT userId, email FROM users WHERE LOWER(email) = LOWER(?)";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        
        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, "s", $email);
            mysqli_stmt_execute($checkStmt);
            $checkResult = mysqli_stmt_get_result($checkStmt);
            $user = mysqli_fetch_assoc($checkResult);
            mysqli_stmt_close($checkStmt);
            
            if (!$user) {
                $error = 'No account found with this email address.';
            } else {
                // Update password for the user with this email
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $userId = $user['userId'];
                
                $updateQuery = "UPDATE users SET password = ? WHERE userId = ?";
                $updateStmt = mysqli_prepare($conn, $updateQuery);
                
                if ($updateStmt) {
                    mysqli_stmt_bind_param($updateStmt, "si", $hashedPassword, $userId);
                    if (mysqli_stmt_execute($updateStmt)) {
                        $success = 'Your password has been reset successfully. You can now login with your new password.';
                    } else {
                        $error = 'Failed to update password. Please try again.';
                    }
                    mysqli_stmt_close($updateStmt);
                } else {
                    $error = 'Database error. Please try again.';
                }
            }
        } else {
            $error = 'Database error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - EVENZA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar navbar-expand-lg navbar-light fixed-top luxury-nav">
        <div class="container">
            <a class="navbar-brand luxury-logo" href="index.php"><img src="../assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link btn-login" href="login.php">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn-register" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="login-page-section py-5 mt-5">
        <div class="container">
            <div class="page-header mb-5 text-center">
                <h1 class="page-title">Reset Your Password</h1>
                <p class="page-subtitle">Create a new password for your account</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="luxury-card p-5">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <div class="text-center mt-4">
                                <a href="login.php" class="btn btn-primary-luxury">Go to Login</a>
                            </div>
                        <?php else: ?>
                            <!-- Reset Password Form -->
                            <form method="post" action="" novalidate>
                                <div class="form-group mb-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input id="email" name="email" type="email" class="form-control luxury-input" required placeholder="Enter your registered email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>

                                <div class="form-group mb-4">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <div class="password-input-wrapper" style="position: relative;">
                                        <input id="new_password" name="new_password" type="password" class="form-control luxury-input" required placeholder="Enter new password">
                                        <button type="button" class="password-toggle-btn" onclick="togglePassword('new_password', 'toggle_new_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6c757d; cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                            <span id="toggle_new_password">Show</span>
                                        </button>
                                    </div>
                                    <small class="text-muted">Must be at least 8 characters with 1 uppercase, 1 lowercase, and 1 number.</small>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <div class="password-input-wrapper" style="position: relative;">
                                        <input id="confirm_password" name="confirm_password" type="password" class="form-control luxury-input" required placeholder="Confirm new password">
                                        <button type="button" class="password-toggle-btn" onclick="togglePassword('confirm_password', 'toggle_confirm_password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6c757d; cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                            <span id="toggle_confirm_password">Show</span>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary-luxury w-100 mb-4">Reset Password</button>

                                <p class="text-center mb-0">
                                    Remember your password? <a href="login.php" class="text-decoration-none">Login here</a>
                                </p>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, toggleId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggle.textContent = 'Show';
            }
        }
    </script>
</body>
</html>

