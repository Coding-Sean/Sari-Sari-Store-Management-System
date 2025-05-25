<?php
require_once 'models/Sale.php';
require_once 'models/Product.php';

class SalesController {
    public function index() {
        $sales = Sale::getAll();
        include 'views/sales/index.php';
    }

    public function create() {
        $products = Product::getAll();
        include 'views/sales/create.php';
    }

    public function store() {
        Sale::add($_POST['product_id'], $_POST['quantity']);
        header('Location: index.php?controller=sales&action=index');
    }
}
?>