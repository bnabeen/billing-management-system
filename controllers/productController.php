<?php
session_start();
require_once '../config/db.php';
require_once '../models/Product.php';

$product = new Product($conn);

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $alert_stock = intval($_POST['alert_stock'] ?? 5);
    $barcode = trim($_POST['barcode'] ?? '');
    
    // Validation
    $errors = [];
    if (empty($name)) $errors[] = "Product name is required";
    if (empty($category)) $errors[] = "Category is required";
    if ($price <= 0) $errors[] = "Price must be greater than 0";
    if ($stock < 0) $errors[] = "Stock cannot be negative";
    
    if (empty($errors)) {
        $result = $product->create($name, $category, $price, $stock, $alert_stock, $barcode ?: null);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
    header("Location: ../public/products.php");
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $alert_stock = intval($_POST['alert_stock'] ?? 5);
    $barcode = trim($_POST['barcode'] ?? '');
    
    // Validation
    $errors = [];
    if ($id <= 0) $errors[] = "Invalid product ID";
    if (empty($name)) $errors[] = "Product name is required";
    if (empty($category)) $errors[] = "Category is required";
    if ($price <= 0) $errors[] = "Price must be greater than 0";
    if ($stock < 0) $errors[] = "Stock cannot be negative";
    
    if (empty($errors)) {
        $result = $product->update($id, $name, $category, $price, $stock, $alert_stock, $barcode ?: null);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
    header("Location: ../public/products.php");
    exit;
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        $_SESSION['error'] = "Invalid product ID";
    } else {
        $result = $product->delete($id);
        
        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }
    }
    
    header("Location: ../public/products.php");
    exit;
}

// Handle AJAX requests for data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
    header('Content-Type: application/json');
    
    if ($_GET['api'] === 'get_product' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $prod = $product->getById($id);
        echo json_encode($prod ?: ['error' => 'Product not found']);
    } elseif ($_GET['api'] === 'search' && isset($_GET['q'])) {
        $query = trim($_GET['q']);
        $results = $product->search($query);
        echo json_encode($results);
    } elseif ($_GET['api'] === 'get_all') {
        $products = $product->getAll();
        echo json_encode($products);
    } elseif ($_GET['api'] === 'get_categories') {
        $categories = $product->getCategories();
        echo json_encode($categories);
    }
    exit;
}
?>
