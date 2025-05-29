product.php
<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        
        // Log initialization for debugging
        error_log("[Product Model] Initialized");
        if (!$this->conn) {
            error_log("[Product Model] WARNING: Database connection is null!");
        }
    }

    public function getAllProducts() {
        try {
            error_log("[Product Model] Getting all products");
            
            $database = new Database();
            $products = $database->callProcedure('get_all_products', [], true);
            
            // Ensure we return an empty array if no products found
            if (!$products) {
                $products = [];
            }
            
            error_log("[Product Model] Found " . count($products) . " products");
            
            return $products;
        } catch (Exception $e) {
            error_log("[Product Model] Error in getAllProducts: " . $e->getMessage());
            return false;
        }
    }

    public function getProductById($productId) {
        try {
            error_log("[Product Model] Getting product by ID: " . $productId);
            
            $database = new Database();
            $product = $database->callProcedure('get_product_by_id', [$productId], false);
            
            if (!$product) {
                error_log("[Product Model] Product not found with ID: " . $productId);
                return false;
            }
            
            error_log("[Product Model] Found product: " . $product['name']);
            
            return $product;
        } catch (Exception $e) {
            error_log("[Product Model] Error in getProductById: " . $e->getMessage());
            return false;
        }
    }

    public function addProduct($categoryId, $name, $description, $price, $quantity) {
        try {
            error_log("[Product Model] Adding product: " . $name);
            
            // Validation
            if (strlen($name) > 100) {
                throw new Exception("Product name must be 100 characters or less");
            }

            if (strlen($description) > 255) {
                throw new Exception("Description must be 255 characters or less");
            }
            
            $database = new Database();
            $result = $database->callProcedure('add_product', [
                $categoryId, 
                $name, 
                $description, 
                $price, 
                $quantity
            ], false);
            
            if (!$result) {
                error_log("[Product Model] Failed to add product");
                return false;
            }
            
            error_log("[Product Model] Successfully added product with ID: " . ($result['product_id'] ?? 'unknown'));
            
            return $result;
        } catch (Exception $e) {
            error_log("[Product Model] Error in addProduct: " . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($productId, $categoryId, $name, $description, $price, $quantity) {
        try {
            error_log("[Product Model] Updating product ID: " . $productId);
            
            // Validation
            if (strlen($name) > 100) {
                throw new Exception("Product name must be 100 characters or less");
            }

            if (strlen($description) > 255) {
                throw new Exception("Description must be 255 characters or less");
            }
            
            $database = new Database();
            $result = $database->callProcedure('update_product', [
                $productId,
                $categoryId, 
                $name, 
                $description, 
                $price, 
                $quantity
            ], false);
            
            error_log("[Product Model] Successfully updated product");
            
            return true;
        } catch (Exception $e) {
            error_log("[Product Model] Error in updateProduct: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($productId) {
        try {
            error_log("[Product Model] Deleting product ID: " . $productId);
            
            $database = new Database();
            $result = $database->callProcedure('delete_product', [$productId], false);
            
            // Check if any rows were affected
            if ($result && isset($result['affected_rows']) && $result['affected_rows'] > 0) {
                error_log("[Product Model] Successfully deleted product");
                return true;
            } else {
                error_log("[Product Model] Product not found or already deleted");
                return false;
            }
        } catch (Exception $e) {
            error_log("[Product Model] Error in deleteProduct: " . $e->getMessage());
            return false;
        }
    }

    public function getProductsByCategory() {
        try {
            error_log("[Product Model] Getting products by category");
            
            $database = new Database();
            $products = $database->callProcedure('get_products_by_category', [], true);
            
            if (!$products) {
                error_log("[Product Model] No products found");
                return [];
            }
            
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
            
            error_log("[Product Model] Found " . count($groupedProducts) . " categories with products");
            
            return array_values($groupedProducts);
        } catch (Exception $e) {
            error_log("[Product Model] Error in getProductsByCategory: " . $e->getMessage());
            return false;
        }
    }

    public function getCategories() {
        try {
            error_log("[Product Model] Getting categories");
            
            $database = new Database();
            $categories = $database->callProcedure('get_all_categories', [], true);
            
            // Ensure we return an empty array if no categories found
            if (!$categories) {
                $categories = [];
            }
            
            error_log("[Product Model] Found " . count($categories) . " categories");
            
            return $categories;
        } catch (Exception $e) {
            error_log("[Product Model] Error in getCategories: " . $e->getMessage());
            return false;
        }
    }
}
?>