<?php
class Product {
    private $conn;
    private $business_id;
    
    public function __construct($db, $business_id) {
        $this->conn = $db;
        $this->business_id = $business_id;
    }
    
    /**
     * Get all products for this business
     */
    public function getAll() {
        $sql = "SELECT * FROM products WHERE business_id = ? ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->business_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get product by ID (and ensure it belongs to business)
     */
    public function getById($id) {
        $sql = "SELECT * FROM products WHERE id = ? AND business_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id, $this->business_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get low stock products
     */
    public function getLowStock($threshold = 10) {
        $sql = "SELECT * FROM products WHERE business_id = ? AND stock <= ? ORDER BY stock ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->business_id, $threshold);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Search products
     */
    public function search($query) {
        $searchTerm = "%{$query}%";
        $sql = "SELECT * FROM products WHERE business_id = ? AND (name LIKE ? OR category LIKE ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $this->business_id, $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Create new product
     */
    public function create($name, $category, $price, $stock, $alert_stock = 5, $barcode = null) {
        $sql = "INSERT INTO products (business_id, name, category, price, stock, alert_stock, barcode) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }
        
        $stmt->bind_param("issdiis", $this->business_id, $name, $category, $price, $stock, $alert_stock, $barcode);
        
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
        // Enforce business_id in update to prevent editing others' data
        $sql = "UPDATE products SET name=?, category=?, price=?, stock=?, alert_stock=?, barcode=? WHERE id=? AND business_id=?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }
        
        $stmt->bind_param("ssdiisii", $name, $category, $price, $stock, $alert_stock, $barcode, $id, $this->business_id);
        
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
        $sql = "DELETE FROM products WHERE id=? AND business_id=?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }
        
        $stmt->bind_param("ii", $id, $this->business_id);
        
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
        $sql = "SELECT COUNT(*) as count FROM products WHERE business_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->business_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM products WHERE business_id = ? AND category IS NOT NULL ORDER BY category ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->business_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
