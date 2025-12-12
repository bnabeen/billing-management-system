<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$business_id = $_SESSION['business_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Create Customer
    if (isset($_POST['action']) && $_POST['action'] == 'create_customer') {
        $name = $_POST['name'];
        $phone = $_POST['phone'];

        // Check duplicate phone
        $check = mysqli_query($conn, "SELECT id FROM udharo_customers WHERE business_id = '$business_id' AND phone = '$phone'");
        if (mysqli_num_rows($check) > 0) {
             header("Location: ../public/udharo.php?error=Customer with this phone already exists");
             exit();
        }

        $query = "INSERT INTO udharo_customers (business_id, name, phone) VALUES ('$business_id', '$name', '$phone')";
        if (mysqli_query($conn, $query)) {
            header("Location: ../public/udharo.php?success=Customer added");
        } else {
            header("Location: ../public/udharo.php?error=Failed to add customer");
        }
    }

    // 2. Add Transaction (Credit / Payment)
    if (isset($_POST['action']) && $_POST['action'] == 'add_transaction') {
        $customer_id = $_POST['customer_id'];
        $type = $_POST['type']; // 'CREDIT' or 'PAYMENT'
        $amount = floatval($_POST['amount']);
        $description = $_POST['description'];
        
        if ($amount <= 0) {
            header("Location: ../public/udharo.php?error=Invalid amount");
            exit();
        }

        // Insert Transaction
        $q_trans = "INSERT INTO udharo_transactions (customer_id, amount, type, description) VALUES ('$customer_id', '$amount', '$type', '$description')";
        
        if (mysqli_query($conn, $q_trans)) {
            // Update Customer Total Debt
            if ($type == 'CREDIT') {
                $q_update = "UPDATE udharo_customers SET total_debt = total_debt + $amount WHERE id = '$customer_id'";
            } else {
                $q_update = "UPDATE udharo_customers SET total_debt = total_debt - $amount WHERE id = '$customer_id'";
            }
            mysqli_query($conn, $q_update);

            header("Location: ../public/udharo.php?success=Transaction recorded");
        } else {
            header("Location: ../public/udharo.php?error=Transaction failed");
        }
    }

} elseif (isset($_GET['api']) && $_GET['api'] == 'get_history') {
    // API to fetch transaction history for a customer
    $customer_id = intval($_GET['customer_id']);
    
    // Security check: ensure customer belongs to business
    $check = mysqli_query($conn, "SELECT id FROM udharo_customers WHERE id='$customer_id' AND business_id='$business_id'");
    if (mysqli_num_rows($check) == 0) {
        echo json_encode(['error' => 'Invalid Access']);
        exit;
    }

    $history = [];
    $q = "SELECT * FROM udharo_transactions WHERE customer_id = '$customer_id' ORDER BY created_at DESC";
    $res = mysqli_query($conn, $q);
    while($row = mysqli_fetch_assoc($res)) {
        $history[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($history);
    exit;
}
