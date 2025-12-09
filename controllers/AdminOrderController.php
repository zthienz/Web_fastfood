<?php

class AdminOrderController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        if (!$this->isAdmin()) {
            redirect('index.php?page=login');
        }
    }
    
    private function isAdmin() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    // Danh sách đơn hàng
    public function index() {
        $search = $_GET['search'] ?? '';
        $orderStatus = $_GET['order_status'] ?? '';
        $paymentStatus = $_GET['payment_status'] ?? '';
        $page = $_GET['p'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $where = ["1=1"];
        $params = [];
        
        if ($search) {
            $where[] = "(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($orderStatus) {
            $where[] = "o.order_status = ?";
            $params[] = $orderStatus;
        }
        
        if ($paymentStatus) {
            $where[] = "o.payment_status = ?";
            $params[] = $paymentStatus;
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Đếm tổng
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM orders o WHERE $whereClause");
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];
        
        // Lấy danh sách
        $stmt = $this->db->prepare("
            SELECT o.*, u.full_name as user_name
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE $whereClause 
            ORDER BY o.created_at DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $orders = $stmt->fetchAll();
        
        $totalPages = ceil($total / $limit);
        
        require_once 'views/admin/orders/index.php';
    }
    
    // Chi tiết đơn hàng
    public function detail() {
        $id = $_GET['id'] ?? 0;
        
        $stmt = $this->db->prepare("
            SELECT o.*, u.full_name as user_name, u.email as user_email
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại!');
            redirect('index.php?page=admin&section=orders');
        }
        
        // Lấy chi tiết sản phẩm
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$id]);
        $items = $stmt->fetchAll();
        
        // Lấy lịch sử trạng thái
        $stmt = $this->db->prepare("
            SELECT osh.*, u.full_name as admin_name
            FROM order_status_history osh
            LEFT JOIN users u ON osh.created_by = u.id
            WHERE osh.order_id = ?
            ORDER BY osh.created_at DESC
        ");
        $stmt->execute([$id]);
        $history = $stmt->fetchAll();
        
        require_once 'views/admin/orders/detail.php';
    }
    
    // Cập nhật trạng thái đơn hàng
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=orders');
        }
        
        $orderId = $_POST['order_id'];
        $status = $_POST['status'];
        $note = sanitize($_POST['note'] ?? '');
        
        // Cập nhật trạng thái
        $stmt = $this->db->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        
        // Lưu lịch sử
        $stmt = $this->db->prepare("
            INSERT INTO order_status_history (order_id, status, note, created_by)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$orderId, $status, $note, $_SESSION['user_id']]);
        
        setFlash('success', 'Cập nhật trạng thái đơn hàng thành công!');
        redirect('index.php?page=admin&section=orders&action=detail&id=' . $orderId);
    }
    
    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=orders');
        }
        
        $orderId = $_POST['order_id'];
        $status = $_POST['payment_status'];
        
        $stmt = $this->db->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        
        setFlash('success', 'Cập nhật trạng thái thanh toán thành công!');
        redirect('index.php?page=admin&section=orders&action=detail&id=' . $orderId);
    }
}
