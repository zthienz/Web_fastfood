<?php

class AdminProductController {
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
    
    // Danh sách sản phẩm
    public function index() {
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = $_GET['p'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $where = ["1=1"];
        $params = [];
        
        if ($search) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($category) {
            $where[] = "p.category_id = ?";
            $params[] = $category;
        }
        
        if ($status) {
            $where[] = "p.status = ?";
            $params[] = $status;
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Đếm tổng
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products p WHERE $whereClause");
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];
        
        // Lấy danh sách
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name,
                   (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE $whereClause 
            ORDER BY p.created_at DESC 
            LIMIT $limit OFFSET $offset
        ");
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        
        // Lấy danh mục
        $categories = $this->db->query("SELECT * FROM categories ORDER BY display_order")->fetchAll();
        
        $totalPages = ceil($total / $limit);
        
        require_once 'views/admin/products/index.php';
    }
    
    // Form thêm sản phẩm
    public function create() {
        $categories = $this->db->query("SELECT * FROM categories ORDER BY display_order")->fetchAll();
        require_once 'views/admin/products/create.php';
    }
    
    // Lưu sản phẩm mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=products');
        }
        
        $name = sanitize($_POST['name']);
        $slug = $this->generateSlug($name);
        $categoryId = $_POST['category_id'];
        $description = sanitize($_POST['description']);
        $price = $_POST['price'];
        $salePrice = $_POST['sale_price'] ?: null;
        $stockQuantity = intval($_POST['stock_quantity']);
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $status = $_POST['status'];
        
        // Tự động chuyển trạng thái nếu tồn kho = 0
        if ($stockQuantity <= 0) {
            $status = 'out_of_stock';
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO products (category_id, name, slug, description, price, sale_price, stock_quantity, is_featured, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $stockQuantity, $isFeatured, $status]);
        
        $productId = $this->db->lastInsertId();
        
        // Xử lý upload ảnh
        $this->handleImageUpload($productId);
        
        setFlash('success', 'Thêm sản phẩm thành công!');
        redirect('index.php?page=admin&section=products');
    }
    
    // Form sửa sản phẩm
    public function edit() {
        $id = $_GET['id'] ?? 0;
        
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            setFlash('error', 'Sản phẩm không tồn tại!');
            redirect('index.php?page=admin&section=products');
        }
        
        $categories = $this->db->query("SELECT * FROM categories ORDER BY display_order")->fetchAll();
        
        $stmt = $this->db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order");
        $stmt->execute([$id]);
        $images = $stmt->fetchAll();
        
