<?php
// ========================================
// DELETE COMPLAINT
// File: delete_complaint.php
// ========================================

require_once __DIR__ . '/../../db_config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get complaint ID
    $complaintId = trim($_POST['complaint_id']);
    
    // Validate input
    if (empty($complaintId)) {
        header("Location: manage_complaints.php?error=no_id");
        exit();
    }
    
    // Check if complaint exists and is Resolved
    $checkQuery = "SELECT complaint_id, status FROM complaints WHERE complaint_id = ?";
    $complaint = getSingleRow($conn, $checkQuery, array($complaintId));
    
    if (!$complaint) {
        header("Location: manage_complaints.php?error=complaint_not_found");
        exit();
    }
    
    // Only allow deletion of Resolved complaints
    if ($complaint['status'] != 'Resolved') {
        header("Location: manage_complaints.php?error=not_resolved");
        exit();
    }
    
    // Delete complaint (records will be auto-deleted due to CASCADE)
    $deleteQuery = "DELETE FROM complaints WHERE complaint_id = ?";
    $deleteStmt = sqlsrv_query($conn, $deleteQuery, array($complaintId));
    
    if ($deleteStmt === false) {
        header("Location: manage_complaints.php?error=delete_failed");
        exit();
    }
    
    // Success
    sqlsrv_free_stmt($deleteStmt);
    closeConnection($conn);
    
    header("Location: manage_complaints.php?success=deleted");
    exit();
    
} else {
    // If not POST request, redirect
    header("Location: manage_complaints.php");
    exit();
}
?>