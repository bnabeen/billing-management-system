<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$business_id = $_SESSION['business_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'create') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);

        $q = "INSERT INTO suppliers (business_id, name, phone, email, address) VALUES ('$business_id', '$name', '$phone', '$email', '$address')";
        if (mysqli_query($conn, $q)) {
            header("Location: ../public/suppliers.php?success=Supplier added");
        } else {
            header("Location: ../public/suppliers.php?error=Failed to add supplier");
        }
    }

    if ($action === 'add_transaction') {
        $supplier_id = $_POST['supplier_id'];
        $amount = (float)$_POST['amount'];
        $type = $_POST['type']; // PURCHASE, PAYMENT
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Insert transaction
        $q = "INSERT INTO supplier_transactions (supplier_id, amount, type, description) VALUES ('$supplier_id', '$amount', '$type', '$description')";
        if (mysqli_query($conn, $q)) {
            // Update supplier balance
            // Purchase increases balance (we owe more)
            // Payment decreases balance (we owe less)
            $balance_change = ($type === 'PURCHASE') ? $amount : -$amount;
            mysqli_query($conn, "UPDATE suppliers SET total_balance = total_balance + $balance_change WHERE id = '$supplier_id'");
            
            header("Location: ../public/supplier_details.php?id=$supplier_id&success=Transaction recorded");
        } else {
            header("Location: ../public/supplier_details.php?id=$supplier_id&error=Failed to record transaction");
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $q = "DELETE FROM suppliers WHERE id = '$id' AND business_id = '$business_id'";
    if (mysqli_query($conn, $q)) {
        header("Location: ../public/suppliers.php?success=Supplier removed");
    } else {
        header("Location: ../public/suppliers.php?error=Delete failed");
    }
}
?>
