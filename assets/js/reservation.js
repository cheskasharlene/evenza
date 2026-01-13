(function() {
    'use strict';

    const reservationData = window.reservationData || { packages: [], selectedPackageId: null, eventId: null, defaultEvent: { date: '', time: '' } };

    const phFormatter = new Intl.NumberFormat('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    function formatPHP(amount) {
        return '₱ ' + phFormatter.format(amount || 0);
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString + 'T00:00:00');
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    function updateReservationSummary() {
        const dateInput = document.getElementById('reservationDate');
        const startTimeValue = (document.getElementById('eventStartTime') || {}).value || '';
        const endTimeValue = (document.getElementById('eventEndTime') || {}).value || '';
        const dateValue = dateInput ? dateInput.value : '';

        const summaryDateEl = document.getElementById('summaryDate');
        const summaryTimeEl = document.getElementById('summaryTimeRange');
        if (summaryDateEl) summaryDateEl.textContent = dateValue ? formatDate(dateValue) : (reservationData.defaultEvent.date || '');

        if (summaryTimeEl) {
            if (startTimeValue && endTimeValue) {
                summaryTimeEl.textContent = `${startTimeValue} - ${endTimeValue}`;
            } else if (startTimeValue) {
                summaryTimeEl.textContent = startTimeValue;
            } else if (endTimeValue) {
                summaryTimeEl.textContent = endTimeValue;
            } else {
                summaryTimeEl.textContent = reservationData.defaultEvent.time || '';
            }
        }
    }

    function setupPackageSelectionListeners() {
        const packageTiles = document.querySelectorAll('.package-tile');
        if (!packageTiles.length) return;

        packageTiles.forEach(t => t.classList.remove('selected'));

        packageTiles.forEach(tile => {
            const selectTile = () => {
                packageTiles.forEach(t => t.classList.remove('selected'));
                tile.classList.add('selected');

                const packageId = tile.getAttribute('data-id');
                const packageTier = tile.getAttribute('data-tier');
                const packageName = tile.getAttribute('data-name');
                const packagePrice = tile.getAttribute('data-price');

                const idEl = document.getElementById('packageId');
                const tierEl = document.getElementById('packageTier');
                const nameEl = document.getElementById('packageName');
                const priceEl = document.getElementById('packagePrice');
                if (idEl) idEl.value = packageId;
                if (tierEl) tierEl.value = packageTier;
                if (nameEl) nameEl.value = packageName;
                if (priceEl) priceEl.value = packagePrice;

                const summaryPkg = document.getElementById('summaryPackage');
                if (summaryPkg) summaryPkg.textContent = packageName;
                const summaryTotal = document.getElementById('summaryTotal');
                if (summaryTotal) summaryTotal.textContent = formatPHP(parseFloat(packagePrice));
            };

            tile.addEventListener('click', selectTile);
            tile.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectTile();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('reservationDate');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        }

        const startTimeSelect = document.getElementById('eventStartTime');
        const endTimeSelect = document.getElementById('eventEndTime');

        setupPackageSelectionListeners();

        updateReservationSummary();
        if (dateInput) dateInput.addEventListener('change', updateReservationSummary);
        if (startTimeSelect) startTimeSelect.addEventListener('change', updateReservationSummary);
        if (endTimeSelect) endTimeSelect.addEventListener('change', updateReservationSummary);

        const reservationForm = document.getElementById('reservationForm');
        if (reservationForm) {
            reservationForm.addEventListener('submit', function(e) {
                const fullName = (document.getElementById('fullName') || {}).value?.trim() || '';
                const email = (document.getElementById('email') || {}).value?.trim() || '';
                const mobile = (document.getElementById('mobile') || {}).value?.trim() || '';
                const packageId = (document.getElementById('packageId') || {}).value || '';
                const dateValue = dateInput ? dateInput.value : '';
                const startTimeValue = startTimeSelect ? startTimeSelect.value : '';
                const endTimeValue = endTimeSelect ? endTimeSelect.value : '';

                if (!fullName || !email || !mobile) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Please enter a valid email address.');
                    return false;
                }
                if (mobile.length < 10) {
                    e.preventDefault();
                    alert('Please enter a valid mobile number.');
                    return false;
                }
                if (!packageId) {
                    e.preventDefault();
                    alert('Please select a package.');
                    return false;
                }

                let hasErrors = false;
                if (!dateValue) {
                    e.preventDefault();
                    alert('Please select a date.');
                    hasErrors = true;
                }
                if (!startTimeValue) {
                    e.preventDefault();
                    alert('Please select a start time.');
                    hasErrors = true;
                }
                if (!endTimeValue) {
                    e.preventDefault();
                    alert('Please select an end time.');
                    hasErrors = true;
                }
                if (hasErrors) return false;

                const timeOrder = ['08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM', '09:00 PM', '10:00 PM', '11:00 PM'];
                const startIndex = timeOrder.indexOf(startTimeValue);
                const endIndex = timeOrder.indexOf(endTimeValue);
                if (startIndex >= endIndex) {
                    e.preventDefault();
                    alert('Event end time must be after start time.');
                    return false;
                }

                const selectedDate = new Date(dateValue + 'T00:00:00');
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate < today) {
                    e.preventDefault();
                    alert('Please select a future date.');
                    if (dateInput) {
                        dateInput.classList.add('is-invalid');
                        dateInput.focus();
                    }
                    return false;
                }
            });
        }
    });

})();

