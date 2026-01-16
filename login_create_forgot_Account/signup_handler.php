<?php
// ---------------- DISPLAY ERRORS FOR DEBUGGING ----------------
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ---------------- START SESSION ----------------
session_start();
require "../db_config.php";

// ---------------- CHECK REQUEST METHOD ----------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $phone_number = trim($_POST["phone_number"]);
    $room_number = trim($_POST["room_number"]);

    // ---------------- VALIDATION ----------------
    if (empty($username) || empty($email) || empty($password) || empty($phone_number) || empty($room_number)) {
        echo "<script>alert('Please fill in all fields'); window.location.href='login.php';</script>";
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format'); window.location.href='login.php';</script>";
        exit;
    }

    // Check if phone number is 11 digits
    if (!preg_match('/^\d{11}$/', $phone_number)) {
        echo "<script>alert('Phone number must be exactly 11 digits'); window.location.href='login.php';</script>";
        exit;
    }

    // ---------------- HASH PASSWORD ----------------
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ---------------- CHECK IF EMAIL EXISTS ----------------
    $checkSql = "SELECT user_id FROM users WHERE email = ?";
    $checkParams = array($email);
    $checkStmt = sqlsrv_query($conn, $checkSql, $checkParams);

    if ($checkStmt === false) {
        die("Database error (email check): " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
        echo "<script>alert('Email already registered'); window.location.href='login.php';</script>";
        sqlsrv_free_stmt($checkStmt);
        sqlsrv_close($conn);
        exit;
    }
    sqlsrv_free_stmt($checkStmt);

    // ---------------- CHECK IF ROOM NUMBER EXISTS ----------------
    $checkRoomSql = "SELECT user_id FROM users WHERE room_number = ?";
    $checkRoomParams = array($room_number);
    $checkRoomStmt = sqlsrv_query($conn, $checkRoomSql, $checkRoomParams);

    if ($checkRoomStmt === false) {
        die("Database error (room check): " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_fetch_array($checkRoomStmt, SQLSRV_FETCH_ASSOC)) {
        echo "<script>alert('Room number already registered'); window.location.href='login.php';</script>";
        sqlsrv_free_stmt($checkRoomStmt);
        sqlsrv_close($conn);
        exit;
    }
    sqlsrv_free_stmt($checkRoomStmt);

    // ---------------- INSERT NEW USER ----------------
    $insertSql = "INSERT INTO users (full_name, email, password, phone_number, room_number, user_type) 
                  VALUES (?, ?, ?, ?, ?, 'user')";
    $insertParams = array($username, $email, $hashedPassword, $phone_number, $room_number);
    
    $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);

    if ($insertStmt === false) {
        die("Signup failed: " . print_r(sqlsrv_errors(), true));
    }

    // ---------------- SUCCESS - REDIRECT TO LOGIN ----------------
    echo "<script>alert('Account created successfully! Please login.'); window.location.href='login.php';</script>";
    
    // ---------------- FREE RESOURCES ----------------
    sqlsrv_free_stmt($insertStmt);
    sqlsrv_close($conn);
    exit;
}

// ---------------- CLOSE CONNECTION ----------------
sqlsrv_close($conn);
?>