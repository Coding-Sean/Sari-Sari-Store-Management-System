<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->prepare("CALL get_all_products()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($name, $description, $price, $quantity) {
        $stmt = $this->conn->prepare("CALL add_product(?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $quantity]);
    }

    public function update($id, $name, $description, $price, $quantity) {
        $stmt = $this->conn->prepare("CALL update_product(?, ?, ?, ?, ?)");
        $stmt->execute([$id, $name, $description, $price, $quantity]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("CALL delete_product(?)");
        $stmt->execute([$id]);
    }
}
?>