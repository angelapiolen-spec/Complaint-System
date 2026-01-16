<?php
// ========================================
// HANDLE COMPLAINT - UPDATE STATUS & ADD NOTES
// File: handle_complaint.php
// ========================================

require_once __DIR__ . '/../../db_config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data
    $complaintId = trim($_POST['complaint_id']);
    $newStatus = trim($_POST['status']);
    $notes = trim($_POST['notes']);
    
    // Validate inputs
    if (empty($complaintId) || empty($newStatus) || empty($notes)) {
        header("Location: manage_complaints.php?error=empty_fields");
        exit();
    }
    
    // Check if complaint exists
    $checkQuery = "SELECT complaint_id, status FROM complaints WHERE complaint_id = ?";
    $complaint = getSingleRow($conn, $checkQuery, array($complaintId));
    
    if (!$complaint) {
        header("Location: manage_complaints.php?error=complaint_not_found");
        exit();
    }
    
    // Prevent updating from Resolved back to other statuses
    if ($complaint['status'] == 'Resolved') {
        header("Location: manage_complaints.php?error=already_resolved");
        exit();
    }
    
    // Update complaint status
    $updateQuery = "UPDATE complaints SET status = ?, last_updated = GETDATE()";
    $params = array($newStatus);
    
    // If status is Resolved, set resolved_date
    if ($newStatus == 'Resolved') {
        $updateQuery .= ", resolved_date = GETDATE()";
    }
    
    $updateQuery .= " WHERE complaint_id = ?";
    $params[] = $complaintId;
    
    $updateStmt = sqlsrv_query($conn, $updateQuery, $params);
    
    if ($updateStmt === false) {
        header("Location: manage_complaints.php?error=update_failed");
        exit();
    }
    
    // Add record/log entry
    $logQuery = "INSERT INTO complaint_records (complaint_id, log_timestamp, log_message) VALUES (?, GETDATE(), ?)";
    $logMessage = "Status updated to '$newStatus'. Notes: $notes";
    $logStmt = sqlsrv_query($conn, $logQuery, array($complaintId, $logMessage));
    
    if ($logStmt === false) {
        header("Location: manage_complaints.php?error=log_failed");
        exit();
    }
    
    // Success - redirect back
    sqlsrv_free_stmt($updateStmt);
    sqlsrv_free_stmt($logStmt);
    closeConnection($conn);
    
    header("Location: manage_complaints.php?success=updated&id=$complaintId");
    exit();
    
} else {
    // If not POST request, redirect to manage complaints
    header("Location: manage_complaints.php");
    exit();
}
?>