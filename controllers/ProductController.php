<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    public function index() {
        $product = new Product();
        $products = $product->getAll();
        require __DIR__ . '/../views/products/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $p = new Product();
            $p->add($_POST['name'], $_POST['description'], $_POST['price'], $_POST['quantity']);
            header("Location: /?controller=product&action=index");
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $p = new Product();
            $p->update($_POST['id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['quantity']);
            header("Location: /?controller=product&action=index");
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $p = new Product();
            $p->delete($_GET['id']);
            header("Location: /?controller=product&action=index");
        }
    }
}
?>