
-- Stored Procedures
DELIMITER $$

-- Enhanced Daily Sales Report
CREATE PROCEDURE get_daily_sales_report(IN p_date DATE)
BEGIN
    SELECT 
        DATE_FORMAT(s.sale_date, '%H:00') as sale_hour,
        COUNT(DISTINCT s.sale_id) as total_transactions,
        SUM(s.total_amount) as total_sales,
        SUM(si.quantity) as total_items_sold,
        AVG(s.total_amount) as average_sale,
        MIN(s.total_amount) as min_sale,
        MAX(s.total_amount) as max_sale,
        GROUP_CONCAT(DISTINCT s.customer_name ORDER BY s.sale_date SEPARATOR ', ') as customers
    FROM sales s
    JOIN sale_items si ON s.sale_id = si.sale_id
    WHERE DATE(s.sale_date) = p_date
    GROUP BY DATE_FORMAT(s.sale_date, '%H')
    ORDER BY sale_hour;
END$$

-- Get All Transactions with Filters
CREATE PROCEDURE get_all_transactions(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_customer_name VARCHAR(100),
    IN p_limit INT
)
BEGIN
    DECLARE sql_query TEXT;
    
    SET sql_query = '
        SELECT 
            s.sale_id,
            s.customer_name,
            s.total_amount,
            s.payment_amount,
            s.change_amount,
            s.sale_date,
            u.username as staff_name,
            COUNT(si.sale_item_id) as total_items,
            SUM(si.quantity) as total_quantity
        FROM sales s
        LEFT JOIN users u ON s.created_by = u.user_id
        LEFT JOIN sale_items si ON s.sale_id = si.sale_id
        WHERE 1=1';
    
    -- Add date filter if provided
    IF p_start_date IS NOT NULL AND p_end_date IS NOT NULL THEN
        SET sql_query = CONCAT(sql_query, ' AND DATE(s.sale_date) BETWEEN ''', p_start_date, ''' AND ''', p_end_date, '''');
    ELSEIF p_start_date IS NOT NULL THEN
        SET sql_query = CONCAT(sql_query, ' AND DATE(s.sale_date) >= ''', p_start_date, '''');
    ELSEIF p_end_date IS NOT NULL THEN
        SET sql_query = CONCAT(sql_query, ' AND DATE(s.sale_date) <= ''', p_end_date, '''');
    END IF;
    
    -- Add customer filter if provided
    IF p_customer_name IS NOT NULL AND p_customer_name != '' THEN
        SET sql_query = CONCAT(sql_query, ' AND s.customer_name LIKE ''%', p_customer_name, '%''');
    END IF;
    
    SET sql_query = CONCAT(sql_query, ' GROUP BY s.sale_id ORDER BY s.sale_date DESC');
    
    -- Add limit if provided
    IF p_limit IS NOT NULL AND p_limit > 0 THEN
        SET sql_query = CONCAT(sql_query, ' LIMIT ', p_limit);
    END IF;
    
    SET @sql = sql_query;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

-- Get Transaction Details by Sale ID
CREATE PROCEDURE get_transaction_details(IN p_sale_id INT)
BEGIN
    -- Get sale header information
    SELECT 
        s.sale_id,
        s.customer_name,
        s.total_amount,
        s.payment_amount,
        s.change_amount,
        s.sale_date,
        u.username as staff_name,
        u.user_id as staff_id
    FROM sales s
    LEFT JOIN users u ON s.created_by = u.user_id
    WHERE s.sale_id = p_sale_id;
    
    -- Get sale items information
    SELECT 
        si.sale_item_id,
        si.product_id,
        p.name as product_name,
        c.name as category_name,
        si.quantity,
        si.unit_price,
        si.subtotal,
        p.description as product_description
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE si.sale_id = p_sale_id
    ORDER BY si.sale_item_id;
END$$

