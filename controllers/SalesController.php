<?php
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Product.php';

class SalesController {
    public function index() {
        $product = new Product();
        $products = $product->getAll();
        require __DIR__ . '/../views/sales/index.php';
    }

    public function record() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customer = $_POST['customer_name'];
            $cart = json_decode($_POST['cart'], true); // Expecting array of {product_id, quantity, price}
            $total = $_POST['total_amount'];

            $sale = new Sale();
            $sale->recordSale($customer, $total, $cart);

            header("Location: /?controller=sales&action=index&success=1");
        }
    }
}
?>