        require_once 'views/admin/products/edit.php';
    }
    
    // Cập nhật sản phẩm
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=products');
        }
        
        $id = $_POST['id'];
        $name = sanitize($_POST['name']);
        $slug = $this->generateSlug($name, $id); // Thêm productId để exclude khi check duplicate
        $categoryId = $_POST['category_id'];
        $description = sanitize($_POST['description']);
        $price = $_POST['price'];
        $salePrice = $_POST['sale_price'] ?: null;
        $stockQuantity = intval($_POST['stock_quantity']);
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $status = $_POST['status'];
        
        // Tự động chuyển trạng thái nếu tồn kho = 0
        if ($stockQuantity <= 0) {
            $status = 'out_of_stock';
        }
        
        $stmt = $this->db->prepare("
            UPDATE products 
            SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, sale_price = ?, 
                stock_quantity = ?, is_featured = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([$categoryId, $name, $slug, $description, $price, $salePrice, $stockQuantity, $isFeatured, $status, $id]);
        
        // Xử lý upload ảnh mới
        $this->handleImageUpload($id);
        
        setFlash('success', 'Cập nhật sản phẩm thành công!');
        redirect('index.php?page=admin&section=products&action=edit&id=' . $id);
    }
    
    // Xóa sản phẩm
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&section=products');
        }
        
        $id = $_POST['id'];
        
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        setFlash('success', 'Xóa sản phẩm thành công!');
        redirect('index.php?page=admin&section=products');
    }
    
    // Xóa ảnh sản phẩm
    public function deleteImage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $imageId = $_POST['image_id'] ?? 0;
        $productId = $_POST['product_id'] ?? 0;
        
        try {
            // Lấy thông tin ảnh
            $stmt = $this->db->prepare("SELECT * FROM product_images WHERE id = ? AND product_id = ?");
            $stmt->execute([$imageId, $productId]);
            $image = $stmt->fetch();
            
            if (!$image) {
                echo json_encode(['success' => false, 'message' => 'Ảnh không tồn tại']);
                return;
            }
            
            $isPrimary = $image['is_primary'];
            
            // Xóa file ảnh
            if (file_exists($image['image_url'])) {
                unlink($image['image_url']);
            }
            
            // Xóa record trong database
            $stmt = $this->db->prepare("DELETE FROM product_images WHERE id = ?");
            $stmt->execute([$imageId]);
            
            // Nếu xóa ảnh chính, tự động đặt ảnh khác làm ảnh chính
            if ($isPrimary) {
                $stmt = $this->db->prepare("
                    SELECT id FROM product_images 
                    WHERE product_id = ? 
                    ORDER BY display_order ASC 
                    LIMIT 1
                ");
                $stmt->execute([$productId]);
                $nextImage = $stmt->fetch();
                
                if ($nextImage) {
                    $stmt = $this->db->prepare("UPDATE product_images SET is_primary = 1 WHERE id = ?");
                    $stmt->execute([$nextImage['id']]);
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Xóa ảnh thành công']);
            
        } catch (Exception $e) {
            error_log("Error deleting image: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa ảnh']);
        }
    }
    
    // Đặt ảnh chính
    public function setPrimaryImage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $imageId = $_POST['image_id'] ?? 0;
        $productId = $_POST['product_id'] ?? 0;
        
        try {
            // Kiểm tra ảnh có tồn tại không
            $stmt = $this->db->prepare("SELECT id FROM product_images WHERE id = ? AND product_id = ?");
            $stmt->execute([$imageId, $productId]);
            
            if (!$stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Ảnh không tồn tại']);
                return;
            }
            
            // Bỏ primary của tất cả ảnh khác
            $stmt = $this->db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?");
            $stmt->execute([$productId]);
            
            // Đặt ảnh được chọn làm primary
            $stmt = $this->db->prepare("UPDATE product_images SET is_primary = 1 WHERE id = ?");
            $stmt->execute([$imageId]);
            
            echo json_encode(['success' => true, 'message' => 'Đặt ảnh chính thành công']);
            
        } catch (Exception $e) {
            error_log("Error setting primary image: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi đặt ảnh chính']);
        }
    }
    
    private function generateSlug($text, $productId = null) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $baseSlug = trim($text, '-');
        
        $slug = $baseSlug;
        $counter = 1;
        
        // Kiểm tra slug đã tồn tại chưa
        while ($this->slugExists($slug, $productId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM products WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    private function handleImageUpload($productId) {
        if (!isset($_FILES['images']) || empty($_FILES['images']['tmp_name'][0])) {
            return;
        }
        
        $uploadDir = 'public/images/products/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Trước khi thêm ảnh mới, set tất cả ảnh cũ thành không phải primary
        $stmt = $this->db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?");
        $stmt->execute([$productId]);
        
        $order = 1;
        $firstImageId = null;
        
        // Insert tất cả ảnh
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK && !empty($tmpName)) {
                $originalName = $_FILES['images']['name'][$key];
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $fileName = time() . '_' . $key . '_' . uniqid() . '.' . $extension;
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    try {
                        // Insert ảnh với is_primary = 0 để tránh trigger
                        $stmt = $this->db->prepare("
                            INSERT INTO product_images (product_id, image_url, is_primary, display_order)
                            VALUES (?, ?, 0, ?)
                        ");
                        $stmt->execute([$productId, $filePath, $order]);
                        
                        $imageId = $this->db->lastInsertId();
                        if ($firstImageId === null) {
                            $firstImageId = $imageId;
                        }
                        $order++;
                    } catch (Exception $e) {
                        // Nếu lỗi database, xóa file đã upload
                        unlink($filePath);
                        error_log("Database error when inserting image: " . $e->getMessage());
                    }
                }
            }
        }
        
        // Sau khi insert xong tất cả, set ảnh đầu tiên làm primary
        if ($firstImageId !== null) {
            try {
                $stmt = $this->db->prepare("UPDATE product_images SET is_primary = 1 WHERE id = ?");
                $stmt->execute([$firstImageId]);
            } catch (Exception $e) {
                error_log("Error setting primary image: " . $e->getMessage());
            }
        }
    }
}
