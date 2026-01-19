<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/permissions.php';

checkAccess($conn, 'dashboard');

$business_id = $_SESSION['business_id'];

// 1. Today's Sales
$todays_sales = 0;
$query_sales = "SELECT SUM(total_amount) as total_today FROM sales WHERE business_id = '$business_id' AND DATE(sale_date) = CURDATE()";
$result_sales = mysqli_query($conn, $query_sales);
if ($result_sales) {
    $row_sales = mysqli_fetch_assoc($result_sales);
    $todays_sales = $row_sales['total_today'] ?? 0;
}

// 2. Low Stock Alerts
$low_stock_count = 0;
// Use alert_stock column
$query_stock = "SELECT COUNT(*) as low_stock FROM products WHERE business_id = '$business_id' AND stock < alert_stock";
$result_stock = mysqli_query($conn, $query_stock);
if ($result_stock) {
    $row_stock = mysqli_fetch_assoc($result_stock);
    $low_stock_count = $row_stock['low_stock'];
}

// 3. Total Products
$total_products = 0;
$query_products = "SELECT COUNT(*) as total_prods FROM products WHERE business_id = '$business_id'";
$result_products = mysqli_query($conn, $query_products);
if ($result_products) {
    $row_products = mysqli_fetch_assoc($result_products);
    $total_products = $row_products['total_prods'];
}

// 4. Total Customer Credit
$total_credit = 0;
$q_credit = "SELECT SUM(total_debt) as debt FROM udharo_customers WHERE business_id = '$business_id'";
$r_credit = mysqli_query($conn, $q_credit);
if($r_credit) {
    $row_c = mysqli_fetch_assoc($r_credit);
    $total_credit = $row_c['debt'] ?? 0;
}

