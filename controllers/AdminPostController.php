<?php

class AdminPostController {
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
    
    // Danh sách bài viết
    public function index() {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = $_GET['p'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $where = ["1=1"];
        $params = [];
        
        if ($search) {
            $where[] = "(title LIKE ? OR content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $where[] = "status = ?";
            $params[] = $status;
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Đếm tổng
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM posts WHERE $whereClause");
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];
        
        // Lấy danh sách
        $stmt = $this->db->prepare("
            SELECT p.*, u.full_name as author_name
            FROM posts p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE $whereClause 
            ORDER BY p.created_at DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $posts = $stmt->fetchAll();
        
        $totalPages = ceil($total / $limit);
        
        require_once 'views/admin/posts/index.php';
    }
    
    // Form thêm bài viết
    public function create() {
        require_once 'views/admin/posts/create.php';
    }
    
    // Lưu bài viết mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=posts');
        }
        
        $title = sanitize($_POST['title']);
        $slug = $this->generateSlug($title);
        $content = $_POST['content']; // Không sanitize vì có HTML
        $excerpt = sanitize($_POST['excerpt']);
        $category = sanitize($_POST['category']);
        $status = $_POST['status'];
        $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;
        
        // Xử lý upload ảnh đại diện
        $featuredImage = $this->handleImageUpload();
        
        $stmt = $this->db->prepare("
            INSERT INTO posts (author_id, title, slug, content, excerpt, category, featured_image, status, published_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $title, $slug, $content, $excerpt, $category, $featuredImage, $status, $publishedAt]);
        
        setFlash('success', 'Thêm bài viết thành công!');
        redirect('index.php?page=admin&section=posts');
    }
    
    // Form sửa bài viết
    public function edit() {
        $id = $_GET['id'] ?? 0;
        
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        
        if (!$post) {
            setFlash('error', 'Bài viết không tồn tại!');
            redirect('index.php?page=admin&section=posts');
        }
        
        require_once 'views/admin/posts/edit.php';
    }
    
    // Cập nhật bài viết
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=posts');
        }
        
        $id = $_POST['id'];
        $title = sanitize($_POST['title']);
        $content = $_POST['content'];
        $excerpt = sanitize($_POST['excerpt']);
        $category = sanitize($_POST['category']);
        $status = $_POST['status'];
        
        // Lấy thông tin bài viết hiện tại
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $currentPost = $stmt->fetch();
        
        $publishedAt = $currentPost['published_at'];
        if ($status === 'published' && !$publishedAt) {
            $publishedAt = date('Y-m-d H:i:s');
        }
        
        // Xử lý upload ảnh mới
        $featuredImage = $this->handleImageUpload();
        if (!$featuredImage) {
            $featuredImage = $currentPost['featured_image'];
        }
        
        $stmt = $this->db->prepare("
            UPDATE posts 
            SET title = ?, content = ?, excerpt = ?, category = ?, featured_image = ?, status = ?, published_at = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $content, $excerpt, $category, $featuredImage, $status, $publishedAt, $id]);
        
        setFlash('success', 'Cập nhật bài viết thành công!');
        redirect('index.php?page=admin&section=posts&action=edit&id=' . $id);
    }
    
    // Xóa bài viết
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=posts');
        }
        
        $id = $_POST['id'];
        
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        
        setFlash('success', 'Xóa bài viết thành công!');
        redirect('index.php?page=admin&section=posts');
    }
    
    private function generateSlug($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
    
    private function handleImageUpload() {
        if (!isset($_FILES['featured_image']) || $_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $uploadDir = 'public/images/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . $_FILES['featured_image']['name'];
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $filePath)) {
            return $filePath;
        }
        
        return null;
    }
}
