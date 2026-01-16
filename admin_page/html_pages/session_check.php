<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: ../../login.php');
    exit();
}

// Check if user is admin (case-insensitive)
$userType = strtolower(trim($_SESSION['user_type']));
if ($userType !== 'admin') {
    header('Location: ../../user_front.php'); 
    exit();
}
?>