<?php

class HomeController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
        
        // Kiểm tra xem bảng favorites có tồn tại không
        $favoritesTableExists = false;
        try {
            $checkTable = $this->db->query("SHOW TABLES LIKE 'favorites'");
            $favoritesTableExists = $checkTable->rowCount() > 0;
        } catch (Exception $e) {
            $favoritesTableExists = false;
        }
        
        $sql = "
            SELECT p.*, c.name as category_name, pi.image_url as primary_image";
        
        if ($userId && $favoritesTableExists) {
            $sql .= ", CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END as is_favorite";
        }
        
        $sql .= "
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE";
            
        if ($userId && $favoritesTableExists) {
            $sql .= " LEFT JOIN favorites f ON p.id = f.product_id AND f.user_id = ?";
        }
        
        $sql .= "
            WHERE p.status = 'active' AND p.is_featured = TRUE
            ORDER BY p.id DESC 
            LIMIT 6
        ";
        
        $stmt = $this->db->prepare($sql);
        
        if ($userId && $favoritesTableExists) {
            $stmt->execute([$userId]);
        } else {
            $stmt->execute();
        }
        
        $products = $stmt->fetchAll();
        
        require_once 'views/home/index.php';
    }
}
