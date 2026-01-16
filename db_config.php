<?php
// ========================================
// UNIFIED DATABASE CONNECTION FILE
// File: db_config.php
// ========================================

$serverName = "localhost\\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "complaint_db",
    "Driver" => "{ODBC Driver 18 for SQL Server}",
    "TrustServerCertificate" => "yes",
    "Encrypt" => "no",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("<div style='color: red; padding: 20px; background: #ffe6e6; border: 1px solid red; margin: 20px;'>
        <strong>Database Connection Failed!</strong><br>
        Error Details: " . print_r(sqlsrv_errors(), true) . "
    </div>");
}

// ========================================
// HELPER FUNCTIONS
// ========================================

// Function to safely close connection
function closeConnection($conn) {
    if ($conn) {
        sqlsrv_close($conn);
    }
}

// Function to execute query and return statement
function executeQuery($conn, $query, $params = array()) {
    $stmt = sqlsrv_query($conn, $query, $params);
    
    if ($stmt === false) {
        die("<div style='color: red; padding: 10px; background: #ffe6e6; border: 1px solid red; margin: 10px;'>
            <strong>Query Error:</strong><br>" . print_r(sqlsrv_errors(), true) . "
        </div>");
    }
    
    return $stmt;
}

// Function to get all rows from query
function getAllRows($conn, $query, $params = array()) {
    $stmt = executeQuery($conn, $query, $params);
    $results = array();
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $results[] = $row;
    }
    
    sqlsrv_free_stmt($stmt);
    return $results;
}

// Function to get single row
function getSingleRow($conn, $query, $params = array()) {
    $stmt = executeQuery($conn, $query, $params);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    return $row;
}

// Function to format datetime for display
function formatDate($date) {
    if ($date instanceof DateTime) {
        return $date->format('M d, Y h:i A');
    }
    if (is_string($date)) {
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('M d, Y h:i A', $timestamp);
        }
    }
    return $date;
}

?>