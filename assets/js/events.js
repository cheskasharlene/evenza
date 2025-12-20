/**
 * EVENZA - Events Page JavaScript
 * Search and filter functionality
 */

(function() {
    'use strict';

    // Filter events function
    window.filterEvents = function() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        const eventCards = document.querySelectorAll('[data-category]');
        
        eventCards.forEach(card => {
            const eventCategory = card.getAttribute('data-category');
            const eventName = card.querySelector('.event-name').textContent.toLowerCase();
            const eventDate = card.querySelector('.event-date-time').textContent.toLowerCase();
            
            // Check category filter
            const categoryMatch = !categoryFilter || eventCategory === categoryFilter;
            
            // Check search term
            const searchMatch = !searchTerm || 
                eventName.includes(searchTerm) || 
                eventDate.includes(searchTerm);
            
            // Show/hide card based on filters
            if (categoryMatch && searchMatch) {
                card.style.display = '';
                card.style.animation = 'fadeInUp 0.5s ease';
            } else {
                card.style.display = 'none';
            }
        });
    };

    // Real-time search as user types
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    filterEvents();
                }, 300); // Debounce for 300ms
            });
        }
        
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                filterEvents();
            });
        }

        // Check URL parameters for category filter
        const urlParams = new URLSearchParams(window.location.search);
        const categoryParam = urlParams.get('category');
        if (categoryParam && categoryFilter) {
            categoryFilter.value = categoryParam;
            filterEvents();
        }
    });

    // Add fade-in animation for event cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }, observerOptions);

    document.addEventListener('DOMContentLoaded', function() {
        const eventCards = document.querySelectorAll('.event-card-grid');
        eventCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    });

})();

