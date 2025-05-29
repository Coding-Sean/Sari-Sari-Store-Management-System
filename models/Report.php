<?php
require_once __DIR__ . '/../config/database.php';

class Report {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        
        // Log initialization for debugging
        error_log("[Report Model] Initialized");
        if (!$this->conn) {
            error_log("[Report Model] WARNING: Database connection is null!");
        }
    }

    public function getDailySales($date) {
        try {
            error_log("[Report Model] Getting daily sales for date: " . $date);
            
            $database = new Database();
            $sales = $database->callProcedure('get_daily_sales_report', [$date], true);
            
            if (!$sales) {
                $sales = [];
            }
            
            error_log("[Report Model] Found " . count($sales) . " daily sales records");
            return $sales;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getDailySales: " . $e->getMessage());
            return false;
        }
    }

    public function getMonthlySales($year, $month) {
        try {
            error_log("[Report Model] Getting monthly sales for: " . $year . "-" . $month);
            
            $database = new Database();
            $sales = $database->callProcedure('get_monthly_sales', [$year, $month], true);
            
            if (!$sales) {
                $sales = [];
            }
            
            error_log("[Report Model] Found " . count($sales) . " monthly sales records");
            return $sales;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getMonthlySales: " . $e->getMessage());
            return false;
        }
    }

    public function getAllTransactions($startDate = null, $endDate = null, $customerName = null, $limit = 100) {
        try {
            error_log("[Report Model] Getting all transactions");
            
            $database = new Database();
            $transactions = $database->callProcedure('get_all_transactions', [
                $startDate, 
                $endDate, 
                $customerName, 
                $limit
            ], true);
            
            if (!$transactions) {
                $transactions = [];
            }
            
            error_log("[Report Model] Found " . count($transactions) . " transactions");
            return $transactions;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getAllTransactions: " . $e->getMessage());
            return false;
        }
    }

    public function getTransactionDetails($saleId) {
        try {
            error_log("[Report Model] Getting transaction details for sale ID: " . $saleId);
            
            $database = new Database();
            
            // Use the stored procedure to get transaction details
            $stmt = $this->conn->prepare("CALL get_transaction_details(?)");
            $stmt->execute([$saleId]);
            
            // Get sale header (first result set)
            $sale = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$sale) {
                error_log("[Report Model] Transaction not found with ID: " . $saleId);
                return false;
            }
            
            // Move to next result set for items
            $stmt->nextRowset();
            $sale['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("[Report Model] Found transaction with " . count($sale['items']) . " items");
            return $sale;

        } catch (Exception $e) {
            error_log("[Report Model] Error in getTransactionDetails: " . $e->getMessage());
            return false;
        }
    }

    public function getSalesSummary($startDate = null, $endDate = null) {
        try {
            error_log("[Report Model] Getting sales summary");
            
            $database = new Database();
            $summary = $database->callProcedure('get_sales_summary', [$startDate, $endDate], false);
            
            error_log("[Report Model] Generated sales summary");
            return $summary;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getSalesSummary: " . $e->getMessage());
            return false;
        }
    }

    public function getCustomerPurchases($customerName = null, $startDate = null, $endDate = null) {
        try {
            error_log("[Report Model] Getting customer purchases");
            
            $database = new Database();
            $customers = $database->callProcedure('get_customer_purchases', [
                $customerName, 
                $startDate, 
                $endDate
            ], true);
            
            if (!$customers) {
                $customers = [];
            }
            
            error_log("[Report Model] Found " . count($customers) . " customer records");
            return $customers;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getCustomerPurchases: " . $e->getMessage());
            return false;
        }
    }

    public function getCustomerTransactionDetails($customerName, $startDate = null, $endDate = null) {
        try {
            error_log("[Report Model] Getting customer transaction details for: " . $customerName);
            
            $database = new Database();
            $transactions = $database->callProcedure('get_customer_transaction_details', [
                $customerName, 
                $startDate, 
                $endDate
            ], true);
            
            if (!$transactions) {
                $transactions = [];
            }
            
            error_log("[Report Model] Found " . count($transactions) . " transactions for customer");
            return $transactions;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getCustomerTransactionDetails: " . $e->getMessage());
            return false;
        }
    }

    public function getHourlySales($date) {
        try {
            error_log("[Report Model] Getting hourly sales for date: " . $date);
            
            $database = new Database();
            $sales = $database->callProcedure('get_hourly_sales_report', [$date], true);
            
            if (!$sales) {
                $sales = [];
            }
            
            error_log("[Report Model] Found " . count($sales) . " hourly sales records");
            return $sales;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getHourlySales: " . $e->getMessage());
            return false;
        }
    }

    public function getWeeklySales($startDate, $endDate) {
        try {
            error_log("[Report Model] Getting weekly sales from " . $startDate . " to " . $endDate);
            
            $database = new Database();
            $sales = $database->callProcedure('get_weekly_sales_report', [$startDate, $endDate], true);
            
            if (!$sales) {
                $sales = [];
            }
            
            error_log("[Report Model] Found " . count($sales) . " weekly sales records");
            return $sales;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getWeeklySales: " . $e->getMessage());
            return false;
        }
    }

    public function getTransactionCountByCustomer($startDate = null, $endDate = null, $minTransactions = 1) {
        try {
            error_log("[Report Model] Getting transaction count by customer");
            
            $database = new Database();
            $customers = $database->callProcedure('get_transaction_count_by_customer', [
                $startDate, 
                $endDate, 
                $minTransactions
            ], true);
            
            if (!$customers) {
                $customers = [];
            }
            
            error_log("[Report Model] Found " . count($customers) . " customer transaction counts");
            return $customers;
            
        } catch (Exception $e) {
            error_log("[Report Model] Error in getTransactionCountByCustomer: " . $e->getMessage());
            return false;
        }
    }
    public function getTopSellingProducts($startDate = null, $endDate = null, $limit = 50) {
    try {
        error_log("[Report Model] Getting top selling products");
        
        $database = new Database();
        $products = $database->callProcedure('get_top_selling_products', [
            $startDate, 
            $endDate, 
            $limit
        ], true);
        
        if (!$products) {
            $products = [];
        }
        
        error_log("[Report Model] Found " . count($products) . " product records");
        return $products;
        
    } catch (Exception $e) {
        error_log("[Report Model] Error in getTopSellingProducts: " . $e->getMessage());
        return false;
        }
    }
}
?>