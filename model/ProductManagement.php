<?php
require_once 'config/database.php';

class Product {
    public static function getAll() {
        $db = (new Database())->getConnection();
        $stmt = $db->query("CALL GetAllProducts()");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("CALL GetProductById(:id)");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function add($name, $price, $stock) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("CALL AddProduct(:name, :price, :stock)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->execute();
    }

    public static function update($id, $name, $price, $stock) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("CALL UpdateProduct(:id, :name, :price, :stock)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->execute();
    }

    public static function delete($id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("CALL DeleteProduct(:id)");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
?>  