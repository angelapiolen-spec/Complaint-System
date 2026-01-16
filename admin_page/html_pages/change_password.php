<?php
session_start();
require_once '../../db_config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$currentPassword = $input['current_password'];
$newPassword = $input['new_password'];
$userId = $_SESSION['user_id'];

// Validate inputs
if (empty($currentPassword) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Validate new password strength
if (strlen($newPassword) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
    exit();
}

if (!preg_match('/[A-Z]/', $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one uppercase letter']);
    exit();
}

if (!preg_match('/[a-z]/', $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one lowercase letter']);
    exit();
}

if (!preg_match('/[0-9]/', $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one number']);
    exit();
}

// Get current password from database
$query = "SELECT password FROM users WHERE user_id = ?";
$params = array($userId);
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
sqlsrv_free_stmt($stmt);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

// Verify current password using password_verify (since your login uses it)
if (!password_verify($currentPassword, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
    exit();
}

// Check if new password is same as current
if ($currentPassword === $newPassword) {
    echo json_encode(['success' => false, 'message' => 'New password must be different from current password']);
    exit();
}

// Hash the new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password
$updateQuery = "UPDATE users SET password = ?, updated_at = GETDATE() WHERE user_id = ?";
$params = array($hashedPassword, $userId);
$stmt = sqlsrv_query($conn, $updateQuery, $params);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to update password']);
    exit();
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode([
    'success' => true, 
    'message' => 'Password changed successfully'
]);
?>