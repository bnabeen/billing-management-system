<?php
require_once '../config/db.php';

function executeQuery($conn, $sql, $msg) {
    if (mysqli_query($conn, $sql)) {
        echo "[SUCCESS] $msg<br>";
    } else {
        echo "[ERROR] $msg: " . mysqli_error($conn) . "<br>";
    }
}

// 1. Udharo Customers
$sql = "CREATE TABLE IF NOT EXISTS udharo_customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    total_debt DECIMAL(10, 2) DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Created udharo_customers table");

// 2. Udharo Transactions
$sql = "CREATE TABLE IF NOT EXISTS udharo_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    type ENUM('CREDIT', 'PAYMENT') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES udharo_customers(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Created udharo_transactions table");

echo "Udharo migration completed.";
?>
