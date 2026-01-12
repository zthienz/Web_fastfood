<?php require_once 'views/layouts/header.php'; ?>

<div class="order-detail-container">
    <!-- Header đơn hàng -->
    <div class="order-header">
        <div class="order-id">
            <span class="order-label">Mã đơn hàng:</span>
            <span class="order-number"><?= e($order['order_number']) ?></span>
        </div>
        <div class="order-status">
            <span class="status-badge status-<?= $order['order_status'] ?>">
                <?= getOrderStatusText($order['order_status']) ?>
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="order-content">
        <!-- Left Column - Customer & Order Info -->
        <div class="order-info-section">
            <!-- Thông tin người nhận -->
            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-user"></i>
                    <h3>Thông tin người nhận</h3>
                </div>
                <div class="info-content">
                    <div class="info-row">
                        <span class="label">Họ tên:</span>
                        <span class="value"><?= e($order['customer_name']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Số điện thoại:</span>
                        <span class="value"><?= e($order['customer_phone']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Địa chỉ:</span>
                        <span class="value"><?= e($order['shipping_address']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-info-circle"></i>
                    <h3>Thông tin đơn hàng</h3>
                </div>
                <div class="info-content">
                    <div class="info-row">
                        <span class="label">Ngày đặt:</span>
                        <span class="value"><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Phương thức thanh toán:</span>
                        <span class="value"><?= getPaymentMethodText($order['payment_method']) ?></span>
                    </div>
                </div>
            </div>

            <?php if ($order['order_status'] === 'delivered'): ?>
                <!-- Review Actions -->
                <div class="review-actions-card">
                    <div class="review-header">
                        <i class="fas fa-star"></i>
                        <h3>Đánh giá đơn hàng</h3>
                    </div>
                    <div class="review-content">
                        <?php
                        // Kiểm tra xem có sản phẩm nào chưa được đánh giá không
                        $hasUnreviewedProducts = false;
                        $totalProducts = count($orderItems);
                        $reviewedProducts = 0;
                        
                        foreach ($orderItems as $item) {
                            if ($item['comment_id']) {
                                $reviewedProducts++;
                            } else {
                                $hasUnreviewedProducts = true;
                            }
                        }
                        ?>
                        
                        <div class="review-status">
                            <div class="review-progress">
                                <span class="progress-text">
                                    Đã đánh giá: <?= $reviewedProducts ?>/<?= $totalProducts ?> sản phẩm
                                </span>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $totalProducts > 0 ? ($reviewedProducts / $totalProducts * 100) : 0 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($hasUnreviewedProducts): ?>
                            <a href="index.php?page=comments&action=order_review&order_id=<?= $order['id'] ?>" class="btn-review">
                                <i class="fas fa-star"></i>
                                Đánh giá đơn hàng
                            </a>
                        <?php else: ?>
                            <div class="all-reviewed">
                                <i class="fas fa-check-circle"></i>
                                <span>Đã đánh giá tất cả sản phẩm</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Products -->
        <div class="products-section">
            <!-- Sản phẩm -->
            <div class="products-card">
                <div class="products-header">
                    <i class="fas fa-box"></i>
                    <h3>Sản phẩm</h3>
                </div>
                <div class="products-list">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="product-item">
                            <div class="product-image">
                                <?php 
                                $imageUrl = '';
                                if ($item['current_product_image']) {
                                    $imageUrl = getImageUrl($item['current_product_image']);
                                } elseif ($item['product_image']) {
                                    $imageUrl = getImageUrl($item['product_image']);
                                }
                                ?>
                                <?php if ($imageUrl): ?>
                                    <img src="<?= $imageUrl ?>" alt="<?= e($item['product_name']) ?>">
                                <?php else: ?>
                                    <div class="product-placeholder">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h4 class="product-name"><?= e($item['current_product_name'] ?: $item['product_name']) ?></h4>
                                <div class="product-details">
                                    <span class="product-sku">SKU: <?= e($item['product_id']) ?></span>
                                    <span class="product-quantity">Số lượng: x<?= e($item['quantity']) ?></span>
                                </div>
                                
                                <?php if ($item['comment_id'] && $order['order_status'] === 'delivered'): ?>
                                    <!-- Hiển thị đánh giá đã có -->
                                    <div class="product-review">
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $item['rating'] ? 'rated' : '' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="rating-text">(<?= $item['rating'] ?>/5)</span>
                                        </div>
                                        <div class="review-content">
                                            <p><?= e($item['comment_content']) ?></p>
                                            <small class="review-date">Đánh giá ngày <?= date('d/m/Y', strtotime($item['comment_date'])) ?></small>
                                        </div>
                                    </div>
                                <?php elseif ($order['order_status'] === 'delivered'): ?>
                                    <!-- Hiển thị trạng thái chưa đánh giá -->
                                    <div class="product-not-reviewed">
                                        <i class="fas fa-star-o"></i>
                                        <span>Chưa đánh giá</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="product-pricing">
                                <div class="unit-price"><?= formatMoney($item['price']) ?></div>
                                <div class="total-price">Tổng: <?= formatMoney($item['subtotal']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tổng tiền -->
            <div class="order-summary">
                <div class="summary-total">
                    <span class="total-label">Tổng tiền:</span>
                    <span class="total-amount"><?= formatMoney($order['total']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="back-section">
        <a href="index.php?page=orders" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Quay lại danh sách đơn hàng
        </a>
        
        <?php if ($order['order_status'] === 'pending'): ?>
            <button type="button" class="btn-cancel-detail" onclick="cancelOrder(<?= $order['id'] ?>, '<?= e($order['order_number']) ?>')">
                <i class="fas fa-times"></i>
                Hủy đơn hàng
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Form ẩn để hủy đơn hàng -->
<form id="cancelOrderForm" method="POST" action="index.php?page=orders&action=cancel" style="display: none;">
    <input type="hidden" name="order_id" id="cancelOrderId">
</form>

<script>
function cancelOrder(orderId, orderNumber) {
    if (confirm(`Bạn có chắc chắn muốn hủy đơn hàng ${orderNumber}?\n\nSau khi hủy, số lượng sản phẩm sẽ được hoàn lại vào kho và bạn không thể khôi phục đơn hàng này.`)) {
        document.getElementById('cancelOrderId').value = orderId;
        document.getElementById('cancelOrderForm').submit();
    }
}
</script>

<style>
/* Order Detail Page Styles - Inspired by the provided image */
.order-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
    min-height: 100vh;
}

/* Order Header */
.order-header {
    background: white;
    padding: 20px 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 4px solid #ff6b35;
}

.order-id {
    display: flex;
    align-items: center;
    gap: 10px;
}

.order-label {
    font-size: 16px;
    color: #666;
    font-weight: 500;
}

.order-number {
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.order-status {
    display: flex;
    align-items: center;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending { background: #fff3e0; color: #f57c00; }
.status-confirmed { background: #e3f2fd; color: #1976d2; }
.status-preparing { background: #f3e5f5; color: #7b1fa2; }
.status-shipping { background: #fce4ec; color: #c2185b; }
.status-delivered { background: #e8f5e9; color: #388e3c; }
.status-cancelled { background: #ffebee; color: #d32f2f; }

/* Main Content Layout */
.order-content {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 20px;
    margin-bottom: 30px;
}

/* Info Cards */
.info-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.info-header {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-header i {
    font-size: 18px;
}

.info-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.info-content {
    padding: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.info-row .label {
    font-weight: 600;
    color: #666;
    min-width: 120px;
}

.info-row .value {
    color: #333;
    text-align: right;
    flex: 1;
    margin-left: 15px;
}

/* Products Section */
.products-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.products-header {
    background: linear-gradient(135deg, #2196f3, #1976d2);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.products-header i {
    font-size: 18px;
}

.products-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.products-list {
    padding: 0;
}

.product-item {
    display: flex;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
    gap: 15px;
}

.product-item:last-child {
    border-bottom: none;
}

.product-image {
    flex-shrink: 0;
}

.product-image img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #f0f0f0;
}

.product-placeholder {
    width: 60px;
    height: 60px;
    background: #f0f0f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 20px;
}

.product-info {
    flex: 1;
}

.product-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0 0 8px 0;
}

.product-details {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.product-sku {
    font-size: 12px;
    color: #999;
    text-transform: uppercase;
}

.product-quantity {
    font-size: 14px;
    color: #666;
}

.product-pricing {
    text-align: right;
    flex-shrink: 0;
}

.unit-price {
    font-size: 14px;
    color: #ff6b35;
    font-weight: 600;
    margin-bottom: 4px;
}

.total-price {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

/* Order Summary */
.order-summary {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 20px;
    overflow: hidden;
}

.summary-total {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-label {
    font-size: 18px;
    font-weight: 600;
}

.total-amount {
    font-size: 24px;
    font-weight: bold;
}

/* Review Actions Card */
.review-actions-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
    border: 2px solid #ff6b35;
}

.review-header {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.review-header i {
    font-size: 18px;
}

.review-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.review-content {
    padding: 20px;
}

.review-status {
    margin-bottom: 20px;
}

.review-progress {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.progress-text {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.btn-review {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: white;
    padding: 12px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
    width: 100%;
    justify-content: center;
}

.btn-review:hover {
    background: linear-gradient(135deg, #ff5722, #e64a19);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.all-reviewed {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #28a745;
    font-weight: 600;
    padding: 12px;
    background: #d4edda;
    border-radius: 8px;
    border: 1px solid #c3e6cb;
}

.all-reviewed i {
    font-size: 18px;
}

/* Product Review Styles */
.product-review {
    margin-top: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #28a745;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 8px;
}

.review-rating .fas.fa-star {
    color: #e9ecef;
    font-size: 14px;
}

.review-rating .fas.fa-star.rated {
    color: #ffc107;
}

.rating-text {
    font-size: 12px;
    color: #666;
    margin-left: 4px;
}

.review-content p {
    margin: 0 0 4px 0;
    font-size: 14px;
    color: #333;
    line-height: 1.4;
}

.review-date {
    font-size: 12px;
    color: #999;
}

.product-not-reviewed {
    margin-top: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6c757d;
    font-size: 14px;
    font-style: italic;
}

.product-not-reviewed i {
    font-size: 14px;
}

.btn-back {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    margin-right: 15px;
}

.btn-back:hover {
    background: linear-gradient(135deg, #5a6268, #495057);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

.btn-cancel-detail {
    background: linear-gradient(135deg, #f44336, #d32f2f);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3);
    font-size: 14px;
}

.btn-cancel-detail:hover {
    background: linear-gradient(135deg, #d32f2f, #c62828);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(244, 67, 54, 0.4);
}

/* Back Section */
.back-section {
    text-align: center;
    margin-top: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .order-detail-container {
        padding: 15px;
    }
    
    .order-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
        padding: 20px;
    }
    
    .order-content {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .product-item {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .product-pricing {
        text-align: center;
    }
    
    .info-row {
        flex-direction: column;
        text-align: center;
        gap: 5px;
    }
    
    .info-row .value {
        margin-left: 0;
        text-align: center;
    }
    
    .summary-total {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .order-header {
        padding: 15px;
    }
    
    .info-header, .products-header {
        padding: 12px 15px;
    }
    
    .info-content, .product-item {
        padding: 15px;
    }
    
    .product-image img, .product-placeholder {
        width: 50px;
        height: 50px;
    }
    
    .total-amount {
        font-size: 20px;
    }
    
    .back-section {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-back, .btn-cancel-detail {
        width: 100%;
        justify-content: center;
        margin-right: 0;
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