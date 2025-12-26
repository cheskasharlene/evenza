<?php
/**
 * Process Reservation Form Submission
 * Handles reservation form data and inserts into database
 */

session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$error = '';
$success = false;
$eventName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and validate form data
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
    $eventId = isset($_POST['eventId']) ? intval($_POST['eventId']) : 0;
    $packageId = isset($_POST['packageId']) ? intval($_POST['packageId']) : null;
    $reservationDate = isset($_POST['reservationDate']) ? trim($_POST['reservationDate']) : '';
    $startTime = isset($_POST['eventStartTime']) ? trim($_POST['eventStartTime']) : '';
    $endTime = isset($_POST['eventEndTime']) ? trim($_POST['eventEndTime']) : '';
    $totalAmount = isset($_POST['totalAmount']) ? floatval($_POST['totalAmount']) : 0;
    
    // Validate user ID matches session
    if ($userId != $_SESSION['user_id']) {
        $error = 'Invalid user session.';
    }
    // Validate required fields
    elseif ($eventId <= 0) {
        $error = 'Invalid event ID.';
    }
    elseif (empty($reservationDate)) {
        $error = 'Reservation date is required.';
    }
    elseif (empty($startTime)) {
        $error = 'Start time is required.';
    }
    elseif (empty($endTime)) {
        $error = 'End time is required.';
    }
    elseif (!$packageId || $packageId <= 0) {
        $error = 'Please select a package.';
    }
    elseif ($totalAmount <= 0) {
        $error = 'Invalid total amount.';
    }
    // Validate date is not in the past
    elseif (strtotime($reservationDate) < strtotime('today')) {
        $error = 'Reservation date cannot be in the past.';
    }
    // Validate end time is after start time
    elseif (strtotime($endTime) <= strtotime($startTime)) {
        $error = 'End time must be after start time.';
    }
    else {
        $conn = getDBConnection();
        
        // Verify event exists and get event name
        $stmt = $conn->prepare("SELECT title FROM events WHERE eventId = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        $stmt->close();
        
        if (!$event) {
            $error = 'Event not found.';
        } else {
            $eventName = $event['title'];
            
            // Verify package exists
            $stmt = $conn->prepare("SELECT packageId, price FROM packages WHERE packageId = ?");
            $stmt->bind_param("i", $packageId);
            $stmt->execute();
            $result = $stmt->get_result();
            $package = $result->fetch_assoc();
            $stmt->close();
            
            if (!$package) {
                $error = 'Package not found.';
            } else {
                // Convert time format from HH:MM to TIME format
                $startTimeFormatted = $startTime . ':00';
                $endTimeFormatted = $endTime . ':00';
                
                // Insert reservation into database
                $stmt = $conn->prepare("INSERT INTO reservations (userId, eventId, packageId, reservationDate, startTime, endTime, totalAmount, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->bind_param("iiisssd", $userId, $eventId, $packageId, $reservationDate, $startTimeFormatted, $endTimeFormatted, $totalAmount);
                
                if ($stmt->execute()) {
                    $success = true;
                    $reservationId = $conn->insert_id;
                    
                    // Store success message in session for display on profile page
                    $_SESSION['reservation_success'] = "Reservation for {$eventName} confirmed! Your reservation ID is #{$reservationId}.";
                    
                    // Redirect to profile page
                    header('Location: profile.php');
                    exit;
                } else {
                    $error = 'Failed to create reservation. Please try again.';
                }
                
                $stmt->close();
            }
        }
        
        $conn->close();
    }
} else {
    $error = 'Invalid request method.';
}

// If we reach here, there was an error - redirect back to form
if (!empty($error)) {
    $_SESSION['reservation_error'] = $error;
    $eventId = isset($_POST['eventId']) ? intval($_POST['eventId']) : 0;
    header('Location: reservationForm.php?eventId=' . $eventId);
    exit;
}

