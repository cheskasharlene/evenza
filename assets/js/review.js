/**
 * Review and Feedback Submission Handler
 */

document.addEventListener('DOMContentLoaded', function() {
    const reviewForm = document.getElementById('reviewForm');
    const starRating = document.getElementById('starRating');
    const reviewRatingInput = document.getElementById('reviewRating');
    const submitBtn = document.getElementById('submitReviewBtn');
    const reviewMessage = document.getElementById('reviewMessage');
    const ratingError = document.getElementById('ratingError');

    if (!reviewForm) return;

    let selectedRating = 0;
    
    // Initialize stars as outline (empty) if not already set
    if (starRating) {
        const stars = starRating.querySelectorAll('.star-icon');
        stars.forEach(star => {
            // Ensure stars start as outline
            if (star.classList.contains('fas') && !star.classList.contains('text-warning')) {
                star.classList.remove('fas');
                star.classList.add('far', 'text-muted');
            }
        });
    }

    // Star rating interaction
    if (starRating) {
        const stars = starRating.querySelectorAll('.star-icon');
        
        stars.forEach(star => {
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                highlightStars(rating);
            });

            star.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-rating'));
                reviewRatingInput.value = selectedRating;
                highlightStars(selectedRating);
                ratingError.textContent = '';
                ratingError.classList.remove('d-block');
            });
        });

        starRating.addEventListener('mouseleave', function() {
            highlightStars(selectedRating);
        });
    }

    function highlightStars(rating) {
        if (!starRating) return;
        const stars = starRating.querySelectorAll('.star-icon');
        stars.forEach((star, index) => {
            const starRatingValue = index + 1;
            if (starRatingValue <= rating) {
                star.classList.remove('far', 'text-muted');
                star.classList.add('fas', 'text-warning');
            } else {
                star.classList.remove('fas', 'text-warning');
                star.classList.add('far', 'text-muted');
            }
        });
    }

    // Form submission
    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const reservationId = document.getElementById('reviewReservationId').value;
        const rating = parseInt(reviewRatingInput.value);
        const comment = document.getElementById('reviewComment').value.trim();

        // Validation - Rating is required
        if (!rating || rating < 1 || rating > 5) {
            ratingError.textContent = 'Please select a rating by clicking on the stars';
            ratingError.style.display = 'block';
            ratingError.style.color = '#dc3545';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Review';
            return;
        }

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        reviewMessage.innerHTML = '';

        // Submit review
        fetch('../api/submitReview.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                reservationId: parseInt(reservationId),
                rating: rating,
                comment: comment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                reviewMessage.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        ${data.message}
                    </div>
                `;
                
                // Hide form and show success message
                reviewForm.style.display = 'none';
                
                // Reload page after 2 seconds to show the review
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                reviewMessage.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.error || 'Failed to submit review. Please try again.'}
                    </div>
                `;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Review';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            reviewMessage.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    An error occurred. Please try again later.
                </div>
            `;
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Review';
        });
    });
});

