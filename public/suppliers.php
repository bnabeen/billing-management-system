<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/permissions.php';

checkAccess($conn, 'suppliers');

$business_id = $_SESSION['business_id'];

// Fetch suppliers
$suppliers = [];
$q = "SELECT * FROM suppliers WHERE business_id = '$business_id' ORDER BY name ASC";
$res = mysqli_query($conn, $q);
while ($row = mysqli_fetch_assoc($res)) {
    $suppliers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suppliers - Kirana Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php $page = 'suppliers'; include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="products-header">
            <h1>Supplier Management</h1>
            <button onclick="openModal('supplierModal')" class="btn-add">+ Add Supplier</button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                        <tr><td colspan="4" style="text-align: center;">No suppliers found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['name']); ?></td>
                            <td><?php echo htmlspecialchars($s['phone'] ?: '-'); ?></td>
                            <td style="color: <?php echo $s['total_balance'] > 0 ? 'red' : 'green'; ?>; font-weight: bold;">
                                रू<?php echo number_format($s['total_balance'], 2); ?>
                            </td>
                            <td>
                                <a href="supplier_details.php?id=<?php echo $s['id']; ?>" class="btn-edit" style="margin-right: 5px;">Ledger</a>
                                <a href="../controllers/supplierController.php?action=delete&id=<?php echo $s['id']; ?>" class="btn-delete" onclick="return confirm('Delete this supplier?');">Remove</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div id="supplierModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Supplier</h2>
                <button class="modal-close" onclick="closeModal('supplierModal')">&times;</button>
            </div>
            <form action="../controllers/supplierController.php" method="POST" class="modal-form">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label>Supplier Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('supplierModal')">Cancel</button>
                    <button type="submit" class="btn-submit">Save Supplier</button>
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
