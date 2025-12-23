// Dashboard sample data and interactivity (replace with API calls as needed)
const sampleTopEvents = [
    { title: 'Autumn Gala', tickets: 420, capacity: 500, revenue: 126000 },
    { title: 'Summer Jazz Night', tickets: 375, capacity: 400, revenue: 112500 },
    { title: 'Food & Wine', tickets: 330, capacity: 350, revenue: 99000 },
    { title: 'Artisan Market', tickets: 280, capacity: 300, revenue: 56000 },
    { title: 'Charity Ball', tickets: 250, capacity: 300, revenue: 75000 }
];

const sampleActivity = [
    { user: 'Cheska Sharlene', event: 'Autumn Gala', seats: 2, at: '2025-12-23 09:12' },
    { user: 'Jana Mae', event: 'Summer Jazz Night', seats: 4, at: '2025-12-22 18:05' },
    { user: 'Mae Macandili', event: 'Food & Wine', seats: 1, at: '2025-12-21 16:30' },
    { user: 'Lance Hendrix', event: 'Artisan Market', seats: 3, at: '2025-12-20 13:20' },
    { user: 'New User', event: 'Charity Ball', seats: 2, at: '2025-12-19 11:02' }
];

function formatCurrency(n) { return n.toLocaleString('en-PH'); }

function populateDashboard() {
    const topEventsBody = document.getElementById('topEventsBody');
    if (topEventsBody) {
        topEventsBody.innerHTML = sampleTopEvents.map(ev => `
            <tr>
                <td>
                    <div class="d-flex flex-column">
                        <div class="fw-semibold">${ev.title}</div>
                        <div class="text-muted small">${Math.round((ev.tickets/ev.capacity)*100)}% capacity</div>
                    </div>
                </td>
                <td>${ev.tickets}</td>
                <td>${ev.capacity}</td>
                <td>₱ ${formatCurrency(ev.revenue)}</td>
            </tr>
        `).join('');
    }

    const recent = document.getElementById('recentActivity');
    if (recent) {
        recent.innerHTML = sampleActivity.map(a => `
            <div class="mb-3 activity-item">
                <div class="d-flex justify-content-between">
                    <div class="fw-semibold">${a.user}</div>
                    <div class="text-muted small">${a.at}</div>
                </div>
                <div class="text-muted small">Reserved ${a.seats} seat(s) — ${a.event}</div>
            </div>
        `).join('');
    }

    const totalRevenue = sampleTopEvents.reduce((s,e) => s + e.revenue, 0);
    const totalTickets = sampleTopEvents.reduce((s,e) => s + e.tickets, 0);
    const trEl = document.getElementById('totalRevenue');
    const tsEl = document.getElementById('ticketsSold');
    const aeEl = document.getElementById('activeEvents');
    const nuEl = document.getElementById('newUsers');

    if (trEl) trEl.textContent = formatCurrency(totalRevenue);
    if (tsEl) tsEl.textContent = totalTickets;
    if (aeEl) aeEl.textContent = sampleTopEvents.length;
    if (nuEl) nuEl.textContent = 12; // placeholder
}

// Small behavior: toggling sidebar for small screens
function initSidebarToggle() {
    const toggleButton = document.getElementById('adminSidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (!toggleButton || !sidebar) return;
    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('d-none');
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    populateDashboard();
    initSidebarToggle();
});
