<?php
require_once 'permissions.php';
// $conn is already included in files that include navbar
?>
<!-- Navbar -->
<nav class="top-navbar">
    <div class="navbar-brand">Kirana Store</div>
    <div class="navbar-menu">
        <?php if (hasPermission($conn, 'dashboard')): ?>
            <a href="dashboard.php" class="navbar-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">Home</a>
        <?php endif; ?>
        
        <?php if (hasPermission($conn, 'sales')): ?>
            <a href="sales.php" class="navbar-item <?php echo ($page === 'sales') ? 'active' : ''; ?>">Sales</a>
        <?php endif; ?>
        
        <?php if (hasPermission($conn, 'products')): ?>
            <a href="products.php" class="navbar-item <?php echo ($page === 'products') ? 'active' : ''; ?>">Products</a>
        <?php endif; ?>
        
        <?php if (hasPermission($conn, 'suppliers')): ?>
            <a href="suppliers.php" class="navbar-item <?php echo ($page === 'suppliers') ? 'active' : ''; ?>">Suppliers</a>
        <?php endif; ?>
        
        <?php if (hasPermission($conn, 'udharo')): ?>
            <a href="udharo.php" class="navbar-item <?php echo ($page === 'udharo') ? 'active' : ''; ?>">Udharo</a>
        <?php endif; ?>
        
        <?php if (hasPermission($conn, 'reports')): ?>
            <a href="reports.php" class="navbar-item <?php echo ($page === 'reports') ? 'active' : ''; ?>">Reports</a>
        <?php endif; ?>
        
        <?php if (hasPermission($conn, 'users')): ?>
            <a href="users.php" class="navbar-item <?php echo ($page === 'users') ? 'active' : ''; ?>">Users</a>
        <?php endif; ?>
        
        <a href="logout.php" class="navbar-item logout">Logout</a>
    </div>
</nav>
