CREATE DATABASE IF NOT EXISTS fastfood_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fastfood_db;

-- =====================================================
-- Bảng: users (Người dùng - Admin và Khách hàng)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NULL COMMENT 'NULL nếu đăng nhập bằng Google',
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    avatar VARCHAR(255),
    google_id VARCHAR(255) UNIQUE,
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    login_method ENUM('normal', 'google') DEFAULT 'normal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_google_id (google_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: categories (Danh mục món ăn)
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: products (Món ăn)
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2),
    image VARCHAR(255),
    images TEXT COMMENT 'JSON array of additional images',
    stock_quantity INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: product_images (Hình ảnh món ăn)
-- =====================================================
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Ảnh chính của sản phẩm',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_primary (is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: posts (Bài viết/Tin tức)
-- =====================================================
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    status ENUM('published', 'draft', 'hidden') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_author (author_id),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_published (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: comments (Bình luận)
-- =====================================================
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT,
    product_id INT,
    parent_id INT NULL COMMENT 'For nested comments',
    content TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5) COMMENT 'Rating for products',
    status ENUM('approved', 'pending', 'rejected', 'hidden') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_post (post_id),
    INDEX idx_product (product_id),
    INDEX idx_status (status),
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: orders (Đơn hàng)
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_fee DECIMAL(10, 2) DEFAULT 0,
    discount DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cod', 'bank_transfer', 'momo', 'vnpay', 'credit_card') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    order_status ENUM('pending', 'confirmed', 'preparing', 'shipping', 'delivered', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    cancelled_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    confirmed_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_payment_status (payment_status),
    INDEX idx_order_status (order_status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: order_items (Chi tiết đơn hàng)
-- =====================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_image VARCHAR(255),
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: payments (Thanh toán)
-- =====================================================
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    transaction_id VARCHAR(255) UNIQUE,
    payment_method ENUM('cod', 'bank_transfer', 'momo', 'vnpay', 'credit_card') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_details TEXT COMMENT 'JSON data for payment gateway response',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_transaction (transaction_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: cart (Giỏ hàng)
-- =====================================================
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: order_status_history (Lịch sử trạng thái đơn hàng)
-- =====================================================
CREATE TABLE IF NOT EXISTS order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'shipping', 'delivered', 'cancelled') NOT NULL,
    note TEXT,
    created_by INT COMMENT 'Admin user ID who changed status',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: favorites (Danh sách yêu thích)
-- =====================================================
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: contacts (Liên hệ từ khách hàng)
-- =====================================================
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DỮ LIỆU MẪU
-- =====================================================

-- Tạo tài khoản Admin mặc định (password: admin123)
INSERT INTO users (email, password, full_name, phone, role, status) VALUES
('admin@fastfood.com', '$2y$10$4IEGO7k.Nj/1MBDtGdFomeblNzQK4wlMiUmXC3ZL6IWA0yAM0NaZu', 'Administrator', '0123456789', 'admin', 'active');

-- Tạo tài khoản khách hàng mẫu (password: customer123)
INSERT INTO users (email, password, full_name, phone, address, role, status) VALUES
('customer@example.com', '$2y$10$4IEGO7k.Nj/1MBDtGdFomeblNzQK4wlMiUmXC3ZL6IWA0yAM0NaZu', 'Nguyễn Văn A', '0987654321', '123 Đường ABC, Quận 1, TP.HCM', 'customer', 'active');

-- Danh mục món ăn
INSERT INTO categories (name, slug, description, display_order, status) VALUES
('Burger', 'burger', 'Các loại burger thơm ngon', 1, 'active'),
('Pizza', 'pizza', 'Pizza Ý đa dạng hương vị', 2, 'active'),
('Gà rán', 'ga-ran', 'Gà rán giòn tan', 3, 'active'),
('Đồ uống', 'do-uong', 'Nước giải khát các loại', 4, 'active'),
('Món phụ', 'mon-phu', 'Khoai tây chiên, salad...', 5, 'active');

-- Món ăn mẫu
INSERT INTO products (category_id, name, slug, description, price, sale_price, stock_quantity, is_featured, status) VALUES
(1, 'Burger Bò Phô Mai', 'burger-bo-pho-mai', 'Burger bò 100% thịt bò Úc, phô mai cheddar tan chảy', 65000, 55000, 100, TRUE, 'active'),
(1, 'Burger Gà Giòn', 'burger-ga-gion', 'Burger gà giòn rụm với sốt mayonnaise đặc biệt', 55000, NULL, 100, FALSE, 'active'),
(2, 'Pizza Hải Sản', 'pizza-hai-san', 'Pizza hải sản tươi ngon với tôm, mực, nghêu', 150000, 135000, 50, TRUE, 'active'),
(2, 'Pizza Xúc Xích', 'pizza-xuc-xich', 'Pizza xúc xích Đức, phô mai mozzarella', 120000, NULL, 50, FALSE, 'active'),
(3, 'Gà Rán Giòn (3 miếng)', 'ga-ran-gion-3-mieng', 'Gà rán giòn tan, tẩm bột đặc biệt', 75000, NULL, 80, TRUE, 'active'),
(3, 'Cánh Gà Sốt BBQ', 'canh-ga-sot-bbq', 'Cánh gà nướng sốt BBQ thơm lừng', 65000, 59000, 80, FALSE, 'active'),
(4, 'Coca Cola', 'coca-cola', 'Nước ngọt Coca Cola 330ml', 15000, NULL, 200, FALSE, 'active'),
(4, 'Trà Đào Cam Sả', 'tra-dao-cam-sa', 'Trà đào cam sả tự nhiên mát lạnh', 35000, NULL, 100, FALSE, 'active'),
(5, 'Khoai Tây Chiên', 'khoai-tay-chien', 'Khoai tây chiên giòn rụm size vừa', 25000, NULL, 150, FALSE, 'active'),
(5, 'Salad Rau Củ', 'salad-rau-cu', 'Salad rau củ tươi với sốt Thousand Island', 30000, NULL, 100, FALSE, 'active');

-- Hình ảnh món ăn mẫu
INSERT INTO product_images (product_id, image_url, alt_text, is_primary, display_order) VALUES
-- Burger Bò Phô Mai (product_id = 1)
(1, 'public/images/products/burger-bo-1.jpg', 'Burger Bò Phô Mai - Ảnh chính', TRUE, 1),
(1, 'public/images/products/burger-bo-2.jpg', 'Burger Bò Phô Mai - Góc cạnh', FALSE, 2),
(1, 'public/images/products/burger-bo-3.jpg', 'Burger Bò Phô Mai - Chi tiết', FALSE, 3),
-- Burger Gà Giòn (product_id = 2)
(2, 'public/images/products/burger-ga-1.jpg', 'Burger Gà Giòn - Ảnh chính', TRUE, 1),
(2, 'public/images/products/burger-ga-2.jpg', 'Burger Gà Giòn - Góc cạnh', FALSE, 2),
-- Pizza Hải Sản (product_id = 3)
(3, 'public/images/products/pizza-haisan-1.jpg', 'Pizza Hải Sản - Ảnh chính', TRUE, 1),
(3, 'public/images/products/pizza-haisan-2.jpg', 'Pizza Hải Sản - Topping', FALSE, 2),
(3, 'public/images/products/pizza-haisan-3.jpg', 'Pizza Hải Sản - Cắt miếng', FALSE, 3),
(3, 'public/images/products/pizza-haisan-4.jpg', 'Pizza Hải Sản - Đóng hộp', FALSE, 4),
-- Pizza Xúc Xích (product_id = 4)
(4, 'public/images/products/pizza-xucxich-1.jpg', 'Pizza Xúc Xích - Ảnh chính', TRUE, 1),
(4, 'public/images/products/pizza-xucxich-2.jpg', 'Pizza Xúc Xích - Chi tiết', FALSE, 2),
-- Gà Rán Giòn (product_id = 5)
(5, 'public/images/products/ga-ran-1.jpg', 'Gà Rán Giòn - Ảnh chính', TRUE, 1),
(5, 'public/images/products/ga-ran-2.jpg', 'Gà Rán Giòn - Combo', FALSE, 2),
(5, 'public/images/products/ga-ran-3.jpg', 'Gà Rán Giòn - Cận cảnh', FALSE, 3),
-- Cánh Gà Sốt BBQ (product_id = 6)
(6, 'public/images/products/canh-ga-bbq-1.jpg', 'Cánh Gà Sốt BBQ - Ảnh chính', TRUE, 1),
(6, 'public/images/products/canh-ga-bbq-2.jpg', 'Cánh Gà Sốt BBQ - Đĩa', FALSE, 2),
-- Coca Cola (product_id = 7)
(7, 'public/images/products/coca-1.jpg', 'Coca Cola - Ảnh chính', TRUE, 1),
-- Trà Đào Cam Sả (product_id = 8)
(8, 'public/images/products/tra-dao-1.jpg', 'Trà Đào Cam Sả - Ảnh chính', TRUE, 1),
(8, 'public/images/products/tra-dao-2.jpg', 'Trà Đào Cam Sả - Ly to', FALSE, 2),
-- Khoai Tây Chiên (product_id = 9)
(9, 'public/images/products/khoai-tay-1.jpg', 'Khoai Tây Chiên - Ảnh chính', TRUE, 1),
-- Salad Rau Củ (product_id = 10)
(10, 'public/images/products/salad-1.jpg', 'Salad Rau Củ - Ảnh chính', TRUE, 1),
(10, 'public/images/products/salad-2.jpg', 'Salad Rau Củ - Tươi mát', FALSE, 2);

-- Bài viết mẫu
INSERT INTO posts (author_id, title, slug, content, excerpt, status, published_at) VALUES
(1, 'Chào mừng đến với FastFood', 'chao-mung-den-voi-fastfood', 'Chúng tôi rất vui mừng được phục vụ quý khách với những món ăn nhanh chất lượng cao nhất...', 'Giới thiệu về nhà hàng FastFood', 'published', NOW()),
(1, 'Khuyến mãi tháng 12', 'khuyen-mai-thang-12', 'Nhân dịp cuối năm, FastFood giảm giá 20% cho tất cả các món burger...', 'Chương trình khuyến mãi hấp dẫn', 'published', NOW());

-- =====================================================
-- VIEWS (Các view hữu ích cho báo cáo)
-- =====================================================

-- View: Thống kê sản phẩm bán chạy
CREATE OR REPLACE VIEW v_best_selling_products AS
SELECT 
    p.id,
    p.name,
    p.price,
    p.image,
    pi.image_url as primary_image,
    c.name as category_name,
    COUNT(oi.id) as total_orders,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.subtotal) as total_revenue
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
GROUP BY p.id
ORDER BY total_quantity_sold DESC;

