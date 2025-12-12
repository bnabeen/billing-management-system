<?php
require_once '../config/db.php';

function executeQuery($conn, $sql, $msg) {
    if (mysqli_query($conn, $sql)) {
        echo "[SUCCESS] $msg<br>";
    } else {
        echo "[ERROR] $msg: " . mysqli_error($conn) . "<br>";
    }
}

// 1. Create Businesses Table
$sql = "CREATE TABLE IF NOT EXISTS businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql, "Created businesses table");

// 2. Alter Users Table
// Check if business_id exists
$check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'business_id'");
if (mysqli_num_rows($check) == 0) {
    // Add business_id
    executeQuery($conn, "ALTER TABLE users ADD COLUMN business_id INT NOT NULL AFTER id", "Added business_id to users");
    
    // Create a dummy business for existing users so we don't break FK
    executeQuery($conn, "INSERT IGNORE INTO businesses (username, name) VALUES ('default', 'Default Business')", "Created default business");
    
    // Update existing users to belong to default business
    executeQuery($conn, "UPDATE users SET business_id = (SELECT id FROM businesses WHERE username='default')", "Updated existing users");

    // Add FK
    executeQuery($conn, "ALTER TABLE users ADD CONSTRAINT fk_user_business FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE", "Added FK to users");
}

// Update role column if needed
executeQuery($conn, "ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'staff') DEFAULT 'staff'", "Updated role enum");

// Add status column
$check_status = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'status'");
if (mysqli_num_rows($check_status) == 0) {
    executeQuery($conn, "ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active'", "Added status column");
}

// Drop old unique index on username if it exists and add composite unique
// This is tricky in MySQL without knowing index name. Usually 'username'.
// We'll try to drop it.
mysqli_query($conn, "DROP INDEX username ON users");
executeQuery($conn, "ALTER TABLE users ADD UNIQUE KEY unique_user_per_business (business_id, username)", "Added unique composite key (business_id, username)");


// 3. Alter Products Table
$check_prod = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'business_id'");
if (mysqli_num_rows($check_prod) == 0) {
    executeQuery($conn, "ALTER TABLE products ADD COLUMN business_id INT NOT NULL AFTER id", "Added business_id to products");
    // Assign to default business
    executeQuery($conn, "UPDATE products SET business_id = (SELECT id FROM businesses WHERE username='default')", "Updated existing products");
    executeQuery($conn, "ALTER TABLE products ADD CONSTRAINT fk_product_business FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE", "Added FK to products");
}

// 4. Create Sales Table
$sql = "CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    total_amount DECIMAL(10, 2) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Created sales table");

// 5. Create Sales Items Table
$sql = "CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
)";
executeQuery($conn, $sql, "Created sale_items table");

echo "Migration completed.";
?>
