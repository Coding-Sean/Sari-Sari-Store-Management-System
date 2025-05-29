<?php
require_once __DIR__ . '/../config/database.php';

class SaleModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function createSale($data) {
        try {
            if ($data['total_amount'] <= 0) {
                throw new Exception("Invalid total amount");
            }

            if ($data['payment_amount'] < $data['total_amount']) {
                throw new Exception("Insufficient payment amount");
            }

            $this->conn->beginTransaction();

            // Get cart items
            $cartItems = $this->db->callProcedure('get_cart_items', [session_id()], true);
            
            if (!$cartItems || empty($cartItems)) {
                throw new Exception("Cart is empty");
            }

            // Create sale
            $saleResult = $this->db->callProcedure('create_sale', [
                $data['customer_name'],
                $data['total_amount'],
                $data['payment_amount'],
                $data['user_id']
            ], false);
            
            $saleId = $saleResult['sale_id'];

            // Process items
            foreach ($cartItems as $item) {
                $this->db->callProcedure('add_sale_item', [
                    $saleId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['unit_price']
                ], false);
            }

            // Clear cart
            $stmt = $this->conn->prepare("DELETE FROM cart_items WHERE session_id = ?");
            $stmt->execute([session_id()]);

            $this->conn->commit();
            
            return [
                'sale_id' => $saleId,
                'customer_name' => $data['customer_name'],
                'items' => $cartItems,
                'total_amount' => $data['total_amount'],
                'payment_amount' => $data['payment_amount'],
                'date' => date('Y-m-d H:i:s')
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