-- View: Sản phẩm với ảnh chính
CREATE OR REPLACE VIEW v_products_with_images AS
SELECT 
    p.*,
    c.name as category_name,
    pi.image_url as primary_image,
    pi.alt_text as primary_image_alt,
    (SELECT COUNT(*) FROM product_images WHERE product_id = p.id) as total_images
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE;

-- View: Thống kê đơn hàng theo trạng thái
CREATE OR REPLACE VIEW v_order_statistics AS
SELECT 
    order_status,
    COUNT(*) as total_orders,
    SUM(total) as total_amount
FROM orders
GROUP BY order_status;

-- View: Thống kê doanh thu theo ngày
CREATE OR REPLACE VIEW v_daily_revenue AS
SELECT 
    DATE(created_at) as order_date,
    COUNT(*) as total_orders,
    SUM(total) as total_revenue,
    AVG(total) as avg_order_value
FROM orders
WHERE payment_status = 'paid'
GROUP BY DATE(created_at)
ORDER BY order_date DESC;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- Procedure: Cập nhật trạng thái đơn hàng
DELIMITER //
CREATE PROCEDURE update_order_status(
    IN p_order_id INT,
    IN p_new_status VARCHAR(20),
    IN p_admin_id INT,
    IN p_note TEXT
)
BEGIN
    -- Cập nhật trạng thái đơn hàng
    UPDATE orders 
    SET order_status = p_new_status,
        confirmed_at = CASE WHEN p_new_status = 'confirmed' THEN NOW() ELSE confirmed_at END,
        delivered_at = CASE WHEN p_new_status = 'delivered' THEN NOW() ELSE delivered_at END
    WHERE id = p_order_id;
    
    -- Lưu lịch sử thay đổi
    INSERT INTO order_status_history (order_id, status, note, created_by)
    VALUES (p_order_id, p_new_status, p_note, p_admin_id);
