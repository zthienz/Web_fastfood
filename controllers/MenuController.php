<?php

class MenuController {
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
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1";
            
        if ($userId && $favoritesTableExists) {
            $sql .= " LEFT JOIN favorites f ON p.id = f.product_id AND f.user_id = ?";
        }
        
        $sql .= "
            WHERE p.status = 'active'
            ORDER BY p.name ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        
        if ($userId && $favoritesTableExists) {
            $stmt->execute([$userId]);
        } else {
            $stmt->execute();
        }
        
        $products = $stmt->fetchAll();
        
        require_once 'views/menu/index.php';
    }
    
    public function search() {
        $keyword = sanitize($_GET['q'] ?? '');
        
        if (empty($keyword)) {
            $this->index();
            return;
        }
        
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
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1";
            
        if ($userId && $favoritesTableExists) {
            $sql .= " LEFT JOIN favorites f ON p.id = f.product_id AND f.user_id = ?";
        }
        
        $sql .= "
            WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)
            ORDER BY p.name ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%$keyword%";
        
        if ($userId && $favoritesTableExists) {
            $stmt->execute([$userId, $searchTerm, $searchTerm]);
        } else {
            $stmt->execute([$searchTerm, $searchTerm]);
        }
        
        $products = $stmt->fetchAll();
        
        require_once 'views/menu/index.php';
    }
    
    public function detail() {
        $productId = (int)($_GET['id'] ?? 0);
        
        if (!$productId) {
            header('Location: index.php?page=menu');
            exit;
        }
        
        // Lấy thông tin sản phẩm
        $sql = "
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ? AND p.status = 'active'
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if (!$product) {
            header('Location: index.php?page=menu');
            exit;
        }
        
        // Cập nhật lượt xem
        $updateViews = $this->db->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
        $updateViews->execute([$productId]);
        $product['views'] = $product['views'] + 1;
        
        // Lấy hình ảnh sản phẩm
        $imagesSql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, id ASC";
        $imagesStmt = $this->db->prepare($imagesSql);
        $imagesStmt->execute([$productId]);
        $images = $imagesStmt->fetchAll();
        
        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedSql = "
            SELECT p.*, pi.image_url as primary_image
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
            ORDER BY RAND()
            LIMIT 4
        ";
        
        $relatedStmt = $this->db->prepare($relatedSql);
        $relatedStmt->execute([$product['category_id'], $productId]);
        $relatedProducts = $relatedStmt->fetchAll();
        
        require_once 'views/menu/detail.php';
    }
}
