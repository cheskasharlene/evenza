<?php
/**
 * @param string 
 * @return string
 */
function formatPhoneNumber($phone) {
    if (empty($phone)) {
        return '';
    }
    
    $digits = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($digits) >= 12 && substr($digits, 0, 2) === '63') {
        $digits = '0' . substr($digits, 2);
    }
    
    if (strlen($digits) >= 10 && substr($digits, 0, 1) !== '0') {
        $digits = '0' . $digits;
    }
    
    if (strlen($digits) === 11) {
        return substr($digits, 0, 4) . ' ' . substr($digits, 4, 3) . ' ' . substr($digits, 7, 4);
    }
    
    if (strlen($digits) === 10) {
        $digits = '0' . $digits;
        return substr($digits, 0, 4) . ' ' . substr($digits, 4, 3) . ' ' . substr($digits, 7, 4);
    }
    
    if (strlen($digits) >= 7) {
        $formatted = substr($digits, 0, 4);
        $remaining = substr($digits, 4);
        
        while (strlen($remaining) > 0) {
            $chunk = substr($remaining, 0, min(3, strlen($remaining)));
            $formatted .= ' ' . $chunk;
            $remaining = substr($remaining, strlen($chunk));
        }
        
        return $formatted;
    }
    
    return $phone;
}

/**
 * @param string
 * @return true|string
 */
function validatePassword($password) {
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters long.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Password must contain at least one number.';
    }
    return true;
}

/**
 * Format time string to 12-hour format with AM/PM
 * @param string $timeString - Time string in format "HH:MM:SS" or "HH:MM" or "HH:MM:SS - HH:MM:SS"
 * @return string - Formatted time in 12-hour format (e.g., "11:00 AM - 4:00 PM")
 */
function formatTime12Hour($timeString) {
    if (empty($timeString) || $timeString === ' - ') {
        return '';
    }
    
    // Handle time range (e.g., "11:00:00 - 16:00:00")
    if (strpos($timeString, ' - ') !== false) {
        $parts = explode(' - ', $timeString);
        $start = formatTime12Hour($parts[0]);
        $end = formatTime12Hour($parts[1]);
        return $start && $end ? $start . ' - ' . $end : $timeString;
    }
    
    // Extract time part (handle formats like "11:00:00" or "11:00")
    $timeOnly = trim(explode(' ', $timeString)[0]);
    $timeParts = explode(':', $timeOnly);
    
    if (count($timeParts) < 2) {
        return $timeString;
    }
    
    $hour = (int)$timeParts[0];
    $minute = isset($timeParts[1]) ? $timeParts[1] : '00';
    
    // Convert to 12-hour format
    $period = $hour >= 12 ? 'PM' : 'AM';
    $hour12 = $hour == 0 ? 12 : ($hour > 12 ? $hour - 12 : $hour);
    
    return sprintf('%d:%s %s', $hour12, $minute, $period);
}

/**
 * Generate a unique reservation code
 * Format: EVZ-YYYYMMDD-XXXXXX (e.g., EVZ-20241201-A3B5C7)
 * @param mysqli $conn - Database connection
 * @return string - Unique reservation code
 */
function generateUniqueReservationCode($conn) {
    $maxAttempts = 10;
    $attempt = 0;
    
    do {
        $datePart = date('Ymd');
        $randomPart = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $reservationCode = 'EVZ-' . $datePart . '-' . $randomPart;
        
        $checkQuery = "SELECT reservationId FROM reservations WHERE reservationCode = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        
        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, "s", $reservationCode);
            mysqli_stmt_execute($checkStmt);
            $result = mysqli_stmt_get_result($checkStmt);
            $exists = mysqli_num_rows($result) > 0;
            mysqli_stmt_close($checkStmt);
            
            if (!$exists) {
                return $reservationCode;
            }
        }
        
        $attempt++;
    } while ($attempt < $maxAttempts);
    
    return 'EVZ-' . $datePart . '-' . strtoupper(substr(md5(uniqid(rand(), true) . time()), 0, 6));
}

/**
 * Send reservation confirmation email using PHPMailer with SMTP
 * @param mysqli $conn - Database connection
 * @param int $reservationId - Reservation ID
 * @return bool - True if email sent successfully, false otherwise
 */
