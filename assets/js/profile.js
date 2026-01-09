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

        if (mobile.length < 7) {
            alert('Please enter a valid mobile number.');
            return;
        }

        const saveBtn = document.querySelector('#editProfileModal .btn-primary-luxury');
        if (saveBtn) saveBtn.disabled = true;

        const formData = new URLSearchParams();
        formData.append('fullName', name);
        formData.append('email', email);
        formData.append('mobile', mobile);

        fetch('../api/updateProfile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                if (modal) modal.hide();
                
                alert('Profile updated successfully!');
                
                window.location.reload();
            } else {
                alert(data.message || 'Failed to update profile.');
                if (saveBtn) saveBtn.disabled = false;
            }
        })
        .catch(err => {
            console.error('Profile update error:', err);
            alert('An error occurred while saving your profile. Please try again.');
            if (saveBtn) saveBtn.disabled = false;
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
    });

})();

