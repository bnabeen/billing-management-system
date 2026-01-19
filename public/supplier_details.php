<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/permissions.php';

checkAccess($conn, 'suppliers');

$supplier_id = $_GET['id'] ?? null;
$business_id = $_SESSION['business_id'];

if (!$supplier_id) {
    header("Location: suppliers.php");
    exit();
}

$q = "SELECT * FROM suppliers WHERE id = '$supplier_id' AND business_id = '$business_id'";
$res = mysqli_query($conn, $q);
$supplier = mysqli_fetch_assoc($res);

if (!$supplier) {
    header("Location: suppliers.php?error=Not found");
    exit();
}

// Fetch transactions
$transactions = [];
$q_t = "SELECT * FROM supplier_transactions WHERE supplier_id = '$supplier_id' ORDER BY created_at DESC";
$res_t = mysqli_query($conn, $q_t);
while ($row = mysqli_fetch_assoc($res_t)) {
    $transactions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($supplier['name']); ?> - Ledger</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php $page = 'suppliers'; include '../includes/navbar.php'; ?>

    <div class="container">
        <header class="header">
            <div>
                <a href="suppliers.php" style="text-decoration: none; color: #666;">&larr; Back</a>
                <h1><?php echo htmlspecialchars($supplier['name']); ?></h1>
            </div>
            <div class="stats-box" style="text-align: right;">
                <span style="color: #666; font-size: 14px;">Current Balance:</span>
                <div style="font-size: 24px; font-weight: 800; color: <?php echo $supplier['total_balance'] > 0 ? 'red' : 'green'; ?>;">
                    रू<?php echo number_format($supplier['total_balance'], 2); ?>
                </div>
            </div>
        </header>

        <div style="margin: 20px 0; display: flex; gap: 10px;">
            <button onclick="openModal('transactionModal')" class="btn-add">Record Transaction</button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <div class="section-title">Transaction History</div>
        <div class="table-responsive">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="4" style="text-align: center;">No transactions recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i', strtotime($t['created_at'])); ?></td>
                            <td>
                                <span class="badge" style="background: <?php echo $t['type'] === 'PURCHASE' ? '#fff1f0' : '#f6ffed'; ?>; color: <?php echo $t['type'] === 'PURCHASE' ? '#cf1322' : '#389e0d'; ?>; padding: 2px 8px; border-radius: 4px; border: 1px solid currentColor;">
                                    <?php echo $t['type']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($t['description']); ?></td>
                            <td style="font-weight: bold;">रू<?php echo number_format($t['amount'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction Modal -->
    <div id="transactionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Record Transaction</h2>
                <button class="modal-close" onclick="closeModal('transactionModal')">&times;</button>
            </div>
            <form action="../controllers/supplierController.php" method="POST" class="modal-form">
                <input type="hidden" name="action" value="add_transaction">
                <input type="hidden" name="user_id" value="%SAME%">
                <input type="hidden" name="supplier_id" value="<?php echo $supplier_id; ?>">
                
                <div class="form-group">
                    <label>Transaction Type</label>
                    <select name="type" required>
                        <option value="PURCHASE">Stock Purchase (Increases Balance)</option>
                        <option value="PAYMENT">Cash Payment (Decreases Balance)</option>
                        <option value="RETURN">Return (Decreases Balance)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" required>
                </div>

                <div class="form-group">
                    <label>Description (e.g. Bill #, Note)</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('transactionModal')">Cancel</button>
                    <button type="submit" class="btn-submit">Record</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).style.display = 'flex'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