// 5. Recent Sales (New)
$recent_sales = [];
$q_recent = "SELECT * FROM sales WHERE business_id = '$business_id' ORDER BY sale_date DESC LIMIT 5";
$r_recent = mysqli_query($conn, $q_recent);
if ($r_recent) {
    while($row = mysqli_fetch_assoc($r_recent)) {
        $recent_sales[] = $row;
    }
}
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
    <?php $page = 'dashboard'; include '../includes/navbar.php'; ?>

    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Dashboard</h1>
            <div class="user-profile">
                <span>Welcome, <b><?php echo $_SESSION['username'] ?? 'User'; ?></b></span>
                <span class="role-badge"><?php echo $_SESSION['role'] ?? 'Staff'; ?></span>
            </div>
        </header>

        <!-- Daily Focus / Key Metrics -->
        <!-- Overview Section -->
        <div class="section-title">Overview</div>

        <!-- Row 1: Low Stock & Products -->
        <div class="row" style="margin-bottom: 20px;">
            <!-- Low Stock -->
            <div class="focus-card" onclick="openLowStockModal()" style="background: white; border-left: 5px solid #e74c3c; cursor: pointer; display: flex; align-items: center; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div class="focus-icon" style="background: #ffe6e6; padding: 10px; border-radius: 50%; margin-right: 15px;">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="#e74c3c"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                </div>
                <div class="focus-content">
                    <h3 style="margin: 0; font-size: 14px; color: #666;">Low Stock Items</h3>
                    <div class="focus-value" style="font-size: 24px; font-weight: bold; color: #333;"><?php echo $low_stock_count; ?></div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="focus-card" onclick="window.location.href='products.php'" style="background: white; border-left: 5px solid #3498db; cursor: pointer; display: flex; align-items: center; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div class="focus-icon" style="background: #e6f7ff; padding: 10px; border-radius: 50%; margin-right: 15px;">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="#3498db"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-4 6h-4v2h4v-2zm-4 8V9h4v8h-4z"/></svg>
                </div>
                <div class="focus-content">
                    <h3 style="margin: 0; font-size: 14px; color: #666;">Total Products</h3>
                    <div class="focus-value" style="font-size: 24px; font-weight: bold; color: #333;"><?php echo number_format($total_products); ?></div>
                </div>
            </div>
        </div>

        <!-- Row 2: Sales & Credit -->
        <div class="row">
            <!-- Today's Sales -->
            <div class="focus-card" onclick="window.location.href='reports.php?filter=today'" style="background: white; border-left: 5px solid #2ecc71; cursor: pointer; display: flex; align-items: center; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                 <div class="focus-icon" style="background: #e6fffa; padding: 10px; border-radius: 50%; margin-right: 15px;">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="#2ecc71"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
                 <div class="focus-content">
                    <h3 style="margin: 0; font-size: 14px; color: #666;">Sales Today</h3>
                    <div class="focus-value" style="font-size: 24px; font-weight: bold; color: #333;">रू<?php echo number_format($todays_sales ?? 0, 2); ?></div>
                </div>
            </div>
            
             <!-- Credit Given -->
            <div class="focus-card" onclick="window.location.href='udharo.php'" style="background: white; border-left: 5px solid #f39c12; cursor: pointer; display: flex; align-items: center; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                 <div class="focus-icon" style="background: #fff7e6; padding: 10px; border-radius: 50%; margin-right: 15px;">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="#f39c12"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                </div>
                 <div class="focus-content">
                    <h3 style="margin: 0; font-size: 14px; color: #666;">Credit Given</h3>
                    <div class="focus-value" style="font-size: 24px; font-weight: bold; color: #333;">रू<?php echo number_format($total_credit ?? 0, 2); ?></div>
                </div>
            </div>
        </div>

        <!-- Low Stock Items Listing (Hidden by default) -->
        <div id="lowStockModal" class="modal">
            <div class="modal-content" style="width: 800px; max-width: 95%;">
                <div class="modal-header">
                    <h2>Low Stock Warning Items</h2>
                    <button class="modal-close" onclick="closeModal('lowStockModal')">&times;</button>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Current Stock</th>
                                <th>Alert Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_low = "SELECT * FROM products WHERE business_id = '$business_id' AND stock < alert_stock ORDER BY stock ASC";
                            $r_low = mysqli_query($conn, $q_low);
                            if (mysqli_num_rows($r_low) == 0) {
                                echo "<tr><td colspan='4' style='text-align:center'>No low stock items!</td></tr>";
                            } else {
                                while($p = mysqli_fetch_assoc($r_low)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($p['name']) . "</td>";
                                    echo "<td><b style='color:red'>" . $p['stock'] . "</b></td>";
                                    echo "<td>" . $p['alert_stock'] . "</td>";
                                    echo "<td><a href='suppliers.php' class='btn-add' style='font-size:11px; padding: 5px 10px;'>Quote Supplier</a></td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function openLowStockModal() { document.getElementById('lowStockModal').style.display = 'flex'; }
            function closeModal(id) { document.getElementById(id).style.display = 'none'; }
            
            // Close on outside click
            window.onclick = function(event) {
                if (event.target.className === 'modal') {
                    event.target.style.display = 'none';
                }
            }
        </script>


        <!-- Recent Bills Table -->
        <div class="section-title">Recent Bills</div>
        <div class="table-responsive" style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <?php if (empty($recent_sales)): ?>
                <p style="text-align:center; color: #777;">No recent transactions.</p>
            <?php else: ?>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_sales as $sale): ?>
                        <tr onclick="window.location.href='bill_view.php?id=<?php echo $sale['id']; ?>'" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='white'">
                            <td><?php echo date('d M, h:i A', strtotime($sale['sale_date'])); ?></td>
                            <td><?php echo htmlspecialchars($sale['customer_name'] ?: 'Walk-in'); ?></td>
                            <td>रू<?php echo number_format($sale['total_amount'], 2); ?></td>
                            <td><span style="color: green; font-size: 12px; font-weight: bold;">PAID</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
