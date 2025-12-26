<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
require_once '../../config/database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn = getDBConnection();
    
    if ($method === 'GET') {
        // Get all reservations
        $result = $conn->query("
            SELECT r.reservationId, r.reservationDate, r.startTime, r.endTime, r.totalAmount, r.status,
                   e.title as eventTitle,
                   u.fullName as userName,
                   p.packageName
            FROM reservations r
            INNER JOIN events e ON r.eventId = e.eventId
            INNER JOIN users u ON r.userId = u.userId
            LEFT JOIN packages p ON r.packageId = p.packageId
            ORDER BY r.createdAt DESC
        ");
        
        $reservations = [];
        while ($row = $result->fetch_assoc()) {
            $reservations[] = $row;
        }
        
        echo json_encode(['success' => true, 'reservations' => $reservations]);
    }
    elseif ($method === 'POST') {
        // Update reservation status
        $reservationId = intval($_POST['reservationId']);
        $status = $_POST['status'];
        
        // Validate status
        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE reservationId = ?");
        $stmt->bind_param("si", $status, $reservationId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Reservation status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        $stmt->close();
    }
    
    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>

