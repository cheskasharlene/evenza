# EVENZA Post-Payment Confirmation System

## Overview
This document describes the post-payment confirmation system that integrates with PayPal flow and MySQL database using MySQLi, with restricted access and comprehensive error handling.

## Files Created/Modified

### 1. Database Schema
**File:** `database_schema_payments.sql`

Creates the `payments` table with:
- `paymentId` (Primary Key, AUTO_INCREMENT)
- `reservationId` (Foreign Key to reservations table)
- `userId` (Foreign Key to users table)
- `transactionId` (VARCHAR, unique - PayPal Transaction Reference)
- `amount` (DECIMAL(10, 2))
- `packageName` (VARCHAR - Bronze/Silver/Gold Package)
- `paymentStatus` (ENUM: 'pending', 'completed', 'failed', 'refunded')
- `createdAt` (TIMESTAMP)

**To apply:** Run this SQL script in your MySQL database.

### 2. PayPal Callback Handler
**File:** `paypalCallback.php` (New)

**Features:**
- Processes PayPal return/callback
- Validates payment status
- Generates secure success token (32-byte hex)
- Stores payment data in session
- Redirects to confirmation with success token

**Security:**
- Success token expires after 5 minutes
- Token stored in session, not URL (more secure)
- Validates payment status before proceeding

### 3. Confirmation Page
**File:** `confirmation.php` (Rewritten)

**Restricted Access:**
- Only accessible with valid success token from PayPal callback
- Token must match session token
- Token must not be expired (5 minutes)
- Direct URL access redirects to home page

**Data Persistence:**
1. **Reservation Save:**
   - Creates reservation if it doesn't exist
   - Updates status to 'confirmed' if reservation exists
   - Uses MySQLi prepared statements

2. **Payment Save:**
   - Inserts payment record into `payments` table
   - Links to reservation via `reservationId`
   - Stores transaction ID, amount, package name
   - Sets status to 'completed'

**Error Handling:**
- Friendly error messages if database save fails
- Displays transaction reference for support
- Provides contact information
- Reassures user that payment was successful

**UI Features:**
- Premium EVENZA aesthetic
- Centered typography with balanced white space
- Displays:
  - Package Tier (with color-coded badge)
  - Transaction ID
  - Event details
  - Amount paid
  - Reservation ID
- "View My Tickets" button
- Responsive design

### 4. Payment Flow Integration
**File:** `payment.php` (Modified)

**Changes:**
- Stores reservation data in session before PayPal redirect
- Includes PayPal icon in button
- Shows redirect message

**File:** `assets/js/payment.js` (Modified)

**Changes:**
- Redirects to `paypalCallback.php` instead of directly to confirmation
- Passes reservation and package data to callback
- Simulates PayPal return (in production, use actual PayPal API)

## Payment Flow

1. **User completes reservation** → `reserve.php` creates reservation
2. **User redirected to payment page** → `payment.php` displays payment summary
3. **User clicks "Pay with PayPal"** → Redirects to PayPal gateway (or callback for testing)
4. **PayPal processes payment** → Returns to `paypalCallback.php`
5. **Callback handler validates payment** → Generates success token
6. **Redirects to confirmation** → `confirmation.php?success=TOKEN&tx=TRANSACTION_ID`
7. **Confirmation page validates token** → Only loads if token is valid
8. **Saves reservation and payment** → Uses MySQLi prepared statements
9. **Displays confirmation** → Shows transaction details

## Security Features

- **Token-based access control:** Confirmation page requires valid success token
- **Token expiration:** Tokens expire after 5 minutes
- **Session-based storage:** Sensitive data stored in session, not URL
- **Prepared statements:** All database operations use MySQLi prepared statements
- **Input validation:** All inputs are validated and sanitized

## Error Handling

If database save fails:
- User sees friendly error message
- Transaction reference is displayed
- User is reassured payment was successful
- Contact information provided
- User can view profile or return home

## Database Structure

### `payments` Table
```sql
paymentId (PK)
reservationId (FK → reservations.reservationId)
userId (FK → users.userid)
transactionId (UNIQUE, PayPal reference)
amount (DECIMAL)
packageName (Bronze/Silver/Gold Package)
paymentStatus (ENUM: pending, completed, failed, refunded)
createdAt (TIMESTAMP)
```

## Testing Checklist

- [ ] Run `database_schema_payments.sql` to create payments table
- [ ] Test PayPal callback flow
- [ ] Verify success token generation and validation
- [ ] Test restricted access (direct URL should redirect)
- [ ] Verify reservation is saved correctly
- [ ] Verify payment is saved correctly
- [ ] Test error handling (simulate database failure)
- [ ] Verify transaction ID is displayed
- [ ] Verify package tier badge displays correctly
- [ ] Test "View My Tickets" button

## Notes

- In production, replace PayPal simulation with actual PayPal API integration
- Success tokens are single-use and expire after 5 minutes
- All database operations use transactions where possible
- Error messages are user-friendly and provide support contact information
- The system ensures data integrity by saving both reservation and payment records

