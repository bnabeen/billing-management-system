<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// 1. Today's Sales
// $query_sales = "SELECT SUM(total) as total_today FROM sales WHERE DATE(sale_date) = CURDATE()";
// $result_sales = mysqli_query($conn, $query_sales);
// $row_sales = mysqli_fetch_assoc($result_sales);
// $todays_sales = $row_sales['total_today'] ?? 0;
$todays_sales = 0; // Placeholder

// 2. Low Stock Alerts (< 20 items)
$query_stock = "SELECT COUNT(*) as low_stock FROM products WHERE stock < 20";
$result_stock = mysqli_query($conn, $query_stock);
$row_stock = mysqli_fetch_assoc($result_stock);
$low_stock_count = $row_stock['low_stock'];

// 3. Total Products
$query_products = "SELECT COUNT(*) as total_prods FROM products";
$result_products = mysqli_query($conn, $query_products);
$row_products = mysqli_fetch_assoc($result_products);
$total_products = $row_products['total_prods'];

// 4. Total Customer Credit
// $query_credit = "SELECT SUM(amount - paid) as total_credit FROM udharo";
// $result_credit = mysqli_query($conn, $query_credit);
// $row_credit = mysqli_fetch_assoc($result_credit);
// $total_credit = $row_credit['total_credit'] ?? 0;
$total_credit = 0; // Placeholder
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sumit Kirana Store</title>
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="top-navbar">
        <div class="navbar-brand">Sumit Kirana Store</div>
        <div class="navbar-menu">
            <a href="dashboard.php" class="navbar-item active">Home</a>
            <a href="sales.php" class="navbar-item">Sales</a>
            <a href="products.php" class="navbar-item">Products</a>
            <a href="udharo.php" class="navbar-item">Udharo</a>
            <a href="logout.php" class="navbar-item logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Dashboard</h1>
            <!-- Profile Icon Removed as per request -->
        </header>

        <!-- Quick Actions -->
        <div class="section-title">Quick Actions</div>
        <section class="quick-actions">
            <!-- 
            <div class="action-card" onclick="window.location.href='sales.php'">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                </div>
                <span class="action-text">Create New Bill</span>
            </div>
            -->
            <div class="action-card" onclick="window.location.href='products.php'">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/></svg>
                </div>
                <span class="action-text">Manage Products</span>
            </div>
            
            <!-- Dummy Box 1 -->
            <div class="action-card dummy-box">
                <span class="action-text">Dummy Box</span>
            </div>

            <!-- Dummy Box 2 -->
            <div class="action-card dummy-box">
                <span class="action-text">Dummy Box</span>
            </div>
            <!--
            <div class="action-card" onclick="window.location.href='users.php'">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <span class="action-text">Add Customer</span>
            </div>
            -->
        </section>

        <!-- Daily Focus -->
        <div class="section-title">Daily Focus</div>
        <section class="daily-focus">
            <!-- Today's Sales -->
            <!--
            <div class="focus-card full-width">
                <div class="focus-content">
                    <h3>Today's Sales</h3>
                    <div class="focus-value">₹<?php echo number_format($todays_sales, 2); ?></div>
                </div>
                <div class="focus-icon icon-green">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
            </div>
            -->

            <!-- Low Stock Alerts -->
            <div class="focus-card full-width">
                <div class="focus-content">
                    <h3>Low Stock Alerts</h3>
                    <div class="focus-value"><?php echo $low_stock_count; ?></div>
                </div>
                <div class="focus-icon icon-orange">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                </div>
            </div>

            <!-- Total Products -->
            <div class="focus-card">
                <div class="focus-content">
                    <h3>Total Products</h3>
                    <div class="focus-value"><?php echo number_format($total_products); ?></div>
                </div>
                <div class="focus-icon icon-blue">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-4 6h-4v2h4v-2zm-4 8V9h4v8h-4z"/></svg>
                </div>
            </div>

             <!-- Dummy Full Width Box -->
             <div class="focus-card full-width dummy-box-lg">
                <div class="focus-content">
                    <h3>Dummy Section</h3>
                </div>
            </div>
            <!-- Total Customer Credit -->
            <!--
            <div class="focus-card">
                <div class="focus-content">
                    <h3>Total Customer Credit</h3>
                    <div class="focus-value">₹<?php echo number_format($total_credit, 2); ?></div>
                </div>
                <div class="focus-icon icon-red">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                </div>
            </div>
            -->
        </section>
    </div>
    <?php include '../includes/footer.php'; ?>
