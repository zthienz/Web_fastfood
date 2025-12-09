<?php

class AdminUserController {
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
    
    // Danh sách người dùng
    public function index() {
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = $_GET['p'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $where = ["1=1"];
        $params = [];
        
        if ($search) {
            $where[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($role) {
            $where[] = "role = ?";
            $params[] = $role;
        }
        
        if ($status) {
            $where[] = "status = ?";
            $params[] = $status;
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Đếm tổng
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users WHERE $whereClause");
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];
        
        // Lấy danh sách
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE $whereClause 
            ORDER BY created_at DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $users = $stmt->fetchAll();
        
        $totalPages = ceil($total / $limit);
        
        require_once 'views/admin/users/index.php';
    }
    
    // Cập nhật trạng thái
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=users');
        }
        
        $userId = $_POST['user_id'] ?? 0;
        $status = $_POST['status'] ?? '';
        
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $userId]);
        
        setFlash('success', 'Cập nhật trạng thái thành công!');
        redirect('index.php?page=admin&section=users');
    }
    
    // Xóa người dùng
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=users');
        }
        
        $userId = $_POST['user_id'] ?? 0;
        
        // Không cho xóa chính mình
        if ($userId == $_SESSION['user_id']) {
            setFlash('error', 'Không thể xóa tài khoản của chính bạn!');
            redirect('index.php?page=admin&section=users');
        }
        
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        setFlash('success', 'Xóa người dùng thành công!');
        redirect('index.php?page=admin&section=users');
    }
}
