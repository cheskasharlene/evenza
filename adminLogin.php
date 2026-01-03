<?php
session_start();
require_once 'connect.php';

$error = '';

if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        // Use MySQLi prepared statement - make email comparison case-insensitive
        $query = "SELECT userId, fullName, email, password, role FROM users WHERE LOWER(email) = LOWER(?) AND role = 'Admin'";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $admin = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['userId'];
                $_SESSION['admin_name'] = $admin['fullName'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['user_role'] = 'Admin';
                header('Location: admin.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
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
    <title>Admin Login - EVENZA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-login-wrapper {
            min-height: 100vh;
            background-color: #F9F7F2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .admin-login-card {
            background-color: #FFFFFF;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 3rem;
            max-width: 450px;
            width: 100%;
        }
        .admin-login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .admin-login-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 600;
            color: #1A1A1A;
            margin-bottom: 0.5rem;
        }
        .admin-login-header p {
            color: rgba(26, 26, 26, 0.7);
            font-size: 0.95rem;
        }
        .admin-login-logo {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .admin-login-logo img {
            height: 50px;
            width: auto;
        }
        .btn-admin-login {
            background-color: #4A5D4A;
            border-color: #4A5D4A;
            color: #FFFFFF;
            font-weight: 500;
            padding: 0.75rem 2rem;
            width: 100%;
        }
        .btn-admin-login:hover {
            background-color: #3a4a3a;
            border-color: #3a4a3a;
            color: #FFFFFF;
        }
        .form-label {
            font-weight: 500;
            color: #1A1A1A;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid rgba(74, 93, 74, 0.2);
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #4A5D4A;
            box-shadow: 0 0 0 0.2rem rgba(74, 93, 74, 0.15);
        }
    </style>
</head>
<body>
    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <div class="admin-login-logo">
                <img src="assets/images/evenzaLogo.png" alt="EVENZA">
            </div>
            
            <div class="admin-login-header">
                <h1>Admin Login</h1>
                <p>Access the EVENZA Admin Dashboard</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="" novalidate>
                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" name="email" type="email" class="form-control" required placeholder="admin@evenza.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" class="form-control" required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-admin-login">Login</button>
            </form>

            <div class="text-center mt-4">
                <a href="index.php" class="text-muted small text-decoration-none">← Back to EVENZA Home</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

