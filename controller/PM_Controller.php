<?php
require_once '../config/database.php';
require_once '../model/ProductManagement.php';

class ProductController {
    public function index() {
        $products = Product::getAll();
        include '../view/products/index.php';
    }

    public function create() {
        include '../view/products/create.php';
    }

    public function store() {
        Product::add($_POST['name'], $_POST['price'], $_POST['stock']);
        header('Location: ../index.php?controller=product&action=index');
    }

    public function edit() {
        $product = Product::getById($_GET['id']);
        include '../view/products/edit.php';
    }

    public function update() {
        Product::update($_POST['id'], $_POST['name'], $_POST['price'], $_POST['stock']);
        header('Location: ../index.php?controller=product&action=index');
    }

    public function delete() {
        Product::delete($_GET['id']);
        header('Location: ../index.php?controller=product&action=index');
    }
}
?>