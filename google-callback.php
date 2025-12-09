<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/functions.php';

// Kiểm tra có credential từ Google không
if (!isset($_POST['credential'])) {
    setFlash('error', 'Lỗi xác thực Google!');
    redirect('index.php?page=login');
}

$credential = $_POST['credential'];

// Giải mã JWT token từ Google
function decodeGoogleJWT($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return null;
    }
    
    $payload = $parts[1];
    $payload = str_replace(['-', '_'], ['+', '/'], $payload);
    $payload = base64_decode($payload);
    
    return json_decode($payload, true);
}

$googleUser = decodeGoogleJWT($credential);

if (!$googleUser || !isset($googleUser['email'])) {
    setFlash('error', 'Không thể lấy thông tin từ Google!');
    redirect('index.php?page=login');
}

// Lấy thông tin người dùng từ Google
$email = $googleUser['email'];
$name = $googleUser['name'] ?? '';
$googleId = $googleUser['sub'] ?? '';
$picture = $googleUser['picture'] ?? '';

try {
    $db = Database::getInstance()->getConnection();
    
    // Kiểm tra user đã tồn tại chưa
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // User đã tồn tại - cập nhật thông tin nếu cần
        if (empty($user['google_id'])) {
            $stmt = $db->prepare("UPDATE users SET google_id = ?, avatar = ? WHERE id = ?");
            $stmt->execute([$googleId, $picture, $user['id']]);
        }
    } else {
        // Tạo user mới
        $stmt = $db->prepare("
            INSERT INTO users (email, full_name, google_id, avatar, role, status, login_method) 
            VALUES (?, ?, ?, ?, 'customer', 'active', 'google')
        ");
        $stmt->execute([$email, $name, $googleId, $picture]);
        
        // Lấy thông tin user vừa tạo
        $userId = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Kiểm tra trạng thái tài khoản
    if ($user['status'] !== 'active') {
        setFlash('error', 'Tài khoản của bạn đã bị khóa!');
        redirect('index.php?page=login');
    }
    
    // Lưu session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['login_method'] = 'google';
    
    setFlash('success', 'Đăng nhập Google thành công!');
    
    // Redirect theo role
    if ($user['role'] === 'admin') {
        redirect('index.php?page=admin');
    } else {
        redirect('index.php');
    }
    
} catch (Exception $e) {
    setFlash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    redirect('index.php?page=login');
}
?>