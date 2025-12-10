<?php
// Add/edit/delete products
require_once '../config/db.php';

// Add Product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    
    $sql = "INSERT INTO products (name, price, stock, category) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdis", $name, $price, $stock, $category);
    
    if ($stmt->execute()) {
        header("Location: ../public/products.php?success=1");
    } else {
        header("Location: ../public/products.php?error=1");
    }
    exit;
}

// Edit Product
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    
    $sql = "UPDATE products SET name=?, price=?, stock=?, category=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdisi", $name, $price, $stock, $category, $id);
    
    if ($stmt->execute()) {
        header("Location: ../public/products.php?updated=1");
    } else {
        header("Location: ../public/products.php?error=1");
    }
    exit;
}

// Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $sql = "DELETE FROM products WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: ../public/products.php?deleted=1");
    } else {
        header("Location: ../public/products.php?error=1");
    }
    exit;
}
?>
