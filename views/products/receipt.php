 mk m<?php
function generateReceipt($data) {
    $change = $data['payment_amount'] - $data['total_amount'];
    $receiptNumber = str_pad($data['sale_id'], 8, '0', STR_PAD_LEFT);

    
    ob_start();
    include __DIR__ . '/receipt_template.php';
    return ob_get_clean();
}
