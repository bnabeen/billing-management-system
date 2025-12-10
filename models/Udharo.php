<?php
class Udharo {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll() {
        $sql = "SELECT * FROM udharo WHERE amount > 0 ORDER BY date DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getByCustomer($customer_name) {
        $sql = "SELECT * FROM udharo WHERE customer_name = ? AND amount > 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $customer_name);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getTotalCredit() {
        $sql = "SELECT SUM(amount) as total FROM udharo WHERE amount > 0";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
}
?>
