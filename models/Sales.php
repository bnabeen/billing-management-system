<?php
class Sales {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll() {
        $sql = "SELECT * FROM sales ORDER BY sale_date DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM sales WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getTodaySales() {
        $sql = "SELECT SUM(total) as total FROM sales WHERE DATE(sale_date) = CURDATE()";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
    
    public function getSaleItems($sale_id) {
        $sql = "SELECT si.*, p.name as product_name FROM sale_items si 
                JOIN products p ON si.product_id = p.id 
                WHERE si.sale_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sale_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
