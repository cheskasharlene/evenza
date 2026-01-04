<?php
header('Content-Type: application/json');
session_start();
require_once '../connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = intval($_SESSION['user_id']);
$fullName = trim($_POST['fullName'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['mobile'] ?? '');

if ($fullName === '' || $email === '' || $phone === '') {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// basic email check
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$query = "UPDATE users SET fullName = ?, email = ?, phone = ? WHERE userId = ?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}

mysqli_stmt_bind_param($stmt, "sssi", $fullName, $email, $phone, $userId);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
    exit;
}

// refresh session data
$_SESSION['user_name'] = $fullName;
$_SESSION['user_email'] = $email;
$_SESSION['user_mobile'] = $phone;

echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
exit;

