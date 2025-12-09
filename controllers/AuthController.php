<?php

class AuthController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function showLogin() {
        if (isLoggedIn()) {
            redirect('index.php');
        }
        require_once 'views/auth/login.php';
    }
    
    public function showRegister() {
        if (isLoggedIn()) {
            redirect('index.php');
        }
        require_once 'views/auth/register.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=login');
        }
        
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin!');
            redirect('index.php?page=login');
        }
        
        // Tìm user theo email
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE email = ? AND status = 'active'
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_method'] = 'normal';
            
            setFlash('success', 'Đăng nhập thành công!');
            
            // Redirect admin đến trang quản trị
            if ($user['role'] === 'admin') {
                redirect('index.php?page=admin');
            } else {
                redirect('index.php');
            }
        } else {
            setFlash('error', 'Email hoặc mật khẩu không đúng!');
            redirect('index.php?page=login');
        }
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=register');
        }
        
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        
        // Validate
        if (empty($name) || empty($email) || empty($password)) {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin!');
            redirect('index.php?page=register');
        }
        
        if (!isValidEmail($email)) {
            setFlash('error', 'Email không hợp lệ!');
            redirect('index.php?page=register');
        }
        
        if (strlen($password) < 6) {
            setFlash('error', 'Mật khẩu phải có ít nhất 6 ký tự!');
            redirect('index.php?page=register');
        }
        
        if ($password !== $confirm) {
            setFlash('error', 'Mật khẩu nhập lại không khớp!');
            redirect('index.php?page=register');
        }
        
        // Kiểm tra email đã tồn tại
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            setFlash('error', 'Email đã được sử dụng!');
            redirect('index.php?page=register');
        }
        
        // Tạo tài khoản
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password, full_name, phone, role, status) 
            VALUES (?, ?, ?, ?, 'customer', 'active')
        ");
        
        if ($stmt->execute([$email, $hashedPassword, $name, $phone])) {
            setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
            redirect('index.php?page=login');
        } else {
            setFlash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
            redirect('index.php?page=register');
        }
    }
    
    public function logout() {
        session_destroy();
        redirect('index.php?page=login');
    }
}
