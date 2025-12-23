<?php

class PostController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Hiển thị tất cả sản phẩm dạng grid
    public function index() {
        $stmt = $this->db->query("
            SELECT p.*, pi.image_url as primary_image, c.name as category_name
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active'
            ORDER BY p.created_at DESC
        ");
        $products = $stmt->fetchAll();

        require_once 'views/posts/index.php';
    }

    // Hiển thị chi tiết 1 sản phẩm
    public function show() {
        $id = $_GET['id'] ?? 1;

        $stmt = $this->db->prepare("
            SELECT p.*, pi.image_url as primary_image, c.name as category_name
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $post = $stmt->fetch();

        if (!$post) {
            header('Location: index.php?page=posts');
            exit;
        }

        require_once 'views/posts/article.php';
    }
}
