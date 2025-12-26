// EVENZA Admin Dashboard - Main JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Navigation routing
    const navLinks = document.querySelectorAll('.nav-link[data-section]');
    const sections = document.querySelectorAll('.admin-section');
    const pageTitle = document.getElementById('pageTitle');
    const pageSubtitle = document.getElementById('pageSubtitle');

    const sectionTitles = {
        'dashboard': { title: 'Dashboard', subtitle: 'Overview of activity and performance' },
        'events': { title: 'Event Management', subtitle: 'Manage all events in the system' },
        'reservations': { title: 'Reservations Management', subtitle: 'View and manage all reservations' },
        'users': { title: 'User Management', subtitle: 'Manage client and admin accounts' }
    };

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSection = this.getAttribute('data-section');
            
            // Update active nav link
            navLinks.forEach(nl => nl.classList.remove('active'));
            this.classList.add('active');
            
            // Show target section
            sections.forEach(s => s.classList.remove('active'));
            document.getElementById(targetSection + '-section').classList.add('active');
            
            // Update page title
            if (sectionTitles[targetSection]) {
                pageTitle.textContent = sectionTitles[targetSection].title;
                pageSubtitle.textContent = sectionTitles[targetSection].subtitle;
            }
            
            // Load section data
            loadSectionData(targetSection);
        });
    });

    // Load data for each section
    function loadSectionData(section) {
        switch(section) {
            case 'dashboard':
                loadDashboardData();
                break;
            case 'events':
                loadEventsData();
                break;
            case 'reservations':
                loadReservationsData();
                break;
            case 'users':
                loadUsersData();
                break;
        }
    }

    // Dashboard data loading
    function loadDashboardData() {
        fetch('admin/api/dashboard.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('totalRevenue').textContent = formatCurrency(data.revenue);
                    document.getElementById('ticketsSold').textContent = data.ticketsSold;
                    document.getElementById('activeEvents').textContent = data.activeEvents;
                    document.getElementById('newUsers').textContent = data.newUsers;
                    
                    // Populate top events
                    const tbody = document.getElementById('topEventsBody');
                    tbody.innerHTML = data.topEvents.map(event => `
                        <tr>
                            <td><strong>${event.title}</strong></td>
                            <td>${event.ticketsSold}</td>
                            <td>${event.capacity}%</td>
                            <td>₱ ${formatCurrency(event.revenue)}</td>
                        </tr>
                    `).join('');
                    
                    // Populate recent activity
                    const activityDiv = document.getElementById('recentActivity');
                    activityDiv.innerHTML = data.recentActivity.map(activity => `
                        <div class="activity-item mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>${activity.userName}</strong>
                                <span class="text-muted small">${activity.date}</span>
                            </div>
                            <div class="text-muted small">${activity.eventName}</div>
                        </div>
                    `).join('');
                }
            })
            .catch(error => console.error('Error loading dashboard:', error));
    }

    // Events data loading
    function loadEventsData() {
        fetch('admin/api/events.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayEvents(data.events);
                }
            })
            .catch(error => console.error('Error loading events:', error));
    }

    function displayEvents(events) {
        const tbody = document.getElementById('eventsTableBody');
        tbody.innerHTML = events.map(event => `
            <tr>
                <td>${event.eventId}</td>
                <td><strong>${escapeHtml(event.title)}</strong></td>
                <td>${escapeHtml(event.venue)}</td>
                <td><span class="badge bg-secondary">${event.category}</span></td>
                <td><img src="${escapeHtml(event.imagePath)}" alt="${escapeHtml(event.title)}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;"></td>
                <td>
                    <button class="icon-btn" onclick="editEvent(${event.eventId})" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="icon-btn text-danger" onclick="deleteEvent(${event.eventId})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Category filter
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const category = this.value;
            fetch(`admin/api/events.php?category=${category}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayEvents(data.events);
                    }
                });
        });
    }

    // Reservations data loading
    function loadReservationsData() {
        fetch('admin/api/reservations.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayReservations(data.reservations);
                }
            })
            .catch(error => console.error('Error loading reservations:', error));
    }

    function displayReservations(reservations) {
        const tbody = document.getElementById('reservationsTableBody');
        tbody.innerHTML = reservations.map(res => {
            const statusClass = `status-${res.status}`;
            return `
                <tr>
                    <td>#${res.reservationId}</td>
                    <td>${escapeHtml(res.eventTitle)}</td>
                    <td>${escapeHtml(res.userName)}</td>
                    <td>${formatDate(res.reservationDate)}<br><small class="text-muted">${formatTime(res.startTime)} - ${formatTime(res.endTime)}</small></td>
                    <td>${res.packageName || 'N/A'}</td>
                    <td><strong>₱ ${formatCurrency(res.totalAmount)}</strong></td>
                    <td><span class="status-badge ${statusClass}">${res.status.charAt(0).toUpperCase() + res.status.slice(1)}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="updateReservationStatus(${res.reservationId}, '${res.status}')">
                            <i class="bi bi-pencil me-1"></i>Update Status
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Users data loading
    function loadUsersData() {
        fetch('admin/api/users.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUsers(data.users);
                }
            })
            .catch(error => console.error('Error loading users:', error));
    }

    function displayUsers(users) {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = users.map(user => `
            <tr>
                <td>${user.userId}</td>
                <td><strong>${escapeHtml(user.fullName)}</strong></td>
                <td>${escapeHtml(user.email)}</td>
                <td><span class="badge ${user.role === 'admin' ? 'bg-danger' : 'bg-primary'}">${user.role}</span></td>
                <td>${formatDate(user.createdAt)}</td>
                <td>
                    <button class="icon-btn text-danger" onclick="deleteUser(${user.userId})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Add Event Form
    const addEventForm = document.getElementById('addEventForm');
    if (addEventForm) {
        addEventForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('admin/api/events.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Event added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
                    this.reset();
                    loadEventsData();
                } else {
                    showAlert('danger', data.message || 'Failed to add event');
                }
            })
            .catch(error => {
                showAlert('danger', 'Error adding event');
                console.error(error);
            });
        });
    }

    // Edit Event Form
    const editEventForm = document.getElementById('editEventForm');
    if (editEventForm) {
        editEventForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update');
            
            fetch('admin/api/events.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Event updated successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('editEventModal')).hide();
                    loadEventsData();
                } else {
                    showAlert('danger', data.message || 'Failed to update event');
                }
            })
            .catch(error => {
                showAlert('danger', 'Error updating event');
                console.error(error);
            });
        });
    }

    // Add User Form
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('admin/api/users.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'User added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                    this.reset();
                    loadUsersData();
                } else {
                    showAlert('danger', data.message || 'Failed to add user');
                }
            })
            .catch(error => {
                showAlert('danger', 'Error adding user');
                console.error(error);
            });
        });
    }

    // Update Status Form
    const updateStatusForm = document.getElementById('updateStatusForm');
    if (updateStatusForm) {
        updateStatusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('admin/api/reservations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Reservation status updated successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
                    loadReservationsData();
                } else {
                    showAlert('danger', data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                showAlert('danger', 'Error updating status');
                console.error(error);
            });
        });
    }

    // Helper functions
    window.editEvent = function(eventId) {
        fetch(`admin/api/events.php?id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.event) {
                    const event = data.event;
                    document.getElementById('editEventId').value = event.eventId;
                    document.getElementById('editEventTitle').value = event.title;
                    document.getElementById('editEventVenue').value = event.venue;
                    document.getElementById('editEventDescription').value = event.description;
                    document.getElementById('editEventCategory').value = event.category;
                    document.getElementById('editEventImagePath').value = event.imagePath;
                    
                    new bootstrap.Modal(document.getElementById('editEventModal')).show();
                }
            });
    };

    window.deleteEvent = function(eventId) {
        if (confirm('Are you sure you want to delete this event?')) {
            fetch('admin/api/events.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ eventId: eventId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Event deleted successfully!');
                    loadEventsData();
                } else {
                    showAlert('danger', data.message || 'Failed to delete event');
                }
            });
        }
    };

    window.updateReservationStatus = function(reservationId, currentStatus) {
        document.getElementById('statusReservationId').value = reservationId;
        document.getElementById('reservationStatus').value = currentStatus;
        new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
    };

    window.deleteUser = function(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            fetch('admin/api/users.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'User deleted successfully!');
                    loadUsersData();
                } else {
                    showAlert('danger', data.message || 'Failed to delete user');
                }
            });
        }
    };

    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    function formatCurrency(amount) {
        return parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function formatTime(timeString) {
        if (!timeString) return '';
        const [hours, minutes] = timeString.split(':');
        const h = parseInt(hours);
        const ampm = h >= 12 ? 'PM' : 'AM';
        const displayHour = h > 12 ? h - 12 : (h === 0 ? 12 : h);
        return `${displayHour}:${minutes} ${ampm}`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Sidebar toggle for mobile
    const sidebarToggle = document.getElementById('adminSidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('d-none');
        });
    }

    // Load initial dashboard data
    loadDashboardData();
});

