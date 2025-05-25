<?php
require_once 'models/Report.php';

class ReportController {
    public function monthly() {
        $month = $_GET['month'] ?? date('Y-m');
        $report = Report::getMonthlySales($month);
        include 'views/reports/monthly.php';
    }
}
?>