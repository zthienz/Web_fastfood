<?php

class CartController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        // Kiểm tra đăng nhập
        if (!isLoggedIn()) {
            setFlash('error', 'Vui lòng đăng nhập để xem giỏ hàng!');
            redirect('index.php?page=login');
        }
        
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $total = 0;
        
        if (!empty($cart)) {
            $ids = array_keys($cart);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $this->db->prepare("
                SELECT p.*, pi.image_url as primary_image
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
                WHERE p.id IN ($placeholders)
            ");
            $stmt->execute($ids);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($products as $product) {
                $quantity = $cart[$product['id']];
                $price = $product['sale_price'] ?? $product['price'];
                $subtotal = $price * $quantity;
                $total += $subtotal;
                
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            }
        }
        
        require_once 'views/cart/index.php';
    }
    
    public function add() {
        // Kiểm tra đăng nhập trước
        if (!isLoggedIn()) {
            setFlash('error', 'Vui lòng đăng nhập hoặc đăng ký để thêm món ăn vào giỏ hàng!');
            redirect('index.php?page=login');
        }
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            redirect('index.php?page=menu');
        }
        
        // Kiểm tra món ăn tồn tại và tồn kho
        $stmt = $this->db->prepare("
            SELECT id, name, stock_quantity, status 
            FROM products 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            setFlash('error', 'Món ăn không tồn tại!');
            redirect('index.php?page=menu');
        }
        
        // Kiểm tra trạng thái
        if ($product['status'] === 'out_of_stock') {
            setFlash('error', 'Món ăn đã hết hàng!');
            redirect('index.php?page=menu');
        }
        
        if ($product['status'] !== 'active') {
            setFlash('error', 'Món ăn hiện không khả dụng!');
            redirect('index.php?page=menu');
        }
        
        // Kiểm tra tồn kho
        if ($product['stock_quantity'] <= 0) {
            setFlash('error', 'Món ăn đã hết hàng!');
            redirect('index.php?page=menu');
        }
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Kiểm tra số lượng trong giỏ
        $currentQty = $_SESSION['cart'][$id] ?? 0;
        if ($currentQty >= $product['stock_quantity']) {
            setFlash('error', 'Không thể thêm! Tồn kho chỉ còn ' . $product['stock_quantity'] . ' sản phẩm.');
            redirect('index.php?page=menu');
        }
        
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }
        
        setFlash('success', 'Đã thêm vào giỏ hàng!');
        redirect('index.php?page=cart');
    }
    
    public function update() {
        if (!isLoggedIn()) {
            setFlash('error', 'Vui lòng đăng nhập!');
            redirect('index.php?page=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=cart');
        }
        
        $id = intval($_POST['id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        
        if ($id > 0 && $quantity > 0 && isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = $quantity;
            setFlash('success', 'Đã cập nhật giỏ hàng!');
        }
        
        redirect('index.php?page=cart');
    }
    
    public function remove() {
        if (!isLoggedIn()) {
            setFlash('error', 'Vui lòng đăng nhập!');
            redirect('index.php?page=login');
        }
        
        $id = intval($_GET['id'] ?? 0);
        
        if ($id > 0 && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            setFlash('success', 'Đã xóa khỏi giỏ hàng!');
        }
        
        redirect('index.php?page=cart');
    }
    
    public function checkout() {
        if (!isLoggedIn()) {
            setFlash('error', 'Vui lòng đăng nhập để đặt hàng!');
            redirect('index.php?page=login');
        }
        
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            setFlash('error', 'Giỏ hàng trống!');
            redirect('index.php?page=cart');
        }
        
        // Lấy danh sách món được chọn từ form
        $selectedItems = $_POST['selected_items'] ?? [];
        if (empty($selectedItems)) {
            setFlash('error', 'Vui lòng chọn ít nhất một món để đặt hàng!');
            redirect('index.php?page=cart');
        }
        
        // Lọc giỏ hàng chỉ lấy các món được chọn
        $selectedCart = [];
        foreach ($selectedItems as $productId) {
            if (isset($cart[$productId])) {
                $selectedCart[$productId] = $cart[$productId];
            }
        }
        
        if (empty($selectedCart)) {
            setFlash('error', 'Không có món nào được chọn hợp lệ!');
            redirect('index.php?page=cart');
        }
        
        try {
            $this->db->beginTransaction();
            
            // Lấy thông tin user
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            // Tạo mã đơn hàng
            $orderNumber = 'ORD' . date('YmdHis') . rand(100, 999);
            
            // Tính tổng tiền chỉ cho các món được chọn
            $subtotal = 0;
            foreach ($selectedCart as $productId => $quantity) {
                $stmt = $this->db->prepare("SELECT price, sale_price FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();
                
                if ($product) {
                    $price = $product['sale_price'] ?? $product['price'];
                    $subtotal += $price * $quantity;
                }
            }
            
            $shippingFee = $subtotal >= 200000 ? 0 : 30000;
            $total = $subtotal + $shippingFee;
            
            // Tạo đơn hàng
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    user_id, order_number, customer_name, customer_email, 
                    customer_phone, shipping_address, subtotal, shipping_fee, 
                    total, payment_method, payment_status, order_status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'cod', 'pending', 'pending')
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $orderNumber,
                $user['full_name'],
                $user['email'],
                $user['phone'] ?? '',
                $user['address'] ?? '',
                $subtotal,
                $shippingFee,
                $total
            ]);
            $orderId = $this->db->lastInsertId();
            
            // Thêm chi tiết đơn hàng và giảm tồn kho chỉ cho các món được chọn
            foreach ($selectedCart as $productId => $quantity) {
                $stmt = $this->db->prepare("
                    SELECT p.*, pi.image_url 
                    FROM products p
                    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
                    WHERE p.id = ?
                ");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();
                
                if ($product) {
                    // Kiểm tra tồn kho
                    if ($product['stock_quantity'] < $quantity) {
                        throw new Exception("Sản phẩm '{$product['name']}' không đủ số lượng trong kho!");
                    }
                    
                    $price = $product['sale_price'] ?? $product['price'];
                    $itemSubtotal = $price * $quantity;
                    
                    // Thêm order item
                    $stmt = $this->db->prepare("
                        INSERT INTO order_items (
                            order_id, product_id, product_name, product_image, 
                            price, quantity, subtotal
                        ) VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $orderId,
                        $productId,
                        $product['name'],
                        $product['image_url'] ?? $product['image'],
                        $price,
                        $quantity,
                        $itemSubtotal
                    ]);
                    
                    // Giảm tồn kho
                    $newStock = $product['stock_quantity'] - $quantity;
                    $newStatus = $product['status'];
                    
                    // Tự động chuyển sang hết hàng nếu tồn kho = 0
                    if ($newStock <= 0) {
                        $newStatus = 'out_of_stock';
                        $newStock = 0;
                    }
                    
                    $stmt = $this->db->prepare("
                        UPDATE products 
                        SET stock_quantity = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$newStock, $newStatus, $productId]);
                }
            }
            
            // Tạo bản ghi thanh toán
            $stmt = $this->db->prepare("
                INSERT INTO payments (order_id, payment_method, amount, status)
                VALUES (?, 'cod', ?, 'pending')
            ");
            $stmt->execute([$orderId, $total]);
            
            // Lưu lịch sử trạng thái
            $stmt = $this->db->prepare("
                INSERT INTO order_status_history (order_id, status, note)
                VALUES (?, 'pending', 'Đơn hàng được tạo')
            ");
            $stmt->execute([$orderId]);
            
            $this->db->commit();
            
            // Xóa các món đã đặt khỏi giỏ hàng
            foreach ($selectedItems as $productId) {
                unset($_SESSION['cart'][$productId]);
            }
            
            // Nếu giỏ hàng trống hoàn toàn thì xóa session
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }
            
            setFlash('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $orderNumber);
            redirect('index.php?page=orders');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect('index.php?page=cart');
        }
    }
}
