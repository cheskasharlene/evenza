<?php
session_start();

$error = '';

// preserve a redirect target so we can send the user back after login
$redirect = '';
if (isset($_GET['redirect'])) {
    $redirect = $_GET['redirect'];
}
if (isset($_POST['redirect'])) {
    $redirect = $_POST['redirect'];
}

if (isset($_SESSION['user_id'])) {
    // already logged in: send to redirect target if valid, otherwise profile
    if (!empty($redirect) && strpos($redirect, 'http') === false && strpos($redirect, '//') === false) {
        header('Location: ' . $redirect);
    } else {
        header('Location: profile.php');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $usersFile = 'data/users.json';
        $users = [];
        
        if (file_exists($usersFile)) {
            $users = json_decode(file_get_contents($usersFile), true) ?? [];
        }

        $userFound = false;
        foreach ($users as $user) {
                if ($user['email'] === $email && password_verify($password, $user['password'])) {
                $userFound = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullName'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_mobile'] = $user['mobile'];
                    // after successful login, redirect to requested page if safe
                    $target = 'profile.php';
                    if (!empty($redirect) && strpos($redirect, 'http') === false && strpos($redirect, '//') === false) {
                        $target = $redirect;
                    }
                    header('Location: ' . $target);
                    exit;
            }
        }

        if (!$userFound) {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to EVENZA</title>
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
                        <li class="nav-item ms-2">
                            <a class="nav-link btn-register" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="login-page-section py-5 mt-5">
        <div class="container">
            <div class="page-header mb-5 text-center">
                <h1 class="page-title">Login to EVENZA</h1>
                <p class="page-subtitle">Sign in to manage your account and reservations</p>
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
                        <form id="loginForm" method="post" action="" novalidate>
                            <div class="form-group mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" name="email" type="email" class="form-control luxury-input" required placeholder="you@example.com">
                            </div>

                            <div class="form-group mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control luxury-input" required placeholder="Enter your password">
                            </div>

                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <button type="submit" class="btn btn-primary-luxury">Login</button>
                                <a href="forgot-password.php" class="text-muted small">Forgot Password?</a>
                            </div>

                            <p class="text-center mb-0">Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
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
