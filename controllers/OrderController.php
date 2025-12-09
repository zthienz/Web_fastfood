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
            SELECT * FROM order_items 
            WHERE order_id = ?
        ");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll();
        
        require_once 'views/orders/detail.php';
    }
}
