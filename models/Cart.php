<?php
class Cart {
    private $conn;
    private $session_id;

    public function __construct($db) {
        $this->conn = $db;
        $this->session_id = session_id();
        
        // Log session information for debugging
        error_log("[Cart Model] Initialized with session ID: " . $this->session_id);
        if (empty($this->session_id)) {
            error_log("[Cart Model] WARNING: Session ID is empty!");
        }
    }    public function addItem($productId, $quantity) {
        try {
            error_log("Adding to cart - Product ID: $productId, Quantity: $quantity, Session: {$this->session_id}");
            
            $db = new Database();
            $cartItem = $db->callProcedure('add_to_cart', [$this->session_id, $productId, $quantity], false);
            
            return $cartItem ?: false;
        } catch (Exception $e) {
            error_log("Error in addItem: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateQuantity($productId, $quantity) {
        try {
            $db = new Database();
            $db->callProcedure('update_cart_quantity', [$this->session_id, $productId, $quantity], false);
            return true;
        } catch (Exception $e) {
            error_log("Error in updateQuantity: " . $e->getMessage());
            throw $e;
        }
    }

    public function removeItem($productId) {
        try {
            $db = new Database();
            $db->callProcedure('remove_from_cart', [$this->session_id, $productId], false);
            return true;
        } catch (Exception $e) {
            error_log("Error in removeItem: " . $e->getMessage());
            throw $e;
        }
    }    public function getItems() {
        try {
            error_log("[Cart Model] Getting cart items for session ID: " . $this->session_id);
            
            $db = new Database();
            $items = $db->callProcedure('get_cart_items', [$this->session_id], true);
            
            // Ensure we return an empty array if no items found
            if (!$items) {
                $items = [];
            }
            
            error_log("[Cart Model] Found " . count($items) . " items in cart");
            error_log("[Cart Model] Raw items data: " . json_encode($items));
            
            // Return just the items array directly (frontend expects this structure)
            return $items;
        } catch (Exception $e) {
            error_log("[Cart Model] Error in getItems: " . $e->getMessage());
            throw $e;
        }
    }public function getCount() {
        try {
            $db = new Database();
            $result = $db->callProcedure('get_cart_count', [$this->session_id], false);
            return isset($result['count']) ? $result['count'] : 0;
        } catch (Exception $e) {
            error_log("Error in getCount: " . $e->getMessage());
            throw $e;
        }
    }

    public function clear() {
        try {
            $stmt = $this->conn->prepare("DELETE FROM cart_items WHERE session_id = ?");
            $stmt->execute([$this->session_id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error in clear: " . $e->getMessage());
            throw $e;
        }
    }
}
?>