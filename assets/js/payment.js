// Make processPayment globally accessible immediately
window.processPayment = function() {
    console.log('processPayment called'); // Debug
    
    const payButton = document.querySelector('.btn-paypal');
    const statusMessages = document.getElementById('statusMessages');
    
    if (!payButton) {
        console.error('PayPal button not found');
        alert('Payment button not found. Please refresh the page.');
        return;
    }
    
    if (!statusMessages) {
        console.error('Status messages container not found');
        // Create status messages container if it doesn't exist
        const paymentSection = payButton.closest('.payment-button-section');
        if (paymentSection) {
            const newStatusDiv = document.createElement('div');
            newStatusDiv.id = 'statusMessages';
            newStatusDiv.className = 'mt-4';
            paymentSection.parentNode.insertBefore(newStatusDiv, paymentSection.nextSibling);
        }
    }
    
    payButton.disabled = true;
    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    const statusContainer = document.getElementById('statusMessages');
    if (statusContainer) {
        statusContainer.innerHTML = `
            <div class="status-message status-processing">
                <div class="status-icon">
                </div>
                <div class="status-content">
                    <h5>Payment Processing</h5>
                    <p>Redirecting to PayPal to complete your payment securely...</p>
                </div>
            </div>
        `;
    }
    
    // Get URL parameters for PayPal redirect
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = urlParams.get('eventId') || 1;
    const packageId = urlParams.get('packageId') || 0;
    const packagePrice = urlParams.get('packagePrice') || 0;
    
    console.log('Payment params:', { eventId, packageId, packagePrice }); // Debug
    
    // In production, this would redirect to actual PayPal gateway
    // PayPal sandbox URL format: https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=...
    
    // Simulate PayPal redirect (in production, use actual PayPal API)
    setTimeout(function() {
        // Build callback URL - use relative path to ensure it works
        let callbackUrl = 'paypalCallback.php';
        const params = new URLSearchParams();
        params.set('eventId', eventId);
        params.set('packageId', packageId);
        params.set('amount', packagePrice);
        params.set('payment_status', 'Completed');
        params.set('PayerID', 'PAYER' + Date.now());
        
        callbackUrl += '?' + params.toString();
        
        console.log('Redirecting to:', callbackUrl); // Debug
        
        // Redirect to PayPal callback handler (simulating PayPal return)
        window.location.href = callbackUrl;
    }, 1500); // Reduced to 1.5 seconds for faster redirect
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment script loaded');
    
    // Verify processPayment is available
    if (typeof window.processPayment === 'function') {
        console.log('processPayment function is available');
    } else {
        console.error('processPayment function is NOT available!');
    }
    
    // Add click event listener as backup (in addition to onclick attribute)
    const payButton = document.querySelector('.btn-paypal');
    if (payButton) {
        payButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('PayPal button clicked via event listener');
            if (typeof window.processPayment === 'function') {
                window.processPayment();
            } else {
                alert('Payment function not available. Please refresh the page.');
            }
        });
    }
    
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
