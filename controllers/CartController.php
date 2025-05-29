<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Cart.php';

$db = new Database();
$conn = $db->getConnection();
$cart = new Cart($conn);

$action = $_POST['action'] ?? '';

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    switch ($action) {
        case 'add':
            $productId = $_POST['product_id'] ?? 0;
            $quantity = intval($_POST['quantity'] ?? 1);
            $result = $cart->addItem($productId, $quantity);
            if ($result === false) {
                throw new Exception("Failed to add item to cart");
            }
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'update':
            $productId = $_POST['product_id'] ?? 0;
            $quantity = intval($_POST['quantity'] ?? 1);
            if ($quantity < 1) {
                throw new Exception("Invalid quantity");
            }
            $cart->updateQuantity($productId, $quantity);
            echo json_encode(['success' => true]);
            break;

        case 'remove':
            $productId = $_POST['product_id'] ?? 0;
            if (!$productId) {
                throw new Exception("Invalid product ID");
            }
            $cart->removeItem($productId);
            echo json_encode(['success' => true]);
            break;

        case 'get':
            error_log("[CartController] Getting cart items for session: " . session_id());
            $items = $cart->getItems();
            if ($items === false) {
                error_log("[CartController] Failed to get cart items - getItems returned false");
                throw new Exception("Failed to get cart items");
            }
            error_log("[CartController] Successfully retrieved " . count($items) . " cart items");
            echo json_encode(['success' => true, 'items' => $items]);
            break;

        case 'count':
            $count = $cart->getCount();
            if ($count === false) {
                throw new Exception("Failed to get cart count");
            }
            echo json_encode(['success' => true, 'count' => $count]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Error in CartController: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>