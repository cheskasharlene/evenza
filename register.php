<?php
session_start();

$error = '';
$success = '';

// If already logged in, redirect to profile
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : '';

    // Validation
    if (empty($fullName) || empty($email) || empty($mobile) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // Create data directory if it doesn't exist
        if (!is_dir('data')) {
            mkdir('data', 0755, true);
        }

        $usersFile = 'data/users.json';
        $users = [];
        
        // Read existing users
        if (file_exists($usersFile)) {
            $users = json_decode(file_get_contents($usersFile), true) ?? [];
        }

        // Check if email already exists
        $emailExists = false;
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $emailExists = true;
                break;
            }
        }

        if ($emailExists) {
            $error = 'Email already registered. Please login or use a different email.';
        } else {
            // Create new user
            $newUser = [
                'id' => uniqid(),
                'fullName' => $fullName,
                'email' => $email,
                'mobile' => $mobile,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'createdAt' => date('Y-m-d H:i:s')
            ];

            $users[] = $newUser;

            // Save users to JSON file
            if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
                // Auto login after registration
                $_SESSION['user_id'] = $newUser['id'];
                $_SESSION['user_name'] = $newUser['fullName'];
                $_SESSION['user_email'] = $newUser['email'];
                $_SESSION['user_mobile'] = $newUser['mobile'];
                header('Location: profile.php');
                exit;
            } else {
                $error = 'Failed to create account. Please try again.';
            }
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item ms-3">
                            <a class="nav-link" href="profile.php">My Profile</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-3">
                            <a class="nav-link btn-login" href="login.php">Login</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register" href="register.php">Register</a>
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
                                <label for="fullName" class="form-label">Full Name</label>
                                <input id="fullName" name="fullName" type="text" class="form-control luxury-input" required placeholder="Enter your full name">
                            </div>

                            <div class="form-group mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" name="email" type="email" class="form-control luxury-input" required placeholder="you@example.com">
                            </div>

                            <div class="form-group mb-4">
                                <label for="mobile" class="form-label">Mobile Number</label>
                                <input id="mobile" name="mobile" type="tel" class="form-control luxury-input" required placeholder="+1 (555) 123-4567">
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
