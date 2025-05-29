
-- Create Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Categories Table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Products Table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Create Sales Table (Transaction Header)
CREATE TABLE sales (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) DEFAULT 'Walk-in Customer',
    total_amount DECIMAL(10,2) NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    change_amount DECIMAL(10,2) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- Create Sale Items Table (Transaction Details)
CREATE TABLE sale_items (
    sale_item_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create Cart Table
CREATE TABLE cart_items (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (session_id, product_id)
);

-- Create Audit Log Table
CREATE TABLE audit_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT NOT NULL,
    changes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Insert Default Admin User (password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Categories
INSERT INTO categories (name, description) VALUES 
('Beverages', 'Drinks and liquid refreshments'),
('Snacks', 'Light food and chips'),
('Canned Goods', 'Preserved and canned foods'),
('Household', 'Home and cleaning items'),
('Personal Care', 'Hygiene and personal items');

-- Insert Sample Products
INSERT INTO products (category_id, name, description, price, quantity) VALUES
-- Beverages
(1, 'Coca-Cola 1.5L', 'Regular Coke Softdrink', 65.00, 50),
(1, 'Sprite 1.5L', 'Lemon-Lime Softdrink', 65.00, 40),
(1, 'Summit Water 500ml', 'Purified Drinking Water', 15.00, 100),
(1, 'Milo 24g Sachet', 'Chocolate Malt Drink', 10.00, 150),
(1, 'Nescafe 3-in-1', 'Instant Coffee Mix', 12.00, 200),

-- Snacks
(2, 'Nova Multi-Pack', 'Assorted Chips', 82.00, 30),
(2, 'Piattos Classic', 'Potato Crisps', 15.00, 60),
(2, 'Sky Flakes', 'Plain Crackers', 25.00, 45),
(2, 'Hansel Sandwich', 'Sweet Sandwich Crackers', 28.00, 40),
(2, 'Clover Chips', 'Cheese Flavored Chips', 12.00, 75),

-- Canned Goods
(3, 'Argentina Corned Beef', 'Premium Corned Beef', 38.00, 40),
(3, 'Century Tuna Regular', 'Tuna in Vegetable Oil', 35.00, 50),
(3, 'Sardinas 555', 'Tomato Sauce Sardines', 22.00, 60),
(3, 'Mega Sardines', 'Spicy Sardines', 23.00, 45),
(3, 'San Marino Tuna', 'Tuna Flakes in Oil', 28.00, 55),

-- Household
(4, 'Joy Dishwashing', 'Lemon Dishwashing Liquid', 42.00, 30),
(4, 'Tide Powder 75g', 'Laundry Detergent', 12.00, 100),
(4, 'Star Margarine 100g', 'Regular Margarine', 24.00, 40),
(4, 'Zonrox Bleach 250ml', 'Original Bleach', 26.00, 35),
(4, 'Baygon Spray', 'Insect Killer Spray', 125.00, 20),

-- Personal Care
(5, 'Safeguard Pure White', 'Antibacterial Soap', 45.00, 60),
(5, 'Head & Shoulders 12ml', 'Anti-Dandruff Shampoo', 8.00, 150),
(5, 'Colgate Regular 50g', 'Toothpaste', 35.00, 75),
(5, 'Rexona Roll-On', 'Antiperspirant Deodorant', 85.00, 40),
(5, 'Lucky Me Pancit Canton', 'Instant Noodles', 15.00, 200);





























