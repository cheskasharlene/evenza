<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    $conn = getDBConnection();
    
    // Total Revenue
    $result = $conn->query("SELECT SUM(totalAmount) as total FROM reservations WHERE status != 'cancelled'");
    $revenue = $result->fetch_assoc()['total'] ?? 0;
    
    // Total Tickets Sold
    $result = $conn->query("SELECT COUNT(*) as total FROM reservations WHERE status != 'cancelled'");
    $ticketsSold = $result->fetch_assoc()['total'] ?? 0;
    
    // Active Events
    $result = $conn->query("SELECT COUNT(*) as total FROM events");
    $activeEvents = $result->fetch_assoc()['total'] ?? 0;
    
    // New Users (last 30 days)
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE createdAt >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $newUsers = $result->fetch_assoc()['total'] ?? 0;
    
    // Top Performing Events
    $result = $conn->query("
        SELECT e.title, 
               COUNT(r.reservationId) as ticketsSold,
               (COUNT(r.reservationId) * 100.0 / e.totalCapacity) as capacity,
               SUM(r.totalAmount) as revenue
        FROM events e
        LEFT JOIN reservations r ON e.eventId = r.eventId AND r.status != 'cancelled'
        GROUP BY e.eventId
        ORDER BY ticketsSold DESC, revenue DESC
        LIMIT 5
    ");
    $topEvents = [];
    while ($row = $result->fetch_assoc()) {
        $topEvents[] = [
            'title' => $row['title'],
            'ticketsSold' => (int)$row['ticketsSold'],
            'capacity' => round($row['capacity'], 1),
            'revenue' => (float)($row['revenue'] ?? 0)
        ];
    }
    
    // Recent Activity
    $result = $conn->query("
        SELECT r.reservationId, r.createdAt,
               u.fullName as userName,
               e.title as eventName
        FROM reservations r
        INNER JOIN users u ON r.userId = u.userId
        INNER JOIN events e ON r.eventId = e.eventId
        ORDER BY r.createdAt DESC
        LIMIT 5
    ");
    $recentActivity = [];
    while ($row = $result->fetch_assoc()) {
        $recentActivity[] = [
            'userName' => $row['userName'],
            'eventName' => $row['eventName'],
            'date' => date('M j, Y', strtotime($row['createdAt']))
        ];
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'revenue' => (float)$revenue,
        'ticketsSold' => (int)$ticketsSold,
        'activeEvents' => (int)$activeEvents,
        'newUsers' => (int)$newUsers,
        'topEvents' => $topEvents,
        'recentActivity' => $recentActivity
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading dashboard data: ' . $e->getMessage()
    ]);
}
?>

