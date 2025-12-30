<?php

class PostController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Hiển thị tất cả bài đăng dạng grid
    public function index()
    {
        $stmt = $this->db->query("
            SELECT p.*, u.full_name as author_name
            FROM posts p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.status = 'published'
            ORDER BY p.published_at DESC
        ");
        $posts = $stmt->fetchAll();

        require_once 'views/posts/index.php';
    }

    // Hiển thị chi tiết 1 bài đăng
    public function show()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header('Location: index.php?page=posts');
            exit;
        }

        $stmt = $this->db->prepare("
            SELECT p.*, u.full_name as author_name
            FROM posts p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.id = ? AND p.status = 'published'
        ");
        $stmt->execute([$id]);
        $post = $stmt->fetch();

        if (!$post) {
            header('Location: index.php?page=posts');
            exit;
        }

        // Tăng lượt xem
        $this->db->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$id]);

        require_once 'views/posts/article.php';
    }
}
