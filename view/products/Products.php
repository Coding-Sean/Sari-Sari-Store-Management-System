<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
require_once __DIR__ . '/../../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sari-Sari Store - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-card {
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
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
            <a class="navbar-brand" href="#">Sari-Sari Store</a>
            <div class="d-flex">
                <a href="Cart.php" class="btn btn-outline-light position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-store me-2"></i>Our Products</h2>
                <p class="text-muted">Browse our products organized by categories</p>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search products...">
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                    <button class="btn btn-outline-secondary" id="viewToggle" data-view="category">
                        <i class="fas fa-th-list me-1"></i>Category View
                    </button>
                </div>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="mb-4" id="categoryTabs">
            <ul class="nav nav-pills nav-fill" id="categoryTabList">
                <li class="nav-item">
                    <a class="nav-link active" data-category="all" href="#all">
                        <i class="fas fa-th-large me-2"></i>All Products
                    </a>
                </li>
            </ul>
        </div>

        <!-- Products Container -->
        <div class="row" id="productsContainer">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading products...</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        let currentView = 'category';
        let allProducts = [];
        let categorizedProducts = [];
        let currentCategory = 'all';
        
        updateCartCount();
        loadProductsByCategory();

        // Load products organized by categories
        function loadProductsByCategory() {
            $.ajax({
                url: '../../controllers/ProductController.php',
                method: 'POST',
                data: { action: 'get_products_by_category' },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        categorizedProducts = result.categories;
                        populateCategoryTabs();
                        populateCategoryFilter();
                        displayProductsByCategory('all');
                    } else {
                        showError('Error loading products: ' + result.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading products:', error);
                    showError('Error loading products. Please try again.');
                }
            });
        }

        // Populate category tabs
        function populateCategoryTabs() {
            let tabsHtml = `
                <li class="nav-item">
                    <a class="nav-link active" data-category="all" href="#all">
                        <i class="fas fa-th-large me-2"></i>All Products
                    </a>
                </li>`;
            
            categorizedProducts.forEach(category => {
                const icon = getCategoryIcon(category.category_name);
                const productCount = category.products.length;
                tabsHtml += `
                    <li class="nav-item">
                        <a class="nav-link" data-category="${category.category_id}" href="#category-${category.category_id}">
                            <i class="${icon} me-2"></i>${category.category_name}
                            <span class="badge bg-secondary ms-1">${productCount}</span>
                        </a>
                    </li>`;
            });
            
            $('#categoryTabList').html(tabsHtml);
        }

        // Populate category filter dropdown
        function populateCategoryFilter() {
            let filterHtml = '<option value="">All Categories</option>';
            categorizedProducts.forEach(category => {
                filterHtml += `<option value="${category.category_id}">${category.category_name}</option>`;
            });
            $('#categoryFilter').html(filterHtml);
        }

        // Display products by category
        function displayProductsByCategory(categoryId) {
            let productsToShow = [];
            
            if (categoryId === 'all') {
                // Show all products from all categories
                categorizedProducts.forEach(category => {
                    productsToShow = productsToShow.concat(category.products.map(product => ({
                        ...product,
                        category_name: category.category_name,
                        category_id: category.category_id
                    })));
                });
                displayProducts(productsToShow, 'All Products');
            } else {
                // Show products from specific category
                const category = categorizedProducts.find(cat => cat.category_id == categoryId);
                if (category) {
                    productsToShow = category.products.map(product => ({
                        ...product,
                        category_name: category.category_name,
                        category_id: category.category_id
                    }));
                    displayProducts(productsToShow, category.category_name, category.category_description);
                }
            }
        }

        // Display products
        function displayProducts(products, categoryTitle, categoryDescription = '') {
            if (products.length === 0) {
                $('#productsContainer').html(`
                    <div class="col-12 text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-box-open fa-4x text-muted"></i>
                        </div>
                        <h4>No products found</h4>
                        <p class="text-muted">No products available in this category.</p>
                    </div>
                `);
                return;
            }

            let html = '';
            
            // Add category header if showing specific category
            if (categoryTitle !== 'All Products') {
                html += `
                    <div class="col-12 mb-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3><i class="${getCategoryIcon(categoryTitle)} me-2"></i>${categoryTitle}</h3>
                                ${categoryDescription ? `<p class="text-muted mb-0">${categoryDescription}</p>` : ''}
                                <small class="text-muted">${products.length} products available</small>
                            </div>
                        </div>
                    </div>
                `;
            }

            products.forEach(product => {
                html += `
                    <div class="col-lg-4 col-md-6 mb-4 product-item" data-category="${product.category_id}">
                        <div class="card product-card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-text flex-grow-1">${product.description || 'No description available'}</p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>${product.category_name}
                                    </small>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0 text-success">₱${parseFloat(product.price).toFixed(2)}</h5>
                                    <div class="input-group input-group-sm" style="width: 120px;">
                                        <input type="number" class="form-control text-center" value="1" min="1" 
                                               max="${product.quantity}" id="qty_${product.product_id}">
                                        <button class="btn btn-primary add-to-cart" 
                                                data-product-id="${product.product_id}"
                                                ${product.quantity < 1 ? 'disabled' : ''}>
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <span class="badge ${product.quantity > 0 ? 'bg-success' : 'bg-danger'}">
                                        ${product.quantity > 0 ? `In Stock (${product.quantity})` : 'Out of Stock'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#productsContainer').html(html);
        }

        // Get category icon
        function getCategoryIcon(categoryName) {
            const icons = {
                'Beverages': 'fas fa-coffee',
                'Snacks': 'fas fa-cookie-bite',
                'Canned Goods': 'fas fa-can-food',
                'Household': 'fas fa-home',
                'Personal Care': 'fas fa-spa'
            };
            return icons[categoryName] || 'fas fa-box';
        }

        // Show error message
        function showError(message) {
            $('#productsContainer').html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${message}
                    </div>
                </div>
            `);
        }

        // Category tab click handler
        $(document).on('click', '.nav-link[data-category]', function(e) {
            e.preventDefault();
            const categoryId = $(this).data('category');
            
            // Update active tab
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            
            // Display products for selected category
            displayProductsByCategory(categoryId);
            currentCategory = categoryId;
        });

        // Add to cart functionality
        $(document).on('click', '.add-to-cart', function() {
            const productId = $(this).data('product-id');
            const quantity = parseInt($(`#qty_${productId}`).val());
            
            console.log('Adding to cart:', { productId, quantity });
            
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
                    console.log('Add to cart response:', result);
                    if (result.success) {
                        updateCartCount();
                        
                        // Show success message
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
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error adding product to cart');
                }
            });
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

        // Search and filter functionality
        $('#searchInput').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            filterProducts(searchTerm, $('#categoryFilter').val());
        });

        $('#categoryFilter').change(function() {
            const categoryId = $(this).val();
            const searchTerm = $('#searchInput').val().toLowerCase();
            
            if (categoryId) {
                // Update tab selection
                $('.nav-link').removeClass('active');
                $(`.nav-link[data-category="${categoryId}"]`).addClass('active');
                displayProductsByCategory(categoryId);
            } else {
                $('.nav-link').removeClass('active');
                $('.nav-link[data-category="all"]').addClass('active');
                displayProductsByCategory('all');
            }
        });

        function filterProducts(searchTerm, categoryId) {
            $('.product-item').each(function() {
                const card = $(this);
                const productName = card.find('.card-title').text().toLowerCase();
                const productDescription = card.find('.card-text').first().text().toLowerCase();
                const productCategory = card.data('category');
                
                const categoryMatch = !categoryId || productCategory == categoryId;
                const searchMatch = !searchTerm || 
                    productName.includes(searchTerm) || 
                    productDescription.includes(searchTerm);

                if (categoryMatch && searchMatch) {
                    card.show();
                } else {
                    card.hide();
                }
            });
        }
    });
    </script>
</body>
</html>