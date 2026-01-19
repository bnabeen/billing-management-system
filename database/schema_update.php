<?php
require_once '../config/db.php';

function executeQuery($conn, $sql, $msg) {
    if (mysqli_query($conn, $sql)) {
        echo "[SUCCESS] $msg<br>";
    } else {
        echo "[ERROR] $msg: " . mysqli_error($conn) . "<br>";
    }
}

function columnExists($conn, $table, $column) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return mysqli_num_rows($result) > 0;
}

// 1. Add purchase_price to products
if (!columnExists($conn, 'products', 'purchase_price')) {
    $sql = "ALTER TABLE products ADD COLUMN purchase_price DECIMAL(10, 2) DEFAULT 0.00 AFTER price";
    executeQuery($conn, $sql, "Added purchase_price to products");
} else {
    echo "[INFO] purchase_price already exists in products<br>";
}

// 2. Updated products category default
$sql = "ALTER TABLE products MODIFY COLUMN category VARCHAR(50) DEFAULT 'General'";
executeQuery($conn, $sql, "Updated products category default");

// 3. User Permissions Table
$sql = "CREATE TABLE IF NOT EXISTS user_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    feature_name VARCHAR(50) NOT NULL,
    can_access TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_permission (user_id, feature_name)
)";
executeQuery($conn, $sql, "Created user_permissions table");

// 4. Suppliers Table
$sql = "CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    total_balance DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Created suppliers table");

// 5. Supplier Transactions Table
$sql = "CREATE TABLE IF NOT EXISTS supplier_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    type ENUM('PURCHASE', 'PAYMENT', 'RETURN') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Created supplier_transactions table");

// 6. Update Udharo Transactions
if (!columnExists($conn, 'udharo_transactions', 'sale_id')) {
    $sql = "ALTER TABLE udharo_transactions ADD COLUMN sale_id INT NULL AFTER customer_id";
    executeQuery($conn, $sql, "Added sale_id to udharo_transactions");
}

if (!columnExists($conn, 'udharo_transactions', 'is_cash')) {
    $sql = "ALTER TABLE udharo_transactions ADD COLUMN is_cash TINYINT(1) DEFAULT 0 AFTER type";
    executeQuery($conn, $sql, "Added is_cash to udharo_transactions");
}

// 7. Add purchase_price to sale_items for historical profit tracking
if (!columnExists($conn, 'sale_items', 'purchase_price')) {
    $sql = "ALTER TABLE sale_items ADD COLUMN purchase_price DECIMAL(10, 2) DEFAULT 0.00 AFTER price";
    executeQuery($conn, $sql, "Added purchase_price to sale_items");
}

// 8. Add last_stock_update to products
if (!columnExists($conn, 'products', 'last_stock_update')) {
    $sql = "ALTER TABLE products ADD COLUMN last_stock_update TIMESTAMP NULL DEFAULT NULL";
    executeQuery($conn, $sql, "Added last_stock_update to products");
}

// 9. Add payment_method to sales
if (!columnExists($conn, 'sales', 'payment_method')) {
    $sql = "ALTER TABLE sales ADD COLUMN payment_method VARCHAR(20) DEFAULT 'cash' AFTER customer_phone";
    executeQuery($conn, $sql, "Added payment_method to sales");
}

// 10. Add discount to sales
if (!columnExists($conn, 'sales', 'discount')) {
    $sql = "ALTER TABLE sales ADD COLUMN discount DECIMAL(10, 2) DEFAULT 0.00 AFTER total_amount";
    executeQuery($conn, $sql, "Added discount to sales");
}

echo "Database schema updates completed.";
?>
