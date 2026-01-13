<?php

class ContactController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Hiển thị trang liên hệ
    public function index()
    {
        require_once 'views/contact/index.php';
    }

    // Xử lý form liên hệ
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=contact');
            exit;
        }
        
        // Kiểm tra quyền admin
        if (isAdmin()) {
            setFlash('error', adminRestrictionMessage());
            header('Location: index.php?page=contact');
            exit;
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        // Validate
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }
        
        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        
        if (empty($message)) {
            $errors[] = 'Vui lòng nhập nội dung';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            header('Location: index.php?page=contact');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO contacts (name, email, phone, subject, message) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $phone, $subject, $message]);

            setFlash('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.');
            header('Location: index.php?page=contact');
            exit;

        } catch (Exception $e) {
            setFlash('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
            header('Location: index.php?page=contact');
            exit;
        }
    }
}