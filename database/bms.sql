-- Database schema for Kirana Store BMS
-- Initial Phase: Authentication & Product Management only

-- 1. Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS kirana_bms;

-- 2. Select the database to use
USE kirana_bms;

-- 3. Create Users Table
-- This stores login information for the staff/admin
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15), -- Added for signup validation
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Create Products Table
-- This stores the inventory details
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    alert_stock INT DEFAULT 5, -- Low stock threshold
    barcode VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 5. No default user inserted. Users will be created via Signup page.

-- 6. Insert some dummy products to start with
INSERT INTO products (name, category, price, stock, alert_stock, barcode) VALUES
('Rice (1kg)', 'Grains', 60.00, 100, 10, 'BAR001'),
('Sugar (1kg)', 'Groceries', 42.00, 80, 10, 'BAR002'),
('Cooking Oil (1L)', 'Oils', 150.00, 50, 5, 'BAR003'),
('Wheat Flour (1kg)', 'Grains', 45.00, 120, 15, 'BAR004'),
('Tea Powder (250g)', 'Beverages', 85.00, 60, 5, 'BAR005');
