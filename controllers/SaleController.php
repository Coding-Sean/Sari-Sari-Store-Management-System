<?php
session_start();
require_once __DIR__ . '/../models/SaleModel.php';

class SaleController {
    private $saleModel;

    public function __construct() {
        $this->saleModel = new SaleModel();
    }

    public function handleRequest() {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                $this->processSale();
                break;
            case 'getDailySales':
                $this->getDailySales();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }

    private function processSale() {
        try {
            $data = [
                'customer_name' => trim($_POST['customer_name'] ?? 'Walk-in Customer'),
                'total_amount' => floatval($_POST['total_amount'] ?? 0),
                'payment_amount' => floatval($_POST['payment_amount'] ?? 0),
                'user_id' => $_SESSION['user_id'] ?? 1
            ];

            $result = $this->saleModel->createSale($data);
            
            // Generate receipt using view
            require_once __DIR__ . '/../views/products/Receipt.php';
            $receipt = generateReceipt($result);
            
            echo json_encode([
                'success' => true,
                'sale_id' => $result['sale_id'],
                'receipt' => $receipt
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

$controller = new SaleController();
$controller->handleRequest();
?>
