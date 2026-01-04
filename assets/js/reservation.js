(function() {
    'use strict';

    const packages = (typeof reservationData !== 'undefined' && Array.isArray(reservationData.packages)) ? reservationData.packages : [];
    const selectedPackageId = (typeof reservationData !== 'undefined' && reservationData.selectedPackageId) ? reservationData.selectedPackageId : (packages[0] && packages[0].id);

    const phFormatter = new Intl.NumberFormat('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function formatPHP(amount) {
        return '₱ ' + phFormatter.format(amount);
    }

    function selectPackageById(id, scrollIntoView = false) {
        const optionEls = document.querySelectorAll('.package-card');
        optionEls.forEach(el => {
            if (el.getAttribute('data-id') === id) {
                el.classList.add('package-selected');
                const name = el.getAttribute('data-name');
                const price = parseFloat(el.getAttribute('data-price')) || 0;
                document.getElementById('packageId').value = id;
                document.getElementById('packageName').value = name;
                document.getElementById('packagePrice').value = price;
                const summaryPkg = document.getElementById('summaryPackage');
                if (summaryPkg) summaryPkg.textContent = name;
                const summaryTotal = document.getElementById('summaryTotal');
                if (summaryTotal) summaryTotal.textContent = formatPHP(price);
                if (scrollIntoView) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                el.classList.remove('package-selected');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const optionEls = document.querySelectorAll('.package-card');
        optionEls.forEach(el => {
            el.addEventListener('click', function() { selectPackageById(el.getAttribute('data-id'), true); });
            el.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectPackageById(el.getAttribute('data-id'), true); } });
            const priceEl = el.querySelector('.package-price');
            if (priceEl) {
                const price = parseFloat(el.getAttribute('data-price')) || 0;
                priceEl.textContent = formatPHP(price);
            }
        });

        if (selectedPackageId) selectPackageById(selectedPackageId);

        const reservationForm = document.getElementById('reservationForm');
        if (reservationForm) {
            reservationForm.addEventListener('submit', function(e) {
                const fullName = document.getElementById('fullName').value.trim();
                const email = document.getElementById('email').value.trim();
                const mobile = document.getElementById('mobile').value.trim();
                const packageId = document.getElementById('packageId').value;

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
            });
        }
    });

})();

