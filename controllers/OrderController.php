<?php

class OrderController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $stmt = $this->db->prepare("
            SELECT o.*, 
                   COUNT(oi.id) as total_items,
                   SUM(oi.quantity) as total_quantity
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ? 
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $orders = $stmt->fetchAll();
        
        require_once 'views/orders/index.php';
    }
    
    public function detail() {
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $orderId = $_GET['id'] ?? 0;
        
        // Lấy thông tin đơn hàng
        $stmt = $this->db->prepare("
            SELECT * FROM orders 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$orderId, $_SESSION['user_id']]);
        $order = $stmt->fetch();
        
        if (!$order) {
            redirect('index.php?page=orders');
        }
        
        // Lấy chi tiết sản phẩm trong đơn
        $stmt = $this->db->prepare("
            SELECT oi.*, p.id as current_product_id, p.name as current_product_name,
                   c.id as comment_id, c.rating, c.content as comment_content, c.created_at as comment_date,
                   pi.image_url as current_product_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN comments c ON c.order_id = oi.order_id AND c.product_id = oi.product_id AND c.user_id = ?
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $orderId]);
        $orderItems = $stmt->fetchAll();
        
        // Đặt biến global để sử dụng trong view
        $GLOBALS['db'] = $this->db;
        
        require_once 'views/orders/detail.php';
    }
}
