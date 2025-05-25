<?php
require_once __DIR__ . '/../config/database.php';

class Report {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function monthlySales($year, $month) {
        $stmt = $this->conn->prepare("CALL get_monthly_sales_report(?, ?)");
        $stmt->execute([$year, $month]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>