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

        // Calculate Subtotal from Items
        $sub_total = 0;
        foreach ($items as $item) {
            $sub_total += $item['price'] * $item['quantity'];
        }

        $discount = $_POST['discount'] ?? 0;
        $total_amount = $sub_total - $discount;
        if ($total_amount < 0) $total_amount = 0;

        // Insert Sale
        $payment_method = $_POST['payment_method'] ?? 'cash';
        $udharo_customer_id = $_POST['udharo_customer_id'] ?? null;
        
        // Auto-Link Logic
        // If Customer ID is not selected from dropdown
        if (!$udharo_customer_id) {
            // Check if user provided Name and Phone
            if (!empty($customer_name) && !empty($customer_phone)) {
                // Check if customer already exists by Phone
                $q_check = "SELECT id FROM udharo_customers WHERE phone = '$customer_phone' AND business_id = '$business_id'";
                $res_check = mysqli_query($conn, $q_check);
                if (mysqli_num_rows($res_check) > 0) {
                    $row_check = mysqli_fetch_assoc($res_check);
                    $udharo_customer_id = $row_check['id'];
                } else {
                    // Create new customer (even for Cash sales, to remember them)
                    $q_new = "INSERT INTO udharo_customers (business_id, name, phone, total_debt) VALUES ('$business_id', '$customer_name', '$customer_phone', 0)";
                    if (mysqli_query($conn, $q_new)) {
                        $udharo_customer_id = mysqli_insert_id($conn);
                    }
                }
            }
        }

        // Enforce Customer for Credit
        if ($payment_method === 'credit' && !$udharo_customer_id) {
             echo "<script>alert('Credit Sale requires a Customer! Please select or enter name/phone.'); window.location='../public/sales.php';</script>";
             exit();
        }
        
        // If we have a Customer ID now (either selected or created), use their current details for the record
        if ($udharo_customer_id) {
            $u_res = mysqli_query($conn, "SELECT name, phone FROM udharo_customers WHERE id = '$udharo_customer_id'");
            $u_row = mysqli_fetch_assoc($u_res);
            $customer_name = $u_row['name'];
            $customer_phone = $u_row['phone'];
        }

        $query = "INSERT INTO sales (business_id, customer_name, customer_phone, payment_method, discount, total_amount) VALUES ('$business_id', '$customer_name', '$customer_phone', '$payment_method', '$discount', '$total_amount')";
        
        if (mysqli_query($conn, $query)) {
            $sale_id = mysqli_insert_id($conn);
            
            // 1. Insert Items
            foreach ($items as $item) {
                $pid = $item['product_id'];
                $qty = $item['quantity'];
                $price = $item['price'];
                $subtotal = $price * $qty;
                
                // Fetch current purchase price from product
                $p_res = mysqli_query($conn, "SELECT purchase_price FROM products WHERE id = '$pid'");
                $p_row = mysqli_fetch_assoc($p_res);
                $purchase_price = $p_row['purchase_price'] ?? 0;
                
                $q_item = "INSERT INTO sale_items (sale_id, product_id, quantity, price, purchase_price, subtotal) VALUES ('$sale_id', '$pid', '$qty', '$price', '$purchase_price', '$subtotal')";
                mysqli_query($conn, $q_item);

                // Decrease Stock
                $q_stock = "UPDATE products SET stock = stock - $qty WHERE id = '$pid'";
                mysqli_query($conn, $q_stock);
            }

            // 2. Handle Udharo/Credit
            if ($payment_method === 'credit' && $udharo_customer_id) {
                $description = "Sale #$sale_id (Total: $total_amount)";
                $q_udharo = "INSERT INTO udharo_transactions (customer_id, sale_id, amount, type, is_cash, description) 
                             VALUES ('$udharo_customer_id', '$sale_id', '$total_amount', 'CREDIT', 0, '$description')";
                mysqli_query($conn, $q_udharo);
                
                // Update customer debt
                mysqli_query($conn, "UPDATE udharo_customers SET total_debt = total_debt + $total_amount WHERE id = '$udharo_customer_id'");
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
