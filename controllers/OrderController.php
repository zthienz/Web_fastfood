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
        
        // Lấy chi tiết sản phẩm trong đơn (nhóm theo product_id để tránh trùng lặp)
        $stmt = $this->db->prepare("
            SELECT 
                oi.product_id,
                MAX(oi.product_name) as product_name,
                MAX(oi.product_image) as product_image,
                MAX(oi.price) as price,
                MAX(oi.price) as unit_price,
                SUM(oi.quantity) as total_quantity,
                (MAX(oi.price) * SUM(oi.quantity)) as subtotal,
                MAX(p.id) as current_product_id, 
                MAX(p.name) as current_product_name,
                MAX(c.id) as comment_id, 
                MAX(c.rating) as rating, 
                MAX(c.content) as comment_content, 
                MAX(c.created_at) as comment_date,
                MAX(pi.image_url) as current_product_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN comments c ON c.order_id = oi.order_id AND c.product_id = oi.product_id AND c.user_id = ?
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE oi.order_id = ?
            GROUP BY oi.product_id
            ORDER BY oi.product_id
        ");
        $stmt->execute([$_SESSION['user_id'], $orderId]);
        $orderItems = $stmt->fetchAll();
        
        // Đặt biến global để sử dụng trong view
        $GLOBALS['db'] = $this->db;
        
        require_once 'views/orders/detail.php';
    }
    
    public function cancel() {
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $orderId = $_POST['order_id'] ?? 0;
        
        try {
            $this->db->beginTransaction();
            
            // Kiểm tra đơn hàng có thuộc về user và có thể hủy không
            $stmt = $this->db->prepare("
                SELECT * FROM orders 
                WHERE id = ? AND user_id = ? AND order_status = 'pending'
            ");
            $stmt->execute([$orderId, $_SESSION['user_id']]);
            $order = $stmt->fetch();
            
            if (!$order) {
                throw new Exception('Không thể hủy đơn hàng này!');
            }
            
            // Lấy danh sách sản phẩm trong đơn hàng để hoàn lại tồn kho
            $stmt = $this->db->prepare("
                SELECT product_id, quantity 
                FROM order_items 
                WHERE order_id = ?
            ");
            $stmt->execute([$orderId]);
            $orderItems = $stmt->fetchAll();
            
            // Hoàn lại số lượng tồn kho cho từng sản phẩm
            foreach ($orderItems as $item) {
                $stmt = $this->db->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity + ? 
                    WHERE id = ?
                ");
                $stmt->execute([$item['quantity'], $item['product_id']]);
                
                // Cập nhật trạng thái sản phẩm nếu cần
                updateProductStatus($item['product_id']);
            }
            
            // Cập nhật trạng thái đơn hàng thành 'cancelled'
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET order_status = 'cancelled', 
                    updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);
            
            $this->db->commit();
            
            setFlash('success', 'Đơn hàng đã được hủy thành công!');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('error', $e->getMessage());
        }
        
        redirect('index.php?page=orders');
    }
}
