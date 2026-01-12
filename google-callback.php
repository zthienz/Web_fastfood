<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/functions.php';

// Bật hiển thị lỗi nếu DEBUG mode
if (defined('DEBUG') && DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Kiểm tra có credential từ Google không
if (!isset($_POST['credential'])) {
    setFlash('error', 'Lỗi xác thực Google! Không nhận được credential.');
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
    // Thêm padding nếu cần
    $payload = str_replace(['-', '_'], ['+', '/'], $payload);
    switch (strlen($payload) % 4) {
        case 2: $payload .= '=='; break;
        case 3: $payload .= '='; break;
    }
    
    $decoded = base64_decode($payload);
    if ($decoded === false) {
        return null;
    }
    
    return json_decode($decoded, true);
}

$googleUser = decodeGoogleJWT($credential);

if (!$googleUser || !isset($googleUser['email'])) {
    $error = 'Không thể lấy thông tin từ Google!';
    if (defined('DEBUG') && DEBUG) {
        $error .= ' JWT decode result: ' . json_encode($googleUser);
    }
    setFlash('error', $error);
    redirect('index.php?page=login');
}

// Lấy thông tin người dùng từ Google
$email = $googleUser['email'];
$name = $googleUser['name'] ?? $googleUser['given_name'] ?? '';
$googleId = $googleUser['sub'] ?? '';
$picture = $googleUser['picture'] ?? '';

// Validate dữ liệu
if (empty($email)) {
    setFlash('error', 'Không thể lấy email từ Google!');
    redirect('index.php?page=login');
}

if (empty($googleId)) {
    setFlash('error', 'Không thể lấy Google ID!');
    redirect('index.php?page=login');
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Bắt đầu transaction
    $db->beginTransaction();
    
    // Kiểm tra user đã tồn tại chưa (theo email hoặc google_id)
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR google_id = ?");
    $stmt->execute([$email, $googleId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // User đã tồn tại - cập nhật thông tin nếu cần
        $updateFields = [];
        $updateValues = [];
        
        if (empty($user['google_id']) && !empty($googleId)) {
            $updateFields[] = "google_id = ?";
            $updateValues[] = $googleId;
        }
        
        if (empty($user['avatar']) && !empty($picture)) {
            $updateFields[] = "avatar = ?";
            $updateValues[] = $picture;
        }
        
        if (!empty($updateFields)) {
            $updateValues[] = $user['id'];
            $stmt = $db->prepare("UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?");
            $stmt->execute($updateValues);
        }
        
        // Refresh user data
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } else {
        // Tạo user mới
        $stmt = $db->prepare("
            INSERT INTO users (email, full_name, google_id, avatar, role, status, login_method, created_at) 
            VALUES (?, ?, ?, ?, 'customer', 'active', 'google', NOW())
        ");
        
        if (!$stmt->execute([$email, $name, $googleId, $picture])) {
            throw new Exception('Không thể tạo tài khoản mới: ' . implode(', ', $stmt->errorInfo()));
        }
        
        // Lấy thông tin user vừa tạo
        $userId = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception('Không thể lấy thông tin user vừa tạo');
        }
    }
    
    // Commit transaction
    $db->commit();
    
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
    // Rollback transaction nếu có lỗi
    if ($db->inTransaction()) {
        $db->rollback();
    }
    
    $error = 'Có lỗi xảy ra: ' . $e->getMessage();
    if (defined('DEBUG') && DEBUG) {
        $error .= '<br>File: ' . $e->getFile() . '<br>Line: ' . $e->getLine();
    }
    setFlash('error', $error);
    redirect('index.php?page=login');
}
?>