<?php

class ProfileController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
    }
    
    public function index() {
        // Lấy thông tin đầy đủ từ database
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            session_destroy();
            redirect('index.php?page=login');
        }
        
        $editMode = $_GET['edit'] ?? false;
        
        require_once 'views/profile.php';
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=profile');
        }
        
        $fullName = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        
        if (empty($fullName)) {
            setFlash('error', 'Vui lòng nhập họ tên!');
            redirect('index.php?page=profile&edit=1');
        }
        
        $stmt = $this->db->prepare("
            UPDATE users 
            SET full_name = ?, phone = ?, address = ?
            WHERE id = ?
        ");
        
        if ($stmt->execute([$fullName, $phone, $address, $_SESSION['user_id']])) {
            $_SESSION['full_name'] = $fullName;
            setFlash('success', 'Cập nhật thông tin thành công!');
        } else {
            setFlash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
        
        redirect('index.php?page=profile');
    }
}
