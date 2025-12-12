<?php
require_once '../config/db.php';

echo "Updating users table status column...<br>";

$sql = "ALTER TABLE users MODIFY status ENUM('active', 'inactive', 'pending') DEFAULT 'active'";

if (mysqli_query($conn, $sql)) {
    echo "[SUCCESS] Updated status column to include 'pending'<br>";
} else {
    echo "[ERROR] Failed to update: " . mysqli_error($conn) . "<br>";
}

echo "Migration completed.";
?>
