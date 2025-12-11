<?php
// Simple script to install the database tables
// Run this file in your browser: http://localhost/bms/public/install.php

$host = "localhost";
$user = "root";
$pass = "";

echo "<h2>Database Installer</h2>";

// 1. Connect to MySQL (without specifying database yet)
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. Read the SQL file
$sqlFile = '../database/bms.sql';
if (!file_exists($sqlFile)) {
    die("Error: database/bms.sql not found!");
}

$sql = file_get_contents($sqlFile);

// 3. Execute queries
if (mysqli_multi_query($conn, $sql)) {
    do {
        // Consume results to clear the buffer for the next query
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    
    echo "<p style='color:green'>Success! Database and tables have been created.</p>";
    echo "<p>Please <a href='signup.php'>Click here to Signup</a> and create your first user.</p>";
} else {
    echo "<p style='color:red'>Error executing SQL: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>
