<div class="receipt-container">
    <div class="text-center mb-4">
        <h3 class="mb-2">🏪 Sari-Sari Store</h3>
        <p class="mb-1"><strong>Official Receipt</strong></p>
        <p class="mb-1"><?= date('M d, Y H:i:s', strtotime($data['date'])) ?></p>
        <p class="mb-0">Receipt #: <?= $receiptNumber ?></p>
    </div>
    
    <div class="mb-3">
        <div class="row">
            <div class="col-6">
                <p class="mb-1"><strong>Customer:</strong></p>
                <p class="mb-0"><?= htmlspecialchars($data['customer_name']) ?></p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-1"><strong>Sale ID:</strong></p>
                <p class="mb-0">#<?= $data['sale_id'] ?></p>
            </div>
        </div>
    </div>

    <hr>

    <div class="mb-3">
        <table class="table table-sm">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalItems = 0;
                $totalQuantity = 0;
                foreach ($data['items'] as $item): 
                    $subtotal = floatval($item['quantity']) * floatval($item['unit_price']);
                    $totalItems++;
                    $totalQuantity += intval($item['quantity']);
                ?>
                <tr>
                    <td>
                        <div>
                            <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                            <br><small class="text-muted"><?= htmlspecialchars($item['category_name']) ?></small>
                        </div>
                    </td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-end">₱<?= number_format(floatval($item['unit_price']), 2) ?></td>
                    <td class="text-end">₱<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <div class="mb-3">
        <div class="row">
            <div class="col-6">
                <p class="mb-1">Total Items: <?= $totalItems ?></p>
                <p class="mb-0">Total Quantity: <?= $totalQuantity ?></p>
            </div>
            <div class="col-6">
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span>₱<?= number_format($data['total_amount'], 2) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Tax (0%):</span>
                    <span>₱0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Total:</strong>
                    <strong>₱<?= number_format($data['total_amount'], 2) ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Cash Payment:</span>
                    <span>₱<?= number_format($data['payment_amount'], 2) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <strong>Change:</strong>
                    <strong class="text-success">₱<?= number_format($data['payment_amount'] - $data['total_amount'], 2) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="text-center mt-4">
        <p class="mb-2"><strong>Thank you for shopping with us!</strong></p>
        <p class="mb-1">Please come again.</p>
        <small class="text-muted">Visit us daily for fresh products!</small>
    </div>
</div>