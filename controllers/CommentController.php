<?php

class CommentController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Hiển thị form bình luận cho đơn hàng đã giao
    public function showOrderCommentForm() {
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $orderId = $_GET['order_id'] ?? 0;
        $productId = $_GET['product_id'] ?? 0;
        
        // Kiểm tra đơn hàng có thuộc về user và đã giao chưa
        $stmt = $this->db->prepare("
            SELECT o.*, oi.product_name, oi.product_id, p.name as current_product_name
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE o.id = ? AND o.user_id = ? AND o.order_status = 'delivered' AND oi.product_id = ?
        ");
        $stmt->execute([$orderId, $_SESSION['user_id'], $productId]);
        $orderItem = $stmt->fetch();
        
        if (!$orderItem) {
            setFlash('error', 'Không tìm thấy đơn hàng hoặc đơn hàng chưa được giao!');
            redirect('index.php?page=orders');
        }
        
        // Kiểm tra đã bình luận chưa
        $stmt = $this->db->prepare("
            SELECT id FROM comments 
            WHERE user_id = ? AND order_id = ? AND product_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $orderId, $productId]);
        $existingComment = $stmt->fetch();
        
        if ($existingComment) {
            setFlash('error', 'Bạn đã bình luận về món ăn này rồi!');
            redirect('index.php?page=orders&action=detail&id=' . $orderId);
        }
        
        require_once 'views/comments/order_comment_form.php';
    }
    
    // Xử lý submit bình luận
    public function submitOrderComment() {
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=orders');
        }
        
        $orderId = $_POST['order_id'] ?? 0;
        $productId = $_POST['product_id'] ?? 0;
        $content = sanitize($_POST['content'] ?? '');
        $rating = (int)($_POST['rating'] ?? 0);
        
        // Validate
        if (empty($content) || $rating < 1 || $rating > 5) {
            setFlash('error', 'Vui lòng nhập nội dung bình luận và chọn đánh giá từ 1-5 sao!');
            redirect('index.php?page=comments&action=form&order_id=' . $orderId . '&product_id=' . $productId);
        }
        
        // Kiểm tra đơn hàng có thuộc về user và đã giao chưa
        $stmt = $this->db->prepare("
            SELECT o.id
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            WHERE o.id = ? AND o.user_id = ? AND o.order_status = 'delivered' AND oi.product_id = ?
        ");
        $stmt->execute([$orderId, $_SESSION['user_id'], $productId]);
        $validOrder = $stmt->fetch();
        
        if (!$validOrder) {
            setFlash('error', 'Đơn hàng không hợp lệ!');
            redirect('index.php?page=orders');
        }
        
        // Kiểm tra đã bình luận chưa
        $stmt = $this->db->prepare("
            SELECT id FROM comments 
            WHERE user_id = ? AND order_id = ? AND product_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $orderId, $productId]);
        $existingComment = $stmt->fetch();
        
        if ($existingComment) {
            setFlash('error', 'Bạn đã bình luận về món ăn này rồi!');
            redirect('index.php?page=orders&action=detail&id=' . $orderId);
        }
        
        // Thêm bình luận
        try {
            $stmt = $this->db->prepare("
                INSERT INTO comments (user_id, product_id, order_id, content, rating, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'approved', NOW())
            ");
            $stmt->execute([$_SESSION['user_id'], $productId, $orderId, $content, $rating]);
            
            setFlash('success', 'Bình luận của bạn đã được gửi thành công!');
            redirect('index.php?page=orders&action=detail&id=' . $orderId);
            
        } catch (Exception $e) {
            setFlash('error', 'Có lỗi xảy ra khi gửi bình luận!');
            redirect('index.php?page=comments&action=form&order_id=' . $orderId . '&product_id=' . $productId);
        }
    }
    
    // Lấy bình luận của sản phẩm
    public function getProductComments($productId, $limit = 10, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.full_name, u.avatar, o.order_number
            FROM comments c
            JOIN users u ON c.user_id = u.id
            LEFT JOIN orders o ON c.order_id = o.id
            WHERE c.product_id = ? AND c.status = 'approved'
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$productId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    // Đếm tổng số bình luận của sản phẩm
    public function countProductComments($productId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM comments 
            WHERE product_id = ? AND status = 'approved'
        ");
        $stmt->execute([$productId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    // Tính điểm đánh giá trung bình
    public function getProductRating($productId) {
        $stmt = $this->db->prepare("
            SELECT 
                AVG(rating) as average_rating,
                COUNT(*) as total_reviews
            FROM comments 
            WHERE product_id = ? AND status = 'approved' AND rating IS NOT NULL
        ");
        $stmt->execute([$productId]);
        $result = $stmt->fetch();
        
        return [
            'average' => round($result['average_rating'] ?? 0, 1),
            'total' => $result['total_reviews'] ?? 0
        ];
    }
}