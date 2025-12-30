<?php 
$pageTitle = 'Đặt hàng thành công - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="success-page">
    <!-- Progress Steps -->
    <div class="checkout-progress">
        <div class="progress-step completed">
            <div class="step-icon"><i class="fas fa-shopping-cart"></i></div>
            <span>Giỏ hàng</span>
        </div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed">
            <div class="step-icon"><i class="fas fa-credit-card"></i></div>
            <span>Thanh toán</span>
        </div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed active">
            <div class="step-icon"><i class="fas fa-check-circle"></i></div>
            <span>Hoàn tất</span>
        </div>
    </div>

    <div class="success-container">
        <!-- Success Animation -->
        <div class="success-animation">
            <div class="checkmark-circle">
                <div class="checkmark"></div>
            </div>
        </div>

        <h1 class="success-title">Đặt hàng thành công!</h1>
        <p class="success-message">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ xác nhận đơn hàng sớm nhất.</p>

        <!-- Order Info Card -->
        <div class="order-info-card">
            <div class="order-number">
                <span class="label">Mã đơn hàng</span>
                <span class="value"><?= e($order['order_number']) ?></span>
            </div>
            <div class="order-details-grid">
                <div class="detail-item">
                    <i class="fas fa-user"></i>
                    <div>
                        <span class="detail-label">Người nhận</span>
                        <span class="detail-value"><?= e($order['customer_name']) ?></span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <span class="detail-label">Số điện thoại</span>
                        <span class="detail-value"><?= e($order['customer_phone']) ?></span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <span class="detail-label">Địa chỉ giao hàng</span>
                        <span class="detail-value"><?= e($order['shipping_address']) ?></span>
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-wallet"></i>
                    <div>
                        <span class="detail-label">Thanh toán</span>
                        <span class="detail-value">
                            <?php
                            $paymentMethods = [
                                'cod' => 'Thanh toán khi nhận hàng',
                                'bank_transfer' => 'Chuyển khoản ngân hàng',
                                'momo' => 'Ví MoMo'
                            ];
                            echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="order-items-card">
            <h3><i class="fas fa-shopping-bag"></i> Chi tiết đơn hàng</h3>
            <div class="items-list">
                <?php foreach ($orderItems as $item): ?>
                <div class="order-item">
                    <div class="item-img">
                        <img src="<?= getImageUrl($item['product_image']) ?>" alt="<?= e($item['product_name']) ?>">
                        <span class="qty-badge"><?= $item['quantity'] ?></span>
                    </div>
                    <div class="item-info">
                        <span class="item-name"><?= e($item['product_name']) ?></span>
                        <span class="item-price"><?= formatMoney($item['price']) ?> x <?= $item['quantity'] ?></span>
                    </div>
                    <div class="item-subtotal"><?= formatMoney($item['subtotal']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="order-totals">
                <div class="total-line">
                    <span>Tạm tính</span>
                    <span><?= formatMoney($order['subtotal']) ?></span>
                </div>
                <div class="total-line">
                    <span>Phí giao hàng</span>
                    <span class="<?= $order['shipping_fee'] == 0 ? 'free' : '' ?>">
                        <?= $order['shipping_fee'] == 0 ? 'Miễn phí' : formatMoney($order['shipping_fee']) ?>
                    </span>
                </div>
                <div class="total-line final">
                    <span>Tổng cộng</span>
                    <span><?= formatMoney($order['total']) ?></span>
                </div>
            </div>
        </div>

        <!-- Bank Transfer Info -->
        <?php if ($order['payment_method'] === 'bank_transfer'): ?>
        <div class="bank-transfer-info">
            <h3><i class="fas fa-university"></i> Thông tin chuyển khoản</h3>
            <div class="bank-details">
                <p><strong>Ngân hàng:</strong> Vietcombank</p>
                <p><strong>Số tài khoản:</strong> 1234567890</p>
                <p><strong>Chủ tài khoản:</strong> FASTFOOD COMPANY</p>
                <p><strong>Số tiền:</strong> <span class="amount"><?= formatMoney($order['total']) ?></span></p>
                <p><strong>Nội dung CK:</strong> <span class="highlight"><?= $order['order_number'] ?></span></p>
            </div>
            <div class="bank-note">
                <i class="fas fa-exclamation-circle"></i>
                Vui lòng chuyển khoản trong vòng 24 giờ để đơn hàng được xử lý.
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="success-actions">
            <a href="index.php?page=orders" class="btn-view-orders">
                <i class="fas fa-list-alt"></i>
                Xem đơn hàng của tôi
            </a>
            <a href="index.php?page=menu" class="btn-continue-shopping">
                <i class="fas fa-utensils"></i>
                Tiếp tục mua hàng
            </a>
        </div>
    </div>
</div>

<style>
.success-page {
    background: linear-gradient(135deg, #667eea15, #764ba215);
    min-height: 100vh;
    padding: 30px 20px 60px;
}

/* Progress */
.checkout-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 500px;
    margin: 0 auto 40px;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.progress-step span {
    font-size: 13px;
    color: #999;
    font-weight: 500;
}

.progress-step.completed .step-icon {
    background: linear-gradient(135deg, #4caf50, #2e7d32);
    color: #fff;
}

.progress-step.completed span {
    color: #4caf50;
}

.progress-step.active .step-icon {
    box-shadow: 0 5px 20px rgba(76, 175, 80, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.progress-line {
    flex: 1;
    height: 3px;
    background: #e0e0e0;
    margin: 0 15px;
    margin-bottom: 25px;
}

.progress-line.completed {
    background: linear-gradient(90deg, #4caf50, #4caf50);
}

/* Container */
.success-container {
    max-width: 700px;
    margin: 0 auto;
    text-align: center;
}

/* Success Animation */
.success-animation {
    margin-bottom: 30px;
}

.checkmark-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4caf50, #2e7d32);
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s ease-out;
    box-shadow: 0 10px 40px rgba(76, 175, 80, 0.3);
}

@keyframes scaleIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.checkmark {
    width: 35px;
    height: 55px;
    border: solid #fff;
    border-width: 0 6px 6px 0;
    transform: rotate(45deg);
    margin-top: -10px;
    animation: checkmark 0.4s ease-out 0.3s forwards;
    opacity: 0;
}

@keyframes checkmark {
    0% { opacity: 0; height: 0; }
    100% { opacity: 1; height: 55px; }
}

.success-title {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin: 0 0 15px;
}

.success-message {
    font-size: 16px;
    color: #666;
    margin: 0 0 30px;
}

/* Order Info Card */
.order-info-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    text-align: left;
}

.order-number {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: #fff;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-number .label {
    font-size: 14px;
    opacity: 0.9;
}

.order-number .value {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 1px;
}

.order-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    padding: 25px;
}

.detail-item {
    display: flex;
    gap: 15px;
}

.detail-item i {
    width: 40px;
    height: 40px;
    background: #f5f5f5;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ff5722;
    flex-shrink: 0;
}

.detail-label {
    display: block;
    font-size: 12px;
    color: #888;
    margin-bottom: 4px;
}

.detail-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

/* Order Items Card */
.order-items-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    text-align: left;
}

.order-items-card h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px 25px;
    margin: 0;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
    font-size: 16px;
    color: #333;
}

.order-items-card h3 i {
    color: #ff5722;
}

.items-list {
    padding: 15px 25px;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-item:last-child {
    border-bottom: none;
}

.item-img {
    position: relative;
    width: 55px;
    height: 55px;
}

.item-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}

.qty-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 20px;
    height: 20px;
    background: #ff5722;
    color: #fff;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.item-info {
    flex: 1;
}

