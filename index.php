<?php
// Basic router for demo purposes
$controller = $_GET['controller'] ?? 'product';
$action = $_GET['action'] ?? 'index';

switch ($controller) {
    case 'product':
        require_once 'controllers/ProductController.php';
        $c = new ProductController();
        break;
    case 'sales':
        require_once 'controllers/SalesController.php';
        $c = new SalesController();
        break;
    case 'report':
        require_once 'controllers/ReportController.php';
        $c = new ReportController();
        break;
    default:
        die("Unknown controller");
}
if (!method_exists($c, $action)) die("Unknown action");
$c->$action();
?>