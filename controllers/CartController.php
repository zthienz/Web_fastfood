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
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm món ăn vào giỏ hàng!', 'redirect' => 'index.php?page=login']);
                exit;
            }
            setFlash('error', 'Vui lòng đăng nhập hoặc đăng ký để thêm món ăn vào giỏ hàng!');
            redirect('index.php?page=login');
        }
        
        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
        $quantity = intval($_GET['quantity'] ?? $_POST['quantity'] ?? 1);
        
        if ($id <= 0) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ!']);
                exit;
            }
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
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Món ăn không tồn tại!']);
                exit;
            }
            setFlash('error', 'Món ăn không tồn tại!');
            redirect('index.php?page=menu');
        }
        
        // Kiểm tra trạng thái
        if ($product['status'] === 'out_of_stock') {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Món ăn đã hết hàng!']);
                exit;
            }
            setFlash('error', 'Món ăn đã hết hàng!');
            redirect('index.php?page=menu');
        }
        
        if ($product['status'] !== 'active') {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Món ăn hiện không khả dụng!']);
                exit;
            }
            setFlash('error', 'Món ăn hiện không khả dụng!');
            redirect('index.php?page=menu');
        }
        
        // Kiểm tra tồn kho
        if ($product['stock_quantity'] <= 0) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Món ăn đã hết hàng!']);
                exit;
            }
            setFlash('error', 'Món ăn đã hết hàng!');
            redirect('index.php?page=menu');
        }
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Kiểm tra số lượng trong giỏ
        $currentQty = $_SESSION['cart'][$id] ?? 0;
        $newQty = $currentQty + $quantity;
        
        if ($newQty > $product['stock_quantity']) {
            $availableQty = $product['stock_quantity'] - $currentQty;
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'message' => $availableQty > 0 ? 
                        "Chỉ có thể thêm tối đa {$availableQty} sản phẩm nữa!" : 
                        "Không thể thêm! Tồn kho chỉ còn {$product['stock_quantity']} sản phẩm và bạn đã có {$currentQty} trong giỏ hàng."
                ]);
                exit;
            }
            setFlash('error', 'Không thể thêm! Tồn kho chỉ còn ' . $product['stock_quantity'] . ' sản phẩm.');
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=menu';
            redirect($referer);
        }
        
        // Thêm vào giỏ hàng
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $quantity;
        } else {
            $_SESSION['cart'][$id] = $quantity;
        }
        
        // Tính tổng số lượng trong giỏ hàng
        $totalItems = array_sum($_SESSION['cart']);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => "Đã thêm {$quantity} {$product['name']} vào giỏ hàng!",
                'cart_count' => $totalItems,
                'product_name' => $product['name'],
                'quantity_added' => $quantity
            ]);
            exit;
        }
        
        setFlash('success', 'Đã thêm vào giỏ hàng!');
        
        // Quay lại trang trước đó thay vì redirect đến giỏ hàng
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=menu';
        redirect($referer);
    }
    
    public function update() {
        if (!isLoggedIn()) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
                exit;
            }
            setFlash('error', 'Vui lòng đăng nhập!');
            redirect('index.php?page=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=cart');
        }
        
        $id = intval($_POST['id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        
        if ($id > 0 && $quantity > 0 && isset($_SESSION['cart'][$id])) {
            // Kiểm tra tồn kho trước khi cập nhật
            $stmt = $this->db->prepare("SELECT name, stock_quantity FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
            
            if ($product && $quantity <= $product['stock_quantity']) {
                $_SESSION['cart'][$id] = $quantity;
                
                // Nếu là AJAX request, trả về JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    echo json_encode(['success' => true, 'message' => 'Đã cập nhật số lượng!']);
                    exit;
                }
                
                setFlash('success', 'Đã cập nhật số lượng!');
            } else {
                // Nếu là AJAX request, trả về JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    echo json_encode(['success' => false, 'message' => 'Số lượng vượt quá tồn kho có sẵn!']);
                    exit;
                }
                
                setFlash('error', 'Số lượng vượt quá tồn kho có sẵn!');
            }
        } else {
            // Nếu là AJAX request, trả về JSON
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ!']);
                exit;
            }
            
            setFlash('error', 'Thông tin không hợp lệ!');
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
    
    // Hiển thị trang thanh toán
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
        $selectedItems = $_POST['selected_items'] ?? $_SESSION['checkout_items'] ?? [];
        if (empty($selectedItems)) {
            setFlash('error', 'Vui lòng chọn ít nhất một món để đặt hàng!');
            redirect('index.php?page=cart');
        }
        
        // Lưu vào session để dùng khi place order
        $_SESSION['checkout_items'] = $selectedItems;
        
        // Lấy thông tin user
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        // Kiểm tra xem user đã có địa chỉ chưa
        $requireAddressInput = empty(trim($user['address'] ?? ''));
        
        // Nếu chưa có địa chỉ và không phải từ form cập nhật địa chỉ
        if ($requireAddressInput && !isset($_POST['address_updated'])) {
            // Hiển thị form yêu cầu nhập địa chỉ
            require_once 'views/cart/address_required.php';
            return;
        }
        
        // Lấy thông tin sản phẩm được chọn
        $checkoutItems = [];
        $subtotal = 0;
        
        $ids = array_map('intval', $selectedItems);
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
            $quantity = $cart[$product['id']] ?? 0;
            if ($quantity > 0) {
                $price = $product['sale_price'] ?? $product['price'];
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;
                
                $checkoutItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal
                ];
            }
        }
        
        $shippingFee = $subtotal >= 200000 ? 0 : 30000;
        $total = $subtotal + $shippingFee;
        
        require_once 'views/cart/checkout.php';
    }
    
    // Xử lý đặt hàng
    public function placeOrder() {
        if (!isLoggedIn()) {
            setFlash('error', 'Vui lòng đăng nhập để đặt hàng!');
            redirect('index.php?page=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=cart');
        }
        
        $cart = $_SESSION['cart'] ?? [];
        $selectedItems = $_SESSION['checkout_items'] ?? [];
        
        if (empty($cart) || empty($selectedItems)) {
            setFlash('error', 'Giỏ hàng trống hoặc chưa chọn món!');
            redirect('index.php?page=cart');
        }
        
        // Lấy thông tin từ form
        $customerName = sanitize($_POST['customer_name'] ?? '');
        $customerPhone = sanitize($_POST['customer_phone'] ?? '');
        $customerEmail = sanitize($_POST['customer_email'] ?? '');
        $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $note = sanitize($_POST['note'] ?? '');
        
        // Validate
        if (empty($customerName) || empty($customerPhone) || empty($shippingAddress)) {
            setFlash('error', 'Vui lòng điền đầy đủ thông tin giao hàng!');
            redirect('index.php?page=cart&action=checkout');
        }
        
        // Cập nhật địa chỉ vào database nếu khác với địa chỉ hiện tại
        $stmt = $this->db->prepare("SELECT address FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $currentUser = $stmt->fetch();
        
        if ($currentUser && $currentUser['address'] !== $shippingAddress) {
            $stmt = $this->db->prepare("UPDATE users SET address = ? WHERE id = ?");
            $stmt->execute([$shippingAddress, $_SESSION['user_id']]);
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
            
            // Tạo mã đơn hàng
            $orderNumber = 'ORD' . date('YmdHis') . rand(100, 999);
            
            // Tính tổng tiền
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
                    total, payment_method, payment_status, order_status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $orderNumber,
                $customerName,
                $customerEmail,
                $customerPhone,
                $shippingAddress,
                $subtotal,
                $shippingFee,
                $total,
                $paymentMethod,
                $note
            ]);
            $orderId = $this->db->lastInsertId();
            
            // Thêm chi tiết đơn hàng
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
                    if ($product['stock_quantity'] < $quantity) {
                        throw new Exception("Sản phẩm '{$product['name']}' không đủ số lượng trong kho!");
                    }
                    
                    $price = $product['sale_price'] ?? $product['price'];
                    $itemSubtotal = $price * $quantity;
                    
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
                    $newStatus = $newStock <= 0 ? 'out_of_stock' : $product['status'];
                    
                    $stmt = $this->db->prepare("UPDATE products SET stock_quantity = ?, status = ? WHERE id = ?");
                    $stmt->execute([max(0, $newStock), $newStatus, $productId]);
                }
            }
            
            // Tạo bản ghi thanh toán
            $stmt = $this->db->prepare("
                INSERT INTO payments (order_id, payment_method, amount, status)
                VALUES (?, ?, ?, 'pending')
            ");
            $stmt->execute([$orderId, $paymentMethod, $total]);
            
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
            unset($_SESSION['checkout_items']);
            
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }
            
            // Chuyển đến trang thành công
            $_SESSION['order_success'] = [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'total' => $total
            ];
            redirect('index.php?page=cart&action=success');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
            redirect('index.php?page=cart&action=checkout');
        }
    }
    
    // Trang đặt hàng thành công
    public function success() {
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $orderSuccess = $_SESSION['order_success'] ?? null;
        if (!$orderSuccess) {
            redirect('index.php?page=orders');
        }
        
        unset($_SESSION['order_success']);
        
        // Lấy thông tin đơn hàng
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderSuccess['order_id']]);
        $order = $stmt->fetch();
        
        // Lấy chi tiết đơn hàng
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderSuccess['order_id']]);
        $orderItems = $stmt->fetchAll();
        
        require_once 'views/cart/success.php';
    }
    
    // Xử lý cập nhật địa chỉ từ trang checkout
    public function updateAddress() {
        if (!isLoggedIn()) {
            setFlash('error', 'Vui lòng đăng nhập!');
            redirect('index.php?page=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=cart');
        }
        
        $address = sanitize($_POST['address'] ?? '');
        $fullName = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        
        if (empty($address)) {
            setFlash('error', 'Vui lòng nhập địa chỉ!');
            redirect('index.php?page=cart&action=checkout');
        }
        
        // Cập nhật thông tin user
        $stmt = $this->db->prepare("
            UPDATE users 
            SET address = ?, full_name = ?, phone = ?
            WHERE id = ?
        ");
        
        if ($stmt->execute([$address, $fullName, $phone, $_SESSION['user_id']])) {
            // Cập nhật session
            $_SESSION['full_name'] = $fullName;
            setFlash('success', 'Đã cập nhật thông tin thành công!');
            
            // Redirect về checkout với flag đã cập nhật địa chỉ
            $_POST['address_updated'] = true;
            $this->checkout();
        } else {
            setFlash('error', 'Có lỗi xảy ra, vui lòng thử lại!');
            redirect('index.php?page=cart&action=checkout');
        }
    }
}