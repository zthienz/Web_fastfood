<?php

class AdminRevenueController {
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
    
    public function index() {
        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        
        // Thống kê tổng quan
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as avg_order_value,
                SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as completed_orders
            FROM orders 
            WHERE payment_status = 'paid' 
                AND DATE(created_at) BETWEEN ? AND ?
        ");
        $stmt->execute([$fromDate, $toDate]);
        $stats = $stmt->fetch();
        
        // Doanh thu theo ngày
        $stmt = $this->db->prepare("
            SELECT 
                DATE(created_at) as date,
                SUM(total) as revenue,
                COUNT(*) as orders
            FROM orders 
            WHERE payment_status = 'paid' 
                AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$fromDate, $toDate]);
        $dailyRevenue = $stmt->fetchAll();
        
        // Doanh thu theo danh mục
        $stmt = $this->db->prepare("
            SELECT 
                c.id as category_id,
                c.name as category_name,
                COUNT(DISTINCT o.id) as total_orders,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.subtotal) as revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            WHERE o.payment_status = 'paid' 
                AND DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY c.id, c.name
            ORDER BY revenue DESC
        ");
        $stmt->execute([$fromDate, $toDate]);
        $categoryRevenue = $stmt->fetchAll();
        
        // Top sản phẩm bán chạy
        $stmt = $this->db->prepare("
            SELECT 
                oi.product_id,
                oi.product_name,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.subtotal) as revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.payment_status = 'paid' 
                AND DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY oi.product_id, oi.product_name
            ORDER BY total_quantity DESC
            LIMIT 10
        ");
        $stmt->execute([$fromDate, $toDate]);
        $topProducts = $stmt->fetchAll();
        
        require_once 'views/admin/revenue.php';
    }
}
