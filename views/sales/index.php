<!DOCTYPE html>
<html>
<head>
    <title>Sales Transaction</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Sales Transaction</h2>
    <form method="post" action="/?controller=sales&action=record">
        <label>Customer Name:</label>
        <input name="customer_name" required><br>
        <table class="table table-bordered">
            <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Add</th></tr></thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= $p['price'] ?></td>
                    <td><input type="number" min="0" value="0" id="qty<?=$p['product_id']?>" style="width: 60px;"></td>
                    <td><button type="button" onclick="addToCart(<?=$p['product_id']?>, '<?=htmlspecialchars($p['name'])?>', <?=$p['price']?>)">Add</button></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <h4>Cart</h4>
        <table class="table" id="cart-table">
            <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
            <tbody></tbody>
        </table>
        <input type="hidden" name="cart" id="cart-input">
        <input type="hidden" name="total_amount" id="total-amount-input">
        <button class="btn btn-success">Submit Sale</button>
    </form>
    <script>
    let cart = [];
    function addToCart(id, name, price) {
        let qty = parseInt(document.getElementById('qty'+id).value);
        if(qty > 0) {
            cart.push({product_id: id, name: name, price: price, quantity: qty});
            renderCart();
        }
    }
    function renderCart() {
        let tbody = document.querySelector("#cart-table tbody");
        tbody.innerHTML = '';
        let total = 0;
        cart.forEach(item => {
            let subtotal = item.price * item.quantity;
            total += subtotal;
            tbody.innerHTML += `<tr><td>${item.name}</td><td>${item.quantity}</td><td>${item.price}</td><td>${subtotal}</td></tr>`;
        });
        document.getElementById('cart-input').value = JSON.stringify(cart);
        document.getElementById('total-amount-input').value = total;
    }
    </script>
</body>
</html>