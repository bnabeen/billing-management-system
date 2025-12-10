document.addEventListener('DOMContentLoaded', () => {
    const productNameInput = document.querySelector('.pos-input[placeholder="Product Name / Barcode"]');
    const qtyInput = document.querySelector('.pos-input[placeholder="Qty"]');
    const priceInput = document.querySelector('.pos-input[placeholder="Price"]');
    const addBtn = document.querySelector('.pos-btn');
    const billPreview = document.querySelector('.bill-preview');
    const totalDisplay = document.querySelector('.bill-total span:last-child');
    const generateBtn = document.querySelector('.generate-btn');

    let billItems = [];

    // Initialize with some dummy data if needed, or start empty
    // billItems = [
    //     { name: 'Rice (5kg)', price: 450, qty: 1 },
    //     { name: 'Sugar (1kg)', price: 42, qty: 1 }
    // ];
    // For now, let's clear the static HTML items and start fresh or keep them?
    // The static HTML had items, let's clear them to be dynamic.
    billItems = [];
    renderBill();

    addBtn.addEventListener('click', () => {
        const name = productNameInput.value.trim();
        const qty = parseFloat(qtyInput.value);
        const price = parseFloat(priceInput.value);

        if (!name || isNaN(qty) || qty <= 0 || isNaN(price) || price < 0) {
            alert('Please enter valid product details.');
            return;
        }

        const total = qty * price;

        const newItem = {
            name: name,
            qty: qty,
            price: price,
            total: total
        };

        billItems.push(newItem);
        renderBill();
        clearInputs();
    });

    generateBtn.addEventListener('click', () => {
        if (billItems.length === 0) {
            alert('No items in the bill!');
            return;
        }

        const grandTotal = calculateGrandTotal();
        alert(`Bill Generated! Total Amount: ₹${grandTotal}\n(Printing functionality to be implemented)`);

        billItems = [];
        renderBill();
    });

    function renderBill() {
        // Clear current list except the total row (or just rebuild everything)
        // Let's keep the structure: .bill-item rows and then .bill-total

        // Remove all .bill-item elements
        const existingItems = billPreview.querySelectorAll('.bill-item');
        existingItems.forEach(item => item.remove());

        // We need to insert items BEFORE the .bill-total element
        const totalRow = billPreview.querySelector('.bill-total');

        billItems.forEach((item, index) => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'bill-item';
            itemDiv.innerHTML = `
                <span>${item.name} <small style="color:#666">x${item.qty}</small></span>
                <div style="display:flex; gap:10px; align-items:center;">
                    <span>₹${item.total}</span>
                    <span class="delete-btn" data-index="${index}" style="color:red; cursor:pointer; font-size:18px;">&times;</span>
                </div>
            `;
            billPreview.insertBefore(itemDiv, totalRow);
        });

        // Update Total
        const grandTotal = calculateGrandTotal();
        totalDisplay.textContent = `₹${grandTotal}`;

        // Add event listeners to delete buttons
        const deleteBtns = billPreview.querySelectorAll('.delete-btn');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = parseInt(e.target.getAttribute('data-index'));
                billItems.splice(index, 1);
                renderBill();
            });
        });
    }

    function calculateGrandTotal() {
        return billItems.reduce((sum, item) => sum + item.total, 0);
    }

    function clearInputs() {
        productNameInput.value = '';
        qtyInput.value = '';
        priceInput.value = '';
        productNameInput.focus();
    }
});
