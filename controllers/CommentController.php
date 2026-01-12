<?php

class CommentController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Hiển thị trang đánh giá đơn hàng (tất cả sản phẩm)
    public function showOrderReview() {
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $orderId = intval($_GET['order_id'] ?? 0);
        
        if ($orderId <= 0) {
            setFlash('error', 'ID đơn hàng không hợp lệ!');
            redirect('index.php?page=orders');
        }
        
        // Lấy thông tin đơn hàng
        $stmt = $this->db->prepare("
            SELECT * FROM orders 
            WHERE id = ? AND user_id = ? AND order_status = 'delivered'
        ");
        $stmt->execute([$orderId, $_SESSION['user_id']]);
        $order = $stmt->fetch();
        
        if (!$order) {
            setFlash('error', 'Không tìm thấy đơn hàng hoặc đơn hàng chưa được giao!');
            redirect('index.php?page=orders');
        }
        
        // Lấy danh sách sản phẩm trong đơn hàng 
        // FIX: Đã sửa lỗi hiển thị sai sản phẩm trong form đánh giá
        // - Loại bỏ GROUP BY để tránh lỗi SQL với DISTINCT
        // - Đảm bảo mỗi sản phẩm trong đơn hàng được hiển thị chính xác
        $stmt = $this->db->prepare("
            SELECT 
                oi.product_id,
                oi.product_name,
                oi.product_image,
                oi.price,
                oi.quantity as total_quantity,
                p.name as current_product_name, 
                pi.image_url as current_product_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE oi.order_id = ?
            ORDER BY oi.product_id
        ");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll();
        
        if (empty($orderItems)) {
            setFlash('error', 'Không tìm thấy sản phẩm trong đơn hàng!');
            redirect('index.php?page=orders');
        }
        
        // Kiểm tra sản phẩm nào đã được đánh giá
        $allReviewed = true;
        foreach ($orderItems as &$item) {
            $stmt = $this->db->prepare("
                SELECT id FROM comments 
                WHERE user_id = ? AND order_id = ? AND product_id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $orderId, $item['product_id']]);
            $item['already_reviewed'] = $stmt->fetch() ? true : false;
            
            // Nếu có ít nhất 1 sản phẩm chưa được đánh giá
            if (!$item['already_reviewed']) {
                $allReviewed = false;
            }
        }
        
        // Nếu tất cả sản phẩm đã được đánh giá, chuyển hướng với thông báo
        if ($allReviewed) {
            setFlash('info', 'Tất cả sản phẩm trong đơn hàng này đã được đánh giá!');
            redirect('index.php?page=orders&action=detail&id=' . $orderId);
        }
        
        require_once 'views/comments/order_review.php';
    }
    
    // Xử lý submit đánh giá nhiều sản phẩm
    public function submitOrderReviews() {
        if (!isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }
        
        $orderId = $_POST['order_id'] ?? 0;
        $reviewsJson = $_POST['reviews'] ?? '';
        
        try {
            $reviews = json_decode($reviewsJson, true);
            
            if (!$reviews || !is_array($reviews)) {
                throw new Exception('Dữ liệu đánh giá không hợp lệ');
            }
            
            // Kiểm tra đơn hàng
            $stmt = $this->db->prepare("
                SELECT id FROM orders 
                WHERE id = ? AND user_id = ? AND order_status = 'delivered'
            ");
            $stmt->execute([$orderId, $_SESSION['user_id']]);
            $validOrder = $stmt->fetch();
            
            if (!$validOrder) {
                throw new Exception('Đơn hàng không hợp lệ');
            }
            
            $this->db->beginTransaction();
            
            foreach ($reviews as $review) {
                $productId = $review['product_id'] ?? 0;
                $rating = 5; // Mặc định 5 sao
                $content = sanitize($review['content'] ?? '');
                
                // Validate
                if (empty($content)) {
                    throw new Exception('Vui lòng nhập nội dung đánh giá');
                }
                
                // Kiểm tra sản phẩm có trong đơn hàng không
                $stmt = $this->db->prepare("
                    SELECT id FROM order_items 
                    WHERE order_id = ? AND product_id = ?
                ");
                $stmt->execute([$orderId, $productId]);
                $validProduct = $stmt->fetch();
                
                if (!$validProduct) {
                    throw new Exception('Sản phẩm không có trong đơn hàng');
                }
                
                // Kiểm tra đã đánh giá chưa
                $stmt = $this->db->prepare("
                    SELECT id FROM comments 
                    WHERE user_id = ? AND order_id = ? AND product_id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $orderId, $productId]);
                $existingComment = $stmt->fetch();
                
                if ($existingComment) {
                    continue; // Bỏ qua nếu đã đánh giá
                }
                
                // Thêm đánh giá
                $stmt = $this->db->prepare("
                    INSERT INTO comments (user_id, product_id, order_id, content, rating, status, created_at)
                    VALUES (?, ?, ?, ?, ?, 'approved', NOW())
                ");
                $stmt->execute([$_SESSION['user_id'], $productId, $orderId, $content, $rating]);
            }
            
            $this->db->commit();
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Đánh giá đã được gửi thành công']);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
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
        $rating = 5; // Mặc định 5 sao
        
        // Validate
        if (empty($content)) {
            setFlash('error', 'Vui lòng nhập nội dung bình luận!');
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
    
    // Lấy bình luận của sản phẩm với replies
    public function getProductComments($productId, $limit = 10, $offset = 0) {
        // Lấy comments chính
        $stmt = $this->db->prepare("
            SELECT c.*, u.full_name, u.avatar, o.order_number
            FROM comments c
            JOIN users u ON c.user_id = u.id
            LEFT JOIN orders o ON c.order_id = o.id
            WHERE c.product_id = ? AND c.status = 'approved' AND c.parent_id IS NULL
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$productId, $limit, $offset]);
        $comments = $stmt->fetchAll();
        
        // Lấy replies cho mỗi comment
        foreach ($comments as &$comment) {
            $stmt = $this->db->prepare("
                SELECT c.*, u.full_name, u.avatar
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.parent_id = ? AND c.status = 'approved'
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([$comment['id']]);
            $comment['replies'] = $stmt->fetchAll();
        }
        
        return $comments;
    }
    
    // Đếm tổng số bình luận của sản phẩm (chỉ comments chính)
    public function countProductComments($productId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM comments 
            WHERE product_id = ? AND status = 'approved' AND parent_id IS NULL
        ");
        $stmt->execute([$productId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    // Xử lý phản hồi admin
    public function adminReply() {
        if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }
        
        $commentId = $_POST['comment_id'] ?? 0;
        $content = sanitize($_POST['content'] ?? '');
        
        try {
            // Validate
            if (empty($content)) {
                throw new Exception('Vui lòng nhập nội dung phản hồi');
            }
            
            // Kiểm tra comment gốc có tồn tại không
            $stmt = $this->db->prepare("
                SELECT id, product_id FROM comments 
                WHERE id = ? AND status = 'approved'
            ");
            $stmt->execute([$commentId]);
            $originalComment = $stmt->fetch();
            
            if (!$originalComment) {
                throw new Exception('Không tìm thấy bình luận gốc');
            }
            
            // Thêm phản hồi
            $stmt = $this->db->prepare("
                INSERT INTO comments (user_id, product_id, parent_id, content, status, created_at)
                VALUES (?, ?, ?, ?, 'approved', NOW())
            ");
            $stmt->execute([
                $_SESSION['user_id'], 
                $originalComment['product_id'], 
                $commentId, 
                $content
            ]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Phản hồi đã được gửi thành công']);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
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