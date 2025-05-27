ProductController.php
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class ProductController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function handleRequest() {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        
        switch ($action) {
            case 'get_categories':
                $this->getCategories();
                break;
            case 'get_products_by_category':
                $this->getProductsByCategory();
                break;
            case 'get_all_products':
                $this->getAllProducts();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }

    private function getCategories() {
        try {
            $categories = $this->db->callProcedure('get_all_categories', [], true);
            echo json_encode(['success' => true, 'categories' => $categories]);
        } catch (Exception $e) {
            error_log("Error getting categories: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getProductsByCategory() {
        try {
            $products = $this->db->callProcedure('get_products_by_category', [], true);
            
            // Group products by category
            $groupedProducts = [];
            foreach ($products as $product) {
                $categoryId = $product['category_id'];
                if (!isset($groupedProducts[$categoryId])) {
                    $groupedProducts[$categoryId] = [
                        'category_id' => $product['category_id'],
                        'category_name' => $product['category_name'],
                        'category_description' => $product['category_description'],
                        'products' => []
                    ];
                }
                
                if ($product['product_id']) { // Only add if product exists
                    $groupedProducts[$categoryId]['products'][] = [
                        'product_id' => $product['product_id'],
                        'name' => $product['product_name'],
                        'description' => $product['product_description'],
                        'price' => $product['price'],
                        'quantity' => $product['quantity'],
                        'stock_status' => $product['stock_status']
                    ];
                }
            }
            
            echo json_encode(['success' => true, 'categories' => array_values($groupedProducts)]);
        } catch (Exception $e) {
            error_log("Error getting products by category: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getAllProducts() {
        try {
            $products = $this->db->callProcedure('get_all_products', [], true);
            echo json_encode(['success' => true, 'products' => $products]);
        } catch (Exception $e) {
            error_log("Error getting all products: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

$controller = new ProductController();
$controller->handleRequest();
?>