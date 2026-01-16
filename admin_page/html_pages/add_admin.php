<?php
// ========================================
// ADD ADMIN - Fixed Version
// File: add_admin.php
// ========================================

require_once __DIR__ . '/../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone_number']);
    $pass = $_POST['password'];

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($pass)) {
        header("Location: manage_users.php?error=empty_fields");
        exit();
    }

    // Check if email already exists
    $checkEmailQuery = "SELECT user_id FROM users WHERE email = ?";
    $existingUser = getSingleRow($conn, $checkEmailQuery, array($email));
    
    if ($existingUser) {
        header("Location: manage_users.php?error=email_exists");
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

    // Insert into users table with user_type = 'Admin' and room_number = 'admin'
    $sql = "INSERT INTO users (full_name, email, phone_number, password, user_type, room_number, created_at)
            VALUES (?, ?, ?, ?, 'Admin', 'admin', GETDATE())";
    
    $params = array($name, $email, $phone, $hashedPassword);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        header("Location: manage_users.php?error=insert_failed");
        exit();
    }

    sqlsrv_free_stmt($stmt);
    closeConnection($conn);

    // Success - redirect
    header("Location: manage_users.php?success=admin_added");
    exit();
    
} else {
    // If not POST request, redirect back
    header("Location: manage_users.php");
    exit();
}
?>