function sendReservationConfirmationEmail($conn, $reservationId) {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../config/email.php';
    
    $query = "SELECT 
                r.reservationId,
                r.reservationCode,
                r.reservationDate,
                r.startTime,
                r.endTime,
                r.totalAmount,
                r.status,
                u.fullName AS customerName,
                u.email AS customerEmail,
                e.title AS eventName,
                e.venue AS eventLocation,
                p.packageName
              FROM reservations r
              INNER JOIN users u ON r.userId = u.userid
              INNER JOIN events e ON r.eventId = e.eventId
              INNER JOIN packages p ON r.packageId = p.packageId
              WHERE r.reservationId = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Email Error: Failed to prepare query - " . mysqli_error($conn));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $reservationId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $reservation = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$reservation || empty($reservation['customerEmail'])) {
        error_log("Email Error: Reservation not found or no email address");
        return false;
    }
    
    $emailConfig = require __DIR__ . '/../config/email.php';
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = $emailConfig['smtp']['host'];
        $mail->SMTPAuth = $emailConfig['smtp']['auth'];
        $mail->Username = $emailConfig['smtp']['username'];
        $mail->Password = $emailConfig['smtp']['password'];
        $mail->SMTPSecure = $emailConfig['smtp']['secure'] === 'ssl' ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $emailConfig['smtp']['port'];
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom($emailConfig['from']['email'], $emailConfig['from']['name']);
        $mail->addAddress($reservation['customerEmail'], $reservation['customerName']);
        $mail->addReplyTo($emailConfig['reply_to']['email'], $emailConfig['reply_to']['name']);
        
        $mail->isHTML(true);
        $mail->Subject = 'Reservation Confirmed - ' . $reservation['eventName'];
        
        $reservationDate = date('F d, Y', strtotime($reservation['reservationDate']));
        $timeRange = '';
        if (!empty($reservation['startTime']) && !empty($reservation['endTime'])) {
            $timeRange = formatTime12Hour($reservation['startTime']) . ' - ' . formatTime12Hour($reservation['endTime']);
        } elseif (!empty($reservation['startTime'])) {
            $timeRange = formatTime12Hour($reservation['startTime']);
        }
        
        $mail->Body = buildReservationEmailHTML($reservation, $reservationDate, $timeRange);
        
        $mail->AltBody = buildReservationEmailText($reservation, $reservationDate, $timeRange);
        
        $mail->send();
        return true;
        
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * @param array $reservation
 * @param string $reservationDate
 * @param string $timeRange
 * @return string
 */
function buildReservationEmailHTML($reservation, $reservationDate, $timeRange) {
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #4A5D4A 0%, #6B8E6B 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .detail-row { margin: 15px 0; padding: 10px; background: white; border-radius: 5px; }
        .detail-label { font-weight: bold; color: #4A5D4A; }
        .reservation-code { font-size: 24px; font-weight: bold; color: #4A5D4A; letter-spacing: 2px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reservation Confirmed!</h1>
            <p>Thank you for your reservation with EVENZA</p>
        </div>
        <div class="content">
            <p>Dear ' . htmlspecialchars($reservation['customerName']) . ',</p>
            <p>Your reservation has been confirmed. Please find the details below:</p>
            
            <div class="detail-row">
                <div class="detail-label">Reservation Code:</div>
                <div class="reservation-code">' . htmlspecialchars($reservation['reservationCode']) . '</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Event Name:</div>
                <div>' . htmlspecialchars($reservation['eventName']) . '</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Package:</div>
                <div>' . htmlspecialchars($reservation['packageName']) . '</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Date:</div>
                <div>' . htmlspecialchars($reservationDate) . '</div>
            </div>';
    
    if (!empty($timeRange)) {
        $html .= '<div class="detail-row">
                <div class="detail-label">Time:</div>
                <div>' . htmlspecialchars($timeRange) . '</div>
            </div>';
    }
    
    $html .= '<div class="detail-row">
                <div class="detail-label">Location:</div>
                <div>' . htmlspecialchars($reservation['eventLocation']) . '</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Total Amount:</div>
                <div>₱ ' . number_format($reservation['totalAmount'], 2) . '</div>
            </div>
            
            <p style="margin-top: 30px;"><strong>Important:</strong> Please keep your reservation code safe. You will need it for check-in and any inquiries.</p>
            
            <p>If you have any questions, please contact us at <a href="mailto:info@evenza.com">info@evenza.com</a></p>
        </div>
        <div class="footer">
            <p>&copy; ' . date('Y') . ' EVENZA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}

/**
 * Build plain text email body for reservation confirmation
 */
function buildReservationEmailText($reservation, $reservationDate, $timeRange) {
    $text = "Reservation Confirmed!\n\n";
    $text .= "Dear " . $reservation['customerName'] . ",\n\n";
    $text .= "Your reservation has been confirmed. Please find the details below:\n\n";
    $text .= "Reservation Code: " . $reservation['reservationCode'] . "\n";
    $text .= "Event Name: " . $reservation['eventName'] . "\n";
    $text .= "Package: " . $reservation['packageName'] . "\n";
    $text .= "Date: " . $reservationDate . "\n";
    
    if (!empty($timeRange)) {
        $text .= "Time: " . $timeRange . "\n";
    }
    
    $text .= "Location: " . $reservation['eventLocation'] . "\n";
    $text .= "Total Amount: ₱ " . number_format($reservation['totalAmount'], 2) . "\n\n";
    $text .= "Important: Please keep your reservation code safe. You will need it for check-in and any inquiries.\n\n";
    $text .= "If you have any questions, please contact us at info@evenza.com\n\n";
    $text .= "© " . date('Y') . " EVENZA. All rights reserved.";
    
    return $text;
}
?>

