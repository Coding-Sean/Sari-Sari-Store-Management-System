<!DOCTYPE html>
<html>
<head>
    <title>Monthly Sales Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Monthly Sales Report (<?= htmlspecialchars($year) ?>-<?= htmlspecialchars($month) ?>)</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sale ID</th><th>Customer</th><th>Date</th>
                <th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $row): ?>
            <tr>
                <td><?= $row['sale_id'] ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= $row['sale_date'] ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['subtotal'] ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>
</html>