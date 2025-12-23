<?php

class MenuController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, pi.image_url as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.status = 'active'
            ORDER BY p.name ASC
        ");
        $stmt->execute();
        $products = $stmt->fetchAll();
        
        require_once 'views/menu/index.php';
    }
    
    public function search() {
        $keyword = sanitize($_GET['q'] ?? '');
        
        if (empty($keyword)) {
            $this->index();
            return;
        }
        
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, pi.image_url as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)
            ORDER BY p.name ASC
        ");
        $searchTerm = "%$keyword%";
        $stmt->execute([$searchTerm, $searchTerm]);
        $products = $stmt->fetchAll();
        
        require_once 'views/menu/index.php';
    }
}
