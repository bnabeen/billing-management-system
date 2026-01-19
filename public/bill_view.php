<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

if (!isset($_GET['id'])) {
    die("Invalid Bill ID");
}

$sale_id = $_GET['id'];
$business_id = $_SESSION['business_id'];

// Fetch Sale
$q_sale = "SELECT * FROM sales WHERE id = '$sale_id' AND business_id = '$business_id'";
$res_sale = mysqli_query($conn, $q_sale);
if (mysqli_num_rows($res_sale) == 0) {
    die("Bill not found.");
}
$sale = mysqli_fetch_assoc($res_sale);

// Fetch Items
$items = [];
$q_items = "SELECT si.*, p.name as product_name 
            FROM sale_items si 
            JOIN products p ON si.product_id = p.id 
            WHERE si.sale_id = '$sale_id'";
$res_items = mysqli_query($conn, $q_items);
while ($row = mysqli_fetch_assoc($res_items)) {
    $items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill #<?php echo $sale_id; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; display: flex; justify-content: center; padding: 20px; }
        .bill-container { background: white; padding: 30px; width: 400px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px dashed #ddd; padding-bottom: 15px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 24px; color: #333; }
        .header p { margin: 5px 0; color: #666; font-size: 14px; }
        
        .info { font-size: 13px; margin-bottom: 15px; }
        .info div { display: flex; justify-content: space-between; margin-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 10px; }
        th, td { text-align: left; padding: 8px 0; border-bottom: 1px solid #eee; }
        th { font-weight: 600; color: #555; }
        td.right { text-align: right; }
        
        .totals { border-top: 2px solid #333; padding-top: 10px; margin-top: 10px; }
        .totals div { display: flex; justify-content: space-between; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        .grand-total { font-size: 18px; font-weight: 800; color: #333; }
        
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; border-top: 1px dashed #ddd; padding-top: 10px; }
        
        @media print {
            body { background: white; padding: 0; }
            .bill-container { box-shadow: none; width: 100%; }
            .no-print { display: none; }
        }
        
        .btn-print { display: block; width: 100%; padding: 10px; background: #4834d4; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; margin-top: 20px; }
        .btn-back { display: block; width: 100%; padding: 10px; background: #a4b0be; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; margin-top: 10px; text-decoration: none; text-align: center; }
    </style>
</head>
<body>

    <div class="bill-container">
        <div class="header">
            <h1>Sumit Kirana Store</h1>
            <p>Kathmandu, Nepal</p>
            <p>Phone: 9841XXXXXX</p>
        </div>
        
        <div class="info">
            <div>
                <span>Bill No: #<?php echo $sale_id; ?></span>
                <span>Date: <?php echo date('d/m/Y', strtotime($sale['sale_date'])); ?></span>
            </div>
            <div>
                <span>Customer: <?php echo htmlspecialchars($sale['customer_name'] ?: 'Walk-in'); ?></span>
                <span><?php echo htmlspecialchars($sale['customer_phone'] ?? ''); ?></span>
            </div>
            <div>
                <span>Payment: <?php echo strtoupper($sale['payment_method'] ?? 'CASH'); ?></span>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Price</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($items as $item): 
                    $subtotal += $item['subtotal'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td class="right"><?php echo $item['quantity']; ?></td>
                    <td class="right"><?php echo number_format($item['price'], 2); ?></td>
                    <td class="right"><?php echo number_format($item['subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="totals">
            <div>
                <span>Subtotal</span>
                <span><?php echo number_format($subtotal, 2); ?></span>
            </div>
            <?php if (($sale['discount'] ?? 0) > 0): ?>
            <div>
                <span>Discount</span>
                <span>-<?php echo number_format($sale['discount'], 2); ?></span>
            </div>
            <?php endif; ?>
            <div class="grand-total">
                <span>Grand Total</span>
                <span><?php echo number_format($sale['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="footer">
            Thank you for shopping with us!<br>
            Please visit again.
        </div>
        
        <button onclick="window.print()" class="btn-print no-print">PRINT BILL</button>
        <a href="sales.php" class="btn-back no-print">BACK TO Sales</a>
    </div>

</body>
</html>
