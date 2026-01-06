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
?>

