(function() {
    'use strict';

    window.saveProfile = function() {
        const name = document.getElementById('editName').value.trim();
        const email = document.getElementById('editEmail').value.trim();
        const mobile = document.getElementById('editMobile').value.trim();

        if (!name || !email || !mobile) {
            alert('Please fill in all fields.');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        if (mobile.length < 10) {
            alert('Please enter a valid mobile number.');
            return;
        }

        alert('Profile updated successfully!');
        
        document.querySelector('.profile-info-value').textContent = name;
        document.querySelectorAll('.profile-info-value')[1].textContent = email;
        document.querySelectorAll('.profile-info-value')[2].textContent = mobile;

        const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
        if (modal) {
            modal.hide();
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
    });

})();

