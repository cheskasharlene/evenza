/**
 * EVENZA - Profile Page JavaScript
 * Profile editing functionality
 */

(function() {
    'use strict';

    // Save profile changes
    window.saveProfile = function() {
        const name = document.getElementById('editName').value.trim();
        const email = document.getElementById('editEmail').value.trim();
        const mobile = document.getElementById('editMobile').value.trim();

        // Validation
        if (!name || !email || !mobile) {
            alert('Please fill in all fields.');
            return;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        // Mobile validation (basic)
        if (mobile.length < 10) {
            alert('Please enter a valid mobile number.');
            return;
        }

        // In a real application, this would send an AJAX request to update the profile
        // For now, we'll just show a success message and close the modal
        alert('Profile updated successfully!');
        
        // Update the displayed values
        document.querySelector('.profile-info-value').textContent = name;
        document.querySelectorAll('.profile-info-value')[1].textContent = email;
        document.querySelectorAll('.profile-info-value')[2].textContent = mobile;

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
        if (modal) {
            modal.hide();
        }
    };

    // Initialize tooltips if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Add any initialization code here
    });

})();

