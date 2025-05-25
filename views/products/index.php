<?php
require_once __DIR__ . '/../config/database.php';

class Sale {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Record a sale and its details
    public function recordSale($customer_name, $total_amount, $products) {
        // Begin transaction
        $this->conn->beginTransaction();

        try {
            // 1. Insert into sales and get sale_id
            $stmt = $this->conn->prepare("CALL record_sale(?, ?)");
            $stmt->execute([$customer_name, $total_amount]);
            $sale_id = $stmt->fetch(PDO::FETCH_ASSOC)['sale_id'];

            // 2. Insert each sale detail and update product quantity
            foreach ($products as $prod) {
                $stmt2 = $this->conn->prepare("CALL add_sale_detail(?, ?, ?, ?)");
                $stmt2->execute([$sale_id, $prod['product_id'], $prod['quantity'], $prod['price']]);
            }

            $this->conn->commit();
            return $sale_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
?>