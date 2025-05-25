<?php
require_once 'config/database.php';

class Sale {
    public static function getAll() {
        $db = (new Database())->getConnection();
        $stmt = $db->query("CALL GetAllSales()");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function add($product_id, $quantity) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("CALL AddSale(:product_id, :quantity)");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();
    }
}
?>