<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/permissions.php';

checkAccess($conn, 'reports');

$business_id = $_SESSION['business_id'];

// Filter logic
$filter = $_GET['filter'] ?? 'today';
$date_query = "";

switch ($filter) {
    case 'today':
        $date_query = "AND DATE(s.sale_date) = CURDATE()";
        break;
    case 'week':
        $date_query = "AND s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case '15days':
        $date_query = "AND s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 15 DAY)";
        break;
    case 'month':
        $date_query = "AND s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        break;
    case 'all':
        $date_query = "";
        break;
}

// 1. Sales Stats
$q_stats = "SELECT 
                SUM(s.total_amount) as total_revenue,
                SUM(si.quantity * (si.price - si.purchase_price)) as total_profit,
                SUM(si.quantity * si.purchase_price) as total_cogs,
                SUM(si.quantity) as total_items
            FROM sales s
            JOIN sale_items si ON s.id = si.sale_id
            WHERE s.business_id = '$business_id' $date_query";
$res_stats = mysqli_query($conn, $q_stats);
$stats = mysqli_fetch_assoc($res_stats);

$total_revenue = $stats['total_revenue'] ?? 0;
$total_profit = $stats['total_profit'] ?? 0;
$total_cogs = $stats['total_cogs'] ?? 0; // Total Cost of Goods Sold (Expenses)
$total_items = $stats['total_items'] ?? 0;

// 2. Sales by Payment Method
$q_pay = "SELECT 
            SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END) as cash_sales,
            SUM(CASE WHEN payment_method = 'credit' THEN total_amount ELSE 0 END) as credit_sales
          FROM sales s WHERE business_id = '$business_id' $date_query";
$res_pay = mysqli_query($conn, $q_pay);
$pay_stats = mysqli_fetch_assoc($res_pay);
$cash_sales = $pay_stats['cash_sales'] ?? 0;
$credit_sales = $pay_stats['credit_sales'] ?? 0;

// 3. Supplier Payments (Expenses Out)
$q_sup = "SELECT SUM(amount) as paid FROM supplier_transactions 
          WHERE type='PAYMENT' AND supplier_id IN (SELECT id FROM suppliers WHERE business_id='$business_id') 
          $date_query"; 
// Note: Date query for transactions uses created_at. We need to alias it or handle it separately.
// For simplicity, let's just use raw date query adjustment for this table or simpler logic.
// Let's parse 'date_query' to replace 's.sale_date' with 'created_at' for transactions
$trans_date_query = str_replace('s.sale_date', 'created_at', $date_query);
$res_sup = mysqli_query($conn, "SELECT SUM(amount) as paid FROM supplier_transactions WHERE type='PAYMENT' AND supplier_id IN (SELECT id FROM suppliers WHERE business_id='$business_id') $trans_date_query");
$sup_stats = mysqli_fetch_assoc($res_sup);
$supplier_paid = $sup_stats['paid'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Reports - Kirana Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .filter-bar { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .filter-btn { padding: 8px 15px; border-radius: 20px; border: 1px solid #ddd; background: #f8f9fa; color: #333; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .filter-btn.active { background: #4834d4; color: white; border-color: #4834d4; }
        .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .report-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #ccc; Display: flex; flex-direction: column; justify-content: center; }
        .report-card.revenue { border-color: #3498db; }
        .report-card.profit { border-color: #2ecc71; }
        .report-card.expense { border-color: #e74c3c; }
        .report-card.warning { border-color: #f1c40f; }
        .report-value { font-size: 24px; font-weight: 800; color: #2d3436; margin-top: 5px; }
        .report-label { color: #636e72; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .sub-text { font-size: 11px; color: #888; margin-top: 5px; }
    </style>
</head>
<body>
    <?php $page = 'reports'; include '../includes/navbar.php'; ?>

    <div class="container">
        <header class="header">
            <h1>Business Intelligence</h1>
            <div class="date-display"><?php echo date('F d, Y'); ?></div>
        </header>

        <div class="filter-bar">
            <strong>Duration:</strong>
            <a href="?filter=today" class="filter-btn <?php echo $filter == 'today' ? 'active' : ''; ?>">Today</a>
            <a href="?filter=week" class="filter-btn <?php echo $filter == 'week' ? 'active' : ''; ?>">Last 7 Days</a>
            <a href="?filter=month" class="filter-btn <?php echo $filter == 'month' ? 'active' : ''; ?>">Month</a>
            <a href="?filter=all" class="filter-btn <?php echo $filter == 'all' ? 'active' : ''; ?>">All Time</a>
        </div>

        <div class="section-title">Financial Overview</div>
        <div class="report-grid">
            <div class="report-card revenue">
                <div class="report-label">Total Revenue</div>
                <div class="report-value">रू<?php echo number_format($total_revenue, 2); ?></div>
                <div class="sub-text"><?php echo number_format($total_items); ?> Items Sold</div>
            </div>
            
            <div class="report-card expense">
                <div class="report-label">Total Expenses (COGS)</div>
                <div class="report-value">रू<?php echo number_format($total_cogs, 2); ?></div>
                <div class="sub-text">Inventory Cost</div>
            </div>

            <div class="report-card profit" style="background: #eaffea;">
                <div class="report-label">Net Profit</div>
                <div class="report-value" style="color: #27ae60;">रू<?php echo number_format($total_profit, 2); ?></div>
                <div class="sub-text">Margin: <?php echo $total_revenue > 0 ? round(($total_profit / $total_revenue) * 100, 1) : 0; ?>%</div>
            </div>
            
            <div class="report-card warning">
                <div class="report-label">Supplier Payments</div>
                <div class="report-value">रू<?php echo number_format($supplier_paid, 2); ?></div>
                <div class="sub-text">Cash Outflow</div>
            </div>
        </div>

        <div class="section-title">Cash Flow & Credit</div>
        <div class="report-grid">
            <div class="report-card revenue">
                <div class="report-label">Total Cash Sales</div>
                <div class="report-value">रू<?php echo number_format($cash_sales, 2); ?></div>
            </div>
            <div class="report-card warning">
                <div class="report-label">Credit Given (Udharo)</div>
                <div class="report-value">रू<?php echo number_format($credit_sales, 2); ?></div>
                <div class="sub-text">Pending Collection</div>
            </div>
        </div>

        <div class="section-title">Top Performing Products</div>
        <div class="table-container" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Sold</th>
                        <th>Revenue</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($top_products)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 20px;">No data found for this period.</td></tr>
                    <?php else: ?>
                        <?php foreach ($top_products as $p): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><?php echo $p['qty']; ?></td>
                            <td>रू<?php echo number_format($p['revenue'], 2); ?></td>
                            <td style="color: #10ac84; font-weight: bold;">रू<?php echo number_format($p['profit'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
