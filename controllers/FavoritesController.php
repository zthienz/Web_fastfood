<?php

class FavoritesController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        // Kiểm tra đăng nhập
        if (!isLoggedIn()) {
            setFlash('error', 'Vui lòng đăng nhập để xem danh sách yêu thích!');
            redirect('index.php?page=login');
        }
        
        // Kiểm tra xem bảng favorites có tồn tại không
        try {
            $checkTable = $this->db->query("SHOW TABLES LIKE 'favorites'");
            if ($checkTable->rowCount() == 0) {
                // Bảng chưa tồn tại, hiển thị thông báo
                $favorites = [];
                $error_message = 'Bảng favorites chưa được tạo. Vui lòng chạy file create_favorites_table.php để tạo bảng.';
                require_once 'views/favorites/index.php';
                return;
            }
        } catch (Exception $e) {
            $favorites = [];
            $error_message = 'Có lỗi khi kiểm tra bảng favorites: ' . $e->getMessage();
            require_once 'views/favorites/index.php';
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Lấy danh sách sản phẩm yêu thích
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, pi.image_url as primary_image, f.created_at as favorited_at
            FROM favorites f
            JOIN products p ON f.product_id = p.id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$userId]);
        $favorites = $stmt->fetchAll();
        
        require_once 'views/favorites/index.php';
    }
    
    public function add() {
        // Kiểm tra đăng nhập
        if (!isLoggedIn()) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
                exit;
            }
            setFlash('error', 'Vui lòng đăng nhập để thêm sản phẩm yêu thích!');
            redirect('index.php?page=login');
        }
        
        // Kiểm tra quyền admin
        if (isAdmin()) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => adminRestrictionMessage()]);
                exit;
            }
            setFlash('error', adminRestrictionMessage());
            redirect($_SERVER['HTTP_REFERER'] ?? 'index.php?page=menu');
        }
        
        $productId = intval($_POST['product_id'] ?? $_GET['id'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ!']);
                exit;
            }
            redirect('index.php?page=menu');
        }
        
        // Kiểm tra sản phẩm có tồn tại không
        $stmt = $this->db->prepare("SELECT id, name FROM products WHERE id = ? AND status = 'active'");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if (!$product) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
                exit;
            }
            setFlash('error', 'Sản phẩm không tồn tại!');
            redirect('index.php?page=menu');
        }
        
        try {
            // Thêm vào danh sách yêu thích (sử dụng INSERT IGNORE để tránh duplicate)
            $stmt = $this->db->prepare("
                INSERT IGNORE INTO favorites (user_id, product_id) 
                VALUES (?, ?)
            ");
            $result = $stmt->execute([$userId, $productId]);
            
            if ($this->isAjax()) {
                if ($result && $stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Đã thêm vào danh sách yêu thích!', 'action' => 'added']);
                } else {
                    echo json_encode(['success' => true, 'message' => 'Sản phẩm đã có trong danh sách yêu thích!', 'action' => 'exists']);
                }
                exit;
            }
            
            if ($result && $stmt->rowCount() > 0) {
                setFlash('success', 'Đã thêm "' . $product['name'] . '" vào danh sách yêu thích!');
            } else {
                setFlash('info', 'Sản phẩm đã có trong danh sách yêu thích!');
            }
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra!']);
                exit;
            }
            setFlash('error', 'Có lỗi xảy ra khi thêm sản phẩm yêu thích!');
        }
        
        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php?page=menu');
    }
    
    public function remove() {
        // Kiểm tra đăng nhập
        if (!isLoggedIn()) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
                exit;
            }
            setFlash('error', 'Vui lòng đăng nhập!');
            redirect('index.php?page=login');
        }
        
        $productId = intval($_POST['product_id'] ?? $_GET['id'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ!']);
                exit;
            }
            redirect('index.php?page=favorites');
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
            $result = $stmt->execute([$userId, $productId]);
            
            if ($this->isAjax()) {
                if ($result && $stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích!', 'action' => 'removed']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong danh sách yêu thích!']);
                }
                exit;
            }
            
            if ($result && $stmt->rowCount() > 0) {
                setFlash('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích!');
            } else {
                setFlash('error', 'Sản phẩm không có trong danh sách yêu thích!');
            }
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra!']);
                exit;
            }
            setFlash('error', 'Có lỗi xảy ra khi xóa sản phẩm yêu thích!');
        }
        
        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php?page=favorites');
    }
    
    public function toggle() {
        // Kiểm tra đăng nhập
        if (!isLoggedIn()) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
                exit;
            }
            setFlash('error', 'Vui lòng đăng nhập!');
            redirect('index.php?page=login');
        }
        
        // Kiểm tra quyền admin
        if (isAdmin()) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => adminRestrictionMessage()]);
                exit;
            }
            setFlash('error', adminRestrictionMessage());
            redirect($_SERVER['HTTP_REFERER'] ?? 'index.php?page=menu');
        }
        
        // Kiểm tra xem bảng favorites có tồn tại không
        try {
            $checkTable = $this->db->query("SHOW TABLES LIKE 'favorites'");
            if ($checkTable->rowCount() == 0) {
                if ($this->isAjax()) {
                    echo json_encode(['success' => false, 'message' => 'Bảng favorites chưa được tạo!']);
                    exit;
                }
                setFlash('error', 'Bảng favorites chưa được tạo!');
                redirect('index.php?page=menu');
            }
        } catch (Exception $e) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra!']);
                exit;
            }
            setFlash('error', 'Có lỗi xảy ra!');
            redirect('index.php?page=menu');
        }
        
        $productId = intval($_POST['product_id'] ?? $_GET['id'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ!']);
                exit;
            }
            redirect('index.php?page=menu');
        }
        
        try {
            // Kiểm tra xem đã yêu thích chưa
            $stmt = $this->db->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Đã yêu thích -> xóa
                $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $productId]);
                $action = 'removed';
                $message = 'Đã xóa khỏi danh sách yêu thích!';
            } else {
                // Chưa yêu thích -> thêm
                $stmt = $this->db->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
                $stmt->execute([$userId, $productId]);
                $action = 'added';
                $message = 'Đã thêm vào danh sách yêu thích!';
            }
            
            if ($this->isAjax()) {
                echo json_encode(['success' => true, 'message' => $message, 'action' => $action]);
                exit;
            }
            
            setFlash('success', $message);
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra!']);
                exit;
            }
            setFlash('error', 'Có lỗi xảy ra!');
        }
        
        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php?page=menu');
    }
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}