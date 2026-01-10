<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item">
                        <a href="index.php" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="index.php?page=orders" class="text-decoration-none">Đơn hàng</a>
                    </li>
                    <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
                </ol>
            </nav>

            <!-- Header đơn hàng -->
            <div class="order-header-card mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="order-title mb-2">
                            <i class="fas fa-receipt text-primary me-2"></i>
                            Đơn hàng #<?= e($order['order_number']) ?>
                        </h2>
                        <p class="order-date mb-0">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Đặt ngày <?= date('d/m/Y lúc H:i', strtotime($order['created_at'])) ?>
                        </p>
                    </div>
                    <div class="order-status-badge">
                        <span class="status-badge status-<?= $order['order_status'] ?>">
                            <i class="fas <?= getOrderStatusIcon($order['order_status']) ?> me-1"></i>
                            <?= getOrderStatusText($order['order_status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Thông tin đơn hàng -->
                <div class="col-lg-8">
                    <!-- Sản phẩm đã đặt -->
                    <div class="products-card mb-4">
                        <div class="card-header-custom">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Sản phẩm đã đặt (<?= count($orderItems) ?> món)
                            </h5>
                        </div>
                        <div class="products-list">
                            <?php foreach ($orderItems as $item): ?>
                                <div class="product-item">
                                    <div class="product-image">
                                        <?php 
                                        $imageUrl = '';
                                        // Ưu tiên hình ảnh hiện tại từ product_images
                                        if ($item['current_product_image']) {
                                            $imageUrl = getImageUrl($item['current_product_image']);
                                        } elseif ($item['product_image']) {
                                            // Fallback về hình ảnh lưu trong order_items
                                            $imageUrl = getImageUrl($item['product_image']);
                                        }
                                        ?>
                                        <?php if ($imageUrl): ?>
                                            <img src="<?= $imageUrl ?>" 
                                                 alt="<?= e($item['product_name']) ?>" 
                                                 class="product-img">
                                        <?php else: ?>
                                            <div class="product-img-placeholder">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="product-details">
                                        <h6 class="product-name">
                                            <?= e($item['current_product_name'] ?: $item['product_name']) ?>
                                        </h6>
                                        <div class="product-meta">
                                            <span class="price"><?= formatMoney($item['price']) ?></span>
                                            <span class="quantity">x<?= e($item['quantity']) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="product-actions">
                                        <div class="subtotal">
                                            <?= formatMoney($item['subtotal']) ?>
                                        </div>
                                        
                                        <?php if ($order['order_status'] === 'delivered'): ?>
                                            <div class="rating-section mt-2">
                                                <?php if ($item['comment_id']): ?>
                                                    <!-- Đã bình luận -->
                                                    <div class="rated-badge">
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        <small class="text-success fw-bold">Đã đánh giá</small>
                                                    </div>
                                                    <div class="rating-stars mt-1">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star <?= $i <= $item['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <small class="text-muted d-block">
                                                        <?= date('d/m/Y', strtotime($item['comment_date'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <!-- Chưa bình luận -->
                                                    <a href="index.php?page=comments&action=form&order_id=<?= e($order['id']) ?>&product_id=<?= e($item['product_id']) ?>" 
                                                       class="btn-rate">
                                                        <i class="fas fa-star me-1"></i>
                                                        Đánh giá
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar thông tin -->
                <div class="col-lg-4">
                    <!-- Thông tin giao hàng -->
                    <div class="info-card mb-4">
                        <div class="info-header">
                            <i class="fas fa-truck text-primary me-2"></i>
                            <h6 class="mb-0">Thông tin giao hàng</h6>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <strong>Người nhận:</strong>
                                <span><?= e($order['customer_name']) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Điện thoại:</strong>
                                <span><?= e($order['customer_phone']) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong>
                                <span><?= e($order['customer_email']) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Địa chỉ:</strong>
                                <span><?= e($order['shipping_address']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin thanh toán -->
                    <div class="info-card mb-4">
                        <div class="info-header">
                            <i class="fas fa-credit-card text-success me-2"></i>
                            <h6 class="mb-0">Thông tin thanh toán</h6>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <strong>Phương thức:</strong>
                                <span><?= getPaymentMethodText($order['payment_method']) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Trạng thái:</strong>
                                <span class="payment-status status-<?= $order['payment_status'] ?>">
                                    <?= getPaymentStatusText($order['payment_status']) ?>
                                </span>
                            </div>
                            <?php if ($order['delivered_at']): ?>
                                <div class="info-item">
                                    <strong>Ngày giao:</strong>
                                    <span><?= date('d/m/Y H:i', strtotime($order['delivered_at'])) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($order['notes']): ?>
                                <div class="info-item">
                                    <strong>Ghi chú:</strong>
                                    <span><?= e($order['notes']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tổng tiền -->
                    <div class="summary-card">
                        <div class="summary-header">
                            <h6 class="mb-0">Tổng kết đơn hàng</h6>
                        </div>
                        <div class="summary-content">
                            <div class="summary-row">
                                <span>Tạm tính:</span>
                                <span><?= formatMoney($order['subtotal']) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Phí vận chuyển:</span>
                                <span><?= formatMoney($order['shipping_fee']) ?></span>
                            </div>
                            <?php if ($order['discount'] > 0): ?>
                                <div class="summary-row discount">
                                    <span>Giảm giá:</span>
                                    <span>-<?= formatMoney($order['discount']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="summary-total">
                                <span>Tổng cộng:</span>
                                <span><?= formatMoney($order['total']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút quay lại -->
            <div class="mt-4">
                <a href="index.php?page=orders" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>
                    Quay lại danh sách đơn hàng
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Order Detail Styles */
.container-fluid {
    max-width: 1200px;
}

.breadcrumb {
    font-size: 0.9rem;
}

.breadcrumb-item a {
    color: #ff6b35;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: #e55a2b;
}

/* Order Header */
.order-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.order-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
}

.order-date {
    opacity: 0.9;
    font-size: 0.95rem;
}

.status-badge {
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.status-pending { background: linear-gradient(135deg, #ffeaa7, #fdcb6e); color: #2d3436; }
.status-confirmed { background: linear-gradient(135deg, #74b9ff, #0984e3); color: white; }
.status-preparing { background: linear-gradient(135deg, #a29bfe, #6c5ce7); color: white; }
.status-shipping { background: linear-gradient(135deg, #fd79a8, #e84393); color: white; }
.status-delivered { background: linear-gradient(135deg, #00b894, #00a085); color: white; }
.status-cancelled { background: linear-gradient(135deg, #ff7675, #d63031); color: white; }

/* Products Card */
.products-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.card-header-custom {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: white;
    padding: 1.5rem;
    border: none;
}

.card-header-custom h5 {
    margin: 0;
    font-weight: 600;
}

.products-list {
    padding: 0;
}

.product-item {
    display: flex;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.3s ease;
}

.product-item:hover {
    background-color: #f8f9fa;
}

.product-item:last-child {
    border-bottom: none;
}

.product-image {
    flex-shrink: 0;
    margin-right: 1.5rem;
}

.product-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.product-img-placeholder {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ddd, #bbb);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    font-size: 1.5rem;
}

.product-details {
    flex-grow: 1;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3436;
    margin-bottom: 0.5rem;
}

.product-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.product-meta .price {
    color: #ff6b35;
    font-weight: 600;
    font-size: 1rem;
}

.product-meta .quantity {
    background: #e9ecef;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
    color: #495057;
}

.product-actions {
    text-align: right;
    flex-shrink: 0;
}

.subtotal {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2d3436;
    margin-bottom: 0.5rem;
}

.rating-section {
    text-align: center;
}

.rated-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
}

.rating-stars {
    font-size: 0.9rem;
}

.btn-rate {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-rate:hover {
    background: linear-gradient(135deg, #e55a2b, #e8851e);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

/* Info Cards */
.info-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.info-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
}

.info-header h6 {
    margin: 0;
    font-weight: 600;
    color: #2d3436;
}

.info-content {
    padding: 1.5rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-item strong {
    color: #636e72;
    font-size: 0.9rem;
    min-width: 100px;
}

.info-item span {
    color: #2d3436;
    font-weight: 500;
    text-align: right;
    flex: 1;
    margin-left: 1rem;
}

.payment-status {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.payment-status.status-pending { background: #fff3e0; color: #f57c00; }
.payment-status.status-paid { background: #e8f5e9; color: #388e3c; }
.payment-status.status-failed { background: #ffebee; color: #d32f2f; }
.payment-status.status-refunded { background: #e3f2fd; color: #1976d2; }

/* Summary Card */
.summary-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.summary-header {
    background: linear-gradient(135deg, #2d3436, #636e72);
    color: white;
    padding: 1rem 1.5rem;
}

.summary-header h6 {
    margin: 0;
    font-weight: 600;
}

.summary-content {
    padding: 1.5rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.summary-row.discount {
    color: #00b894;
    font-weight: 600;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 2px solid #f1f3f4;
    font-size: 1.2rem;
    font-weight: 700;
    color: #ff6b35;
}

/* Back Button */
.btn-back {
    background: linear-gradient(135deg, #636e72, #2d3436);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-back:hover {
    background: linear-gradient(135deg, #2d3436, #636e72);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .order-header-card {
        padding: 1.5rem;
    }
    
    .order-header-card .d-flex {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .product-item {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .product-image {
        margin-right: 0;
    }
    
    .product-actions {
        text-align: center;
    }
    
    .info-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .info-item span {
        margin-left: 0;
        text-align: center;
    }
}
</style>

<?php
// Helper functions for status display
function getOrderStatusText($status) {
    $statuses = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'shipping' => 'Đang giao hàng',
        'delivered' => 'Đã giao hàng',
        'cancelled' => 'Đã hủy'
    ];
    return $statuses[$status] ?? $status;
}

function getOrderStatusIcon($status) {
    $icons = [
        'pending' => 'fa-clock',
        'confirmed' => 'fa-check-circle',
        'preparing' => 'fa-utensils',
        'shipping' => 'fa-truck',
        'delivered' => 'fa-check-double',
        'cancelled' => 'fa-times-circle'
    ];
    return $icons[$status] ?? 'fa-question-circle';
}

function getPaymentMethodText($method) {
    $methods = [
        'cod' => 'Thanh toán khi nhận hàng',
        'bank_transfer' => 'Chuyển khoản ngân hàng',
        'momo' => 'Ví MoMo',
        'vnpay' => 'VNPay',
        'credit_card' => 'Thẻ tín dụng'
    ];
    return $methods[$method] ?? $method;
}

function getPaymentStatusText($status) {
    $statuses = [
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thanh toán thất bại',
        'refunded' => 'Đã hoàn tiền'
    ];
    return $statuses[$status] ?? $status;
}
?>

<?php require_once 'views/layouts/footer.php'; ?>