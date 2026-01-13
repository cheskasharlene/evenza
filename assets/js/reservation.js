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
        const mobileSummaryDateEl = document.getElementById('mobileSummaryDate');
        const mobileSummaryTimeEl = document.getElementById('mobileSummaryTimeRange');
        
        const formattedDate = dateValue ? formatDate(dateValue) : (reservationData.defaultEvent.date || '');
        if (summaryDateEl) summaryDateEl.textContent = formattedDate;
        if (mobileSummaryDateEl) mobileSummaryDateEl.textContent = formattedDate;
        
        let timeText = '';
        if (startTimeValue && endTimeValue) {
            timeText = `${startTimeValue} - ${endTimeValue}`;
        } else if (startTimeValue) {
            timeText = startTimeValue;
        } else if (endTimeValue) {
            timeText = endTimeValue;
        } else {
            timeText = reservationData.defaultEvent.time || '';
        }
        
        if (summaryTimeEl) summaryTimeEl.textContent = timeText;
        if (mobileSummaryTimeEl) mobileSummaryTimeEl.textContent = timeText;
    }

    function setupPackageSelectionListeners() {
        const packageTiles = document.querySelectorAll('.package-tile');
        if (!packageTiles.length) return;

        // Set initial selected package based on hidden input value
        const initialPackageId = document.getElementById('packageId')?.value;
        if (initialPackageId) {
            packageTiles.forEach(tile => {
                if (tile.getAttribute('data-id') === initialPackageId) {
                    tile.classList.add('selected');
                } else {
                    tile.classList.remove('selected');
                }
            });
        } else {
            packageTiles.forEach(t => t.classList.remove('selected'));
        }

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
                
                // Update mobile summary
                const mobileSummaryPkg = document.getElementById('mobileSummaryPackage');
                if (mobileSummaryPkg) mobileSummaryPkg.textContent = packageName;
                const mobileSummaryTotal = document.getElementById('mobileSummaryTotal');
                if (mobileSummaryTotal) mobileSummaryTotal.textContent = formatPHP(parseFloat(packagePrice));
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
            // Helper function to show error
            function showError(field, errorMessageEl, message) {
                field.classList.add('is-invalid');
                if (errorMessageEl) {
                    errorMessageEl.textContent = message;
                    errorMessageEl.classList.add('show');
                }
            }

            // Helper function to clear error
            function clearError(field, errorMessageEl) {
                field.classList.remove('is-invalid');
                if (errorMessageEl) {
                    errorMessageEl.textContent = '';
                    errorMessageEl.classList.remove('show');
                }
            }

            // Clear errors when user starts typing/selecting
            if (dateInput) {
                dateInput.addEventListener('input', function() {
                    clearError(this, document.getElementById('reservationDateError'));
                });
            }
            if (startTimeSelect) {
                startTimeSelect.addEventListener('change', function() {
                    clearError(this, document.getElementById('eventStartTimeError'));
                });
            }
            if (endTimeSelect) {
                endTimeSelect.addEventListener('change', function() {
                    clearError(this, document.getElementById('eventEndTimeError'));
                });
            }

            reservationForm.addEventListener('submit', function(e) {
                const fullName = (document.getElementById('fullName') || {}).value?.trim() || '';
                const email = (document.getElementById('email') || {}).value?.trim() || '';
                const mobile = (document.getElementById('mobile') || {}).value?.trim() || '';
                const packageId = (document.getElementById('packageId') || {}).value || '';
                const dateValue = dateInput ? dateInput.value : '';
                const startTimeValue = startTimeSelect ? startTimeSelect.value : '';
                const endTimeValue = endTimeSelect ? endTimeSelect.value : '';

                // Clear all previous errors
                const allFields = [dateInput, startTimeSelect, endTimeSelect].filter(f => f);
                allFields.forEach(field => {
                    const errorId = field.id + 'Error';
                    clearError(field, document.getElementById(errorId));
                });

                let hasErrors = false;

                // Validate required fields
                if (!fullName || !email || !mobile) {
                    e.preventDefault();
                    hasErrors = true;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && !emailRegex.test(email)) {
                    e.preventDefault();
                    hasErrors = true;
                }

                if (mobile && mobile.length < 10) {
                    e.preventDefault();
                    hasErrors = true;
                }

                if (!packageId) {
                    e.preventDefault();
                    hasErrors = true;
                }

                // Validate date
                if (!dateValue) {
                    e.preventDefault();
                    showError(dateInput, document.getElementById('reservationDateError'), 'Please select a preferred date');
                    hasErrors = true;
                } else {
                    const selectedDate = new Date(dateValue + 'T00:00:00');
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (selectedDate < today) {
                        e.preventDefault();
                        showError(dateInput, document.getElementById('reservationDateError'), 'Please select a future date');
                        hasErrors = true;
                    }
                }

                // Validate start time
                if (!startTimeValue) {
                    e.preventDefault();
                    showError(startTimeSelect, document.getElementById('eventStartTimeError'), 'Please select a start time');
                    hasErrors = true;
                }

                // Validate end time
                if (!endTimeValue) {
                    e.preventDefault();
                    showError(endTimeSelect, document.getElementById('eventEndTimeError'), 'Please select an end time');
                    hasErrors = true;
                }

                // Validate time order
                if (startTimeValue && endTimeValue) {
                    const timeOrder = ['08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM', '09:00 PM', '10:00 PM', '11:00 PM'];
                    const startIndex = timeOrder.indexOf(startTimeValue);
                    const endIndex = timeOrder.indexOf(endTimeValue);
                    if (startIndex >= endIndex) {
                        e.preventDefault();
                        showError(endTimeSelect, document.getElementById('eventEndTimeError'), 'Event end time must be after start time');
                        hasErrors = true;
                    }
                }

                if (hasErrors) {
                    return false;
                }
            });
        }
    });

})();

