<?php

class ProfileController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
    }
    
    public function index() {
        // Lấy thông tin đầy đủ từ database
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            session_destroy();
            redirect('index.php?page=login');
        }
        
        $editMode = $_GET['edit'] ?? false;
        
        require_once 'views/profile.php';
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=profile');
        }
        
        // Debug: Hiển thị thông tin debug tạm thời
        if (isset($_GET['debug'])) {
            echo "<pre style='background: #f0f0f0; padding: 10px; margin: 10px;'>";
            echo "POST data:\n";
            print_r($_POST);
            echo "\nFILES data:\n";
            print_r($_FILES);
            echo "</pre>";
        }
        
        $fullName = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        
        if (empty($fullName)) {
            setFlash('error', 'Vui lòng nhập họ tên!');
            redirect('index.php?page=profile&edit=1');
        }
        
        // Xử lý upload avatar
        $avatarPath = null;
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
            // Debug log
            error_log("Avatar upload - FILES data: " . print_r($_FILES['avatar'], true));
            
            if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatarPath = $this->handleAvatarUpload($_FILES['avatar']);
                if (!$avatarPath) {
                    redirect('index.php?page=profile&edit=1');
                    return;
                }
            } else {
                // Xử lý các lỗi upload khác
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => 'File quá lớn (vượt quá upload_max_filesize)',
                    UPLOAD_ERR_FORM_SIZE => 'File quá lớn (vượt quá MAX_FILE_SIZE)',
                    UPLOAD_ERR_PARTIAL => 'File chỉ được upload một phần',
                    UPLOAD_ERR_NO_FILE => 'Không có file nào được upload',
                    UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
                    UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file',
                    UPLOAD_ERR_EXTENSION => 'Upload bị dừng bởi extension'
                ];
                
                $errorMsg = $uploadErrors[$_FILES['avatar']['error']] ?? 'Lỗi upload không xác định';
                setFlash('error', 'Lỗi upload ảnh: ' . $errorMsg);
                redirect('index.php?page=profile&edit=1');
                return;
            }
        }
        
        // Cập nhật thông tin
        if ($avatarPath) {
            // Xóa avatar cũ nếu có
            $stmt = $this->db->prepare("SELECT avatar FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $oldUser = $stmt->fetch();
            
            if ($oldUser && $oldUser['avatar'] && file_exists('public/images/avatars/' . $oldUser['avatar'])) {
                unlink('public/images/avatars/' . $oldUser['avatar']);
            }
            
            $stmt = $this->db->prepare("
                UPDATE users 
                SET full_name = ?, phone = ?, address = ?, avatar = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([$fullName, $phone, $address, $avatarPath, $_SESSION['user_id']]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET full_name = ?, phone = ?, address = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([$fullName, $phone, $address, $_SESSION['user_id']]);
        }
        
        if ($result) {
            $_SESSION['full_name'] = $fullName;
            setFlash('success', 'Cập nhật thông tin thành công!');
        } else {
            setFlash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
        
        redirect('index.php?page=profile');
    }
    
    private function handleAvatarUpload($file) {
        $uploadDir = 'public/images/avatars/';
        
        // Debug log
        error_log("Avatar upload attempt - File: " . print_r($file, true));
        
        // Kiểm tra thư mục upload
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Kiểm tra kích thước file (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            setFlash('error', 'Kích thước ảnh không được vượt quá 2MB!');
            return false;
        }
        
        // Kiểm tra định dạng file bằng extension và MIME type
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            setFlash('error', 'Chỉ chấp nhận file ảnh định dạng JPG, PNG, GIF!');
            return false;
        }
        
        // Kiểm tra MIME type nếu function tồn tại
        if (function_exists('mime_content_type')) {
            $fileType = mime_content_type($file['tmp_name']);
            if (!in_array($fileType, $allowedMimeTypes)) {
                setFlash('error', 'File không đúng định dạng ảnh!');
                return false;
            }
        }
        
        // Tạo tên file unique
        $fileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        
        // Debug log
        error_log("Attempting to move file to: " . $filePath);
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            error_log("Avatar upload successful: " . $fileName);
            return $fileName;
        } else {
            error_log("Avatar upload failed - move_uploaded_file returned false");
            setFlash('error', 'Có lỗi xảy ra khi upload ảnh!');
            return false;
        }
    }
}