END //
DELIMITER ;

-- Procedure: Tính tổng giỏ hàng
DELIMITER //
CREATE PROCEDURE calculate_cart_total(IN p_user_id INT)
BEGIN
    SELECT 
        SUM(p.price * c.quantity) as subtotal,
        COUNT(c.id) as total_items,
        SUM(c.quantity) as total_quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = p_user_id AND p.status = 'active';
END //
DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger: Tự động tăng view count khi xem sản phẩm
DELIMITER //
CREATE TRIGGER update_product_views
BEFORE UPDATE ON products
FOR EACH ROW
BEGIN
    IF NEW.views > OLD.views THEN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
END //
DELIMITER ;

-- Trigger: Đảm bảo chỉ có 1 ảnh chính cho mỗi sản phẩm
DELIMITER //
CREATE TRIGGER after_product_image_insert
AFTER INSERT ON product_images
FOR EACH ROW
BEGIN
    IF NEW.is_primary = TRUE THEN
        UPDATE product_images 
        SET is_primary = FALSE 
        WHERE product_id = NEW.product_id AND id != NEW.id;
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER after_product_image_update
AFTER UPDATE ON product_images
FOR EACH ROW
BEGIN
    IF NEW.is_primary = TRUE AND OLD.is_primary = FALSE THEN
        UPDATE product_images 
        SET is_primary = FALSE 
        WHERE product_id = NEW.product_id AND id != NEW.id;
    END IF;
END //
DELIMITER ;

-- =====================================================
-- INDEXES
-- =====================================================

-- Index cho tìm kiếm sản phẩm
CREATE FULLTEXT INDEX idx_product_search ON products(name, description);

-- Index cho tìm kiếm bài viết
CREATE FULLTEXT INDEX idx_post_search ON posts(title, content);