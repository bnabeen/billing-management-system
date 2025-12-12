<?php
require_once '../config/db.php';
require_once '../models/Product.php';
require_once '../includes/auth.php';

$product = new Product($conn, $_SESSION['business_id']);
$products_list = $product->getAll();

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['success']);
unset($_SESSION['error']);

$pageTitle = "Products - Kirana Store";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Kirana Store</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Navbar -->
    <?php $page = 'products'; include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="products-header">
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="dashboard.php" class="btn-back" style="text-decoration: none; color: #666; font-size: 24px;">&larr;</a>
                <h1>Products</h1>
            </div>
            <button id="openAddProductModal" class="btn-add">+ Add Product</button>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Stats Section -->
         <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total Products</div>
                <div class="stat-value"><?php echo count($products_list); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Value</div>
                <div class="stat-value">₹<?php echo number_format(array_sum(array_map(function($p) { return $p['price'] * $p['stock']; }, $products_list)), 2); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-value" style="color: #e74c3c;"><?php echo count(array_filter($products_list, function($p) { return $p['stock'] <= 10; })); ?></div>
            </div>
        </div>

        <?php if (empty($products_list)): ?>
            <div class="empty-state">
                <h2>No products yet</h2>
                <p>Start by adding your first product</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Alert Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products_list as $prod): ?>
                            <tr>
                                <td class="product-name"><?php echo htmlspecialchars($prod['name']); ?></td>
                                <td><span class="category"><?php echo htmlspecialchars($prod['category']); ?></span></td>
                                <td class="price">₹<?php echo number_format($prod['price'], 2); ?></td>
                                <td class="<?php echo $prod['stock'] <= ($prod['alert_stock'] ?? 10) ? 'stock-low' : 'stock-ok'; ?>">
                                    <?php echo intval($prod['stock']); ?> units
                                    <?php if ($prod['stock'] <= ($prod['alert_stock'] ?? 10)): ?>
                                        <span style="background: #fee; color: #c33; padding: 2px 8px; border-radius: 3px; margin-left: 5px; font-size: 11px;">Low</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo intval($prod['alert_stock'] ?? 5); ?></td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-edit" 
                                            onclick="openEditProductModal(this)"
                                            data-id="<?php echo $prod['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($prod['name']); ?>"
                                            data-category="<?php echo htmlspecialchars($prod['category']); ?>"
                                            data-price="<?php echo floatval($prod['price']); ?>"
                                            data-stock="<?php echo intval($prod['stock']); ?>"
                                            data-alert="<?php echo intval($prod['alert_stock'] ?? 5); ?>"
                                            data-barcode="<?php echo htmlspecialchars($prod['barcode'] ?? ''); ?>"
                                        >Edit</button>
                                        <a href="../controllers/productController.php?action=delete&id=<?php echo intval($prod['id']); ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Modal (Add/Edit) -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Product</h2>
                <button class="modal-close" onclick="closeProductModal()">&times;</button>
            </div>
            <form id="productForm" action="../controllers/productController.php" method="POST" class="modal-form">
                <input type="hidden" id="action" name="action" value="create">
                <input type="hidden" id="productId" name="id" value="">

                <div class="form-group">
                    <label for="product-name">Product Name *</label>
                    <input type="text" id="product-name" name="name" required placeholder="Enter product name">
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="product-category">Category *</label>
                        <select id="product-category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Grains">Grains</option>
                            <option value="Groceries">Groceries</option>
                            <option value="Oils">Oils</option>
                            <option value="Beverages">Beverages</option>
                            <option value="Dairy">Dairy</option>
                            <option value="Bakery">Bakery</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="product-price">Price (₹) *</label>
                        <input type="number" id="product-price" name="price" required placeholder="0.00" step="0.01" min="0">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="product-stock">Stock Quantity *</label>
                        <input type="number" id="product-stock" name="stock" required placeholder="0" min="0">
                    </div>

                    <div class="form-group">
                        <label for="product-alert">Low Stock Alert (Qty)</label>
                        <input type="number" id="product-alert" name="alert_stock" placeholder="5" min="0" value="5">
                    </div>
                </div>
                 <!-- Hidden barcode field if needed, or just remove it if strictly replacing. I'll keep it hidden just in case schema requires it or logic does. -->
                 <input type="hidden" id="product-barcode" name="barcode" value="">

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeProductModal()">Cancel</button>
                    <button type="submit" id="submitBtn" class="btn-submit">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/product.js"></script>
    <script>
        const productModal = document.getElementById('productModal');
        const openAddProductModalBtn = document.getElementById('openAddProductModal');
        const productForm = document.getElementById('productForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const actionInput = document.getElementById('action');
        const idInput = document.getElementById('productId');

        function openAddProductModal() {
            // Reset form for clean state
            productForm.reset();
            actionInput.value = 'create';
            idInput.value = '';
            modalTitle.textContent = 'Add New Product';
            submitBtn.textContent = 'Add Product';
            document.getElementById('product-alert').value = 5; // Default

            productModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function openEditProductModal(btn) {
            // Populate form with existing data
            const data = btn.dataset;
            
            document.getElementById('product-name').value = data.name;
            document.getElementById('product-category').value = data.category;
            document.getElementById('product-price').value = data.price;
            document.getElementById('product-stock').value = data.stock;
            document.getElementById('product-alert').value = data.alert;
            document.getElementById('product-barcode').value = data.barcode;

            // Set Form to Update Mode
            actionInput.value = 'update';
            idInput.value = data.id;
            modalTitle.textContent = 'Edit Product';
            submitBtn.textContent = 'Update Product';

            productModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeProductModal() {
            productModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        if(openAddProductModalBtn) {
            openAddProductModalBtn.addEventListener('click', openAddProductModal);
        }

        // Close modal when clicking outside
        productModal.addEventListener('click', function(event) {
            if (event.target === productModal) {
                closeProductModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && productModal.style.display === 'flex') {
                closeProductModal();
            }
        });
    </script>

<?php require_once '../includes/footer.php'; ?>
