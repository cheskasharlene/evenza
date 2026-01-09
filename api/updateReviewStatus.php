<?php
/**
 * API endpoint to update review status
 */

session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();

require_once '../admin/adminAuth.php';
require_once '../core/connect.php';

try {
    if (!isset($_SESSION['admin_id'])) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }

    $reviewId = isset($input['reviewId']) ? intval($input['reviewId']) : 0;
    $status = isset($input['status']) ? trim($input['status']) : '';

    if ($reviewId <= 0) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['error' => 'Invalid review ID']);
        exit;
    }

    if (!in_array($status, ['approved', 'rejected', 'pending'])) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['error' => 'Invalid status']);
        exit;
    }

    $updateQuery = "UPDATE reviews SET status = ? WHERE reviewId = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    
    if (!$updateStmt) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
        exit;
    }

    mysqli_stmt_bind_param($updateStmt, "si", $status, $reviewId);
    
    if (mysqli_stmt_execute($updateStmt)) {
        mysqli_stmt_close($updateStmt);
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Review status updated successfully'
        ]);
    } else {
        mysqli_stmt_close($updateStmt);
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update review status']);
    }

} catch (Exception $e) {
    ob_end_clean();
    error_log('Update review status error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while updating review status']);
}

set_exception_handler(function($exception) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred']);
    exit;
});

