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

$name = trim($input['name']);
$email = trim($input['email']);
$phone = trim($input['phone']);
$userId = $_SESSION['user_id'];

// Validate inputs
if (empty($name) || empty($email) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Validate name
if (strlen($name) < 2 || strlen($name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Name must be between 2 and 100 characters']);
    exit();
}

if (!preg_match("/^[a-zA-Z\s'-]+$/", $name)) {
    echo json_encode(['success' => false, 'message' => 'Name contains invalid characters']);
    exit();
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit();
}

// Validate phone (11 digits)
if (!preg_match('/^\d{11}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Phone number must be exactly 11 digits']);
    exit();
}

// Check if email is already used by another user
$checkEmailQuery = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
$params = array($email, $userId);
$stmt = sqlsrv_query($conn, $checkEmailQuery, $params);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$existingUser = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
sqlsrv_free_stmt($stmt);

if ($existingUser) {
    echo json_encode(['success' => false, 'message' => 'Email is already in use by another account']);
    exit();
}

// Update user profile
$updateQuery = "UPDATE users 
                SET full_name = ?, 
                    email = ?, 
                    phone_number = ?, 
                    updated_at = GETDATE() 
                WHERE user_id = ?";

$params = array($name, $email, $phone, $userId);
$stmt = sqlsrv_query($conn, $updateQuery, $params);

if ($stmt === false) {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to update profile'
    ]);
    exit();
}

// Update session variables
$_SESSION['username'] = $name;
$_SESSION['email'] = $email;

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode([
    'success' => true, 
    'message' => 'Profile updated successfully'
]);
?>