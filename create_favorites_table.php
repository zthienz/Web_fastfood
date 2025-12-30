<?php
// File tạo bảng favorites
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Tạo bảng favorites
    $sql = "CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY unique_favorite (user_id, product_id),
        INDEX idx_user (user_id),
        INDEX idx_product (product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    
    echo "✅ Bảng favorites đã được tạo thành công!<br>";
    echo "Bây giờ bạn có thể sử dụng tính năng yêu thích.<br>";
    echo '<a href="index.php">← Quay lại trang chủ</a>';
    
} catch (PDOException $e) {
    echo "❌ Lỗi khi tạo bảng favorites: " . $e->getMessage();
}
?>