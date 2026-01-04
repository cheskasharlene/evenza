# EVENZA Reservation System Implementation

## Overview
This document describes the implementation of the Package Selection logic connected to the Reservation System for EVENZA, ensuring full synchronization between Client-side booking and Admin-side dashboard using MySQLi.

## Files Created/Modified

### 1. Database Schema
**File:** `database_schema_reservations.sql`

This SQL script creates the `reservations` table with the following structure:
- `reservationId` (Primary Key, AUTO_INCREMENT)
- `userId` (Foreign Key to users table)
- `eventId` (Foreign Key to events table)
- `packageTier` (ENUM: 'Bronze', 'Silver', 'Gold')
- `totalPrice` (DECIMAL(10, 2))
- `bookingDate` (DATETIME)
- `status` (ENUM: 'Pending', 'Confirmed', 'Cancelled')
- `createdAt` (TIMESTAMP)
- `updatedAt` (TIMESTAMP)

**To apply:** Run this SQL script in your MySQL database.

### 2. Client-Side Reservation Form
**File:** `reservation.php` (Modified)

**Features:**
- Package selection with three tiers: Bronze (₱7,000), Silver (₱10,000), Gold (₱15,000)
- Price calculation using `calculatePackagePrice()` function with switch statement
- Real-time package selection updates
- Form submission to `reserve.php`
- Success/error message display

**Price Calculation Logic:**
```php
function calculatePackagePrice($packageTier) {
    switch(strtolower($packageTier)) {
        case 'bronze': return 7000;
        case 'silver': return 10000;
        case 'gold': return 15000;
        default: return 0;
    }
}
```

### 3. Reservation Processing
**File:** `reserve.php` (New)

**Features:**
- Validates user session (requires login)
- Processes POST requests from reservation form
- Calculates price based on package tier using switch statement
- Inserts reservation into database using MySQLi prepared statements
- Sets success message: "Reservation for [Package Name] confirmed!"
- Redirects to payment page with reservation details

**Database Insert:**
```php
$query = "INSERT INTO reservations (userId, eventId, packageTier, totalPrice, bookingDate, status) 
          VALUES (?, ?, ?, ?, ?, 'Pending')";
```

### 4. Admin Dashboard
**File:** `reservationsManagement.php` (Modified)

**Features:**
- Fetches reservations using MySQLi with JOINs:
  - `INNER JOIN users` - to get customer name and email
  - `INNER JOIN events` - to get event title and venue
- Displays:
  - Customer Name (from users table)
  - Event Title (from events table)
  - Package Tier and Revenue (from reservations table)
- Revenue Summary section showing:
  - Total Revenue
  - Revenue by Package Tier (Bronze, Silver, Gold)
- Filtering by package tier and date
- Status management (Pending, Confirmed, Cancelled)

**SQL Query:**
```sql
SELECT 
    r.reservationId, r.userId, r.eventId, r.packageTier, r.totalPrice,
    r.bookingDate, r.status, u.fullName AS customerName, u.email AS customerEmail,
    e.title AS eventTitle, e.venue AS eventVenue, e.eventDate, e.eventTime
FROM reservations r
INNER JOIN users u ON r.userId = u.userid
INNER JOIN events e ON r.eventId = e.eventId
WHERE 1=1
ORDER BY r.bookingDate DESC, r.createdAt DESC
```

### 5. Status Update API
**File:** `api/updateReservationStatus.php` (New)

**Features:**
- Updates reservation status using MySQLi prepared statements
- Validates admin authentication
- Returns JSON response for AJAX calls

### 6. Payment Page
**File:** `payment.php` (Modified)

**Features:**
- Displays success/error messages from session
- Handles packageTier parameter from URL
- Shows package details and total amount

## Data Flow

1. **User selects package** on `reservation.php`
   - Selects Bronze, Silver, or Gold package
   - Price is calculated using switch statement
   - Form submits to `reserve.php`

2. **Reservation is processed** in `reserve.php`
   - Validates inputs
   - Calculates price: Bronze=7000, Silver=10000, Gold=15000
   - Inserts into database using MySQLi prepared statement
   - Sets success message
   - Redirects to payment page

3. **Admin views reservations** in `reservationsManagement.php`
   - Fetches all reservations with JOINs
   - Displays customer name, event title, package tier, and revenue
   - Shows total revenue and revenue by package tier
   - Updates reflect immediately in dashboard

## Data Integrity

- **Immediate Updates:** Reservations are inserted directly into the database, so they appear immediately in the admin dashboard
- **Success Messages:** Users see "Reservation for [Package Name] confirmed!" message
- **Foreign Key Constraints:** Database enforces referential integrity between reservations, users, and events
- **Prepared Statements:** All database operations use MySQLi prepared statements to prevent SQL injection

## Package Pricing

| Package Tier | Price (PHP) |
|--------------|-------------|
| Bronze       | ₱7,000      |
| Silver       | ₱10,000     |
| Gold         | ₱15,000     |

## Testing Checklist

- [ ] Run `database_schema_reservations.sql` to create/update the reservations table
- [ ] Test package selection on reservation page
- [ ] Verify price calculation (Bronze=7000, Silver=10000, Gold=15000)
- [ ] Submit a reservation and verify database insertion
- [ ] Check admin dashboard shows the new reservation immediately
- [ ] Verify customer name, event title, and package details are displayed correctly
- [ ] Test revenue calculation in admin dashboard
- [ ] Test status updates in admin dashboard
- [ ] Verify success message appears after reservation submission

## Notes

- All database operations use MySQLi (not PDO) for consistency
- The system requires users to be logged in to make reservations
- Reservations are created with status 'Pending' by default
- Admin can update reservation status to 'Confirmed' or 'Cancelled'
- Revenue is calculated in real-time from the database

