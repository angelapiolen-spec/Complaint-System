<?php
// ========================================
// DELETE USER
// File: delete_user.php
// ========================================

require_once __DIR__ . '/../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get user ID
    $userId = trim($_POST['user_id']);
    
    // Validate input
    if (empty($userId)) {
        header("Location: manage_users.php?error=no_id");
        exit();
    }
    
    // Check if user exists and is NOT an Admin
    $checkQuery = "SELECT user_id, user_type FROM users WHERE user_id = ?";
    $user = getSingleRow($conn, $checkQuery, array($userId));
    
    if (!$user) {
        header("Location: manage_users.php?error=user_not_found");
        exit();
    }
    
    // Prevent deleting Admins
    if ($user['user_type'] == 'Admin') {
        header("Location: manage_users.php?error=cannot_delete_admin");
        exit();
    }
    
    // Delete user
    $deleteQuery = "DELETE FROM users WHERE user_id = ?";
    $deleteStmt = sqlsrv_query($conn, $deleteQuery, array($userId));
    
    if ($deleteStmt === false) {
        header("Location: manage_users.php?error=delete_failed");
        exit();
    }
    
    // Success
    sqlsrv_free_stmt($deleteStmt);
    closeConnection($conn);
    
    header("Location: manage_users.php?success=user_deleted");
    exit();
    
} else {
    header("Location: manage_users.php");
    exit();
}
?>