<!-- Navbar -->
<nav class="top-navbar">
    <div class="navbar-brand">Kirana Store</div>
    <div class="navbar-menu">
        <a href="dashboard.php" class="navbar-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">Home</a>
        <a href="sales.php" class="navbar-item <?php echo ($page === 'sales') ? 'active' : ''; ?>">Sales</a>
        <a href="products.php" class="navbar-item <?php echo ($page === 'products') ? 'active' : ''; ?>">Products</a>
        <a href="udharo.php" class="navbar-item <?php echo ($page === 'udharo') ? 'active' : ''; ?>">Udharo</a>
        <a href="reports.php" class="navbar-item <?php echo ($page === 'reports') ? 'active' : ''; ?>">Reports</a>
        <a href="users.php" class="navbar-item <?php echo ($page === 'users') ? 'active' : ''; ?>">Users</a>
        <a href="logout.php" class="navbar-item logout">Logout</a>
    </div>
</nav>
