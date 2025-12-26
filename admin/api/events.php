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
        // Get single event or list
        if (isset($_GET['id'])) {
            $eventId = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT eventId, title, description, venue, category, imagePath FROM events WHERE eventId = ?");
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
            $stmt->close();
            
            if ($event) {
                echo json_encode(['success' => true, 'event' => $event]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Event not found']);
            }
        } else {
            // Get all events with optional category filter
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            
            if ($category) {
                $stmt = $conn->prepare("SELECT eventId, title, description, venue, category, imagePath FROM events WHERE category = ? ORDER BY eventId DESC");
                $stmt->bind_param("s", $category);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $conn->query("SELECT eventId, title, description, venue, category, imagePath FROM events ORDER BY eventId DESC");
            }
            
            $events = [];
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
            
            if (isset($stmt)) $stmt->close();
            echo json_encode(['success' => true, 'events' => $events]);
        }
    }
    elseif ($method === 'POST') {
        // Add or update event
        $action = $_POST['action'] ?? 'add';
        
        if ($action === 'update') {
            $eventId = intval($_POST['eventId']);
            $title = trim($_POST['title']);
            $venue = trim($_POST['venue']);
            $description = trim($_POST['description']);
            $category = $_POST['category'];
            $imagePath = trim($_POST['imagePath']);
            
            $stmt = $conn->prepare("UPDATE events SET title = ?, venue = ?, description = ?, category = ?, imagePath = ? WHERE eventId = ?");
            $stmt->bind_param("sssssi", $title, $venue, $description, $category, $imagePath, $eventId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update event']);
            }
            $stmt->close();
        } else {
            // Add new event
            $title = trim($_POST['title']);
            $venue = trim($_POST['venue']);
            $description = trim($_POST['description']);
            $category = $_POST['category'];
            $imagePath = trim($_POST['imagePath']);
            
            $stmt = $conn->prepare("INSERT INTO events (title, venue, description, category, imagePath, totalCapacity, availableSlots) VALUES (?, ?, ?, ?, ?, 100, 100)");
            $stmt->bind_param("sssss", $title, $venue, $description, $category, $imagePath);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Event added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add event']);
            }
            $stmt->close();
        }
    }
    elseif ($method === 'DELETE') {
        // Delete event
        $data = json_decode(file_get_contents('php://input'), true);
        $eventId = intval($data['eventId']);
        
        $stmt = $conn->prepare("DELETE FROM events WHERE eventId = ?");
        $stmt->bind_param("i", $eventId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete event']);
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

