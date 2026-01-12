/**
 * Complaint Desk - User Frontend JavaScript
 * Handles all user interactions and API calls
 */

// ============================================
// GLOBAL VARIABLES
// ============================================
let allComplaints = [];
let currentFilters = {
    status: '',
    type: 'All Types',
    date: '',
    month: 'All Months'
};

// ============================================
// DOM ELEMENTS
// ============================================
const modal = document.getElementById('complaintModal');
const addComplaintBtn = document.querySelector('.add-complaint-btn');
const closeModalBtn = document.querySelector('.btn-secondary');
const complaintForm = document.getElementById('complaintForm');
const validateMessage = document.querySelector('.validate-message');

// Filter elements
const typeFilter = document.getElementById('typeFilter');
const dateFilter = document.getElementById('dateFilter');
const monthFilter = document.getElementById('monthFilter');
const clearFiltersBtn = document.querySelector('.clear-filters-btn');

// ============================================
// INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    loadComplaints();
    setupEventListeners();
    populateMonthFilter();
});

// ============================================
// EVENT LISTENERS
// ============================================
function setupEventListeners() {
    // Modal controls
    addComplaintBtn.addEventListener('click', () => openModal());
    closeModalBtn.addEventListener('click', () => closeModal());
    
    // Click outside modal to close
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Form submission
    complaintForm.addEventListener('submit', handleComplaintSubmit);
    
    // Filter controls
    typeFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);
    monthFilter.addEventListener('change', applyFilters);
    clearFiltersBtn.addEventListener('click', clearFilters);
}

// ============================================
// MODAL FUNCTIONS
// ============================================
function openModal() {
    modal.classList.add('active');
    validateMessage.classList.remove('active');
    complaintForm.reset();
}

function closeModal() {
    modal.classList.remove('active');
    validateMessage.classList.remove('active');
}

