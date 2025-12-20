(function() {
    'use strict';

    const ticketPrice = (typeof reservationData !== 'undefined' && reservationData.ticketPrice) ? reservationData.ticketPrice : 299;
    const maxTickets = (typeof reservationData !== 'undefined' && reservationData.maxTickets) ? reservationData.maxTickets : parseInt(document.getElementById('ticketQuantity')?.getAttribute('max')) || 1;

    window.increaseTicketQuantity = function() {
        const quantityInput = document.getElementById('ticketQuantity');
        const hiddenQuantity = document.getElementById('hiddenQuantity');
        let currentValue = parseInt(quantityInput.value) || 1;
        if (currentValue < maxTickets) {
            quantityInput.value = currentValue + 1;
            hiddenQuantity.value = currentValue + 1;
            updateSummary();
        }
    };

    window.decreaseTicketQuantity = function() {
        const quantityInput = document.getElementById('ticketQuantity');
        const hiddenQuantity = document.getElementById('hiddenQuantity');
        let currentValue = parseInt(quantityInput.value) || 1;
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            hiddenQuantity.value = currentValue - 1;
            updateSummary();
        }
    };

    function updateSummary() {
        const quantityInput = document.getElementById('ticketQuantity');
        const quantity = parseInt(quantityInput.value) || 1;
        const total = ticketPrice * quantity;

        const summaryQuantity = document.getElementById('summaryQuantity');
        if (summaryQuantity) {
            summaryQuantity.textContent = quantity;
        }

        const summaryTotal = document.getElementById('summaryTotal');
        if (summaryTotal) {
            summaryTotal.textContent = '$' + total.toLocaleString();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('ticketQuantity');
        const hiddenQuantity = document.getElementById('hiddenQuantity');
        
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                let value = parseInt(this.value) || 1;
                if (value < 1) value = 1;
                if (value > maxTickets) value = maxTickets;
                this.value = value;
                if (hiddenQuantity) {
                    hiddenQuantity.value = value;
                }
                updateSummary();
            });
        }

        const reservationForm = document.getElementById('reservationForm');
        if (reservationForm) {
            reservationForm.addEventListener('submit', function(e) {
                const fullName = document.getElementById('fullName').value.trim();
                const email = document.getElementById('email').value.trim();
                const mobile = document.getElementById('mobile').value.trim();

                if (!fullName || !email || !mobile) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                // Email validation
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
            });
        }
    });

})();

