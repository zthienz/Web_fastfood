<?php

class AdminController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            redirect('index.php?page=login');
        }
    }
    
    private function isAdmin() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    // Dashboard - Tổng quan
    public function dashboard() {
        $stats = $this->getDashboardStats();
        require_once 'views/admin/dashboard.php';
    }
    
    private function getDashboardStats() {
        // Tổng doanh thu
        $stmt = $this->db->query("SELECT SUM(total) as total_revenue FROM orders WHERE payment_status = 'paid'");
        $revenue = $stmt->fetch()['total_revenue'] ?? 0;
        
        // Tổng đơn hàng
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders");
        $totalOrders = $stmt->fetch()['total'];
        
        // Tổng khách hàng
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
        $totalCustomers = $stmt->fetch()['total'];
        
        // Tổng sản phẩm
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products");
        $totalProducts = $stmt->fetch()['total'];
        
        // Đơn hàng mới (pending)
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'pending'");
        $pendingOrders = $stmt->fetch()['total'];
        
        // Doanh thu theo tháng (12 tháng gần nhất)
        $stmt = $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(total) as revenue
            FROM orders 
            WHERE payment_status = 'paid' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        $monthlyRevenue = $stmt->fetchAll();
        
        // Sản phẩm bán chạy
        $stmt = $this->db->query("
            SELECT p.name, SUM(oi.quantity) as total_sold
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            GROUP BY oi.product_id
            ORDER BY total_sold DESC
            LIMIT 5
        ");
        $topProducts = $stmt->fetchAll();
        
        return [
            'total_revenue' => $revenue,
            'total_orders' => $totalOrders,
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'pending_orders' => $pendingOrders,
            'monthly_revenue' => $monthlyRevenue,
            'top_products' => $topProducts
        ];
    }
}