// ============================================
// LOAD COMPLAINTS
// ============================================
async function loadComplaints() {
    try {
        showLoading();
        
        const params = new URLSearchParams(currentFilters);
        const response = await fetch(`backend/api/get_complaints.php?${params}`);
        const result = await response.json();
        
        if (result.success) {
            allComplaints = result.data.all;
            displayComplaints(result.data.grouped);
            updateCounts(result.data.counts);
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
// DISPLAY COMPLAINTS
// ============================================
function displayComplaints(grouped) {
    // Clear all columns
    document.getElementById('pendingCards').innerHTML = '';
    document.getElementById('progressCards').innerHTML = '';
    document.getElementById('resolvedCards').innerHTML = '';
    
    // Display pending complaints
    if (grouped.Pending && grouped.Pending.length > 0) {
        grouped.Pending.forEach(complaint => {
            document.getElementById('pendingCards').appendChild(
                createComplaintCard(complaint, 'pending')
            );
        });
    } else {
        document.getElementById('pendingCards').innerHTML = 
            '<p style="text-align: center; color: #9ca3af; padding: 20px;">No pending complaints</p>';
    }
    
    // Display in progress complaints
    if (grouped['In Progress'] && grouped['In Progress'].length > 0) {
        grouped['In Progress'].forEach(complaint => {
            document.getElementById('progressCards').appendChild(
                createComplaintCard(complaint, 'progress')
            );
        });
    } else {
        document.getElementById('progressCards').innerHTML = 
            '<p style="text-align: center; color: #9ca3af; padding: 20px;">No complaints in progress</p>';
    }
    
    // Display resolved complaints
    if (grouped.Resolved && grouped.Resolved.length > 0) {
        grouped.Resolved.forEach(complaint => {
            document.getElementById('resolvedCards').appendChild(
                createComplaintCard(complaint, 'resolved')
            );
        });
    } else {
        document.getElementById('resolvedCards').innerHTML = 
            '<p style="text-align: center; color: #9ca3af; padding: 20px;">No resolved complaints</p>';
    }
}

// ============================================
// CREATE COMPLAINT CARD
// ============================================
function createComplaintCard(complaint, columnType) {
    const card = document.createElement('div');
    card.className = 'complaint-card';
    card.dataset.complaintId = complaint.ComplaintID;
    
    let cardHTML = `
        <div class="card-id">${complaint.ComplaintCode}</div>
        <div class="card-info">
            <span>🏠 Room ${complaint.RoomNumber}</span>
            <span>👤 ${complaint.FullName}</span>
        </div>
        <div class="type-badge">${complaint.ComplaintType}</div>
        <div class="card-description">${complaint.Description}</div>
        <div class="card-date">📅 ${complaint.CreatedAt}</div>
    `;
    
    // Add update info if not pending
    if (columnType !== 'pending' && complaint.UpdatedAt) {
        cardHTML += `<div class="last-update">📝 Updated: ${complaint.UpdatedAt}</div>`;
    }
    
    // Add resolved info
    if (columnType === 'resolved' && complaint.ResolvedAt) {
        cardHTML += `<div class="last-update">✅ Resolved: ${complaint.ResolvedAt}</div>`;
    }
    
    // Add record logs if available
    if (complaint.RecordLogs && complaint.RecordLogs.length > 0) {
        cardHTML += `
            <div class="records-title">Records</div>
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
    
    // Add delete button for resolved complaints
    if (columnType === 'resolved') {
        cardHTML += `
            <button class="delete-btn" onclick="deleteComplaint(${complaint.ComplaintID})">
                🗑️ Delete
            </button>
        `;
    }
    
    card.innerHTML = cardHTML;
    return card;
}

// ============================================
// UPDATE COUNTS
// ============================================
function updateCounts(counts) {
    document.querySelector('#pendingColumn .column-count').textContent = counts.pending;
    document.querySelector('#progressColumn .column-count').textContent = counts.in_progress;
    document.querySelector('#resolvedColumn .column-count').textContent = counts.resolved;
}

// ============================================
// SUBMIT COMPLAINT
// ============================================
async function handleComplaintSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(complaintForm);
    
    // Basic validation
    const type = formData.get('complaint_type');
    const description = formData.get('description');
    
    if (!type || type === 'Select Type') {
        showValidationError('Please select a complaint type');
        return;
    }
    
    if (!description || description.trim().length < 10) {
        showValidationError('Description must be at least 10 characters');
        return;
    }
    
    try {
        const response = await fetch('backend/api/create_complaint.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeModal();
            showSuccess('Complaint submitted successfully!');
            loadComplaints(); // Reload complaints
        } else {
            showValidationError(result.message || 'Failed to submit complaint');
        }
    } catch (error) {
        showValidationError('Error submitting complaint: ' + error.message);
    }
}

// ============================================
// DELETE COMPLAINT
// ============================================
async function deleteComplaint(complaintId) {
    if (!confirm('Are you sure you want to delete this complaint?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('complaint_id', complaintId);
        
        const response = await fetch('backend/api/delete_complaint.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Complaint deleted successfully!');
            loadComplaints(); // Reload complaints
        } else {
            showError(result.message || 'Failed to delete complaint');
        }
    } catch (error) {
        showError('Error deleting complaint: ' + error.message);
    }
}

// ============================================
// FILTER FUNCTIONS
// ============================================
function applyFilters() {
    currentFilters = {
        status: '',
        type: typeFilter.value,
        date: dateFilter.value,
        month: monthFilter.value
    };
    
    loadComplaints();
}

function clearFilters() {
    typeFilter.value = 'All Types';
    dateFilter.value = '';
    monthFilter.value = 'All Months';
    
    currentFilters = {
        status: '',
        type: 'All Types',
        date: '',
        month: 'All Months'
    };
    
    loadComplaints();
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
function showValidationError(message) {
    validateMessage.textContent = message;
    validateMessage.classList.add('active');
}

function showSuccess(message) {
    alert(message); // You can replace this with a better notification system
}

function showError(message) {
    alert(message); // You can replace this with a better notification system
}

function showLoading() {
    // Add loading indicator if needed
    console.log('Loading...');
}

function hideLoading() {
    // Remove loading indicator if needed
    console.log('Loading complete');
}