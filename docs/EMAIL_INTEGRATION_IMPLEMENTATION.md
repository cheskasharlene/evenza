# Email Integration Implementation

## Overview
This document describes the email integration system that automatically sends confirmation emails to users when their payment is successful and reservation status is marked as "Completed."

## Features Implemented

1. **Unique Reservation Codes**: Each completed reservation gets a unique code in the format `EVZ-YYYYMMDD-XXXXXX` (e.g., `EVZ-20241201-A3B5C7`)
2. **Automatic Email Sending**: Emails are sent automatically when:
   - Payment is successful via PayPal
   - Reservation status is updated to "completed"
3. **Email Content**: Includes full reservation details:
   - Reservation code
   - Event name
   - Date and time
   - Location
   - Package name
   - Total amount

## Files Created/Modified

### 1. Database Migration
**File:** `database_add_reservation_code.sql`
- Adds `reservationCode` column to `reservations` table
- Creates unique constraint and index for faster lookups

**To apply:**
```sql
-- Run this SQL script in your MySQL database
source database_add_reservation_code.sql;
-- OR
mysql -u root -p evenza < database_add_reservation_code.sql
```

### 2. Email Configuration
**File:** `config/email.php`
- SMTP configuration for PHPMailer
- Contains Gmail SMTP settings (can be customized for other providers)
- From and Reply-To addresses

**Configuration:**
- SMTP Host: `smtp.gmail.com`
- Port: `465` (SSL)
- Authentication: Required
- Username/Password: Configured in the file

**Note:** For Gmail, you need to use an App Password, not your regular password. Generate one at: https://myaccount.google.com/apppasswords

### 3. Helper Functions
**File:** `includes/helpers.php` (Modified)
- `generateUniqueReservationCode($conn)`: Generates unique reservation codes
- `sendReservationConfirmationEmail($conn, $reservationId)`: Sends confirmation email
- `buildReservationEmailHTML()`: Builds HTML email template
- `buildReservationEmailText()`: Builds plain text email template

### 4. Payment Processing Updates
**File:** `api/paypal-capture-order.php` (Modified)
- Generates reservation code when status is updated to "completed"
- Sends confirmation email after successful payment capture

### 5. Confirmation Page Updates
**File:** `confirmation.php` (Modified)
- Generates reservation code for new reservations
- Updates existing reservations with code if missing
- Displays reservation code on confirmation page
- Sends confirmation email after reservation is completed

## Email Template

The email includes:
- Professional HTML design with EVENZA branding
- Reservation code (prominently displayed)
- Event details (name, date, time, location)
- Package information
- Total amount paid
- Contact information

## Reservation Code Format

Format: `EVZ-YYYYMMDD-XXXXXX`

Example: `EVZ-20241201-A3B5C7`

- **EVZ**: Prefix for EVENZA
- **YYYYMMDD**: Date when reservation was completed
- **XXXXXX**: 6-character random alphanumeric code

The system ensures uniqueness by:
1. Checking database for existing codes
2. Regenerating if duplicate found (up to 10 attempts)
3. Adding timestamp as fallback if needed

## Setup Instructions

### 1. Database Setup
```bash
# Run the SQL migration
mysql -u root -p evenza < database_add_reservation_code.sql
```

### 2. Email Configuration
Edit `config/email.php` with your SMTP settings:

```php
return [
    'smtp' => [
        'host' => 'smtp.gmail.com',        // Your SMTP server
        'port' => 465,                      // SMTP port
        'secure' => 'ssl',                  // 'ssl' or 'tls'
        'auth' => true,
        'username' => 'your-email@gmail.com',
        'password' => 'your-app-password',   // App password for Gmail
    ],
    'from' => [
        'email' => 'your-email@gmail.com',
        'name' => 'EVENZA'
    ],
    'reply_to' => [
        'email' => 'your-email@gmail.com',
        'name' => 'EVENZA Support'
    ]
];
```

### 3. Gmail App Password Setup
1. Go to https://myaccount.google.com/apppasswords
2. Select "Mail" and "Other (Custom name)"
3. Enter "EVENZA" as the name
4. Click "Generate"
5. Copy the 16-character password
6. Paste it in `config/email.php` as the `password` value

### 4. Test Email Sending
After a successful payment, check:
1. User's email inbox for confirmation email
2. Server error logs if email fails
3. Check spam folder if email not received

## Error Handling

- Email sending failures are logged to PHP error log
- Page load is not blocked if email fails
- Reservation is still saved even if email fails
- Errors are logged with context for debugging

## Testing Checklist

- [ ] Database migration applied successfully
- [ ] Email configuration updated with correct SMTP settings
- [ ] Test payment flow completes successfully
- [ ] Reservation code is generated and saved
- [ ] Confirmation email is received by user
- [ ] Email contains all reservation details
- [ ] Reservation code is displayed on confirmation page
- [ ] Email works for both new and existing reservations

## Troubleshooting

### Email Not Sending
1. Check SMTP credentials in `config/email.php`
2. Verify Gmail App Password is correct
3. Check PHP error logs: `tail -f /var/log/php_errors.log`
4. Test SMTP connection manually
5. Check firewall/network restrictions

### Reservation Code Not Generated
1. Verify database column exists: `DESCRIBE reservations;`
2. Check for unique constraint conflicts
3. Review error logs for database errors

### Duplicate Reservation Codes
- The system has built-in uniqueness checking
- If duplicates occur, check database constraints
- Verify `reservationCode` column has UNIQUE constraint

## Security Notes

- SMTP credentials are stored in `config/email.php` (keep secure)
- Email addresses are validated before sending
- Prepared statements prevent SQL injection
- Reservation codes are generated using secure random functions

## Future Enhancements

Potential improvements:
- Email templates customization
- Multiple email recipients (CC/BCC)
- Email delivery status tracking
- Resend email functionality
- Email preferences per user

