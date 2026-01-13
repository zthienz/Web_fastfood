<?php

// Escape output để tránh XSS
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Format tiền tệ
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

// Kiểm tra đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Kiểm tra quyền admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Thông báo lỗi cho admin khi sử dụng chức năng không được phép
function adminRestrictionMessage() {
    return 'Bạn là admin và không thể sử dụng chức năng này!';
}

// Redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Flash messages
function setFlash($key, $message) {
    $_SESSION["flash_$key"] = $message;
}

function getFlash($key) {
    if (isset($_SESSION["flash_$key"])) {
        $message = $_SESSION["flash_$key"];
        unset($_SESSION["flash_$key"]);
        return $message;
    }
    return null;
}

function hasFlash($key) {
    return isset($_SESSION["flash_$key"]);
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Get asset URL (for images, css, js)
function asset($path) {
    // Remove leading slash if exists
    $path = ltrim($path, '/');
    return BASE_URL . $path;
}

// Get image URL with fallback
function getImageUrl($imagePath, $default = 'public/images/products/default.svg') {
    if (empty($imagePath)) {
        return asset($default);
    }
    
    // Check if file exists
    $fullPath = __DIR__ . '/../' . $imagePath;
    if (file_exists($fullPath)) {
        return asset($imagePath);
    }
    
    return asset($default);
}

// Tự động cập nhật trạng thái sản phẩm dựa trên tồn kho
function updateProductStatus($productId) {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT stock_quantity, status FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if ($product) {
        $newStatus = $product['status'];
        
        // Nếu tồn kho = 0 và đang active -> chuyển sang out_of_stock
        if ($product['stock_quantity'] <= 0 && $product['status'] === 'active') {
            $newStatus = 'out_of_stock';
        }
        // Nếu tồn kho > 0 và đang out_of_stock -> chuyển sang active
        elseif ($product['stock_quantity'] > 0 && $product['status'] === 'out_of_stock') {
            $newStatus = 'active';
        }
        
        // Cập nhật nếu có thay đổi
        if ($newStatus !== $product['status']) {
            $stmt = $db->prepare("UPDATE products SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $productId]);
            return true;
        }
    }
    
    return false;
}
