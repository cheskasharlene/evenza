# SMS Integration Setup Guide

## Overview
This SMS integration allows the admin to automatically send SMS messages to users when reservation status changes, and receive SMS messages from users through an Android SMS Forwarder.

## Prerequisites
1. Android device with SMS Gateway and SMS Forwarder apps installed
2. Android device and server (XAMPP) on the same network
3. Database table `sms_messages` created (run `database_schema_sms.sql`)

## Setup Steps

### 1. Database Setup
Run the SQL script to create the SMS messages table:
```sql
-- Run database_schema_sms.sql in your MySQL database
```

### 2. Android SMS Forwarder Configuration

#### For Receiving SMS (Webhook):
1. Open SMS Forwarder app on your Android device
2. Configure the webhook URL to point to your server:
   ```
   http://your-server-ip/evenza/webhook.php
   ```
   Replace `your-server-ip` with your XAMPP server's IP address (e.g., `192.168.1.50`)

#### For Sending SMS (API):
1. Note your Android device's IP address (Settings > About Phone > Status > IP Address)
2. Open `api/sendSMS.php` and update the `$smsGatewayUrl` variable:
   ```php
   $smsGatewayUrl = 'http://YOUR_ANDROID_IP:PORT/send';
   ```
   Replace `YOUR_ANDROID_IP` and `PORT` with your Android device's IP and the SMS Gateway port

### 3. Testing

#### Test Receiving SMS:
1. Send a test SMS to the phone number configured in SMS Forwarder
2. Check `sms_log.txt` in the project root for raw data
3. Check the `sms_messages` table in the database
4. View messages in the admin SMS Inbox page

#### Test Sending SMS:
1. Update a reservation status to "Confirmed" or "Cancelled" in the admin panel
2. The system will automatically send an SMS to the user's phone number
3. Check the SMS Gateway app logs on your Android device

## Features

### Automatic SMS Sending
- When admin updates reservation status to **"Confirmed"**: 
  - Sends confirmation SMS with event details, date, time, venue, and package
- When admin updates reservation status to **"Cancelled"**: 
  - Sends cancellation SMS informing the user the date/time is occupied

### SMS Inbox
- Admin can view all incoming SMS messages
- Messages are marked as read/unread
- Pagination support for large message lists
- Accessible from admin sidebar navigation

## File Structure

```
evenza/
├── webhook.php                    # Receives SMS from Android forwarder
├── smsInbox.php                   # Admin SMS inbox page
├── api/
│   ├── sendSMS.php               # API for sending SMS
│   └── markSMSRead.php           # API for marking SMS as read
├── includes/
│   └── helpers.php               # Contains sendReservationStatusSMS() function
├── database_schema_sms.sql        # Database schema for SMS table
└── sms_log.txt                   # Log file for debugging (auto-created)
```

## SMS Message Format

### Confirmed Reservation SMS:
```
Hello [Customer Name]! Your reservation for [Event Name] has been CONFIRMED. 
Date: [Date] | Time: [Time] | Venue: [Venue] | Package: [Package Name]. 
We look forward to seeing you! - EVENZA
```

### Cancelled Reservation SMS:
```
Hello [Customer Name], we regret to inform you that your reservation for [Event Name] 
on [Date] ([Time]) has been CANCELLED. The selected date and time are already occupied. 
Please contact us for alternative dates. - EVENZA
```

## Troubleshooting

### SMS Not Being Received:
1. Check if `webhook.php` is accessible from Android device
2. Check `sms_log.txt` for incoming data
3. Verify SMS Forwarder app is running and configured correctly
4. Check database connection in `webhook.php`

### SMS Not Being Sent:
1. Verify `$smsGatewayUrl` in `api/sendSMS.php` is correct
2. Check Android device IP address hasn't changed
3. Verify SMS Gateway app is running on Android device
4. Check PHP error logs for cURL errors
5. Verify user has a valid phone number in the database

### Database Errors:
1. Ensure `sms_messages` table exists
2. Check database connection in `connect.php`
3. Verify table structure matches `database_schema_sms.sql`

## Security Notes
- The webhook endpoint (`webhook.php`) is publicly accessible - consider adding authentication
- SMS Gateway URL should be on a private network (not exposed to internet)
- Consider rate limiting for SMS sending to prevent abuse

## Next Steps
1. Configure Android SMS Forwarder webhook URL
2. Update SMS Gateway URL in `api/sendSMS.php`
3. Test receiving an SMS
4. Test sending an SMS by updating a reservation status
5. Check SMS Inbox in admin panel

