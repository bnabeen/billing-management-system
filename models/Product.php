<?php
class Product {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all products
     */
    public function getAll() {
        $sql = "SELECT * FROM products ORDER BY name ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get product by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get low stock products
     */
    public function getLowStock($threshold = 10) {
        $sql = "SELECT * FROM products WHERE stock <= ? ORDER BY stock ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Search products by name or category
     */
    public function search($query) {
        $searchTerm = "%{$query}%";
        $sql = "SELECT * FROM products WHERE name LIKE ? OR category LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Create new product
     */
    public function create($name, $category, $price, $stock, $alert_stock = 5, $barcode = null) {
        $sql = "INSERT INTO products (name, category, price, stock, alert_stock, barcode) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }
        
        $stmt->bind_param("ssdiis", $name, $category, $price, $stock, $alert_stock, $barcode);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id, 'message' => 'Product created successfully'];
        } else {
            return ['success' => false, 'message' => 'Execute failed: ' . $stmt->error];
        }
    }
    
    /**
     * Update product
     */
    public function update($id, $name, $category, $price, $stock, $alert_stock = 5, $barcode = null) {
        $sql = "UPDATE products SET name=?, category=?, price=?, stock=?, alert_stock=?, barcode=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }
        
        $stmt->bind_param("ssdiisi", $name, $category, $price, $stock, $alert_stock, $barcode, $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Product updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Execute failed: ' . $stmt->error];
        }
    }
    
    /**
     * Delete product
     */
    public function delete($id) {
        $sql = "DELETE FROM products WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }
        
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Product deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Execute failed: ' . $stmt->error];
        }
    }
    
    /**
     * Get total products count
     */
    public function getCount() {
        $sql = "SELECT COUNT(*) as count FROM products";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
