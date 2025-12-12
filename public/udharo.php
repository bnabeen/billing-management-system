<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$business_id = $_SESSION['business_id'];

// Fetch Customers
$customers = [];
$q = "SELECT * FROM udharo_customers WHERE business_id = '$business_id' ORDER BY total_debt DESC";
$res = mysqli_query($conn, $q);
if($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $customers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Udharo (Credit) - Kirana Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .debt-high { color: red; font-weight: bold; }
        .debt-low { color: green; }
        .trans-CREDIT { color: red; }
        .trans-PAYMENT { color: green; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php $page = 'udharo'; include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="products-header">
            <div style="display: flex; align-items: center; gap: 15px;">
                 <a href="dashboard.php" class="btn-back" style="text-decoration: none; color: #666; font-size: 24px;">&larr;</a>
                 <h1>Udharo Management</h1>
            </div>
            <button onclick="openAddCustomerModal()" class="btn-add">+ Add Customer</button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <?php if (empty($customers)): ?>
            <div class="empty-state">
                <h2>No customers found</h2>
                <p>Add customers to start tracking credit.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Total Debt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $cust): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cust['name']); ?></td>
                                <td><?php echo htmlspecialchars($cust['phone']); ?></td>
                                <td class="<?php echo $cust['total_debt'] > 0 ? 'debt-high' : 'debt-low'; ?>">
                                    ₹<?php echo number_format($cust['total_debt'], 2); ?>
                                </td>
                                <td>
                                    <button class="btn-edit" onclick="openTransactionModal(<?php echo $cust['id']; ?>, '<?php echo htmlspecialchars($cust['name']); ?>', 'CREDIT')">Give Credit</button>
                                    <button class="btn-delete" style="background-color: #2ecc71;" onclick="openTransactionModal(<?php echo $cust['id']; ?>, '<?php echo htmlspecialchars($cust['name']); ?>', 'PAYMENT')">Take Payment</button>
                                    <button class="btn-view" style="background-color: #3498db; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;" onclick="viewHistory(<?php echo $cust['id']; ?>, '<?php echo htmlspecialchars($cust['name']); ?>')">History</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

    <!-- Add Customer Modal -->
    <div id="addCustomerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Customer</h2>
                <button class="modal-close" onclick="closeModal('addCustomerModal')">&times;</button>
            </div>
            <form action="../controllers/udharoController.php" method="POST" class="modal-form">
                <input type="hidden" name="action" value="create_customer">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-submit">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaction Modal -->
    <div id="transactionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="transTitle">Record Transaction</h2>
                <button class="modal-close" onclick="closeModal('transactionModal')">&times;</button>
            </div>
            <form action="../controllers/udharoController.php" method="POST" class="modal-form">
                <input type="hidden" name="action" value="add_transaction">
                <input type="hidden" name="customer_id" id="transCustId">
                <input type="hidden" name="type" id="transType">
                
                <p id="transInfo" style="margin-bottom: 15px; font-weight: bold;"></p>

                <div class="form-group">
                    <label>Amount (₹)</label>
                    <input type="number" name="amount" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Description / Note</label>
                    <input type="text" name="description" placeholder="e.g. Rice, Oil, or Cash Payment">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-submit" id="transSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- History Modal -->
    <div id="historyModal" class="modal">
        <div class="modal-content" style="width: 600px;">
            <div class="modal-header">
                <h2 id="histTitle">History</h2>
                <button class="modal-close" onclick="closeModal('historyModal')">&times;</button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <table class="products-table" style="font-size: 0.9em;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Desc</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        <!-- Ajax Content -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        
        function openAddCustomerModal() {
            document.getElementById('addCustomerModal').style.display = 'flex';
        }

        function openTransactionModal(custId, name, type) {
            document.getElementById('transCustId').value = custId;
            document.getElementById('transType').value = type;
            document.getElementById('transInfo').innerText = type === 'CREDIT' ? 'Giving Credit to ' + name : 'Accepting Payment from ' + name;
            document.getElementById('transTitle').innerText = type === 'CREDIT' ? 'Add Credit' : 'Record Payment';
            document.getElementById('transSubmitBtn').style.backgroundColor = type === 'CREDIT' ? '#e74c3c' : '#2ecc71';
            document.getElementById('transactionModal').style.display = 'flex';
        }

        function viewHistory(custId, name) {
            document.getElementById('histTitle').innerText = 'History - ' + name;
            document.getElementById('historyModal').style.display = 'flex';
            document.getElementById('historyBody').innerHTML = '<tr><td colspan="4">Loading...</td></tr>';

            fetch('../controllers/udharoController.php?api=get_history&customer_id=' + custId)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('historyBody');
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4">No transactions found</td></tr>';
                } else {
                    data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleString();
                        const cls = 'trans-' + item.type;
                        tbody.innerHTML += `
                            <tr>
                                <td>${date}</td>
                                <td class="${cls}">${item.type}</td>
                                <td>${item.description || '-'}</td>
                                <td class="${cls}">₹${item.amount}</td>
                            </tr>
                        `;
                    });
                }
            });
        }

        // Close on escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal('addCustomerModal');
                closeModal('transactionModal');
                closeModal('historyModal');
            }
        });
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
