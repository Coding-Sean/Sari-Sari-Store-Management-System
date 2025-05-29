
<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';

$db = new Database();
$conn = $db->getConnection();
$product = new Product($conn);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    switch ($action) {
        case 'get_categories':
            error_log("[ProductController] Getting categories");
            $categories = $product->getCategories();
            if ($categories === false) {
                error_log("[ProductController] Failed to get categories");
                throw new Exception("Failed to get categories");
            }
            error_log("[ProductController] Successfully retrieved " . count($categories) . " categories");
            echo json_encode(['success' => true, 'categories' => $categories]);
            break;

        case 'get_products_by_category':
            error_log("[ProductController] Getting products by category");
            $products = $product->getProductsByCategory();
            if ($products === false) {
                error_log("[ProductController] Failed to get products by category");
                throw new Exception("Failed to get products by category");
            }
            error_log("[ProductController] Successfully retrieved products by category");
            echo json_encode(['success' => true, 'categories' => $products]);
            break;

        case 'get_all_products':
            error_log("[ProductController] Getting all products");
            $products = $product->getAllProducts();
            if ($products === false) {
                error_log("[ProductController] Failed to get all products");
                throw new Exception("Failed to get all products");
            }
            error_log("[ProductController] Successfully retrieved " . count($products) . " products");
            echo json_encode(['success' => true, 'products' => $products]);
            break;

        case 'add_product':
            $categoryId = $_POST['category_id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 0);

            if (!$categoryId || !$name || $price <= 0 || $quantity < 0) {
                throw new Exception("All fields are required and must be valid");
            }

            $result = $product->addProduct($categoryId, $name, $description, $price, $quantity);
            if ($result === false) {
                throw new Exception("Failed to add product");
            }
            echo json_encode(['success' => true, 'message' => 'Product added successfully', 'product_id' => $result['product_id'] ?? null]);
            break;

        case 'edit_product':
            $productId = $_POST['product_id'] ?? 0;
            $categoryId = $_POST['category_id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 0);

            if (!$productId || !$name || $price <= 0 || $quantity < 0) {
                throw new Exception("All fields are required and must be valid");
            }

            $result = $product->updateProduct($productId, $categoryId, $name, $description, $price, $quantity);
            if ($result === false) {
                throw new Exception("Failed to update product");
            }
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            break;

        case 'delete_product':
            $productId = $_POST['product_id'] ?? 0;
            if (!$productId) {
                throw new Exception("Product ID is required");
            }

            $result = $product->deleteProduct($productId);
            if ($result === false) {
                throw new Exception("Failed to delete product");
            }
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            break;

        case 'get_product_by_id':
            $productId = $_POST['product_id'] ?? 0;
            if (!$productId) {
                throw new Exception("Product ID is required");
            }

            $productData = $product->getProductById($productId);
            if ($productData === false) {
                throw new Exception("Product not found");
            }
            echo json_encode(['success' => true, 'product' => $productData]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Error in ProductController: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
