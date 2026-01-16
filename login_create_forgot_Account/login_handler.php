<?php
// ---------------- HIDE ODBC INFO MESSAGES ----------------
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ---------------- START SESSION ----------------
session_start();
require "../db_config.php";  // Adjust path kung nasaan ang db_config.php

// ---------------- CHECK REQUEST METHOD ----------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields'); window.location.href='login.php';</script>";
        exit;
    }

    // ---------------- QUERY USER ----------------
    $sql = "SELECT user_id, full_name, email, password, user_type 
            FROM users 
            WHERE email = ?";
    $params = array($email);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("Database error: " . print_r(sqlsrv_errors(), true));
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user) {
        // ---------------- VERIFY PASSWORD ----------------
        if (password_verify($password, $user['password'])) {
            // ---------------- LOGIN SUCCESS ----------------
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];

            // ---------------- REDIRECT BASED ON ROLE ----------------
            if (strtolower($user['user_type']) === 'admin') {
                echo "<script>window.location.href='/Complaint-System/admin_page/html_pages/dashboard.php';</script>";
            } else {
                echo "<script>window.location.href='user_front.php';</script>";
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location.href='login.php';</script>";
    }

    // ---------------- FREE STATEMENT ----------------
    sqlsrv_free_stmt($stmt);
}

// ---------------- CLOSE CONNECTION ----------------
sqlsrv_close($conn);
?>