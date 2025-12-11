<?php
// Top bar / title / nav
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sumit Kirana Store'; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="top-navbar">
        <div class="navbar-brand">Kirana Store</div>
        <div class="navbar-menu">
            <a href="../public/dashboard.php" class="navbar-item">Home</a>
            <a href="../public/sales.php" class="navbar-item">Sales</a>
            <a href="../public/products.php" class="navbar-item">Products</a>
            <a href="../public/udharo.php" class="navbar-item">Udharo</a>
            <a href="../public/logout.php" class="navbar-item logout">Logout</a>
        </div>
    </nav>
