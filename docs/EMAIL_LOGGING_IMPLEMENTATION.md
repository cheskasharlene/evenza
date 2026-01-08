# Email Logging Implementation

## Overview
This implementation adds comprehensive email logging to the EVENZA platform. All emails sent by the system are now automatically logged to the database for record-keeping and tracking purposes.

## Database Table

### Table: `email_logs`
Stores all email records with the following structure:

- **email_log_id** (INT, Primary Key, Auto Increment) - Unique identifier
- **recipient_email** (VARCHAR(255)) - Email address of the recipient
- **recipient_name** (VARCHAR(255)) - Name of the recipient
- **subject** (VARCHAR(500)) - Email subject line
- **email_type** (VARCHAR(100)) - Type of email (e.g., 'reservation_confirmation')
- **related_id** (INT) - ID of related record (e.g., reservationId, userId)
- **email_body_html** (TEXT) - HTML content of the email
- **email_body_text** (TEXT) - Plain text content of the email
- **status** (ENUM: 'sent', 'failed') - Status of the email
- **error_message** (TEXT) - Error message if email failed to send
- **sent_at** (DATETIME) - Timestamp when email was sent
- **created_at** (TIMESTAMP) - Record creation timestamp

### Indexes
The table includes indexes on:
- `recipient_email` - For quick lookups by email
- `email_type` - For filtering by email type
- `related_id` - For finding emails related to specific records
- `sent_at` - For date-based queries
- `status` - For filtering by success/failure

## Installation

### Step 1: Run the Database Migration
Execute the SQL migration file to create the `email_logs` table:

```bash
mysql -u root -p evenza < database_create_email_logs.sql
```

Or via MySQL command line:
```sql
source database_create_email_logs.sql;
```

### Step 2: Verify Installation
Check that the table was created successfully:
```sql
DESCRIBE email_logs;
SHOW INDEXES FROM email_logs;
```

## Implementation Details

### Helper Function: `logEmailToDatabase()`
Located in `includes/helpers.php`, this function logs emails to the database.

**Parameters:**
- `$conn` - Database connection (mysqli)
- `$recipientEmail` - Recipient email address
- `$recipientName` - Recipient name
- `$subject` - Email subject
- `$emailType` - Type of email (e.g., 'reservation_confirmation')
- `$relatedId` - Related record ID (optional)
- `$emailBodyHtml` - HTML content (optional)
- `$emailBodyText` - Plain text content (optional)
- `$status` - Status: 'sent' or 'failed' (default: 'sent')
- `$errorMessage` - Error message if failed (optional)

**Returns:** `bool` - True if logged successfully, false otherwise

### Updated Function: `sendReservationConfirmationEmail()`
The email sending function now automatically logs:
- **Successful emails**: Logged with status 'sent' after successful delivery
- **Failed emails**: Logged with status 'failed' and error message in the catch block

## Usage Examples

### Querying Email Logs

**Get all emails sent to a specific user:**
```sql
SELECT * FROM email_logs 
WHERE recipient_email = 'user@example.com' 
ORDER BY sent_at DESC;
```

**Get all reservation confirmation emails:**
```sql
SELECT * FROM email_logs 
WHERE email_type = 'reservation_confirmation' 
ORDER BY sent_at DESC;
```

**Get failed emails:**
```sql
SELECT * FROM email_logs 
WHERE status = 'failed' 
ORDER BY sent_at DESC;
```

**Get emails for a specific reservation:**
```sql
SELECT * FROM email_logs 
WHERE email_type = 'reservation_confirmation' 
AND related_id = 123;
```

**Get email statistics:**
```sql
SELECT 
    email_type,
    status,
    COUNT(*) as count
FROM email_logs
GROUP BY email_type, status;
```

## Benefits

1. **Audit Trail**: Complete record of all emails sent by the system
2. **Troubleshooting**: Easy identification of failed emails and their error messages
3. **Analytics**: Track email delivery rates and types
4. **Compliance**: Maintain records for regulatory requirements
5. **Customer Support**: Quick access to email history for support inquiries

## Future Enhancements

To extend this system for other email types:

1. **Add new email types** when creating new email functions:
   ```php
   logEmailToDatabase(
       $conn,
       $email,
       $name,
       $subject,
       'password_reset',  // New email type
       $userId,
       $htmlBody,
       $textBody,
       'sent',
       null
   );
   ```

2. **Create email sending wrapper** that automatically logs all emails:
   ```php
   function sendEmailWithLogging($conn, $to, $subject, $body, $emailType, $relatedId = null) {
       // Send email logic
       // Log to database
   }
   ```

## Notes

- Email logging does not block email sending - if logging fails, the email still attempts to send
- Large email bodies are stored in TEXT fields - consider archiving old logs periodically
- The `sent_at` field uses NOW() to record the exact send time
- Failed emails are logged even if the email body couldn't be fully constructed

