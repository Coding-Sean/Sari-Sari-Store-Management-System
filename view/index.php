<?php
// index.php
// This is a simple Sari-Sari Store web application using HTML, CSS, and JavaScript.    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sari-Sari Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/style.css">
</head>
<body>
    <div class="header">
        <img src="https://cdn-icons-png.flaticon.com/512/3075/3075977.png" alt="Store Icon">
        <span style="font-size:2rem;font-weight:700;">Sari-Sari Store</span>
    </div>
    <div class="nav-btns">
        <button onclick="showContent('products')">PRODUCTS</button>
        <button onclick="showContent('cart')">CART</button>
        <button onclick="showContent('transactions')">TRANSACTIONS</button>
        <button onclick="showContent('sales')">SALES</button>
    </div>
    <div class="main-content" id="main-content">
        Welcome to Sari-Sari Store!
    </div>
    <script>
        // Example products data, replace with PHP if available
        const products = [
            { id: 1, name: "Softdrinks", price: 20 },
            { id: 2, name: "Chips", price: 15 },
            { id: 3, name: "Candy", price: 2 },
            { id: 4, name: "Bread", price: 10 }
        ];
        let cart = [];

        function showContent(section) {
            let content = '';
            if (section === 'products') {
                content = `<h5>Products</h5>
                <ul class="product-list">` +
                products.map(p => `
                    <li>
                        <span>${p.name} - ₱${p.price}</span>
                        <button onclick="addToCart(${p.id})">Add</button>
                    </li>
                `).join('') +
                `</ul>`;
            } else if (section === 'cart') {
                if (cart.length === 0) {
                    content = `<h5>Your Cart is empty.</h5>`;
                } else {
                    content = `<h5>Your Cart</h5>
                    <ul class="cart-list">` +
                    cart.map((item, idx) => `
                        <li>
                            <span>${item.name} - ₱${item.price}</span>
                            <button onclick="removeFromCart(${idx})">Remove</button>
                        </li>
                    `).join('') +
                    `</ul>
                    <div><b>Total: ₱${cart.reduce((sum, item) => sum + item.price, 0)}</b></div>`;
                }
            } else if (section === 'transactions') {
                content = `<h5>Transactions</h5>
                <table>
                    <tr><th>Date</th><th>Items</th><th>Total</th></tr>
                    <tr><td>2025-05-25</td><td>Softdrinks, Chips</td><td>₱35</td></tr>
                    <tr><td>2025-05-24</td><td>Bread, Candy</td><td>₱12</td></tr>
                </table>`;
            } else if (section === 'sales') {
                content = `<h5>Sales Report</h5>
                <table>
                    <tr><th>Date</th><th>Sales</th></tr>
                    <tr><td>2025-05-25</td><td>₱120</td></tr>
                    <tr><td>2025-05-24</td><td>₱80</td></tr>
                </table>`;
            } else {
                content = 'Welcome to Sari-Sari Store!';
            }
            document.getElementById('main-content').innerHTML = content;
        }

        function addToCart(id) {
            const product = products.find(p => p.id === id);
            if (product) {
                cart.push(product);
                showContent('cart');
            }
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            showContent('cart');
        }

        // Expose functions to global scope for inline onclick
        window.showContent = showContent;
        window.addToCart = addToCart;
        window.removeFromCart = removeFromCart;
    </script>
</body>
</html>
