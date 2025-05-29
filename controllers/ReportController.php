<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Report.php';

$db = new Database();
$conn = $db->getConnection();
$report = new Report($conn);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    switch ($action) {
        case 'get_daily_sales':
            $date = $_POST['date'] ?? date('Y-m-d');
            $dailySales = $report->getDailySales($date);
            if ($dailySales === false) {
                throw new Exception("Failed to get daily sales");
            }
            echo json_encode(['success' => true, 'sales' => $dailySales]);
            break;

        case 'get_monthly_sales':
            $year = intval($_POST['year'] ?? date('Y'));
            $month = intval($_POST['month'] ?? date('n'));
            $monthlySales = $report->getMonthlySales($year, $month);
            if ($monthlySales === false) {
                throw new Exception("Failed to get monthly sales");
            }
            echo json_encode(['success' => true, 'sales' => $monthlySales]);
            break;

        case 'get_all_transactions':
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            $customerName = $_POST['customer_name'] ?? null;
            $limit = intval($_POST['limit'] ?? 100);
            $transactions = $report->getAllTransactions($startDate, $endDate, $customerName, $limit);
            if ($transactions === false) {
                throw new Exception("Failed to get transactions");
            }
            echo json_encode(['success' => true, 'transactions' => $transactions]);
            break;

        case 'get_transaction_details':
            $saleId = intval($_POST['sale_id'] ?? 0);
            if (!$saleId) {
                throw new Exception("Sale ID is required");
            }
            $transactionDetails = $report->getTransactionDetails($saleId);
            if ($transactionDetails === false) {
                throw new Exception("Transaction not found");
            }
            echo json_encode(['success' => true, 'transaction' => $transactionDetails]);
            break;

        case 'get_sales_summary':
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            $summary = $report->getSalesSummary($startDate, $endDate);
            if ($summary === false) {
                throw new Exception("Failed to get sales summary");
            }
            echo json_encode(['success' => true, 'summary' => $summary]);
            break;

        case 'get_customer_purchases':
            $customerName = $_POST['customer_name'] ?? null;
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            $customers = $report->getCustomerPurchases($customerName, $startDate, $endDate);
            if ($customers === false) {
                throw new Exception("Failed to get customer purchases");
            }
            echo json_encode(['success' => true, 'customers' => $customers]);
            break;

        case 'get_customer_transaction_details':
            $customerName = $_POST['customer_name'] ?? '';
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            if (!$customerName) {
                throw new Exception("Customer name is required");
            }
            $customerTransactions = $report->getCustomerTransactionDetails($customerName, $startDate, $endDate);
            if ($customerTransactions === false) {
                throw new Exception("Failed to get customer transaction details");
            }
            echo json_encode(['success' => true, 'transactions' => $customerTransactions]);
            break;

        case 'get_hourly_sales':
            $date = $_POST['date'] ?? date('Y-m-d');
            $hourlySales = $report->getHourlySales($date);
            if ($hourlySales === false) {
                throw new Exception("Failed to get hourly sales");
            }
            echo json_encode(['success' => true, 'sales' => $hourlySales]);
            break;

        case 'get_weekly_sales':
            $startDate = $_POST['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
            $endDate = $_POST['end_date'] ?? date('Y-m-d');
            $weeklySales = $report->getWeeklySales($startDate, $endDate);
            if ($weeklySales === false) {
                throw new Exception("Failed to get weekly sales");
            }
            echo json_encode(['success' => true, 'sales' => $weeklySales]);
            break;

        case 'get_transaction_count_by_customer':
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            $minTransactions = intval($_POST['min_transactions'] ?? 1);
            $customerCounts = $report->getTransactionCountByCustomer($startDate, $endDate, $minTransactions);
            if ($customerCounts === false) {
                throw new Exception("Failed to get transaction count by customer");
            }
            echo json_encode(['success' => true, 'customers' => $customerCounts]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Error in ReportController: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>