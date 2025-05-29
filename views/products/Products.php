
<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Product.php';

// Initialize database and get products
$db = new Database();
$conn = $db->getConnection();
$product = new Product($conn);

// Get all products using the existing method
$products = $product->getAllProducts();
if (!$products) {
    $products = [];
}

// Get categories for dropdown
$categories = $product->getCategories();
if (!$categories) {
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sari-Sari Store - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="Maindashboard.php">
                <i class="fas fa-store me-2"></i>Sari-Sari Store
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="Products.php">
                    <i class="fas fa-box me-1"></i>Products
                </a>
                <a class="nav-link" href="Cart.php">
                    <i class="fas fa-shopping-cart me-1"></i>Cart
                </a>
                <a class="nav-link" href="../reports/ManagementReport.php">
                    <i class="fas fa-chart-bar me-1"></i>Reports
                </a>
            </div>
            <div class="d-flex ms-3">
                <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-1"></i>Add Product
                </button>
                <a href="Cart.php" class="btn btn-outline-light position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Product List</h2>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>
        
        <table class="table table-bordered" id="productsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Add to Cart</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $row) : ?>
                <tr>
                    <td><?= $row['product_id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['category_name'] ?? 'N/A') ?></td>
                    <td>₱<?= number_format($row['price'], 2) ?></td>
                    <td>
                        <span class="badge <?= $row['quantity'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                            <?= $row['quantity'] > 0 ? $row['quantity'] . ' in stock' : 'Out of stock' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['quantity'] > 0) : ?>
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" class="form-control form-control-sm" 
                                   style="width: 60px;" value="1" min="1" max="<?= $row['quantity'] ?>" 
                                   id="qty_<?= $row['product_id'] ?>">
                            <button class="btn btn-primary btn-sm add-to-cart" 
                                    data-product-id="<?= $row['product_id'] ?>">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                        <?php else : ?>
                        <button class="btn btn-secondary btn-sm" disabled>
                            <i class="fas fa-times"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm edit-product" 
                                data-product-id="<?= $row['product_id'] ?>">Edit</button>
                        <button class="btn btn-danger btn-sm delete-product" 
                                data-product-id="<?= $row['product_id'] ?>"
                                data-product-name="<?= htmlspecialchars($row['name']) ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <div class="mb-3">
                            <label for="addCategory" class="form-label">Category</label>
                            <select class="form-select" id="addCategory" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="addName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="addName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="addDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="addPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="addPrice" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="addQuantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="addQuantity" name="quantity" min="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveNewProductBtn">Add Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" id="editProductId" name="product_id">
                        <div class="mb-3">
                            <label for="editCategory" class="form-label">Category</label>
                            <select class="form-select" id="editCategory" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category) : ?>
                                <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="editPrice" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="editQuantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="editQuantity" name="quantity" min="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveProductBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#productsTable').DataTable({
            pageLength: 25,
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [5, 6] }
            ]
        });

        updateCartCount();

        // Add to cart functionality
        $(document).on('click', '.add-to-cart', function() {
            const productId = $(this).data('product-id');
            const quantity = parseInt($(`#qty_${productId}`).val());
            
            $.ajax({
                url: '../../controllers/CartController.php',
                method: 'POST',
                data: {
                    action: 'add',
                    product_id: productId,
                    quantity: quantity
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        updateCartCount();
                        const button = $(`button[data-product-id="${productId}"]`);
                        const originalHtml = button.html();
                        button.html('<i class="fas fa-check"></i>').addClass('btn-success').removeClass('btn-primary');
                        
                        setTimeout(() => {
                            button.html(originalHtml).removeClass('btn-success').addClass('btn-primary');
                        }, 1000);
                    } else {
                        alert(result.message || 'Error adding product to cart');
                    }
                },
                error: function() {
                    alert('Error adding product to cart');
                }
            });
        });

        // Edit product functionality
        $(document).on('click', '.edit-product', function() {
            const productId = $(this).data('product-id');
            
            $.ajax({
                url: '../../controllers/ProductController.php',
                method: 'POST',
                data: {
                    action: 'get_product_by_id',
                    product_id: productId
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        const product = result.product;
                        $('#editProductId').val(product.product_id);
                        $('#editCategory').val(product.category_id);
                        $('#editName').val(product.name);
                        $('#editDescription').val(product.description);
                        $('#editPrice').val(product.price);
                        $('#editQuantity').val(product.quantity);
                        
                        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                        modal.show();
                    } else {
                        alert('Error loading product details');
                    }
                }
            });
        });

        // Save product changes
        $('#saveProductBtn').click(function() {
            const formData = {
                action: 'edit_product',
                product_id: $('#editProductId').val(),
                category_id: $('#editCategory').val(),
                name: $('#editName').val(),
                description: $('#editDescription').val(),
                price: $('#editPrice').val(),
                quantity: $('#editQuantity').val()
            };

            $.ajax({
                url: '../../controllers/ProductController.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        alert('Product updated successfully!');
                        location.reload();
                    } else {
                        alert('Error updating product');
                    }
                }
            });
        });

        // Add new product
        $('#saveNewProductBtn').click(function() {
            const formData = {
                action: 'add_product',
                category_id: $('#addCategory').val(),
                name: $('#addName').val(),
                description: $('#addDescription').val(),
                price: $('#addPrice').val(),
                quantity: $('#addQuantity').val()
            };

            $.ajax({
                url: '../../controllers/ProductController.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        alert('Product added successfully!');
                        location.reload();
                    } else {
                        alert('Error adding product');
                    }
                }
            });
        });

        // Delete product functionality
        $(document).on('click', '.delete-product', function() {
            const productId = $(this).data('product-id');
            const productName = $(this).data('product-name');
            
            if (confirm(`Are you sure you want to delete "${productName}"?`)) {
                $.ajax({
                    url: '../../controllers/ProductController.php',
                    method: 'POST',
                    data: {
                        action: 'delete_product',
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            alert('Product deleted successfully!');
                            location.reload();
                        } else {
                            alert('Error deleting product');
                        }
                    }
                });
            }
        });

        // Update cart count
        function updateCartCount() {
            $.ajax({
                url: '../../controllers/CartController.php',
                method: 'POST',
                data: { action: 'count' },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        $('#cartCount').text(result.count);
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
