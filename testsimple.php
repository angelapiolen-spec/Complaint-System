<?php
echo "PHP Version: " . phpversion() . "<br>";
echo "Loaded Extensions:<br>";

$extensions = get_loaded_extensions();
if (in_array('sqlsrv', $extensions)) {
    echo "✅ sqlsrv is loaded<br>";
} else {
    echo "❌ sqlsrv is NOT loaded<br>";
}

if (in_array('pdo_sqlsrv', $extensions)) {
    echo "✅ pdo_sqlsrv is loaded<br>";
} else {
    echo "❌ pdo_sqlsrv is NOT loaded<br>";
}

echo "<br>===== CONNECTION TEST =====<br><br>";

// Test different connection methods
$tests = array(
    "localhost\\SQLEXPRESS",
    "(local)\\SQLEXPRESS",
    ".\\SQLEXPRESS"
);

foreach ($tests as $server) {
    echo "Testing: $server<br>";
    $conn = @sqlsrv_connect($server, array("Database" => "complaint_db"));
    
    if ($conn) {
        echo "✅ <strong>SUCCESS!</strong><br><br>";
        sqlsrv_close($conn);
        break;
    } else {
        echo "❌ Failed<br>";
        $errors = sqlsrv_errors();
        if ($errors) {
            echo "Error: " . $errors[0]['message'] . "<br><br>";
        }
    }
}
?>