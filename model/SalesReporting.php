<?php
require_once 'config/database.php';

class Report {
    public static function getMonthlySales($month) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("CALL GetMonthlySales(:month)");
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>