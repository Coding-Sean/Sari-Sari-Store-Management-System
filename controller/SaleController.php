<?php
require_once 'models/Sale.php';
require_once 'models/Product.php';

class SaleController {
    private $sale;
    private $product;
    
    public function __construct() {
        $this->sale = new Sale();
        $this->product = new Product();
    }
    
    public function index() {
        $sales = $this->sale->getAll();
        require_once 'views/sales/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customer_name = $_POST['customer_name'];
            $items = json_decode($_POST['items'], true);
            $total_amount = 0;
            
            // Calculate total
            foreach ($items as $item) {
                $total_amount += $item['subtotal'];
            }
            
            // Create sale
            $sale_id = $this->sale->create($customer_name, $total_amount);
            
            if ($sale_id) {
                // Add sale items
                $success = true;
                foreach ($items as $item) {
                    $result = $this->sale->addItem(
                        $sale_id,
                        $item['product_id'],
                        $item['quantity'],
                        $item['unit_price'],
                        $item['subtotal']
                    );
                    if (!$result) {
                        $success = false;
                        break;
                    }
                }
                
                if ($success) {
                    echo json_encode(['success' => true, 'sale_id' => $sale_id]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add sale items']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create sale']);
            }
            exit;
        }
        
        $products = $this->product->getAll();
        require_once 'views/sales/create.php';
    }
    
    public function details($id) {
        $saleDetails = $this->sale->getDetails($id);
        require_once 'views/sales/details.php';
    }
    
    public function reports() {
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $month = isset($_GET['month']) ? $_GET['month'] : date('n');
        
        $monthlyReport = $this->sale->getMonthlySalesReport($year, $month);
        $topProducts = $this->sale->getTopSellingProducts($year, $month);
        
        require_once 'views/sales/reports.php';
    }
}
?>