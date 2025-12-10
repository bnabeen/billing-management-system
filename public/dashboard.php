<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kirana Store</title>
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Kirana Store</h1>
            <div class="profile-icon">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
        </header>

        <!-- Quick Actions -->
        <div class="section-title">Quick Actions</div>
        <section class="quick-actions">
            <div class="action-card" onclick="window.location.href='sales.php'">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                </div>
                <span class="action-text">Create New Bill</span>
            </div>
            <div class="action-card" onclick="window.location.href='add-product.php'">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/></svg>
                </div>
                <span class="action-text">Add Products</span>
            </div>
            <div class="action-card" onclick="window.location.href='users.php'">
                <div class="action-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <span class="action-text">Add Customer</span>
            </div>
        </section>

        <!-- POS Billing Section -->
        <div class="section-title">Quick POS Billing</div>
        <section class="pos-section">
            <div class="pos-form">
                <input type="text" class="pos-input" placeholder="Product Name / Barcode">
                <input type="number" class="pos-input" placeholder="Qty" style="max-width: 80px;">
                <input type="number" class="pos-input" placeholder="Price" style="max-width: 100px;">
                <button class="pos-btn">Add</button>
            </div>
            
            <div class="bill-preview">
                <div class="bill-item">
                    <span>Rice (5kg)</span>
                    <span>₹450</span>
                </div>
                <div class="bill-item">
                    <span>Sugar (1kg)</span>
                    <span>₹42</span>
                </div>
                <div class="bill-total">
                    <span>Total</span>
                    <span>₹492</span>
                </div>
            </div>
            <button class="generate-btn">Generate Bill & Print</button>
        </section>

        <!-- Daily Focus -->
        <div class="section-title">Daily Focus</div>
        <section class="daily-focus">
            <!-- Today's Sales -->
            <div class="focus-card full-width">
                <div class="focus-content">
                    <h3>Today's Sales</h3>
                    <div class="focus-value">₹8,540</div>
                </div>
                <div class="focus-icon icon-green">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="focus-card full-width">
                <div class="focus-content">
                    <h3>Low Stock Alerts</h3>
                    <div class="focus-value">15</div>
                </div>
                <div class="focus-icon icon-orange">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                </div>
            </div>

            <!-- Total Products -->
            <div class="focus-card">
                <div class="focus-content">
                    <h3>Total Products</h3>
                    <div class="focus-value">1,240</div>
                </div>
                <div class="focus-icon icon-blue">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-4 6h-4v2h4v-2zm-4 8V9h4v8h-4z"/></svg>
                </div>
            </div>

            <!-- Total Customer Credit -->
            <div class="focus-card">
                <div class="focus-content">
                    <h3>Total Customer Credit</h3>
                    <div class="focus-value">₹12,350</div>
                </div>
                <div class="focus-icon icon-red">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                </div>
            </div>
        </section>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item active">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span>Home</span>
        </a>
        <a href="sales.php" class="nav-item">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M18 17H6v-2h12v2zm0-4H6v-2h12v2zm0-4H6V7h12v2zM3 22l1.5-1.5L6 22l1.5-1.5L9 22l1.5-1.5L12 22l1.5-1.5L15 22l1.5-1.5L18 22l1.5-1.5L21 22V2l-1.5 1.5L18 2l-1.5 1.5L15 2l-1.5 1.5L12 2 10.5 3.5 9 2 7.5 3.5 6 2 4.5 3.5 3 2v20z"/></svg>
            <span>Sales</span>
        </a>
        <a href="products.php" class="nav-item">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27-7.38 5.74zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16z"/></svg>
            <span>Products</span>
        </a>
        <a href="udharo.php" class="nav-item">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
            <span>Credit</span>
        </a>
    </nav>

    <script src="../assets/js/billing.js"></script>
</body>
</html>
