<?php

class AdminContactController {
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
    
    // Danh sách liên hệ
    public function index() {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = $_GET['p'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $where = ["1=1"];
        $params = [];
        
        if ($search) {
            $where[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $where[] = "status = ?";
            $params[] = $status;
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Đếm tổng
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM contacts WHERE $whereClause");
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];
        
        // Lấy danh sách
        $stmt = $this->db->prepare("
            SELECT * FROM contacts 
            WHERE $whereClause 
            ORDER BY created_at DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $contacts = $stmt->fetchAll();
        
        $totalPages = ceil($total / $limit);
        
        // Thống kê
        $stats = $this->getStats();
        
        require_once 'views/admin/contacts/index.php';
    }
    
    // Chi tiết liên hệ
    public function show() {
        $id = $_GET['id'] ?? 0;
        
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        $contact = $stmt->fetch();
        
        if (!$contact) {
            setFlash('error', 'Liên hệ không tồn tại!');
            redirect('index.php?page=admin&section=contacts');
        }
        
        // Đánh dấu đã đọc
        if ($contact['status'] === 'new') {
            $this->db->prepare("UPDATE contacts SET status = 'read' WHERE id = ?")->execute([$id]);
            $contact['status'] = 'read';
        }
        
        require_once 'views/admin/contacts/show.php';
    }
    
    // Cập nhật trạng thái
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=contacts');
        }
        
        $id = $_POST['id'];
        $status = $_POST['status'];
        
        $stmt = $this->db->prepare("UPDATE contacts SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        $statusText = [
            'new' => 'Mới',
            'read' => 'Đã đọc', 
            'replied' => 'Đã phản hồi'
        ];
        
        setFlash('success', 'Đã cập nhật trạng thái thành "' . $statusText[$status] . '"');
        redirect('index.php?page=admin&section=contacts');
    }
    
    // Xóa liên hệ
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=contacts');
        }
        
        $id = $_POST['id'];
        
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        
        setFlash('success', 'Xóa liên hệ thành công!');
        redirect('index.php?page=admin&section=contacts');
    }
    
    // Thống kê
    private function getStats() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
                SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
                SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied_count
            FROM contacts
        ");
        
        return $stmt->fetch();
    }
}