/**
 * EVENZA - Payment Page JavaScript
 * Payment processing simulation
 */

(function() {
    'use strict';

    // Process payment
    window.processPayment = function() {
        const payButton = document.querySelector('.btn-paypal');
        const statusMessages = document.getElementById('statusMessages');
        
        // Disable button
        payButton.disabled = true;
        payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        
        // Show processing message
        statusMessages.innerHTML = `
            <div class="status-message status-processing">
                <div class="status-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="status-content">
                    <h5>Payment Processing</h5>
                    <p>Your payment is being processed. Please wait...</p>
                </div>
            </div>
        `;
        
        // Simulate payment processing (in real app, this would call PayPal API)
        setTimeout(function() {
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const eventId = urlParams.get('eventId') || 1;
            const quantity = urlParams.get('quantity') || 1;
            
            // Get form data from payment information section or URL
            const paymentInfo = document.querySelector('.payment-information');
            let fullName = 'Guest';
            let email = '';
            
            if (paymentInfo) {
                const nameText = paymentInfo.textContent.match(/Name:\s*([^\n]+)/);
                const emailText = paymentInfo.textContent.match(/Email:\s*([^\n]+)/);
                if (nameText) fullName = nameText[1].trim();
                if (emailText) email = emailText[1].trim();
            }
            
            // Fallback to URL params if available
            if (urlParams.get('fullName')) fullName = urlParams.get('fullName');
            if (urlParams.get('email')) email = urlParams.get('email');
            
            // Redirect to confirmation page
            const confirmationUrl = new URL('confirmation.php', window.location.origin);
            confirmationUrl.searchParams.set('eventId', eventId);
            confirmationUrl.searchParams.set('quantity', quantity);
            confirmationUrl.searchParams.set('fullName', fullName);
            confirmationUrl.searchParams.set('email', email);
            
            window.location.href = confirmationUrl.toString();
        }, 3000); // Simulate 3 second processing time
    };

    // Auto-scroll to status message if processing or success
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        
        if (status === 'processing' || status === 'success') {
            setTimeout(function() {
                const statusMessages = document.getElementById('statusMessages');
                if (statusMessages) {
                    statusMessages.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 500);
        }
    });

})();

