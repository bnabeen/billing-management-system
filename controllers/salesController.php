<?php
// Handle sales
require_once '../config/db.php';

if (isset($_POST['create_sale'])) {
    $customer_name = $_POST['customer_name'] ?? 'Walk-in Customer';
    $items = json_decode($_POST['items'], true);
    $total = $_POST['total'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert sale
        $sql = "INSERT INTO sales (customer_name, total, sale_date) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sd", $customer_name, $total);
        $stmt->execute();
        $sale_id = $conn->insert_id;
        
        // Insert sale items and update stock
        foreach ($items as $item) {
            $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiid", $sale_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
            
            // Update stock
            $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmt->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'sale_id' => $sale_id]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