-- Get Customer Purchase History
CREATE PROCEDURE get_customer_purchases(
    IN p_customer_name VARCHAR(100),
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    SELECT 
        s.customer_name,
        COUNT(s.sale_id) as total_purchases,
        SUM(s.total_amount) as total_spent,
        AVG(s.total_amount) as average_purchase,
        MIN(s.total_amount) as min_purchase,
        MAX(s.total_amount) as max_purchase,
        MIN(s.sale_date) as first_purchase,
        MAX(s.sale_date) as last_purchase,
        SUM(si.quantity) as total_items_bought,
        COUNT(DISTINCT si.product_id) as unique_products_bought
    FROM sales s
    LEFT JOIN sale_items si ON s.sale_id = si.sale_id
    WHERE 1=1
    AND (p_customer_name IS NULL OR p_customer_name = '' OR s.customer_name LIKE CONCAT('%', p_customer_name, '%'))
    AND (p_start_date IS NULL OR DATE(s.sale_date) >= p_start_date)
    AND (p_end_date IS NULL OR DATE(s.sale_date) <= p_end_date)
    GROUP BY s.customer_name
    HAVING COUNT(s.sale_id) > 0
    ORDER BY total_spent DESC;
END$$

-- Get Sales Summary for Dashboard
CREATE PROCEDURE get_sales_summary(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    SELECT 
        COUNT(DISTINCT s.sale_id) as total_transactions,
        COALESCE(SUM(s.total_amount), 0) as total_sales,
        COALESCE(AVG(s.total_amount), 0) as average_sale,
        COALESCE(SUM(si.quantity), 0) as total_items_sold,
        COUNT(DISTINCT si.product_id) as unique_products_sold,
        COUNT(DISTINCT s.customer_name) as unique_customers,
        MIN(s.sale_date) as first_sale_date,
        MAX(s.sale_date) as last_sale_date
    FROM sales s
    LEFT JOIN sale_items si ON s.sale_id = si.sale_id
    WHERE (p_start_date IS NULL OR DATE(s.sale_date) >= p_start_date)
    AND (p_end_date IS NULL OR DATE(s.sale_date) <= p_end_date);
END$$

-- Get Customer Transaction Details
CREATE PROCEDURE get_customer_transaction_details(
    IN p_customer_name VARCHAR(100),
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    SELECT 
        s.sale_id,
        s.customer_name,
        s.total_amount,
        s.payment_amount,
        s.change_amount,
        s.sale_date,
        u.username as staff_name,
        COUNT(si.sale_item_id) as item_count,
        SUM(si.quantity) as total_quantity
    FROM sales s
    LEFT JOIN users u ON s.created_by = u.user_id
    LEFT JOIN sale_items si ON s.sale_id = si.sale_id
    WHERE s.customer_name = p_customer_name
    AND (p_start_date IS NULL OR DATE(s.sale_date) >= p_start_date)
    AND (p_end_date IS NULL OR DATE(s.sale_date) <= p_end_date)
    GROUP BY s.sale_id
    ORDER BY s.sale_date DESC;
END$$

-- Get Hourly Sales Report
CREATE PROCEDURE get_hourly_sales_report(IN p_date DATE)
BEGIN
    WITH RECURSIVE hours AS (
        SELECT 0 as hour
        UNION ALL
        SELECT hour + 1 FROM hours WHERE hour < 23
    )
    SELECT 
        CONCAT(LPAD(h.hour, 2, '0'), ':00') as sale_hour,
        COALESCE(COUNT(DISTINCT s.sale_id), 0) as total_transactions,
        COALESCE(SUM(s.total_amount), 0) as total_sales,
        COALESCE(SUM(si.quantity), 0) as total_items_sold
    FROM hours h
    LEFT JOIN sales s ON HOUR(s.sale_date) = h.hour 
        AND DATE(s.sale_date) = p_date
    LEFT JOIN sale_items si ON s.sale_id = si.sale_id
    GROUP BY h.hour
    ORDER BY h.hour;
END$$

-- Get Weekly Sales Report
CREATE PROCEDURE get_weekly_sales_report(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    SELECT 
        DATE(s.sale_date) as sale_date,
        DAYNAME(s.sale_date) as day_name,
        COUNT(DISTINCT s.sale_id) as total_transactions,
        SUM(s.total_amount) as total_sales,
        SUM(si.quantity) as total_items_sold,
        AVG(s.total_amount) as average_sale
    FROM sales s
    LEFT JOIN sale_items si ON s.sale_id = si.sale_id
    WHERE DATE(s.sale_date) BETWEEN p_start_date AND p_end_date
    GROUP BY DATE(s.sale_date)
    ORDER BY sale_date;
END$$

-- Get Transaction Count by Customer
CREATE PROCEDURE get_transaction_count_by_customer(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_min_transactions INT
)
BEGIN
    SELECT 
        s.customer_name,
        COUNT(s.sale_id) as transaction_count,
        SUM(s.total_amount) as total_spent,
        AVG(s.total_amount) as average_spent,
        MIN(s.sale_date) as first_visit,
        MAX(s.sale_date) as last_visit,
        DATEDIFF(MAX(s.sale_date), MIN(s.sale_date)) as customer_lifespan_days
    FROM sales s
    WHERE (p_start_date IS NULL OR DATE(s.sale_date) >= p_start_date)
    AND (p_end_date IS NULL OR DATE(s.sale_date) <= p_end_date)
    GROUP BY s.customer_name
    HAVING COUNT(s.sale_id) >= COALESCE(p_min_transactions, 1)
    ORDER BY transaction_count DESC, total_spent DESC;
END$$

DELIMITER ;


-- Cart Management Procedures
DELIMITER $$

CREATE PROCEDURE get_cart_count(
    IN p_session_id VARCHAR(255)
)
BEGIN
    SELECT COUNT(*) as count 
    FROM cart_items 
    WHERE session_id = p_session_id;
END$$

CREATE PROCEDURE get_all_products()
BEGIN
    SELECT 
        p.*, 
        c.name as category_name,
        CASE 
            WHEN p.quantity > 0 THEN 'In Stock'
            ELSE 'Out of Stock'
        END as stock_status
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.is_active = 1
    ORDER BY p.name;
END$$

CREATE PROCEDURE get_product_by_id(IN p_id INT)
BEGIN
    SELECT 
        p.*, 
        c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = p_id AND p.is_active = 1;
END$$

CREATE PROCEDURE add_product(
    IN p_category_id INT,
    IN p_name VARCHAR(100),
    IN p_description VARCHAR(255),
    IN p_price DECIMAL(10,2),
    IN p_quantity INT
)
BEGIN
    INSERT INTO products (category_id, name, description, price, quantity, is_active)
    VALUES (p_category_id, p_name, p_description, p_price, p_quantity, 1);
    SELECT LAST_INSERT_ID() as product_id;
END$$

CREATE PROCEDURE update_product(
    IN p_id INT,
    IN p_category_id INT,
    IN p_name VARCHAR(100),
    IN p_description VARCHAR(255),
    IN p_price DECIMAL(10,2),
    IN p_quantity INT
)
BEGIN
    UPDATE products 
    SET category_id = p_category_id,
        name = p_name,
        description = p_description,
        price = p_price,
        quantity = p_quantity,
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = p_id AND is_active = 1;
END$$

CREATE PROCEDURE delete_product(IN p_id INT)
BEGIN
    UPDATE products 
    SET is_active = 0,
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = p_id;
    
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE add_to_cart(
    IN p_session_id VARCHAR(255),
    IN p_product_id INT,
    IN p_quantity INT
)
BEGIN
    DECLARE current_stock INT DEFAULT 0;
    DECLARE current_cart INT DEFAULT 0;
    DECLARE product_active INT DEFAULT 0;
    
    -- Check if product is active
    SELECT COALESCE(is_active, 0) INTO product_active 
    FROM products 
    WHERE product_id = p_product_id;
    
    IF product_active = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Product is no longer available';
    END IF;
    
    -- Check product stock
    SELECT COALESCE(quantity, 0) INTO current_stock 
    FROM products 
    WHERE product_id = p_product_id AND is_active = 1;
    
    -- Check current cart quantity
    SELECT COALESCE(SUM(quantity), 0) INTO current_cart
    FROM cart_items 
    WHERE session_id = p_session_id 
    AND product_id = p_product_id;
    
    IF current_stock >= (current_cart + p_quantity) THEN
        INSERT INTO cart_items (session_id, product_id, quantity)
        VALUES (p_session_id, p_product_id, p_quantity)
        ON DUPLICATE KEY UPDATE 
            quantity = quantity + p_quantity,
            updated_at = CURRENT_TIMESTAMP;
            
        -- Return updated cart item
        SELECT 
            ci.cart_item_id,
            ci.product_id,
            ci.quantity,
            p.name as product_name,
            p.price as unit_price,
            p.quantity as available_stock,
            (ci.quantity * p.price) as subtotal
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.session_id = p_session_id
        AND ci.product_id = p_product_id
        AND p.is_active = 1;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock';
    END IF;
END$$

CREATE PROCEDURE update_cart_quantity(
    IN p_session_id VARCHAR(255),
    IN p_product_id INT,
    IN p_quantity INT
)
BEGIN
    DECLARE current_stock INT DEFAULT 0;
    
    -- Check product stock
    SELECT COALESCE(quantity, 0) INTO current_stock 
    FROM products 
    WHERE product_id = p_product_id AND is_active = 1;
    
    IF current_stock >= p_quantity THEN
        UPDATE cart_items 
        SET quantity = p_quantity,
            updated_at = CURRENT_TIMESTAMP
        WHERE session_id = p_session_id 
        AND product_id = p_product_id;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock';
    END IF;
END$$

CREATE PROCEDURE remove_from_cart(
    IN p_session_id VARCHAR(255),
    IN p_product_id INT
)
BEGIN
    DELETE FROM cart_items 
    WHERE session_id = p_session_id 
    AND product_id = p_product_id;
END$$

CREATE PROCEDURE get_cart_items(IN p_session_id VARCHAR(255))
BEGIN
    SELECT 
        ci.cart_item_id,
        ci.product_id,
        ci.quantity,
        ci.session_id,
        p.name as product_name,
        p.description as product_description,
        c.name as category_name,
        p.price as unit_price,
        p.quantity as available_stock,
        (ci.quantity * p.price) as subtotal,
        CASE 
            WHEN p.quantity > 0 THEN 'In Stock'
            ELSE 'Out of Stock'
        END as stock_status
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.product_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE ci.session_id = p_session_id
    AND p.is_active = 1
    ORDER BY ci.cart_item_id DESC;
END$$

CREATE PROCEDURE create_sale(
    IN p_customer_name VARCHAR(100),
    IN p_total_amount DECIMAL(10,2),
    IN p_payment_amount DECIMAL(10,2),
    IN p_created_by INT
)
BEGIN
    DECLARE v_sale_id INT;
    DECLARE v_change DECIMAL(10,2);
    
    SET v_change = p_payment_amount - p_total_amount;
    
    IF v_change < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient payment amount';
    END IF;
    
    INSERT INTO sales (
        customer_name, 
        total_amount, 
        payment_amount,
        change_amount,
        created_by
    ) VALUES (
        p_customer_name,
        p_total_amount,
        p_payment_amount,
        v_change,
        p_created_by
    );
    
    SET v_sale_id = LAST_INSERT_ID();
    
    SELECT v_sale_id as sale_id;
END$$

CREATE PROCEDURE add_sale_item(
    IN p_sale_id INT,
    IN p_product_id INT,
    IN p_quantity INT,
    IN p_unit_price DECIMAL(10,2)
)
BEGIN
    DECLARE v_current_stock INT DEFAULT 0;
    DECLARE v_subtotal DECIMAL(10,2);
    
    -- Get current stock
    SELECT COALESCE(quantity, 0) INTO v_current_stock
    FROM products
    WHERE product_id = p_product_id AND is_active = 1;
    
    IF v_current_stock < p_quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock';
    END IF;
    
    SET v_subtotal = p_quantity * p_unit_price;
    
    -- Add sale item
    INSERT INTO sale_items (
        sale_id,
        product_id,
        quantity,
        unit_price,
        subtotal
    ) VALUES (
        p_sale_id,
        p_product_id,
        p_quantity,
        p_unit_price,
        v_subtotal
    );
    
    -- Update product stock
    UPDATE products
    SET quantity = quantity - p_quantity,
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = p_product_id AND is_active = 1;
END$$

CREATE PROCEDURE get_daily_sales(IN p_date DATE)
BEGIN
    SELECT 
        DATE_FORMAT(s.sale_date, '%H:00') as hour,
        COUNT(DISTINCT s.sale_id) as total_transactions,
        SUM(s.total_amount) as total_sales,
        GROUP_CONCAT(DISTINCT p.name) as products_sold
    FROM sales s
    JOIN sale_items si ON s.sale_id = si.sale_id
    JOIN products p ON si.product_id = p.product_id
    WHERE DATE(s.sale_date) = p_date
    GROUP BY DATE_FORMAT(s.sale_date, '%H')
    ORDER BY hour;
END$$

CREATE PROCEDURE get_monthly_sales(IN p_year INT, IN p_month INT)
BEGIN
    SELECT 
        DATE(s.sale_date) as sale_date,
        COUNT(DISTINCT s.sale_id) as total_transactions,
        SUM(s.total_amount) as total_sales,
        SUM(si.quantity) as items_sold
    FROM sales s
    JOIN sale_items si ON s.sale_id = si.sale_id
    WHERE YEAR(s.sale_date) = p_year
    AND MONTH(s.sale_date) = p_month
    GROUP BY DATE(s.sale_date)
    ORDER BY sale_date;
END$$

CREATE PROCEDURE get_product_sales_report(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    SELECT 
        p.name as product_name,
        c.name as category_name,
        COUNT(DISTINCT s.sale_id) as times_sold,
        SUM(si.quantity) as total_quantity,
        SUM(si.subtotal) as total_sales
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN sale_items si ON p.product_id = si.product_id
    LEFT JOIN sales s ON si.sale_id = s.sale_id
    WHERE s.sale_date BETWEEN p_start_date AND p_end_date
    GROUP BY p.product_id
    ORDER BY total_sales DESC;
END$$

CREATE PROCEDURE get_all_categories()
BEGIN
    SELECT 
        c.*,
        COUNT(p.product_id) as product_count,
        SUM(CASE WHEN p.quantity > 0 THEN 1 ELSE 0 END) as available_products
    FROM categories c
    LEFT JOIN products p ON c.category_id = p.category_id AND p.is_active = 1
    GROUP BY c.category_id, c.name, c.description, c.created_at
    ORDER BY c.name;
END$$

CREATE PROCEDURE get_products_by_category()
BEGIN
    SELECT 
        c.category_id,
        c.name as category_name,
        c.description as category_description,
        p.product_id,
        p.name as product_name,
        p.description as product_description,
        p.price,
        p.quantity,
        CASE 
            WHEN p.quantity > 0 THEN 'In Stock'
            ELSE 'Out of Stock'
        END as stock_status
    FROM categories c
    LEFT JOIN products p ON c.category_id = p.category_id AND p.is_active = 1
    ORDER BY c.name, p.name;
END$$

CREATE PROCEDURE get_products_by_category_id(IN p_category_id INT)
BEGIN
    SELECT 
        p.*, 
        c.name as category_name,
        CASE 
            WHEN p.quantity > 0 THEN 'In Stock'
            ELSE 'Out of Stock'
        END as stock_status
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.category_id = p_category_id AND p.is_active = 1
    ORDER BY p.name;
END$$

DELIMITER ;


-- sales summary procedure
DELIMITER $$

CREATE PROCEDURE get_sales_summary(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    -- Aggregate sale totals first
    SELECT 
        COUNT(*) AS total_transactions,
        COALESCE(SUM(s.total_amount), 0) AS total_sales,
        COALESCE(AVG(s.total_amount), 0) AS average_sale,
        COALESCE(SUM(items.total_quantity), 0) AS total_items_sold,
        (SELECT COUNT(DISTINCT si.product_id)
         FROM sales s2
         JOIN sale_items si ON s2.sale_id = si.sale_id
         WHERE (p_start_date IS NULL OR DATE(s2.sale_date) >= p_start_date)
           AND (p_end_date IS NULL OR DATE(s2.sale_date) <= p_end_date)
        ) AS unique_products_sold,
        COUNT(DISTINCT s.customer_name) AS unique_customers,
        MIN(s.sale_date) AS first_sale_date,
        MAX(s.sale_date) AS last_sale_date
    FROM sales s
    LEFT JOIN (
        SELECT sale_id, SUM(quantity) AS total_quantity
        FROM sale_items
        GROUP BY sale_id
    ) items ON s.sale_id = items.sale_id
    WHERE (p_start_date IS NULL OR DATE(s.sale_date) >= p_start_date)
      AND (p_end_date IS NULL OR DATE(s.sale_date) <= p_end_date);
END$$

DELIMITER ;

-- Top Selling Products Procedure
DELIMITER //

CREATE PROCEDURE get_top_selling_products(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_limit INT
)
BEGIN
    SELECT 
        p.name as product_name,
        c.name as category_name,
        COUNT(si.sale_id) as times_sold,
        SUM(si.quantity) as total_quantity,
        SUM(si.quantity * si.unit_price) as total_sales,
        AVG(si.unit_price) as avg_price
    FROM sale_items si
    INNER JOIN products p ON si.product_id = p.product_id
    INNER JOIN categories c ON p.category_id = c.category_id
    INNER JOIN sales s ON si.sale_id = s.sale_id
    WHERE 
        (p_start_date IS NULL OR DATE(s.sale_date) >= p_start_date)
        AND (p_end_date IS NULL OR DATE(s.sale_date) <= p_end_date)
    GROUP BY p.product_id, p.name, c.name
    ORDER BY total_sales DESC
    LIMIT p_limit;
END //

DELIMITER ;