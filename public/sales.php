<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

// Fetch Products for Dropdown
$business_id = $_SESSION['business_id'] ?? 0; // Should be set if schema update worked and login flows through
// Fallback if business_id not set (older login session), force relogin or handle gracefully
if (!$business_id) {
    // Attempt to fetch from user if not in session (temporary fix)
    $u_id = $_SESSION['user_id'];
    $q = "SELECT business_id, role FROM users WHERE id = '$u_id'";
    $r = mysqli_query($conn, $q);
    if ($r && mysqli_num_rows($r) > 0) {
        $row = mysqli_fetch_assoc($r);
        $_SESSION['business_id'] = $row['business_id'];
        $_SESSION['role'] = $row['role'];
        $business_id = $row['business_id'];
    }
}

$query_products = "SELECT * FROM products WHERE business_id = '$business_id'";
$result_products = mysqli_query($conn, $query_products);
$products = [];
while ($row = mysqli_fetch_assoc($result_products)) {
    $products[] = $row;
}

// Fetch Sales History
$query_sales = "SELECT s.*, (SELECT COUNT(*) FROM sale_items si WHERE si.sale_id = s.id) as item_count 
                FROM sales s 
                WHERE s.business_id = '$business_id' 
                ORDER BY s.sale_date DESC LIMIT 50";
$result_sales = mysqli_query($conn, $query_sales);
$sales_list = [];
if ($result_sales) {
    while ($row = mysqli_fetch_assoc($result_sales)) {
        $sales_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales - Kirana Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        /* Specific Styles for Sales Page */
        .sale-items-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.9rem; }
        .sale-items-table th, .sale-items-table td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        .sale-items-table th { background: #f8f9fa; }
        .remove-item { color: red; cursor: pointer; text-decoration: underline; }
        .total-row { font-weight: bold; text-align: right; }
        .modal-body { max-height: 80vh; overflow-y: auto; padding-right: 10px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php $page = 'sales'; include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="products-header">
            <div style="display: flex; align-items: center; gap: 15px;">
                 <a href="dashboard.php" class="btn-back" style="text-decoration: none; color: #666; font-size: 24px;">&larr;</a>
                 <h1>Sales Management</h1>
            </div>
            <button onclick="openNewSaleModal()" class="btn-add">+ New Sale</button>
        </div>

        <!-- Sales List -->
        <?php if (empty($sales_list)): ?>
            <div class="empty-state">
                <h2>No sales recorded yet</h2>
                <p>Create your first sale bill</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales_list as $sale): ?>
                            <tr>
                                <td><?php echo date('d M Y, h:i A', strtotime($sale['sale_date'])); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($sale['customer_name'] ?: 'Walk-in Customer'); ?>
                                    <?php if($sale['customer_phone']): ?>
                                        <br><small><?php echo htmlspecialchars($sale['customer_phone']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $sale['item_count']; ?> items</td>
                                <td class="price">₹<?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td>
                                    <!-- View/Delete Actions -->
                                    <a href="../controllers/salesController.php?action=delete&id=<?php echo $sale['id']; ?>" class="btn-delete" onclick="return confirm('Delete this sale record?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- New Sale Modal -->
    <div id="saleModal" class="modal">
        <div class="modal-content" style="width: 800px; max-width: 95%;">
            <div class="modal-header">
                <h2>New Sale Bill</h2>
                <button class="modal-close" onclick="closeSaleModal()">&times;</button>
            </div>
            
            <form id="saleForm" action="../controllers/salesController.php" method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="modal-body">
                    <!-- Customer Details -->
                    <div class="row">
                        <div class="form-group" style="flex:1">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" placeholder="Walk-in Customer">
                        </div>
                        <div class="form-group" style="flex:1">
                            <label>Phone Number</label>
                            <input type="text" name="customer_phone" placeholder="Optional">
                        </div>
                    </div>

                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

                    <!-- Add Item Section -->
                    <div style="background: #f9f9f9; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                        <h4>Add Item</h4>
                        <div class="row" style="align-items: flex-end;">
                            <div class="form-group" style="flex: 2;">
                                <label>Product</label>
                                <select id="productSelect" onchange="updatePrice()">
                                    <option value="">Select Product...</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>" data-name="<?php echo htmlspecialchars($p['name']); ?>">
                                            <?php echo htmlspecialchars($p['name']); ?> (Default: ₹<?php echo $p['price']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label>Price</label>
                                <input type="number" id="itemPrice" step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label>Qty</label>
                                <input type="number" id="itemQty" value="1" min="1">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn-add" onclick="addItem()" style="margin-bottom: 2px;">Add</button>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <table class="sale-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="width: 80px;">Price</th>
                                <th style="width: 60px;">Qty</th>
                                <th style="width: 80px;">Subtotal</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Items will be added here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="total-row">Grand Total:</td>
                                <td class="total-row" id="grandTotal">₹0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                     <!-- Hidden Inputs for Items -->
                    <div id="hiddenInputs"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeSaleModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Bill</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let cart = [];
        const modal = document.getElementById('saleModal');

        function openNewSaleModal() {
            modal.style.display = 'flex';
            cart = [];
            renderCart();
        }

        function closeSaleModal() {
            modal.style.display = 'none';
        }

        function updatePrice() {
            const select = document.getElementById('productSelect');
            const option = select.options[select.selectedIndex];
            if (option && option.dataset.price) {
                document.getElementById('itemPrice').value = option.dataset.price;
            }
        }

        function addItem() {
            const select = document.getElementById('productSelect');
            const productId = select.value;
            const productName = select.options[select.selectedIndex].dataset.name;
            const price = parseFloat(document.getElementById('itemPrice').value);
            const qty = parseInt(document.getElementById('itemQty').value);

            if (!productId || isNaN(price) || isNaN(qty) || qty <= 0) {
                alert("Please select a valid product, price, and quantity.");
                return;
            }

            // Check if exists
            const existing = cart.find(item => item.id === productId);
            if (existing) {
                existing.qty += qty;
                existing.price = price; // Update price to latest
            } else {
                cart.push({ id: productId, name: productName, price: price, qty: qty });
            }

            renderCart();
            
            // Reset fields
            select.value = "";
            document.getElementById('itemPrice').value = "";
            document.getElementById('itemQty').value = "1";
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            const tbody = document.getElementById('itemsBody');
            const hiddenDiv = document.getElementById('hiddenInputs');
            const totalEl = document.getElementById('grandTotal');
            
            tbody.innerHTML = "";
            hiddenDiv.innerHTML = "";
            
            let total = 0;

            cart.forEach((item, index) => {
                const subtotal = item.price * item.qty;
                total += subtotal;

                // Table Row
                const row = `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.price.toFixed(2)}</td>
                        <td>${item.qty}</td>
                        <td>${subtotal.toFixed(2)}</td>
                        <td><span class="remove-item" onclick="removeItem(${index})">&times;</span></td>
                    </tr>
                `;
                tbody.innerHTML += row;

                // Hidden Inputs
                hiddenDiv.innerHTML += `
                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
                    <input type="hidden" name="items[${index}][price]" value="${item.price}">
                `;
            });

            totalEl.innerText = "₹" + total.toFixed(2);
        }
        
        // Keydown Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closeSaleModal();
        });
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
