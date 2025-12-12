<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['action'] == 'create') {
        $business_id = $_SESSION['business_id'];
        $customer_name = $_POST['customer_name'];
        $customer_phone = $_POST['customer_phone'];
        $items = $_POST['items'] ?? [];

        if (empty($items)) {
            echo "<script>alert('Please add at least one item'); window.location='../public/sales.php';</script>";
            exit();
        }

        // Calculate Total
        $total_amount = 0;
        foreach ($items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Insert Sale
        $query = "INSERT INTO sales (business_id, customer_name, customer_phone, total_amount) VALUES ('$business_id', '$customer_name', '$customer_phone', '$total_amount')";
        
        if (mysqli_query($conn, $query)) {
            $sale_id = mysqli_insert_id($conn);
            
            // Insert Items
            foreach ($items as $item) {
                $pid = $item['product_id'];
                $qty = $item['quantity'];
                $price = $item['price'];
                $subtotal = $price * $qty;
                
                $q_item = "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) VALUES ('$sale_id', '$pid', '$qty', '$price', '$subtotal')";
                mysqli_query($conn, $q_item);

                // Decrease Stock
                $q_stock = "UPDATE products SET stock = stock - $qty WHERE id = '$pid'";
                mysqli_query($conn, $q_stock);
            }

            echo "<script>window.location='../public/sales.php?success=Sale recorded';</script>";
        } else {
             echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location='../public/sales.php';</script>";
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] == 'delete') {
    // Delete Sale (and restore stock? Usually yes, but for simplicity maybe just delete)
    // Let's restore stock first
    $id = $_GET['id'];
    $business_id = $_SESSION['business_id'];

    // Get items to restore stock
    $q_items = "SELECT product_id, quantity FROM sale_items WHERE sale_id = '$id'";
    $res = mysqli_query($conn, $q_items);
    while($row = mysqli_fetch_assoc($res)) {
        $pid = $row['product_id'];
        $qty = $row['quantity'];
        mysqli_query($conn, "UPDATE products SET stock = stock + $qty WHERE id = '$pid'");
    }

    $query = "DELETE FROM sales WHERE id = '$id' AND business_id = '$business_id'"; // Security check on business_id
    if (mysqli_query($conn, $query)) {
        header("Location: ../public/sales.php?success=Deleted");
    } else {
        echo "Error deleting sale";
    }
}
