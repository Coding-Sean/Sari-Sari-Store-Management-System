<?php
require_once "../config/database.php";
require_once "../model/ProductManagement.php";

$db = new Database();
$conn = $db->getConnection();
$productModel = new ProductManagement($conn);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        // Example: expects POST data
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $quantity = $_POST['quantity'] ?? 0;
        $productModel->addProduct($name, $price, $quantity);
        break;
    case 'update':
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $quantity = $_POST['quantity'] ?? 0;
        $productModel->updateProduct($id, $name, $price, $quantity);
        break;
    case 'delete':
        $id = $_POST['id'] ?? 0;
        $productModel->deleteProduct($id);
        break;
    case 'list':
    default:
        $products = $productModel->getProducts();
        // You can include a view here to display $products
        break;
}
?>