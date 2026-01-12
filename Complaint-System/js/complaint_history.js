/**
 * Complaint History Page - JavaScript
 * Handles display and filtering of resolved complaints
 */

// ============================================
// GLOBAL VARIABLES
// ============================================
let resolvedComplaints = [];
let currentFilters = {
    search_name: '',
    type: 'All Types',
    date: '',
    month: 'All Months'
};

// ============================================
// DOM ELEMENTS
// ============================================
const searchInput = document.getElementById('searchName');
const typeFilter = document.getElementById('typeFilter');
const dateFilter = document.getElementById('dateFilter');
const monthFilter = document.getElementById('monthFilter');
const clearFiltersBtn = document.querySelector('.clear-filters-btn');
const resolvedCardsContainer = document.getElementById('resolvedCards');
const resolvedCount = document.querySelector('.column-count');

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    loadResolvedComplaints();
    setupEventListeners();
    populateMonthFilter();
});

// ============================================
// EVENT LISTENERS
// ============================================
function setupEventListeners() {
    // Search input with debounce
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search_name = searchInput.value;
            loadResolvedComplaints();
        }, 500);
    });
    
    // Filter changes
    typeFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);
    monthFilter.addEventListener('change', applyFilters);
    
    // Clear filters
    clearFiltersBtn.addEventListener('click', clearFilters);
}

// ============================================
// LOAD RESOLVED COMPLAINTS
// ============================================
async function loadResolvedComplaints() {
    try {
        showLoading();
        
        // Add status filter for resolved complaints only
        const params = new URLSearchParams({
            ...currentFilters,
            status: 'Resolved'
        });
        
        const response = await fetch(`backend/api/get_complaints.php?${params}`);
        const result = await response.json();
        
        if (result.success) {
            resolvedComplaints = result.data.grouped.Resolved || [];
            displayResolvedComplaints(resolvedComplaints);
            updateCount(resolvedComplaints.length);
        } else {
            showError('Failed to load complaints: ' + result.message);
        }
    } catch (error) {
        showError('Error loading complaints: ' + error.message);
    } finally {
        hideLoading();
    }
}

// ============================================
// DISPLAY RESOLVED COMPLAINTS
// ============================================
function displayResolvedComplaints(complaints) {
    resolvedCardsContainer.innerHTML = '';
    
    if (!complaints || complaints.length === 0) {
        resolvedCardsContainer.innerHTML = `
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                <p style="font-size: 18px; margin-bottom: 10px;">📋 No resolved complaints found</p>
                <p style="font-size: 14px;">Try adjusting your filters or search criteria</p>
            </div>
        `;
        return;
    }
    
    complaints.forEach(complaint => {
        const card = createComplaintCard(complaint);
        resolvedCardsContainer.appendChild(card);
    });
}

// ============================================
// CREATE COMPLAINT CARD
// ============================================
function createComplaintCard(complaint) {
    const card = document.createElement('div');
    card.className = 'complaint-card';
    card.dataset.complaintId = complaint.ComplaintID;
    
    let cardHTML = `
        <div class="card-header">
            <div class="card-id">${complaint.ComplaintCode}</div>
            <button class="export-pdf-btn" onclick="exportToPDF(${complaint.ComplaintID})">
                <span class="material-symbols-rounded">download</span>
                Export PDF
            </button>
        </div>
        
        <div class="card-info">
            <span>🏠 Room ${complaint.RoomNumber}</span>
            <span>👤 ${complaint.FullName}</span>
        </div>
        
        <div class="type-badge">${complaint.ComplaintType}</div>
        
        <div class="card-description">${complaint.Description}</div>
        
        <div class="card-date">📅 Filed: ${complaint.CreatedAt}</div>
        
        <div class="last-update">✅ Resolved: ${complaint.ResolvedAt || 'N/A'}</div>
    `;
    
    // Add record logs if available
    if (complaint.RecordLogs && complaint.RecordLogs.length > 0) {
        cardHTML += `
            <div class="records-title">Resolution History</div>
            <div class="record-logs-container">
        `;
        
        complaint.RecordLogs.forEach(log => {
            cardHTML += `
                <div class="record-log">
                    <span class="log-timestamp">${log.LogTimestamp}:</span>
                    <span class="log-message">${log.LogMessage}</span>
                </div>
            `;
        });
        
        cardHTML += `</div>`;
    }
    
    card.innerHTML = cardHTML;
    return card;
}

// ============================================
// EXPORT TO PDF
// ============================================
function exportToPDF(complaintId) {
    // Open PDF export in new tab
    const url = `backend/api/export_pdf.php?complaint_id=${complaintId}`;
    window.open(url, '_blank');
}

// ============================================
// UPDATE COUNT
// ============================================
function updateCount(count) {
    resolvedCount.textContent = count;
}

// ============================================
// FILTER FUNCTIONS
// ============================================
function applyFilters() {
    currentFilters = {
        search_name: searchInput.value,
        type: typeFilter.value,
        date: dateFilter.value,
        month: monthFilter.value
    };
    
    loadResolvedComplaints();
}

function clearFilters() {
    searchInput.value = '';
    typeFilter.value = 'All Types';
    dateFilter.value = '';
    monthFilter.value = 'All Months';
    
    currentFilters = {
        search_name: '',
        type: 'All Types',
        date: '',
        month: 'All Months'
    };
    
    loadResolvedComplaints();
}

// ============================================
// POPULATE MONTH FILTER
// ============================================
function populateMonthFilter() {
    const months = [
        'January 2025', 'February 2025', 'March 2025', 'April 2025',
        'May 2025', 'June 2025', 'July 2025', 'August 2025',
        'September 2025', 'October 2025', 'November 2025', 'December 2025'
    ];
    
    months.forEach(month => {
        const option = document.createElement('option');
        option.value = month;
        option.textContent = month;
        monthFilter.appendChild(option);
    });
}

// ============================================
// UTILITY FUNCTIONS
// ============================================
function showError(message) {
    console.error(message);
    alert(message); // Replace with better notification
}

function showLoading() {
    resolvedCardsContainer.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <p>Loading complaints...</p>
        </div>
    `;
}

function hideLoading() {
    // Loading is removed when content is displayed
}