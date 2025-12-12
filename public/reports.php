<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$business_id = $_SESSION['business_id'];

// 1. Total Sales (Today, Week, Month)
$sales_stats = [];

// Today
$q = "SELECT SUM(total_amount) as total FROM sales WHERE business_id = '$business_id' AND DATE(sale_date) = CURDATE()";
$op = mysqli_fetch_assoc(mysqli_query($conn, $q));
$sales_stats['today'] = $op['total'] ?? 0;

// This Week
$q = "SELECT SUM(total_amount) as total FROM sales WHERE business_id = '$business_id' AND YEARWEEK(sale_date, 1) = YEARWEEK(CURDATE(), 1)";
$op = mysqli_fetch_assoc(mysqli_query($conn, $q));
$sales_stats['week'] = $op['total'] ?? 0;

// This Month
$q = "SELECT SUM(total_amount) as total FROM sales WHERE business_id = '$business_id' AND YEAR(sale_date) = YEAR(CURDATE()) AND MONTH(sale_date) = MONTH(CURDATE())";
$op = mysqli_fetch_assoc(mysqli_query($conn, $q));
$sales_stats['month'] = $op['total'] ?? 0;


// 2. Top Selling Products
$top_products = [];
$q = "SELECT p.name, SUM(si.quantity) as qty, SUM(si.subtotal) as total 
      FROM sale_items si 
      JOIN products p ON si.product_id = p.id 
      JOIN sales s ON si.sale_id = s.id 
      WHERE s.business_id = '$business_id'
      GROUP BY si.product_id 
      ORDER BY qty DESC LIMIT 5";
$res = mysqli_query($conn, $q);
while ($row = mysqli_fetch_assoc($res)) {
    $top_products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Kirana Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .report-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .report-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .report-value { font-size: 24px; font-weight: bold; color: #333; margin-top: 10px; }
        .report-label { color: #666; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        
        .section-header { margin: 30px 0 15px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php $page = 'reports'; include '../includes/navbar.php'; ?>

    <div class="container">
        <h1>Business Reports</h1>
        
        <h3 class="section-header">Sales Overview</h3>
        <div class="report-grid">
            <div class="report-card">
                <div class="report-label">Sales Today</div>
                <div class="report-value">₹<?php echo number_format($sales_stats['today'], 2); ?></div>
            </div>
            <div class="report-card">
                <div class="report-label">This Week</div>
                <div class="report-value">₹<?php echo number_format($sales_stats['week'], 2); ?></div>
            </div>
            <div class="report-card">
                <div class="report-label">This Month</div>
                <div class="report-value">₹<?php echo number_format($sales_stats['month'], 2); ?></div>
            </div>
        </div>

        <h3 class="section-header">Top Selling Products</h3>
        <?php if (empty($top_products)): ?>
            <p>No sales data available yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity Sold</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_products as $prod): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prod['name']); ?></td>
                                <td><?php echo $prod['qty']; ?></td>
                                <td>₹<?php echo number_format($prod['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
