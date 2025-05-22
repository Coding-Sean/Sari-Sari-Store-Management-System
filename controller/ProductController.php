<?php
require_once 'models/Product.php';

class ProductController {
    private $product;
    
    public function __construct() {
        $this->product = new Product();
    }
    
    public function index() {
        $products = $this->product->getAll();
        require_once 'views/products/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock_quantity = $_POST['stock_quantity'];
            $category = $_POST['category'];
            
            $result = $this->product->create($name, $description, $price, $stock_quantity, $category);
            
            if ($result) {
                header('Location: index.php?page=products&success=Product added successfully');
            } else {
                header('Location: index.php?page=products&error=Failed to add product');
            }
            exit;
        }
        require_once 'views/products/create.php';
    }
    
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock_quantity = $_POST['stock_quantity'];
            $category = $_POST['category'];
            
            $result = $this->product->update($id, $name, $description, $price, $stock_quantity, $category);
            
            if ($result) {
                header('Location: index.php?page=products&success=Product updated successfully');
            } else {
                header('Location: index.php?page=products&error=Failed to update product');
            }
            exit;
        }
        
        $product = $this->product->getById($id);
        require_once 'views/products/edit.php';
    }
    
    public function delete($id) {
        $result = $this->product->delete($id);
        
        if ($result) {
            header('Location: index.php?page=products&success=Product deleted successfully');
        } else {
            header('Location: index.php?page=products&error=Failed to delete product');
        }
        exit;
    }
    
    public function getProduct($id) {
        return $this->product->getById($id);
    }
    
    public function getAllProducts() {
        return $this->product->getAll();
    }
}
?>