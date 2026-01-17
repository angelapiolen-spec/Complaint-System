// complaints.js - Shared functions for all complaint-related pages

/**
 * Delete complaint function
 * @param {number} id - Complaint ID
 * @param {string} redirectPage - Optional page to redirect to after deletion
 */
function deleteComplaint(id, redirectPage = '') {
    if (confirm('Are you sure you want to delete this complaint?')) {
        let url = 'delete_complaint.php?id=' + id;
        if (redirectPage) {
            url += '&redirect=' + redirectPage;
        }
        window.location.href = url;
    }
}

/**
 * Export PDF function - Opens PDF in new tab
 * @param {number} complaintId - Complaint ID
 */
function exportToPDF(complaintId) {
    window.open('export_pdf.php?id=' + complaintId, '_blank');
}

/**
 * Common validation for complaint forms
 * @param {string} complaintType - Type of complaint
 * @param {string} description - Complaint description
 * @param {string} urgency - Urgency level
 * @returns {boolean} True if valid, false otherwise
 */
function validateComplaintForm(complaintType, description, urgency) {
    if (!complaintType) {
        alert('Please select a complaint type.');
        return false;
    }
    
    if (!description.trim()) {
        alert('Please enter a description of your complaint.');
        return false;
    }
    
    if (description.trim().length < 10) {
        alert('Please provide a more detailed description (minimum 10 characters).');
        return false;
    }
    
    if (!urgency) {
        alert('Please select an urgency level.');
        return false;
    }
    
    return true;
}

/**
 * Format date in readable format
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Apply filters to complaint history
 */
function applyFilters() {
    const type = document.getElementById('filter-type')?.value || '';
    const date = document.getElementById('filter-date')?.value || '';
    const month = document.getElementById('monthFilter')?.value || 'all';
    
    let url = window.location.pathname + '?';
    const params = new URLSearchParams(window.location.search);
    
    // Update parameters
    if (type) params.set('type', type);
    else params.delete('type');
    
    if (date) params.set('date', date);
    else params.delete('date');
    
    if (month !== 'all') params.set('month', month);
    else params.delete('month');
    
    // Navigate to filtered URL
    window.location.href = url + params.toString();
}

/**
 * Clear all filters
 */
function clearFilters() {
    const url = window.location.pathname;
    window.location.href = url;
}

/**
 * Toggle complaint details
 * @param {string} complaintId - Complaint ID
 */
function toggleComplaintDetails(complaintId) {
    const details = document.getElementById('details-' + complaintId);
    if (details) {
        details.style.display = details.style.display === 'none' ? 'block' : 'none';
    }
}

/**
 * Update complaint status (for admin use)
 * @param {number} complaintId - Complaint ID
 * @param {string} status - New status
 */
function updateComplaintStatus(complaintId, status) {
    if (confirm('Are you sure you want to update the complaint status to ' + status + '?')) {
        window.location.href = 'update_status.php?id=' + complaintId + '&status=' + status;
    }
}

/**
 * Show loading state on buttons
 * @param {HTMLElement} button - Button element
 * @param {string} loadingText - Text to show while loading
 */
function setButtonLoading(button, loadingText = 'Processing...') {
    button.dataset.originalText = button.innerHTML;
    button.innerHTML = '<span class="loading-spinner"></span>' + loadingText;
    button.disabled = true;
}

/**
 * Reset button to original state
 * @param {HTMLElement} button - Button element
 */
function resetButton(button) {
    if (button.dataset.originalText) {
        button.innerHTML = button.dataset.originalText;
        button.disabled = false;
    }
}

// Event listeners for complaint cards
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to complaint cards
    const cards = document.querySelectorAll('.complaint-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 1px 4px rgba(0,0,0,0.05)';
        });
    });
    
    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    });
});