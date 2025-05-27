<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Sari-Sari Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="Products.php">Sari-Sari Store</a>
            <a href="Products.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Shopping Cart</h2>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div id="cartItems">
                            <!-- Cart items will be loaded here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span id="subtotal">₱0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong id="total">₱0.00</strong>
                        </div>
                        <button class="btn btn-success w-100" id="checkoutBtn">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    <script>    $(document).ready(function() {
        loadCart();

        function loadCart() {
            console.log('Loading cart...');            $.ajax({
                url: '../../controllers/CartController.php',
                method: 'POST',
                data: { action: 'get' },
                dataType: 'json',
                beforeSend: function() {
                    $('#cartItems').html('<p class="text-center">Loading cart items...</p>');
                },
                success: function(result) {
                    console.log('Cart response:', result);
                    
                    try {
                        let html = '';
                        let total = 0;

                        if (!result.success) {
                            html = '<p class="text-center text-danger">Error loading cart: ' + result.message + '</p>';
                            $('#checkoutBtn').prop('disabled', true);
                        } else if (!result.items || result.items.length === 0) {
                            html = `
                                <div class="text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                                    </div>
                                    <h4>Your cart is empty</h4>
                                    <p class="text-muted">Browse our products and add some items to your cart!</p>
                                    <a href="Products.php" class="btn btn-primary">
                                        <i class="fas fa-shopping-bag"></i> Browse Products
                                    </a>
                                </div>`;
                            $('#checkoutBtn').prop('disabled', true);
                        } else {
                            result.items.forEach(item => {
                                total += parseFloat(item.subtotal);
                                html += `
                                    <div class="card mb-3 shadow-sm">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="card-title mb-1">${item.product_name}</h5>
                                                    <p class="card-text mb-2">
                                                        <span class="text-success fw-bold">₱${parseFloat(item.unit_price).toFixed(2)}</span> each
                                                    </p>
                                                    <div class="mb-2">
                                                        <span class="badge bg-${item.available_stock > 0 ? 'success' : 'danger'} me-2">
                                                            ${item.available_stock > 0 ? 'In Stock' : 'Out of Stock'}
                                                        </span>
                                                        <span class="badge bg-info">
                                                            ${item.category_name}
                                                        </span>
                                                    </div>
                                                </div>                                                <div class="col-auto">
                                                    <div class="input-group input-group-sm" style="width: 120px;">
                                                        <button class="btn btn-outline-primary update-quantity" 
                                                                data-product-id="${item.product_id}" 
                                                                data-action="decrease"
                                                                ${item.quantity <= 1 ? 'disabled' : ''}>
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="form-control text-center" 
                                                               value="${item.quantity}" 
                                                               min="1" 
                                                               max="${item.available_stock}"
                                                               data-product-id="${item.product_id}"
                                                               style="background-color: white;">
                                                        <button class="btn btn-outline-primary update-quantity" 
                                                                data-product-id="${item.product_id}" 
                                                                data-action="increase"
                                                                ${item.quantity >= item.available_stock ? 'disabled' : ''}>
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div class="text-center text-muted small mt-1">
                                                        <span title="Available stock">${item.available_stock} available</span>
                                                    </div>
                                                </div>
                                                <div class="col-auto text-end">
                                                    <h5 class="mb-0 text-success">₱${parseFloat(item.subtotal).toFixed(2)}</h5>
                                                    <small class="text-muted">
                                                        ${item.quantity} × ₱${parseFloat(item.unit_price).toFixed(2)}
                                                    </small>
                                                </div>
                                                <div class="col-auto">
                                                    <button class="btn btn-outline-danger btn-sm remove-item" 
                                                            data-product-id="${item.product_id}"
                                                            title="Remove item">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                            });
                            $('#checkoutBtn').prop('disabled', false);
                        }
                        
                        $('#cartItems').html(html);
                        $('#subtotal').text(`₱${total.toFixed(2)}`);
                        $('#total').text(`₱${total.toFixed(2)}`);
                    } catch (error) {
                        console.error('Error parsing cart response:', error);
                        const errorHtml = '<p class="text-center text-danger">Error loading cart items</p>';
                        $('#cartItems').html(errorHtml);
                        $('#checkoutBtn').prop('disabled', true);
                    }
                },                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    let errorMessage = 'Error loading cart items';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }
                    const errorHtml = `<p class="text-center text-danger">${errorMessage}</p>`;
                    $('#cartItems').html(errorHtml);
                    $('#checkoutBtn').prop('disabled', true);
                }
            });
        }

        $(document).on('click', '.update-quantity', function() {
            const productId = $(this).data('product-id');
            const action = $(this).data('action');
            const input = $(`input[data-product-id="${productId}"]`);
            let quantity = parseInt(input.val());

            if (action === 'increase') {
                quantity++;
            } else {
                quantity--;
            }

            if (quantity >= 1 && quantity <= parseInt(input.attr('max'))) {
                updateCartItem(productId, quantity);
            }
        });

        $(document).on('change', 'input[type="number"]', function() {
            const productId = $(this).data('product-id');
            const quantity = parseInt($(this).val());
            
            if (quantity >= 1 && quantity <= parseInt($(this).attr('max'))) {
                updateCartItem(productId, quantity);
            }
        });

        $(document).on('click', '.remove-item', function() {
            const productId = $(this).data('product-id');
            
            $.ajax({
                url: '../../controllers/CartController.php',
                method: 'POST',
                data: {
                    action: 'remove',
                    product_id: productId
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        loadCart();
                    }
                }
            });
        });

        function updateCartItem(productId, quantity) {
            $.ajax({
                url: '../../controllers/CartController.php',
                method: 'POST',
                data: {
                    action: 'update',
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        loadCart();
                    } else {
                        alert(result.message);
                    }
                }
            });
        }

        $('#checkoutBtn').click(function() {
            window.location.href = 'Checkout.php';
        });
    });
    </script>
</body>
</html>