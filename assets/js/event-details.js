/**
 * EVENZA - Event Details Page JavaScript
 * Quantity selector and reservation functionality
 */

(function() {
    'use strict';

    const eventPrice = parseFloat(document.querySelector('.price-large')?.textContent.replace('$', '').replace(',', '')) || 299;
    const maxTickets = parseInt(document.getElementById('ticketQuantity')?.getAttribute('max')) || 1;

    // Increase quantity
    window.increaseQuantity = function() {
        const quantityInput = document.getElementById('ticketQuantity');
        let currentValue = parseInt(quantityInput.value) || 1;
        if (currentValue < maxTickets) {
            quantityInput.value = currentValue + 1;
            updateTotalPrice();
        }
    };

    // Decrease quantity
    window.decreaseQuantity = function() {
        const quantityInput = document.getElementById('ticketQuantity');
        let currentValue = parseInt(quantityInput.value) || 1;
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            updateTotalPrice();
        }
    };

    // Update total price
    function updateTotalPrice() {
        const quantityInput = document.getElementById('ticketQuantity');
        const totalPriceElement = document.getElementById('totalPrice');
        const quantity = parseInt(quantityInput.value) || 1;
        const total = eventPrice * quantity;
        totalPriceElement.textContent = '$' + total.toLocaleString();
    }

    // Quantity input change
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('ticketQuantity');
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                let value = parseInt(this.value) || 1;
                if (value < 1) value = 1;
                if (value > maxTickets) value = maxTickets;
                this.value = value;
                updateTotalPrice();
            });
        }
    });

    // Reserve tickets
    window.reserveTickets = function() {
        const quantityInput = document.getElementById('ticketQuantity');
        const quantity = parseInt(quantityInput.value) || 1;
        
        // Get event ID from URL or use default
        const urlParams = new URLSearchParams(window.location.search);
        const eventId = urlParams.get('id') || 1;
        
        // Redirect to reservation page
        window.location.href = 'reservation.php?eventId=' + eventId + '&quantity=' + quantity;
    };

    // AI Assistant functionality
    window.askAI = function() {
        const questionInput = document.getElementById('aiQuestion');
        const question = questionInput.value.trim();
        
        if (!question) {
            return;
        }

        // Add user question to chat
        const chatBox = document.querySelector('.ai-chat-box');
        const userMessage = document.createElement('div');
        userMessage.className = 'user-message mb-2';
        userMessage.innerHTML = '<p class="mb-0"><strong>You:</strong> ' + question + '</p>';
        chatBox.appendChild(userMessage);

        // Clear input
        questionInput.value = '';

        // Simulate AI response (in a real app, this would call an API)
        setTimeout(function() {
            const aiMessage = document.createElement('div');
            aiMessage.className = 'ai-message';
            aiMessage.innerHTML = '<p class="mb-0"><strong>AI:</strong> ' + getAIResponse(question) + '</p>';
            chatBox.appendChild(aiMessage);
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 1000);
    };

    // Get AI response (simulated)
    function getAIResponse(question) {
        const lowerQuestion = question.toLowerCase();
        
        if (lowerQuestion.includes('price') || lowerQuestion.includes('cost')) {
            return 'The ticket price is $' + eventPrice.toLocaleString() + ' per person. The total will be calculated based on the number of tickets you select.';
        } else if (lowerQuestion.includes('date') || lowerQuestion.includes('when')) {
            return 'The event date and time are displayed in the event details above. Please check the Date & Time section for specific information.';
        } else if (lowerQuestion.includes('venue') || lowerQuestion.includes('location') || lowerQuestion.includes('where')) {
            return 'The event will be held at the venue specified in the event details. Full address and directions are provided in the Venue section.';
        } else if (lowerQuestion.includes('cancel') || lowerQuestion.includes('refund')) {
            return 'Cancellations made 48 hours before the event will receive a full refund. Please check the FAQs section for more details.';
        } else if (lowerQuestion.includes('parking')) {
            return 'Complimentary valet parking is available for all event attendees. Please arrive 15 minutes early to allow time for parking.';
        } else {
            return 'Thank you for your question! For detailed information, please check the FAQs section below or contact our support team at info@evenza.com.';
        }
    }

    // Allow Enter key to submit AI question
    document.addEventListener('DOMContentLoaded', function() {
        const aiQuestionInput = document.getElementById('aiQuestion');
        if (aiQuestionInput) {
            aiQuestionInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    askAI();
                }
            });
        }
    });

})();

