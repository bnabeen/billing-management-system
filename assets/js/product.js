// Product validation logic
document.addEventListener('DOMContentLoaded', () => {
    const productForm = document.getElementById('productForm');

    if (productForm) {
        productForm.addEventListener('submit', (e) => {
            const name = document.getElementById('product_name')?.value.trim();
            const price = parseFloat(document.getElementById('product_price')?.value);
            const stock = parseInt(document.getElementById('product_stock')?.value);

            let errors = [];

            if (!name || name.length < 2) {
                errors.push('Product name must be at least 2 characters long.');
            }

            if (isNaN(price) || price <= 0) {
                errors.push('Price must be a positive number.');
            }

            if (isNaN(stock) || stock < 0) {
                errors.push('Stock must be a non-negative number.');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errors.join('\n'));
            }
        });
    }

    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-product');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to delete this product?')) {
                e.preventDefault();
            }
        });
    });
});
