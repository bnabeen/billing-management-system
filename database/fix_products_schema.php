<?php
require_once '../config/db.php';

echo "Checking and fixing products table schema...<br><br>";

// Check if barcode column exists
$check = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'barcode'");
if (mysqli_num_rows($check) == 0) {
    echo "Adding barcode column...<br>";
    $sql = "ALTER TABLE products ADD COLUMN barcode VARCHAR(100) AFTER alert_stock";
    if (mysqli_query($conn, $sql)) {
        echo "[SUCCESS] Added barcode column<br>";
    } else {
        echo "[ERROR] Failed to add barcode: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "[INFO] Barcode column already exists<br>";
}

echo "<br>Migration completed.";
?>
