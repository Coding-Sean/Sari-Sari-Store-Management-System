<?php
require_once __DIR__ . '/../models/Report.php';

class ReportController {
    public function monthly() {
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('n');
        $report = new Report();
        $data = $report->monthlySales($year, $month);
        require __DIR__ . '/../views/reports/monthly.php';
    }
}
?>