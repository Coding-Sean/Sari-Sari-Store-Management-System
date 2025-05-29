Checkout.php
<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sari-Sari Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .receipt-container {
            max-width: 400px;
            margin: 0 auto;
            font-family: 'Courier New', monospace;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            .receipt-container, .receipt-container * {
                visibility: visible;
            }
            .receipt-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .modal-footer {
                display: none !important;
            }
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="Products.php">Sari-Sari Store</a>
            <a href="Cart.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Back to Cart
            </a>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Checkout</h2>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-user me-2"></i>Customer Information
                        </h5>
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Customer Name *</label>
                            <input type="text" class="form-control" id="customerName" 
                                   placeholder="Enter customer name" value="Walk-in Customer">
                            <div class="form-text">Enter customer name or leave as 'Walk-in Customer'</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-shopping-bag me-2"></i>Order Items
                        </h5>
                        <div id="orderItems">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading order items...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-credit-card me-2"></i>Payment Details
                        </h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span id="subtotal">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Tax (0%)</span>
                                <span>₱0.00</span>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>Total Amount</strong>
                                <strong id="total" class="text-success">₱0.00</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="paymentAmount" class="form-label">Cash Payment</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="paymentAmount" 
                                       step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div id="changeAmount" class="form-text"></div>
                        </div>
                        <button class="btn btn-success w-100" id="completeOrderBtn" disabled>
                            <i class="fas fa-check-circle me-2"></i>Complete Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt me-2"></i>Transaction Complete
                    </h5>
                </div>
                <div class="modal-body" id="receiptContent">
                    <!-- Receipt content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="printReceipt()">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i>New Transaction
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        let total = 0;
        loadOrderItems();

        function loadOrderItems() {
            console.log('Loading order items...');
            $.ajax({
                url: '../../controllers/CartController.php',
                method: 'POST',
                data: { action: 'get' },
                dataType: 'json',
                success: function(result) {
                    console.log('Order items response:', result);
                    let html = '';
                    total = 0;

                    if (!result.success || !result.items || result.items.length === 0) {
                        // Redirect to cart if empty
                        window.location.href = 'Cart.php';
                        return;
                    }

                    // Create detailed order items display
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-hover">';
                    html += '<thead class="table-light">';
                    html += '<tr>';
                    html += '<th>Product</th>';
                    html += '<th class="text-center">Qty</th>';
                    html += '<th class="text-end">Unit Price</th>';
                    html += '<th class="text-end">Subtotal</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';

                    result.items.forEach(item => {
                        const subtotal = parseFloat(item.subtotal);
                        total += subtotal;
                        
                        html += `
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-1">${item.product_name}</h6>
                                        <small class="text-muted">${item.category_name}</small>
                                        <br>
                                        <span class="badge bg-${item.available_stock > item.quantity ? 'success' : 'warning'} small">
                                            ${item.available_stock} in stock
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-primary">${item.quantity}</span>
                                </td>
                                <td class="text-end align-middle">
                                    ₱${parseFloat(item.unit_price).toFixed(2)}
                                </td>
                                <td class="text-end align-middle">
                                    <strong>₱${subtotal.toFixed(2)}</strong>
                                </td>
                            </tr>`;
                    });

                    html += '</tbody>';
                    html += '</table>';
                    html += '</div>';
                    
                    // Add order summary
                    html += `
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="mb-2">Order Summary</h6>
                                    <p class="mb-1"><small>Total Items: ${result.items.length}</small></p>
                                    <p class="mb-0"><small>Total Quantity: ${result.items.reduce((sum, item) => sum + parseInt(item.quantity), 0)}</small></p>
                                </div>
                            </div>
                        </div>`;

                    // Update order items and totals
                    $('#orderItems').html(html);
                    $('#subtotal').text(`₱${total.toFixed(2)}`);
                    $('#total').text(`₱${total.toFixed(2)}`);
                    
                    // Set minimum payment amount
                    $('#paymentAmount').attr('min', total.toFixed(2));
                },
                error: function(xhr, status, error) {
                    console.error('Error loading order items:', error);
                    $('#orderItems').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading order items. Please try again.
                        </div>`);
                }
            });
        }

        $('#paymentAmount').on('input', function() {
            const payment = parseFloat($(this).val()) || 0;
            const change = payment - total;
            
            if (payment === 0) {
                $('#changeAmount').text('').removeClass('text-success text-danger');
                $('#completeOrderBtn').prop('disabled', true);
            } else if (change >= 0) {
                $('#changeAmount')
                    .text(`Change: ₱${change.toFixed(2)}`)
                    .removeClass('text-danger')
                    .addClass('text-success');
                $('#completeOrderBtn').prop('disabled', false);
            } else {
                $('#changeAmount')
                    .text(`Insufficient payment (₱${Math.abs(change).toFixed(2)} short)`)
                    .removeClass('text-success')
                    .addClass('text-danger');
                $('#completeOrderBtn').prop('disabled', true);
            }
        });

        $('#completeOrderBtn').click(function() {
            const customerName = $('#customerName').val().trim();
            const paymentAmount = parseFloat($('#paymentAmount').val());

            if (!customerName) {
                alert('Please enter customer name');
                $('#customerName').focus();
                return;
            }

            if (paymentAmount < total) {
                alert('Payment amount is insufficient');
                $('#paymentAmount').focus();
                return;
            }

            // Disable button and show processing
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');

            $.ajax({
                url: '../../controllers/SaleController.php',
                method: 'POST',
                data: {
                    action: 'create',
                    customer_name: customerName,
                    total_amount: total,
                    payment_amount: paymentAmount
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        showReceipt(result.receipt);
                        // Clear cart and redirect after modal is closed
                        $('#receiptModal').on('hidden.bs.modal', function () {
                            window.location.href = 'Products.php';
                        });
                    } else {
                        alert('Error: ' + result.message);
                        $('#completeOrderBtn')
                            .prop('disabled', false)
                            .html('<i class="fas fa-check-circle me-2"></i>Complete Order');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error creating sale:', error);
                    alert('Error processing order. Please try again.');
                    $('#completeOrderBtn')
                        .prop('disabled', false)
                        .html('<i class="fas fa-check-circle me-2"></i>Complete Order');
                }
            });
        });

        function showReceipt(receipt) {
            const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
            $('#receiptContent').html(receipt);
            modal.show();
        }

        // Add print function for receipt
        window.printReceipt = function() {
            const printContent = document.getElementById('receiptContent').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <html>
                <head>
                    <title>Receipt - Sari-Sari Store</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .table { border-collapse: collapse; width: 100%; }
                        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        .table th { background-color: #f2f2f2; }
                        .text-center { text-align: center; }
                        .text-end { text-align: right; }
                        .d-flex { display: flex; }
                        .justify-content-between { justify-content: space-between; }
                        .mb-0, .mb-1, .mb-3, .mb-4 { margin-bottom: 0; }
                        .mt-4 { margin-top: 1rem; }
                        @media print {
                            body { margin: 0; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>${printContent}</body>
                </html>`;
            
            window.print();
            document.body.innerHTML = originalContent;
            location.reload(); // Reload to restore functionality
        };
    });
    </script>
</body>
</html>