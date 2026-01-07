<?php
session_start();
require_once 'connect.php';

$error = '';
$success = '';

if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phoneNumber = isset($_POST['phoneNumber']) ? trim($_POST['phoneNumber']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : '';

    if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT userId FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered';
            } else {
                $fullName = trim($firstName . ' ' . $lastName);
                
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (firstName, lastName, fullName, email, phoneNumber, password, role) VALUES (?, ?, ?, ?, ?, ?, 'user')");
                if ($stmt->execute([$firstName, $lastName, $fullName, $email, $phoneNumber, $hashedPassword])) {
                    $userId = $pdo->lastInsertId();
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $fullName;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_mobile'] = $phoneNumber;
                    $_SESSION['user_role'] = 'user';
                    
                    header('Location: profile.php');
                    exit;
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            }
        } catch(PDOException $e) {
            $error = 'Database error. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EVENZA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="navbar navbar-expand-lg navbar-light fixed-top luxury-nav">
        <div class="container">
            <a class="navbar-brand luxury-logo" href="index.php"><img src="assets/images/evenzaLogo.png" alt="EVENZA" class="evenza-logo-img"></a>
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
                    <li class="nav-item nav-divider">
                        <span class="nav-separator"></span>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">My Profile</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn-login" href="login.php">Login</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register active" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="register-page-section py-5 mt-5">
        <div class="container">
            <div class="page-header mb-5 text-center">
                <h1 class="page-title">Create an EVENZA Account</h1>
                <p class="page-subtitle">Join us to discover and book amazing events</p>
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
                        <form id="registerForm" method="post" action="" novalidate>
                            <div class="form-group mb-4">
                                <label for="firstName" class="form-label">First Name</label>
                                <input id="firstName" name="firstName" type="text" class="form-control luxury-input" required placeholder="Enter your first name">
                            </div>

                            <div class="form-group mb-4">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input id="lastName" name="lastName" type="text" class="form-control luxury-input" required placeholder="Enter your last name">
                            </div>

                            <div class="form-group mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" name="email" type="email" class="form-control luxury-input" required placeholder="you@example.com">
                            </div>

                            <div class="form-group mb-4">
                                <label for="phoneNumber" class="form-label">Phone Number</label>
                                <input id="phoneNumber" name="phoneNumber" type="tel" class="form-control luxury-input" required placeholder="+1 (555) 123-4567">
                            </div>

                            <div class="form-group mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control luxury-input" required placeholder="Enter your password">
                            </div>

                            <div class="form-group mb-4">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input id="confirmPassword" name="confirmPassword" type="password" class="form-control luxury-input" required placeholder="Confirm your password">
                            </div>

                            <button type="submit" class="btn btn-primary-luxury w-100 mb-4">Register</button>

                            <p class="text-center mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/login.js"></script>
</body>
</html>
