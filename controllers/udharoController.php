<?php
// Credit logic
require_once '../config/db.php';

// Add Credit Entry
if (isset($_POST['add_credit'])) {
    $customer_name = $_POST['customer_name'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    
    $sql = "INSERT INTO udharo (customer_name, amount, description, date) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sds", $customer_name, $amount, $description);
    
    if ($stmt->execute()) {
        header("Location: ../public/udharo.php?success=1");
    } else {
        header("Location: ../public/udharo.php?error=1");
    }
    exit;
}

// Pay Credit
if (isset($_POST['pay_credit'])) {
    $id = $_POST['id'];
    $payment = $_POST['payment'];
    
    $sql = "UPDATE udharo SET amount = amount - ?, paid = paid + ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddi", $payment, $payment, $id);
    
    if ($stmt->execute()) {
        header("Location: ../public/udharo.php?paid=1");
    } else {
        header("Location: ../public/udharo.php?error=1");
    }
    exit;
}
?>
