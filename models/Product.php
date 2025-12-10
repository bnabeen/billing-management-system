<?php
class Product {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll() {
        $sql = "SELECT * FROM products ORDER BY name ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getLowStock($threshold = 10) {
        $sql = "SELECT * FROM products WHERE stock <= ? ORDER BY stock ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function search($query) {
        $searchTerm = "%{$query}%";
        $sql = "SELECT * FROM products WHERE name LIKE ? OR category LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
