/**
 * EVENZA - Confirmation Page JavaScript
 * QR Code generation
 */

(function() {
    'use strict';

    // Generate QR Code when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Get ticket ID from the page
        const ticketIdElement = document.querySelector('.ticket-id');
        const ticketId = ticketIdElement ? ticketIdElement.textContent.trim() : 'EVZ-00000000';
        
        // Get event details for QR code data
        const eventName = document.querySelector('.confirmation-value')?.textContent.trim() || 'Event';
        
        // Create QR code data (you can customize this format)
        const qrData = JSON.stringify({
            ticketId: ticketId,
            event: eventName,
            timestamp: new Date().toISOString()
        });
        
        // Generate QR Code
        const qrContainer = document.getElementById('qrcode');
        if (qrContainer) {
            if (typeof QRCode !== 'undefined') {
                // Clear container
                qrContainer.innerHTML = '';
                
                // Use QRCode.js library (davidshimjs version)
                new QRCode(qrContainer, {
                    text: qrData,
                    width: 200,
                    height: 200,
                    colorDark: '#5A6B4F',
                    colorLight: '#FDFCF9',
                    correctLevel: QRCode.CorrectLevel.H
                });
            } else {
                // Fallback if QRCode library didn't load
                showQRFallback(qrContainer, ticketId);
            }
        }
        
        function showQRFallback(container, ticketId) {
            container.innerHTML = `
                <div style="width: 200px; height: 200px; background: linear-gradient(135deg, #6B7F5A 0%, #B8A082 100%); display: flex; align-items: center; justify-content: center; border-radius: 8px; color: white; text-align: center; padding: 1rem;">
                    <div>
                        <p style="font-size: 0.9rem; margin: 0; font-weight: 600;">QR Code</p>
                        <p style="font-size: 0.75rem; margin: 0.5rem 0 0 0; font-family: monospace;">${ticketId}</p>
                    </div>
                </div>
            `;
        }
    });

    // Print functionality enhancement
    window.addEventListener('beforeprint', function() {
        // Add print-specific styles if needed
        document.body.classList.add('printing');
    });

    window.addEventListener('afterprint', function() {
        document.body.classList.remove('printing');
    });

})();

