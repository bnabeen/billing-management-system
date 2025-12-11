<?php
require_once '../config/db.php';
require_once '../models/Product.php';
require_once '../includes/auth.php';

$product = new Product($conn);
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['error'] = "Invalid product ID";
    header("Location: products.php");
    exit;
}

$prod = $product->getById($id);
if (!$prod) {
    $_SESSION['error'] = "Product not found";
    header("Location: products.php");
    exit;
}

$categories = $product->getCategories();
$errors = $_SESSION['errors'] ?? [];
$error = $_SESSION['error'] ?? '';

unset($_SESSION['errors']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Sumit Kirana Store</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Navbar -->
    <nav class="top-navbar">
        <div class="navbar-brand">Sumit Kirana Store</div>
        <div class="navbar-menu">
            <a href="dashboard.php" class="navbar-item">Home</a>
            <a href="sales.php" class="navbar-item">Sales</a>
            <a href="products.php" class="navbar-item">Products</a>
            <a href="udharo.php" class="navbar-item">Udharo</a>
            <a href="logout.php" class="navbar-item logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h1>Edit Product</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="../controllers/productController.php" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo intval($prod['id']); ?>">

                <div class="row">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required placeholder="Enter product name" value="<?php echo htmlspecialchars($prod['name']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Grains" <?php echo $prod['category'] === 'Grains' ? 'selected' : ''; ?>>Grains</option>
                            <option value="Groceries" <?php echo $prod['category'] === 'Groceries' ? 'selected' : ''; ?>>Groceries</option>
                            <option value="Oils" <?php echo $prod['category'] === 'Oils' ? 'selected' : ''; ?>>Oils</option>
                            <option value="Beverages" <?php echo $prod['category'] === 'Beverages' ? 'selected' : ''; ?>>Beverages</option>
                            <option value="Dairy" <?php echo $prod['category'] === 'Dairy' ? 'selected' : ''; ?>>Dairy</option>
                            <option value="Bakery" <?php echo $prod['category'] === 'Bakery' ? 'selected' : ''; ?>>Bakery</option>
                            <option value="Snacks" <?php echo $prod['category'] === 'Snacks' ? 'selected' : ''; ?>>Snacks</option>
                            <option value="Other" <?php echo $prod['category'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="price">Price (₹) *</label>
                        <input type="number" id="price" name="price" required placeholder="0.00" step="0.01" min="0" value="<?php echo floatval($prod['price']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" required placeholder="0" min="0" value="<?php echo intval($prod['stock']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="barcode">Barcode (Optional)</label>
                    <input type="text" id="barcode" name="barcode" placeholder="Enter barcode" value="<?php echo htmlspecialchars($prod['barcode'] ?? ''); ?>">
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit">Update Product</button>
                    <a href="products.php" class="btn-cancel" style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
