<?php

class MenuController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
        
        // Debug: Log filter parameters (only in development)
        if (isset($_GET['debug'])) {
            error_log("Menu filter parameters: " . print_r($_GET, true));
        }
        
        // Kiểm tra xem bảng favorites có tồn tại không
        $favoritesTableExists = false;
        try {
            $checkTable = $this->db->query("SHOW TABLES LIKE 'favorites'");
            $favoritesTableExists = $checkTable->rowCount() > 0;
        } catch (Exception $e) {
            $favoritesTableExists = false;
        }
        
        // Lấy danh sách categories để hiển thị filter
        $categoriesStmt = $this->db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY display_order ASC, name ASC");
        $categories = $categoriesStmt->fetchAll();
        
        // Xử lý các tham số lọc với validation
        $categoryId = null;
        if (isset($_GET['category']) && $_GET['category'] !== '' && $_GET['category'] !== '0') {
            $categoryId = (int)$_GET['category'];
        }
        
        $minPrice = null;
        if (isset($_GET['min_price']) && $_GET['min_price'] !== '' && is_numeric($_GET['min_price'])) {
            $minPrice = (float)$_GET['min_price'];
            if ($minPrice < 0) $minPrice = null;
        }
        
        $maxPrice = null;
        if (isset($_GET['max_price']) && $_GET['max_price'] !== '' && is_numeric($_GET['max_price'])) {
            $maxPrice = (float)$_GET['max_price'];
            if ($maxPrice < 0) $maxPrice = null;
        }
        
        $status = null;
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $status = sanitize($_GET['status']);
        }
        
        $sortBy = 'name';
        if (isset($_GET['sort']) && $_GET['sort'] !== '') {
            $sortBy = sanitize($_GET['sort']);
        }
        
        // Debug: Log processed parameters (only in development)
        if (isset($_GET['debug'])) {
            error_log("Processed parameters - Category: $categoryId, MinPrice: $minPrice, MaxPrice: $maxPrice, Status: $status, Sort: $sortBy");
        }
        
        // Xây dựng câu query với điều kiện lọc
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
        
        // Điều kiện WHERE
        $whereConditions = ["p.status != 'inactive'"];
        $params = [];
        
        if ($userId && $favoritesTableExists) {
            $params[] = $userId;
        }
        
        // Lọc theo danh mục
        if ($categoryId) {
            $whereConditions[] = "p.category_id = ?";
            $params[] = $categoryId;
            if (isset($_GET['debug'])) {
                error_log("Added category filter: $categoryId");
            }
        }
        
        // Lọc theo giá
        if ($minPrice !== null && $minPrice >= 0) {
            $whereConditions[] = "COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $minPrice;
            if (isset($_GET['debug'])) {
                error_log("Added min price filter: $minPrice");
            }
        }
        
        if ($maxPrice !== null && $maxPrice >= 0) {
            $whereConditions[] = "COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $maxPrice;
            if (isset($_GET['debug'])) {
                error_log("Added max price filter: $maxPrice");
            }
        }
        
        // Lọc theo trạng thái
        if ($status) {
            if ($status === 'available') {
                $whereConditions[] = "p.status = 'active' AND p.stock_quantity > 0";
            } elseif ($status === 'out_of_stock') {
                $whereConditions[] = "(p.status = 'out_of_stock' OR p.stock_quantity <= 0)";
            } elseif ($status === 'sale') {
                $whereConditions[] = "p.sale_price IS NOT NULL AND p.sale_price > 0";
            }
            if (isset($_GET['debug'])) {
                error_log("Added status filter: $status");
            }
        }
        
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
        
        // Sắp xếp
        switch ($sortBy) {
            case 'price_asc':
                $sql .= " ORDER BY COALESCE(p.sale_price, p.price) ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY COALESCE(p.sale_price, p.price) DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY p.created_at DESC";
                break;
            case 'popular':
                $sql .= " ORDER BY p.views DESC";
                break;
            default:
                $sql .= " ORDER BY p.name ASC";
        }
        
        if (isset($_GET['debug'])) {
            error_log("Final SQL: $sql");
            error_log("Parameters: " . print_r($params, true));
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        
        if (isset($_GET['debug'])) {
            error_log("Found " . count($products) . " products");
        }
        
        // Lấy khoảng giá để hiển thị slider
        $priceRangeStmt = $this->db->query("
            SELECT 
                MIN(COALESCE(sale_price, price)) as min_price,
                MAX(COALESCE(sale_price, price)) as max_price
            FROM products 
            WHERE status != 'inactive'
        ");
        $priceRange = $priceRangeStmt->fetch();
        
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
        
        // Lấy danh sách categories để hiển thị filter
        $categoriesStmt = $this->db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY display_order ASC, name ASC");
        $categories = $categoriesStmt->fetchAll();
        
        // Xử lý các tham số lọc (giống như index) với validation
        $categoryId = null;
        if (isset($_GET['category']) && $_GET['category'] !== '' && $_GET['category'] !== '0') {
            $categoryId = (int)$_GET['category'];
        }
        
        $minPrice = null;
        if (isset($_GET['min_price']) && $_GET['min_price'] !== '' && is_numeric($_GET['min_price'])) {
            $minPrice = (float)$_GET['min_price'];
            if ($minPrice < 0) $minPrice = null;
        }
        
        $maxPrice = null;
        if (isset($_GET['max_price']) && $_GET['max_price'] !== '' && is_numeric($_GET['max_price'])) {
            $maxPrice = (float)$_GET['max_price'];
            if ($maxPrice < 0) $maxPrice = null;
        }
        
        $status = null;
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $status = sanitize($_GET['status']);
        }
        
        $sortBy = 'name';
        if (isset($_GET['sort']) && $_GET['sort'] !== '') {
            $sortBy = sanitize($_GET['sort']);
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
        
        // Điều kiện WHERE với tìm kiếm (chỉ tìm theo tên món ăn)
        $whereConditions = ["p.status != 'inactive'", "p.name LIKE ?"];
        $params = [];
        
        if ($userId && $favoritesTableExists) {
            $params[] = $userId;
        }
        
        $searchTerm = "%$keyword%";
        $params[] = $searchTerm;
        
        // Thêm các điều kiện lọc khác
        if ($categoryId) {
            $whereConditions[] = "p.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($minPrice !== null) {
            $whereConditions[] = "COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $minPrice;
        }
        
        if ($maxPrice !== null) {
            $whereConditions[] = "COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $maxPrice;
        }
        
        if ($status) {
            if ($status === 'available') {
                $whereConditions[] = "p.status = 'active' AND p.stock_quantity > 0";
            } elseif ($status === 'out_of_stock') {
                $whereConditions[] = "(p.status = 'out_of_stock' OR p.stock_quantity <= 0)";
            } elseif ($status === 'sale') {
                $whereConditions[] = "p.sale_price IS NOT NULL AND p.sale_price > 0";
            }
        }
        
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
        
        // Sắp xếp
        switch ($sortBy) {
            case 'price_asc':
                $sql .= " ORDER BY COALESCE(p.sale_price, p.price) ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY COALESCE(p.sale_price, p.price) DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY p.created_at DESC";
                break;
            case 'popular':
                $sql .= " ORDER BY p.views DESC";
                break;
            default:
                $sql .= " ORDER BY p.name ASC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        
        // Lấy khoảng giá để hiển thị slider
        $priceRangeStmt = $this->db->query("
            SELECT 
                MIN(COALESCE(sale_price, price)) as min_price,
                MAX(COALESCE(sale_price, price)) as max_price
            FROM products 
            WHERE status != 'inactive'
        ");
        $priceRange = $priceRangeStmt->fetch();
        
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
        
        // Lấy bình luận và đánh giá của sản phẩm
        require_once 'controllers/CommentController.php';
        $commentController = new CommentController();
        
        $comments = $commentController->getProductComments($productId, 10, 0);
        $totalComments = $commentController->countProductComments($productId);
        $productRating = $commentController->getProductRating($productId);
        
        require_once 'views/menu/detail.php';
    }
}
