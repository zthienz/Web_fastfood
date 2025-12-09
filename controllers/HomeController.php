<?php

class HomeController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        // Lấy 6 món ăn nổi bật
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, pi.image_url as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
            WHERE p.status = 'active' AND p.is_featured = TRUE
            ORDER BY p.id DESC 
            LIMIT 6
        ");
        $stmt->execute();
        $products = $stmt->fetchAll();
        
        require_once 'views/home/index.php';
    }
}
