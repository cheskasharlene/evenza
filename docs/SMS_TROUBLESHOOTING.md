# SMS Integration Troubleshooting Guide

## Common Issues and Solutions

### 1. SQL Syntax Error in SMS Inbox
**Error:** `You have an error in your SQL syntax; check the manual... near "%"type":"sent"%'`

**Status:** ✅ FIXED
- The issue was with escaping quotes in SQL LIKE clauses
- Fixed by using prepared statements with bound parameters

### 2. SMS Not Being Sent

#### Check 1: SMS Gateway URL Configuration
1. Open `api/sendSMS.php`
2. Verify the `$smsGatewayUrl` is set correctly:
   ```php
   $smsGatewayUrl = 'http://YOUR_ANDROID_IP:PORT/ENDPOINT';
   ```
3. Common endpoints:
   - `/send`
   - `/api/send`
   - `/sms/send`
   - Just the base URL (some apps use root)

#### Check 2: Android SMS Gateway App
1. Ensure the SMS Gateway app is running on your Android device
2. Check the app's documentation for the correct endpoint URL
3. Verify the app is listening on the correct port
4. Make sure your Android device and server are on the same network

#### Check 3: Network Connectivity
1. Test if you can access the SMS Gateway URL from your server:
   ```bash
   curl http://YOUR_ANDROID_IP:PORT
   ```
2. Check firewall settings on both devices
3. Verify the Android device's IP address hasn't changed

#### Check 4: Error Logs
1. Check PHP error logs (usually in `C:\xampp\php\logs\php_error_log` or `C:\xampp\apache\logs\error.log`)
2. Look for "SMS Error:" messages
3. Check the `sms_log.txt` file in the project root

#### Check 5: User Phone Number
1. Verify the user has a phone number in the database (`users.phone` column)
2. Check if the phone number is in the correct format
3. Ensure the phone number is not NULL or empty

### 3. Testing SMS Sending

#### Manual Test via API
You can test the SMS sending API directly:

```bash
# Using curl (from command line)
curl -X POST http://localhost/evenza/api/sendSMS.php \
  -d "phone=09123456789" \
  -d "message=Test message" \
  -b "PHPSESSID=your_session_id"
```

Or create a test file `test_sms.php`:
```php
<?php
session_start();
// Set admin session (for testing only)
$_SESSION['admin_id'] = 1; // or your admin user ID

require_once 'api/sendSMS.php';
?>
```

#### Check Database
After attempting to send an SMS, check the `sms_messages` table:
```sql
SELECT * FROM sms_messages 
WHERE raw_data LIKE '%"type":"sent"%' 
ORDER BY created_at DESC 
LIMIT 5;
```

### 4. SMS Gateway App Configuration

Different SMS Gateway apps use different formats. Common ones:

#### SMS Gateway (by C.T. Lin)
- Endpoint: `http://IP:PORT/send`
- Format: JSON with `to` and `message` fields
- Example:
  ```json
  {
    "to": "09123456789",
    "message": "Test message"
  }
  ```

#### SMS Forwarder
- May use different endpoint
- Check app settings for webhook/API configuration

### 5. Debugging Steps

1. **Enable Error Logging:**
   - Check `api/sendSMS.php` - it now logs all attempts
   - Check PHP error logs

2. **Test Webhook Reception:**
   - Send a test SMS to your Android device
   - Check `sms_log.txt` for incoming data
   - Check `sms_messages` table

3. **Test SMS Sending:**
   - Update a reservation status to "Confirmed" or "Cancelled"
   - Check error logs for SMS sending attempts
   - Verify the SMS Gateway URL is accessible

4. **Check Reservation Data:**
   ```sql
   SELECT r.reservationId, u.fullName, u.phone, r.status
   FROM reservations r
   INNER JOIN users u ON r.userId = u.userid
   WHERE r.reservationId = YOUR_RESERVATION_ID;
   ```

### 6. Common Error Messages

#### "SMS Gateway URL not configured"
- **Solution:** Update `$smsGatewayUrl` in `api/sendSMS.php`

#### "SMS Gateway Connection Error"
- **Solution:** 
  - Check if SMS Gateway app is running
  - Verify IP address and port
  - Check network connectivity

#### "Reservation not found or no phone number"
- **Solution:** 
  - Verify user has phone number in database
  - Check reservation exists

#### "HTTP Code 404"
- **Solution:** 
  - Wrong endpoint URL
  - Check SMS Gateway app documentation for correct endpoint

#### "HTTP Code 500"
- **Solution:** 
  - SMS Gateway app error
  - Check SMS Gateway app logs
  - Verify request format matches app requirements

### 7. Verification Checklist

- [ ] `sms_messages` table exists in database
- [ ] SMS Gateway app is installed and running on Android device
- [ ] Android device and server are on same network
- [ ] `$smsGatewayUrl` in `api/sendSMS.php` is correct
- [ ] SMS Gateway app port is accessible
- [ ] Users have phone numbers in database
- [ ] Webhook URL is configured in SMS Forwarder app
- [ ] Test SMS can be received (check `sms_log.txt`)

### 8. Getting Help

If issues persist:
1. Check all error logs
2. Verify SMS Gateway app documentation
3. Test network connectivity
4. Verify database structure matches expected schema
5. Check PHP version compatibility (should be 7.4+)