.item-name {
    display: block;
    font-weight: 600;
    color: #333;
    font-size: 14px;
    margin-bottom: 4px;
}

.item-price {
    font-size: 12px;
    color: #888;
}

.item-subtotal {
    font-weight: 700;
    color: #ff5722;
}

.order-totals {
    padding: 20px 25px;
    background: #f8f9fa;
    border-top: 1px solid #eee;
}

.total-line {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
    color: #666;
}

.total-line .free {
    color: #4caf50;
    font-weight: 600;
}

.total-line.final {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px dashed #ddd;
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

.total-line.final span:last-child {
    color: #ff5722;
}

/* Bank Transfer Info */
.bank-transfer-info {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    text-align: left;
}

.bank-transfer-info h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px 25px;
    margin: 0;
    background: #e3f2fd;
    border-bottom: 1px solid #bbdefb;
    font-size: 16px;
    color: #1976d2;
}

.bank-details {
    padding: 20px 25px;
}

.bank-details p {
    margin: 10px 0;
    font-size: 14px;
    color: #555;
}

.bank-details .amount {
    color: #ff5722;
    font-weight: 700;
    font-size: 18px;
}

.bank-details .highlight {
    background: #ff5722;
    color: #fff;
    padding: 4px 12px;
    border-radius: 5px;
    font-weight: 700;
}

.bank-note {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 25px;
    background: #fff3e0;
    color: #f57c00;
    font-size: 13px;
}

/* Actions */
.success-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-view-orders, .btn-continue-shopping {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-view-orders {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: #fff;
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.3);
}

.btn-view-orders:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
}

.btn-continue-shopping {
    background: #fff;
    color: #333;
    border: 2px solid #eee;
}

.btn-continue-shopping:hover {
    border-color: #ff5722;
    color: #ff5722;
}

/* Responsive */
@media (max-width: 768px) {
    .order-details-grid {
        grid-template-columns: 1fr;
    }
    
    .success-title {
        font-size: 26px;
    }
    
    .success-actions {
        flex-direction: column;
    }
    
    .btn-view-orders, .btn-continue-shopping {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>
