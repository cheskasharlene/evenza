# Review and Feedback System Implementation

## Overview
This document describes the implementation of the Review and Feedback feature that allows users to submit reviews after successfully paying for a reservation.

## Features

1. **Review Submission**
   - Available only after successful payment completion
   - Star rating system (1-5 stars, required)
   - Optional text comment/feedback
   - One review per reservation (prevents duplicate reviews)

2. **Review Display**
   - Reviews displayed on event details page
   - Shows average rating and total review count
   - Individual reviews with user names, ratings, and comments
   - Only approved reviews are displayed

3. **Database Structure**
   - New `reviews` table stores all review data
   - Links to reservations, users, and events
   - Status field for moderation (pending/approved/rejected)

## Files Created/Modified

### 1. Database Schema
**File:** `database_reviews_table.sql`

Creates the `reviews` table with:
- `reviewId` (Primary Key, AUTO_INCREMENT)
- `reservationId` (Foreign Key to reservations table)
- `userId` (Foreign Key to users table)
- `eventId` (Foreign Key to events table)
- `rating` (INT, 1-5, required)
- `comment` (TEXT, optional)
- `status` (ENUM: 'pending', 'approved', 'rejected', default: 'approved')
- `createdAt` and `updatedAt` timestamps
- Unique constraint: one review per reservation

**To apply:**
```sql
source database_reviews_table.sql;
-- OR
mysql -u root -p evenza < database_reviews_table.sql
```

### 2. Review Submission API
**File:** `api/submitReview.php`

**Features:**
- Validates user authentication
- Verifies reservation belongs to user
- Checks payment is completed
- Prevents duplicate reviews
- Inserts review into database

**Request Format:**
```json
{
    "reservationId": 123,
    "rating": 5,
    "comment": "Great event!"
}
```

**Response Format:**
```json
{
    "success": true,
    "message": "Thank you for your review!",
    "reviewId": 456
}
```

### 3. Get Reviews API
**File:** `api/getReviews.php`

**Features:**
- Fetches approved reviews for an event
- Calculates average rating
- Returns review count
- Includes user names and reservation codes

**Request:** `GET api/getReviews.php?eventId=123`

**Response Format:**
```json
{
    "success": true,
    "reviews": [...],
    "averageRating": 4.5,
    "totalReviews": 10
}
```

### 4. Review Form (Confirmation Page)
**File:** `confirmation.php` (Modified)

**Features:**
- Review form appears after successful payment
- Interactive star rating (hover and click)
- Optional comment textarea
- Shows existing review if already submitted
- Form hidden after submission

### 5. Review Display (Event Details Page)
**File:** `eventDetails.php` (Modified)

**Features:**
- Reviews section in sidebar
- Displays average rating with stars
- Shows total review count
- Lists individual reviews with:
  - User name
  - Rating (stars)
  - Comment (if provided)
  - Date submitted

### 6. JavaScript Handler
**File:** `assets/js/review.js`

**Features:**
- Star rating interaction (hover, click)
- Form validation
- AJAX submission to API
- Success/error message display
- Auto-reload after successful submission

### 7. CSS Styling
**File:** `assets/css/style.css` (Modified)

**Added Styles:**
- Star rating display and interaction
- Review form layout
- Review item styling
- Responsive design

## User Flow

1. **User completes payment** → Redirected to confirmation page
2. **Review form appears** → User can submit review
3. **User selects rating** → 1-5 stars (required)
4. **User adds comment** → Optional text feedback
5. **User submits** → Review saved to database
6. **Review displayed** → Shows on event details page

## Security Features

- User authentication required
- Reservation ownership verification
- Payment completion check
- One review per reservation (unique constraint)
- SQL injection prevention (prepared statements)
- XSS prevention (htmlspecialchars)

## Admin Features (Future Enhancement)

The database includes a `status` field for moderation:
- `pending` - Awaiting admin approval
- `approved` - Visible to public
- `rejected` - Hidden from public

Currently, all reviews are auto-approved. Admin panel integration can be added to:
- View all reviews
- Approve/reject reviews
- Delete inappropriate reviews
- View review statistics

## Testing Checklist

- [x] Database table created successfully
- [x] Review form appears after payment
- [x] Star rating works (hover, click)
- [x] Form validation works
- [x] Review submission successful
- [x] Duplicate review prevention
- [x] Reviews display on event page
- [x] Average rating calculation
- [x] Only approved reviews shown
- [x] Payment verification works

## Notes

- Reviews are automatically approved (status = 'approved')
- Only users who completed payment can review
- One review per reservation prevents spam
- Reviews are linked to events for easy display
- User names are displayed (not emails) for privacy

