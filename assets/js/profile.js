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

        // Disable button while saving
        const saveBtn = document.querySelector('#editProfileModal .btn-primary-luxury');
        if (saveBtn) saveBtn.disabled = true;

        const formData = new URLSearchParams();
        formData.append('fullName', name);
        formData.append('email', email);
        formData.append('mobile', mobile);

        fetch('api/updateProfile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update visible profile info
                const infoValues = document.querySelectorAll('.profile-info-value');
                if (infoValues.length >= 3) {
                    infoValues[0].textContent = name;
                    infoValues[1].textContent = email;
                    infoValues[2].textContent = mobile;
                }
                alert('Profile updated successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                if (modal) modal.hide();
            } else {
                alert(data.message || 'Failed to update profile.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while saving your profile. Please try again.');
        })
        .finally(() => {
            if (saveBtn) saveBtn.disabled = false;
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
    });

})();

