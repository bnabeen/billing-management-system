// Product validation logic
document.addEventListener('DOMContentLoaded', () => {
    const productForm = document.getElementById('productForm');

    if (productForm) {
        productForm.addEventListener('submit', (e) => {
            const name = document.getElementById('product-name')?.value.trim();
            const price = parseFloat(document.getElementById('product-price')?.value);
            const stock = parseInt(document.getElementById('product-stock')?.value);
            const alertStock = parseInt(document.getElementById('product-alert')?.value);

            let errors = [];

            if (!name || name.length < 2) {
                errors.push('Product name must be at least 2 characters long.');
            }

            if (isNaN(price) || price <= 0) {
                errors.push('Price must be a positive number.');
            }

            if (isNaN(stock) || stock < 0) {
                errors.push('Stock cannot be negative.');
            }

            if (isNaN(alertStock) || alertStock < 0) {
                errors.push('Alert stock cannot be negative.');
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errors.join('\n'));
            }
        });
    }
});
