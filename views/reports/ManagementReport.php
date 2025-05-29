<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Reports - Sari-Sari Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .report-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sales-chart {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .product-chart {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .customer-chart {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .btn-export {
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../products/Products.php">
                <i class="fas fa-store me-2"></i>Sari-Sari Store
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../products/Products.php">
                    <i class="fas fa-box me-1"></i>Products
                </a>
                <a class="nav-link" href="../products/Cart.php">
                    <i class="fas fa-shopping-cart me-1"></i>Cart
                </a>
                <a class="nav-link active" href="ManagementReport.php">
                    <i class="fas fa-chart-bar me-1"></i>Reports
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-chart-bar me-2"></i>Management Reports & Analytics</h2>
                <p class="text-muted">Comprehensive sales reports and customer transaction analytics</p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <input type="date" class="form-control" id="startDate" value="<?= date('Y-m-01') ?>">
                    <input type="date" class="form-control" id="endDate" value="<?= date('Y-m-d') ?>">
                    <button class="btn btn-primary" id="applyDateFilter">
                        <i class="fas fa-filter me-1"></i>Apply Filter
                    </button>
                    <button class="btn btn-success" id="exportReport">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card metric-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-cash-register fa-3x mb-3"></i>
                        <h3 id="totalSales">₱0.00</h3>
                        <p class="mb-0">Total Sales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card sales-chart h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-receipt fa-3x mb-3"></i>
                        <h3 id="totalTransactions">0</h3>
                        <p class="mb-0">Total Transactions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card product-chart h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <h3 id="itemsSold">0</h3>
                        <p class="mb-0">Items Sold</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card customer-chart h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <h3 id="averageSale">₱0.00</h3>
                        <p class="mb-0">Average Sale</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button">
                    <i class="fas fa-list me-1"></i>All Transactions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button">
                    <i class="fas fa-calendar-alt me-1"></i>Monthly Sales
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                    <i class="fas fa-box me-1"></i>Product Report
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers" type="button">
                    <i class="fas fa-users me-1"></i>Customer Purchases
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reportTabContent">
            <!-- All Transactions Tab -->
            <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Transactions</h5>
                        <div>
                            <select class="form-select form-select-sm d-inline-block w-auto" id="transactionLimit">
                                <option value="50">Last 50</option>
                                <option value="100" selected>Last 100</option>
                                <option value="250">Last 250</option>
                                <option value="500">Last 500</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary btn-export" id="exportTransactions">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="transactionsTable">
                                <thead>
                                    <tr>
                                        <th>Sale ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Change</th>
                                        <th>Staff</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionsTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales Tab -->
            <div class="tab-pane fade" id="monthly" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Monthly Sales Report</h5>
                        <div>
                            <select class="form-select form-select-sm d-inline-block w-auto me-2" id="monthSelect">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                            <select class="form-select form-select-sm d-inline-block w-auto me-2" id="yearSelect">
                                <option value="2024">2024</option>
                                <option value="2025" selected>2025</option>
                                <option value="2026">2026</option>
                            </select>
                            <button class="btn btn-sm btn-primary" id="loadMonthlyReport">
                                <i class="fas fa-sync me-1"></i>Load
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="monthlyTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transactions</th>
                                        <th>Items Sold</th>
                                        <th>Total Sales</th>
                                    </tr>
                                </thead>
                                <tbody id="monthlyTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Select month and year to load report</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Report Tab -->
            <div class="tab-pane fade" id="products" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-box me-2"></i>Product Sales Report</h5>
                        <button class="btn btn-sm btn-outline-primary btn-export" id="exportProducts">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="productsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Times Sold</th>
                                        <th>Quantity Sold</th>
                                        <th>Total Sales</th>
                                        <th>Avg Price</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Purchases Tab -->
            <div class="tab-pane fade" id="customers" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Customer Purchase History</h5>
                        <div>
                            <input type="text" class="form-control form-control-sm d-inline-block w-auto me-2" 
                                   id="customerSearch" placeholder="Search customer...">
                            <button class="btn btn-sm btn-outline-primary btn-export" id="exportCustomers">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="customersTable">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Total Purchases</th>
                                        <th>Total Spent</th>
                                        <th>Average Purchase</th>
                                        <th>First Purchase</th>
                                        <th>Last Purchase</th>
                                    </tr>
                                </thead>
                                <tbody id="customersTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="transactionDetails">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="printTransaction">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function() {
        let currentStartDate = $('#startDate').val();
        let currentEndDate = $('#endDate').val();
        
        // Set current month for monthly report
        $('#monthSelect').val(new Date().getMonth() + 1);
        
        // Initialize the dashboard
        loadSalesSummary();
        loadAllTransactions();
        loadProductReport();
        loadCustomerPurchases();

        // Date filter functionality
        $('#applyDateFilter').click(function() {
            currentStartDate = $('#startDate').val();
            currentEndDate = $('#endDate').val();
            
            loadSalesSummary();
            loadAllTransactions();
            loadProductReport();
            loadCustomerPurchases();
        });

        // Tab switch handlers
        $('#transactions-tab').click(() => loadAllTransactions());
        $('#products-tab').click(() => loadProductReport());
        $('#customers-tab').click(() => loadCustomerPurchases());

        // Monthly report handler
        $('#loadMonthlyReport').click(function() {
            const year = $('#yearSelect').val();
            const month = $('#monthSelect').val();
            loadMonthlyReport(year, month);
        });

        // Transaction limit change
        $('#transactionLimit').change(() => loadAllTransactions());

        // Customer search
        $('#customerSearch').on('input', function() {
            const searchTerm = $(this).val();
            loadCustomerPurchases(searchTerm);
        });

        function loadSalesSummary() {
            $.ajax({
                url: '../../controllers/ReportController.php',
                method: 'POST',
                data: {
                    action: 'get_sales_summary',
                    start_date: currentStartDate,
                    end_date: currentEndDate
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        const summary = result.summary;
                        $('#totalSales').text('₱' + parseFloat(summary.total_sales || 0).toLocaleString('en-US', {minimumFractionDigits: 2}));
                        $('#totalTransactions').text((summary.total_transactions || 0).toLocaleString());
                        $('#itemsSold').text((summary.total_items_sold || 0).toLocaleString());
                        $('#averageSale').text('₱' + parseFloat(summary.average_sale || 0).toLocaleString('en-US', {minimumFractionDigits: 2}));
                    }
                },
                error: function() {
                    console.error('Error loading sales summary');
                }
            });
        }

        function loadAllTransactions() {
            const limit = $('#transactionLimit').val();
            
            $.ajax({
                url: '../../controllers/ReportController.php',
                method: 'POST',
                data: {
                    action: 'get_all_transactions',
                    start_date: currentStartDate,
                    end_date: currentEndDate,
                    limit: limit
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        displayTransactions(result.transactions);
                    } else {
                        showError('transactionsTableBody', 'Error loading transactions: ' + result.message);
                    }
                },
                error: function() {
                    showError('transactionsTableBody', 'Error loading transactions');
                }
            });
        }

        function displayTransactions(transactions) {
            let html = '';
            
            if (transactions.length === 0) {
                html = '<tr><td colspan="9" class="text-center text-muted">No transactions found</td></tr>';
            } else {
                transactions.forEach(transaction => {
                    const date = new Date(transaction.sale_date).toLocaleDateString();
                    const time = new Date(transaction.sale_date).toLocaleTimeString();
                    
                    html += `
                        <tr>
                            <td>#${transaction.sale_id}</td>
                            <td>${transaction.customer_name}</td>
                            <td>
                                <div>${date}</div>
                                <small class="text-muted">${time}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">${transaction.total_items}</span>
                                <small class="text-muted">(${transaction.total_quantity} qty)</small>
                            </td>
                            <td>₱${parseFloat(transaction.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td>₱${parseFloat(transaction.payment_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td>₱${parseFloat(transaction.change_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td>${transaction.staff_name || 'N/A'}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-transaction" 
                                        data-sale-id="${transaction.sale_id}"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            
            $('#transactionsTableBody').html(html);
            
            // Initialize DataTables if not already initialized
            if (!$.fn.DataTable.isDataTable('#transactionsTable')) {
                $('#transactionsTable').DataTable({
                    pageLength: 25,
                    order: [[0, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: -1 }
                    ]
                });
            }
        }

        function loadMonthlyReport(year, month) {
            $.ajax({
                url: '../../controllers/ReportController.php',
                method: 'POST',
                data: {
                    action: 'get_monthly_sales',
                    year: year,
                    month: month
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        displayMonthlyReport(result.sales);
                    } else {
                        showError('monthlyTableBody', 'Error loading monthly report: ' + result.message);
                    }
                },
                error: function() {
                    showError('monthlyTableBody', 'Error loading monthly report');
                }
            });
        }

        function displayMonthlyReport(sales) {
            let html = '';
            let totalSales = 0;
            let totalTransactions = 0;
            let totalItems = 0;
            
            if (sales.length === 0) {
                html = '<tr><td colspan="4" class="text-center text-muted">No sales data found for selected month</td></tr>';
            } else {
                sales.forEach(sale => {
                    totalSales += parseFloat(sale.total_sales);
                    totalTransactions += parseInt(sale.total_transactions);
                    totalItems += parseInt(sale.items_sold);
                    
                    html += `
                        <tr>
                            <td>${new Date(sale.sale_date).toLocaleDateString()}</td>
                            <td>${sale.total_transactions}</td>
                            <td>${sale.items_sold}</td>
                            <td>₱${parseFloat(sale.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        </tr>
                    `;
                });
                
                // Add summary row
                html += `
                    <tr class="table-info fw-bold">
                        <td>TOTAL</td>
                        <td>${totalTransactions}</td>
                        <td>${totalItems}</td>
                        <td>₱${totalSales.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                    </tr>
                `;
            }
            
            $('#monthlyTableBody').html(html);
        }

        function loadProductReport() {
            $.ajax({
                url: '../../controllers/ReportController.php',
                method: 'POST',
                data: {
                    action: 'get_top_selling_products',
                    start_date: currentStartDate,
                    end_date: currentEndDate,
                    limit: 50
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        displayProductReport(result.products);
                    } else {
                        showError('productsTableBody', 'Error loading product report: ' + result.message);
                    }
                },
                error: function() {
                    showError('productsTableBody', 'Error loading product report');
                }
            });
        }

        function displayProductReport(products) {
            let html = '';
            
            if (products.length === 0) {
                html = '<tr><td colspan="6" class="text-center text-muted">No product sales found</td></tr>';
            } else {
                products.forEach(product => {
                    html += `
                        <tr>
                            <td>${product.product_name}</td>
                            <td>
                                <span class="badge bg-secondary">${product.category_name}</span>
                            </td>
                            <td>${product.times_sold}</td>
                            <td>${product.total_quantity}</td>
                            <td>₱${parseFloat(product.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td>₱${parseFloat(product.avg_price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        </tr>
                    `;
                });
            }
            
            $('#productsTableBody').html(html);
        }

        function loadCustomerPurchases(customerName = null) {
            $.ajax({
                url: '../../controllers/ReportController.php',
                method: 'POST',
                data: {
                    action: 'get_customer_purchases',
                    customer_name: customerName,
                    start_date: currentStartDate,
                    end_date: currentEndDate
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        displayCustomerPurchases(result.customers);
                    } else {
                        showError('customersTableBody', 'Error loading customer data: ' + result.message);
                    }
                },
                error: function() {
                    showError('customersTableBody', 'Error loading customer data');
                }
            });
        }

        function displayCustomerPurchases(customers) {
            let html = '';
            
            if (customers.length === 0) {
                html = '<tr><td colspan="6" class="text-center text-muted">No customer purchases found</td></tr>';
            } else {
                customers.forEach(customer => {
                    html += `
                        <tr>
                            <td>${customer.customer_name}</td>
                            <td>${customer.total_purchases}</td>
                            <td>₱${parseFloat(customer.total_spent).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td>₱${parseFloat(customer.average_purchase).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            <td>${new Date(customer.first_purchase).toLocaleDateString()}</td>
                            <td>${new Date(customer.last_purchase).toLocaleDateString()}</td>
                        </tr>
                    `;
                });
            }
            
            $('#customersTableBody').html(html);
        }

        // View transaction details
        $(document).on('click', '.view-transaction', function() {
            const saleId = $(this).data('sale-id');
            
            $.ajax({
                url: '../../controllers/ReportController.php',
                method: 'POST',
                data: {
                    action: 'get_transaction_details',
                    sale_id: saleId
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        displayTransactionDetails(result.transaction);
                        const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
                        modal.show();
                    } else {
                        alert('Error loading transaction details: ' + result.message);
                    }
                },
                error: function() {
                    alert('Error loading transaction details');
                }
            });
        });

        function displayTransactionDetails(transaction) {
            let itemsHtml = '';
            transaction.items.forEach(item => {
                itemsHtml += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-end">₱${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td class="text-end">₱${parseFloat(item.subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });

            const html = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Transaction Information</h6>
                        <p><strong>Sale ID:</strong> #${transaction.sale_id}</p>
                        <p><strong>Customer:</strong> ${transaction.customer_name}</p>
                        <p><strong>Date:</strong> ${new Date(transaction.sale_date).toLocaleString()}</p>
                        <p><strong>Staff:</strong> ${transaction.staff_name || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Information</h6>
                        <p><strong>Total Amount:</strong> ₱${parseFloat(transaction.total_amount).toFixed(2)}</p>
                        <p><strong>Payment:</strong> ₱${parseFloat(transaction.payment_amount).toFixed(2)}</p>
                        <p><strong>Change:</strong> ₱${parseFloat(transaction.change_amount).toFixed(2)}</p>
                    </div>
                </div>
                <h6>Items Purchased</h6>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                </table>
            `;
            
            $('#transactionDetails').html(html);
        }

        function showError(tableBodyId, message) {
            const colspan = $(`#${tableBodyId}`).parent().find('thead th').length;
            $(`#${tableBodyId}`).html(`
                <tr>
                    <td colspan="${colspan}" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${message}
                    </td>
                </tr>
            `);
        }

        // Export functionality (placeholder)
        $('#exportReport, #exportTransactions, #exportProducts, #exportCustomers').click(function() {
            alert('Export functionality will be implemented soon!');
        });
    });
    </script>
</body>
</html>