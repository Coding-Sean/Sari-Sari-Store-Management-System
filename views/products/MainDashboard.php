<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sari-Sari Store Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="Maindashboard.php">
                <i class="fas fa-store me-2"></i>Sari-Sari Store
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h1 class="text-center mb-2">🏪 Sari-Sari Store Management System</h1>
                <p class="text-center text-muted">Choose an option below to get started</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Products Management -->
            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100 text-center" onclick="window.location.href='Products.php'">
                    <div class="card-body">
                        <i class="fas fa-box feature-icon text-primary"></i>
                        <h4>Product Management</h4>
                        <p class="text-muted">Manage your inventory, add products, update stock levels</p>
                        <div class="mt-auto">
                            <span class="badge bg-primary">Inventory</span>
                            <span class="badge bg-secondary">Stock Control</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100 text-center" onclick="window.location.href='Cart.php'">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart feature-icon text-success"></i>
                        <h4>Point of Sale</h4>
                        <p class="text-muted">Process customer purchases, manage shopping cart</p>
                        <div class="mt-auto">
                            <span class="badge bg-success">POS</span>
                            <span class="badge bg-info">Sales</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Reports -->
            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100 text-center" onclick="window.location.href='../reports/ManagementReport.php'">
                    <div class="card-body">
                        <i class="fas fa-chart-bar feature-icon text-warning"></i>
                        <h4>Management Reports</h4>
                        <p class="text-muted">View sales reports, customer analytics, transaction history</p>
                        <div class="mt-auto">
                            <span class="badge bg-warning">Analytics</span>
                            <span class="badge bg-danger">Reports</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="Products.php" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-1"></i>Add Product
                            </a>
                            <a href="Cart.php" class="btn btn-outline-success">
                                <i class="fas fa-shopping-cart me-1"></i>New Sale
                            </a>
                            <a href="../reports/ManagementReport.php" class="btn btn-outline-warning">
                                <i class="fas fa-chart-line me-1"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2025 Sari-Sari Store Management System</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>