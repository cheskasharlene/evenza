(function() {
    'use strict';

    // Lightweight event-details script adapted for inquiry model

    window.askAI = function() {
        const questionInput = document.getElementById('aiQuestion');
        const question = questionInput.value.trim();
        if (!question) return;
        const chatBox = document.querySelector('.ai-chat-box');
        const userMessage = document.createElement('div');
        userMessage.className = 'user-message mb-2';
        userMessage.innerHTML = '<p class="mb-0"><strong>You:</strong> ' + question + '</p>';
        chatBox.appendChild(userMessage);
        questionInput.value = '';

        setTimeout(function() {
            const aiMessage = document.createElement('div');
            aiMessage.className = 'ai-message';
            aiMessage.innerHTML = '<p class="mb-0"><strong>AI:</strong> ' + getAIResponse(question) + '</p>';
            chatBox.appendChild(aiMessage);
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 700);
    };

    function getAIResponse(question) {
        const lowerQuestion = question.toLowerCase();
        if (lowerQuestion.includes('price') || lowerQuestion.includes('cost')) {
            return 'Package pricing is available on the reservation page after you click Inquire Reservation — each package is a flat rate for the event.';
        } else if (lowerQuestion.includes('date') || lowerQuestion.includes('when')) {
            return 'For scheduling details, please check the event page or contact support; inquiries go to the reservation form for availability.';
        } else if (lowerQuestion.includes('venue') || lowerQuestion.includes('location') || lowerQuestion.includes('where')) {
            return 'Venue details are shown in the Venue section above, including the full address.';
        } else if (lowerQuestion.includes('cancel') || lowerQuestion.includes('refund')) {
            return 'Cancellations made 48 hours before the event will receive a full refund. Please see the FAQs for details.';
        } else if (lowerQuestion.includes('parking')) {
            return 'Complimentary valet parking is available; please arrive 15 minutes early.';
        } else {
            return 'Thanks for your question! For more details, contact support at info@evenza.com or use the reservation inquiry form.';
        }
    }

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

