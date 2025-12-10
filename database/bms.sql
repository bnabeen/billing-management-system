-- Database schema for Kirana Store BMS
-- Create database
CREATE DATABASE IF NOT EXISTS kirana_bms;
USE kirana_bms;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    barcode VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sales table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) DEFAULT 'Walk-in Customer',
    total DECIMAL(10, 2) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Sale items table
CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Udharo (Credit) table
CREATE TABLE IF NOT EXISTS udharo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    paid DECIMAL(10, 2) DEFAULT 0,
    description TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample products
INSERT INTO products (name, category, price, stock, barcode) VALUES
('Rice (1kg)', 'Grains', 60.00, 100, 'BAR001'),
('Sugar (1kg)', 'Groceries', 42.00, 80, 'BAR002'),
('Cooking Oil (1L)', 'Oils', 150.00, 50, 'BAR003'),
('Wheat Flour (1kg)', 'Grains', 45.00, 120, 'BAR004'),
('Tea Powder (250g)', 'Beverages', 85.00, 60, 'BAR005'),
('Salt (1kg)', 'Groceries', 20.00, 150, 'BAR006'),
('Milk (1L)', 'Dairy', 55.00, 40, 'BAR007'),
('Bread', 'Bakery', 35.00, 30, 'BAR008'),
('Eggs (12pcs)', 'Dairy', 72.00, 25, 'BAR009'),
('Biscuits', 'Snacks', 25.00, 90, 'BAR010');
