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

// Fetch Udharo Customers
$udharo_customers = [];
$q_uc = "SELECT * FROM udharo_customers WHERE business_id = '$business_id' ORDER BY name ASC";
$r_uc = mysqli_query($conn, $q_uc);
while ($row = mysqli_fetch_assoc($r_uc)) {
    $udharo_customers[] = $row;
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
                            <tr onclick="window.location.href='bill_view.php?id=<?php echo $sale['id']; ?>'" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='white'">
                                <td><?php echo date('d M Y, h:i A', strtotime($sale['sale_date'])); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($sale['customer_name'] ?: 'Walk-in Customer'); ?>
                                    <?php if($sale['customer_phone']): ?>
                                        <br><small><?php echo htmlspecialchars($sale['customer_phone']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $sale['item_count']; ?> items</td>
                                <td class="price">रू<?php echo number_format($sale['total_amount'], 2); ?></td>
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
        <div class="modal-content" style="width: 1000px; max-width: 98%; height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header">
                <h2>Point of Sale (POS)</h2>
                <button class="modal-close" onclick="closeSaleModal()">&times;</button>
            </div>
            
            <form id="saleForm" action="../controllers/salesController.php" method="POST" style="flex: 1; display: flex; overflow: hidden;">
                <input type="hidden" name="action" value="create">
                
                <!-- Left Side: Product Selection -->
                <div style="flex: 2; padding: 20px; border-right: 1px solid #eee; overflow-y: auto; background: #fcfcfc;">
                    <div class="form-group">
                        <label>Search Products (Name or Barcode)</label>
                        <input type="text" id="posSearch" placeholder="Type to search..." onkeyup="filterPOSProducts()" style="padding: 12px; font-size: 16px;">
                    </div>

                    <div id="posProductGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-top: 15px;">
                        <?php foreach ($products as $p): ?>
                            <div class="pos-item" 
                                 onclick="addItemByID('<?php echo $p['id']; ?>', '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>)"
                                 data-name="<?php echo strtolower($p['name']); ?>"
                                 data-barcode="<?php echo strtolower($p['barcode'] ?? ''); ?>"
                                 style="background: white; border: 1px solid #ddd; padding: 10px; border-radius: 8px; cursor: pointer; text-align: center; transition: 0.2s;">
                                <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;"><?php echo htmlspecialchars($p['name']); ?></div>
                                <div style="color: #4834d4; font-weight: 800;">रू<?php echo number_format($p['price'], 2); ?></div>
                                <div style="font-size: 11px; color: <?php echo $p['stock'] < 10 ? 'red' : '#666'; ?>;">Stock: <?php echo $p['stock']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Right Side: Cart & Payment -->
                <div style="flex: 1.5; padding: 20px; display: flex; flex-direction: column;">
                    <div style="flex: 1; overflow-y: auto; margin-bottom: 20px;">
                        <h4 style="margin-top:0">Shopping Cart</h4>
                        <table class="sale-items-table" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th style="width: 50px;">Qty</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                        <div id="hiddenInputs"></div>
                    </div>

                    <div style="border-top: 2px solid #eee; padding-top: 15px;">
                        
                        <!-- Subtotal -->
                        <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 5px;">
                            <span style="color: #666;">Subtotal:</span>
                            <span id="subTotal">रू0.00</span>
                        </div>

                        <!-- Discount -->
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px; margin-bottom: 10px;">
                            <span style="color: #666;">Discount (रू):</span>
                            <input type="number" name="discount" id="discountInput" value="0" min="0" step="1" 
                                   style="width: 80px; padding: 5px; text-align: right; border: 1px solid #ddd; border-radius: 4px;"
                                   oninput="renderCart()">
                        </div>

                        <!-- Grand Total -->
                        <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 900; margin-bottom: 15px;">
                            <span>Grand Total:</span>
                            <span id="grandTotal" style="color: #4834d4;">रू0.00</span>
                        </div>
                        
                        <input type="hidden" name="final_total" id="finalTotalInput">

                        <!-- Customer Section -->
                        <div class="form-group" style="background: #f8f9fa; padding: 10px; border-radius: 8px;">
                            <label style="font-size: 12px; color: #666;">Customer (Optional for Cash)</label>
                            
                            <!-- Search/Select Existing -->
                            <select name="udharo_customer_id" id="udharoCustomer" onchange="onCustomerSelect()" style="padding: 8px; font-size: 13px; margin-bottom: 10px;">
                                <option value="">-- Select Existing Customer --</option>
                                <?php foreach ($udharo_customers as $uc): ?>
                                    <option value="<?php echo $uc['id']; ?>" data-name="<?php echo htmlspecialchars($uc['name']); ?>" data-phone="<?php echo $uc['phone']; ?>">
                                        <?php echo htmlspecialchars($uc['name']); ?> (<?php echo $uc['phone']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- New/Walk-in Inputs -->
                            <div class="row" style="gap: 5px;">
                                <input type="text" name="customer_name" id="custName" placeholder="Name (New/Walk-in)" style="flex: 1; padding: 8px;">
                                <input type="text" name="customer_phone" id="custPhone" placeholder="Phone" style="flex: 1; padding: 8px;">
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" id="paymentMethod" style="padding: 10px; font-weight: bold; width: 100%;">
                                <option value="cash">Cash Payment</option>
                                <option value="credit">Udharo (Credit)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; font-size: 18px; font-weight: bold; background: #10ac84;">Generate Bill</button>
                    </div>
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
            document.getElementById('discountInput').value = 0;
            document.getElementById('udharoCustomer').value = "";
            document.getElementById('custName').value = "";
            document.getElementById('custPhone').value = "";
            renderCart();
            document.getElementById('posSearch').focus();
        }

        function closeSaleModal() {
            modal.style.display = 'none';
        }

        function filterPOSProducts() {
            const query = document.getElementById('posSearch').value.toLowerCase();
            const items = document.querySelectorAll('.pos-item');
            items.forEach(item => {
                const name = item.dataset.name;
                const barcode = item.dataset.barcode;
                if (name.includes(query) || barcode.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function addItemByID(id, name, price) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ id: id, name: name, price: price, qty: 1 });
            }
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            const tbody = document.getElementById('itemsBody');
            const hiddenDiv = document.getElementById('hiddenInputs');
            const subTotalEl = document.getElementById('subTotal');
            const grandTotalEl = document.getElementById('grandTotal');
            const discountInput = document.getElementById('discountInput');
            
            tbody.innerHTML = "";
            hiddenDiv.innerHTML = "";
            
            let total = 0;

            cart.forEach((item, index) => {
                const subtotal = item.price * item.qty;
                total += subtotal;

                const row = `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.price.toFixed(2)}</td>
                        <td>
                            <input type="number" value="${item.qty}" min="1" 
                                   style="width: 50px; padding: 2px;" 
                                   onchange="updateQty(${index}, this.value)">
                        </td>
                        <td>${subtotal.toFixed(2)}</td>
                        <td><span class="remove-item" onclick="removeItem(${index})">&times;</span></td>
                    </tr>
                `;
                tbody.innerHTML += row;

                hiddenDiv.innerHTML += `
                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
                    <input type="hidden" name="items[${index}][price]" value="${item.price}">
                `;
            });

            // Calculate Totals
            let discount = parseFloat(discountInput.value) || 0;
            let finalTotal = total - discount;
            if (finalTotal < 0) finalTotal = 0;

            subTotalEl.innerText = "रू" + total.toFixed(2);
            grandTotalEl.innerText = "रू" + finalTotal.toFixed(2);
            
            // Set hidden field for total if backend needs it, or backend handles it?
            // Controller expects total to be calculated from items, but now we have discount.
            // We should pass discount to controller.
            // I'll add a hidden input for discount just in case, but the input name="discount" handles it.
        }

        function updateQty(index, val) {
            const qty = parseInt(val);
            if (qty > 0) {
                cart[index].qty = qty;
                renderCart();
            }
        }

        function onCustomerSelect() {
            const select = document.getElementById('udharoCustomer');
            const nameInput = document.getElementById('custName');
            const phoneInput = document.getElementById('custPhone');
            
            const selectedOption = select.options[select.selectedIndex];
            
            if (select.value) {
                nameInput.value = selectedOption.getAttribute('data-name');
                phoneInput.value = selectedOption.getAttribute('data-phone');
                nameInput.readOnly = true;
                // phoneInput.readOnly = true; // Maybe allow editing phone? Better not if it's linked to ID.
            } else {
                nameInput.value = "";
                phoneInput.value = "";
                nameInput.readOnly = false;
                phoneInput.readOnly = false;
            }
        }
        
        // Keydown Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closeSaleModal();
        });
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
