<?php 
$pageTitle = 'Thanh toán - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="checkout-page">
    <!-- Progress Steps -->
    <div class="checkout-progress">
        <div class="progress-step completed">
            <div class="step-icon"><i class="fas fa-shopping-cart"></i></div>
            <span>Giỏ hàng</span>
        </div>
        <div class="progress-line completed"></div>
        <div class="progress-step active">
            <div class="step-icon"><i class="fas fa-credit-card"></i></div>
            <span>Thanh toán</span>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
            <div class="step-icon"><i class="fas fa-check-circle"></i></div>
            <span>Hoàn tất</span>
        </div>
    </div>

    <div class="checkout-container">
        <form method="POST" action="index.php?page=cart&action=placeOrder" id="checkoutForm">
            <div class="checkout-layout">
                <!-- Left: Form thông tin -->
                <div class="checkout-main">
                    <!-- Thông tin giao hàng -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-truck"></i></div>
                            <h2>Thông tin giao hàng</h2>
                        </div>
                        <div class="section-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Họ và tên <span class="required">*</span></label>
                                    <input type="text" name="customer_name" class="form-input" 
                                           value="<?= e($user['full_name'] ?? '') ?>" required
                                           placeholder="Nhập họ và tên người nhận">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-phone"></i> Số điện thoại <span class="required">*</span></label>
                                    <input type="tel" name="customer_phone" class="form-input" 
                                           value="<?= e($user['phone'] ?? '') ?>" required
                                           placeholder="Nhập số điện thoại">
                                </div>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="customer_email" class="form-input" 
                                       value="<?= e($user['email'] ?? '') ?>"
                                       placeholder="Nhập email (không bắt buộc)">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng <span class="required">*</span></label>
                                <textarea name="shipping_address" class="form-input form-textarea" required
                                          placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố)"><?= e($user['address'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-sticky-note"></i> Ghi chú</label>
                                <textarea name="note" class="form-input form-textarea" 
                                          placeholder="Ghi chú cho đơn hàng (ví dụ: giao giờ hành chính, gọi trước khi giao...)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-wallet"></i></div>
                            <h2>Phương thức thanh toán</h2>
                        </div>
                        <div class="section-body">
                            <div class="payment-methods">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cod" checked>
                                    <div class="payment-box">
                                        <div class="payment-icon cod">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="payment-info">
                                            <span class="payment-name">Thanh toán khi nhận hàng (COD)</span>
                                            <span class="payment-desc">Thanh toán bằng tiền mặt khi nhận hàng</span>
                                        </div>
                                        <div class="payment-check"><i class="fas fa-check-circle"></i></div>
                                    </div>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="bank_transfer">
                                    <div class="payment-box">
                                        <div class="payment-icon bank">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div class="payment-info">
                                            <span class="payment-name">Chuyển khoản ngân hàng</span>
                                            <span class="payment-desc">Chuyển khoản trước khi giao hàng</span>
                                        </div>
                                        <div class="payment-check"><i class="fas fa-check-circle"></i></div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Bank info (hidden by default) -->
                            <div class="bank-info" id="bankInfo" style="display: none;">
                                <div class="bank-card">
                                    <h4><i class="fas fa-info-circle"></i> Thông tin chuyển khoản</h4>
                                    <div class="bank-details">
                                        <p><strong>Ngân hàng:</strong> Vietcombank</p>
                                        <p><strong>Số tài khoản:</strong> 1234567890</p>
                                        <p><strong>Chủ tài khoản:</strong> FASTFOOD COMPANY</p>
                                        <p><strong>Nội dung CK:</strong> <span class="highlight">ĐH + Số điện thoại</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Summary -->
                <div class="checkout-sidebar">
                    <div class="order-summary">
                        <div class="summary-header">
                            <h3><i class="fas fa-receipt"></i> Đơn hàng của bạn</h3>
                            <span class="item-count"><?= count($checkoutItems) ?> món</span>
                        </div>
                        
                        <div class="summary-items">
                            <?php foreach ($checkoutItems as $item): ?>
                            <div class="summary-item">
                                <div class="item-image">
                                    <?php $image = $item['product']['primary_image'] ?? $item['product']['image'] ?? ''; ?>
                                    <img src="<?= getImageUrl($image) ?>" alt="<?= e($item['product']['name']) ?>">
                                    <span class="item-qty"><?= $item['quantity'] ?></span>
                                </div>
                                <div class="item-details">
                                    <span class="item-name"><?= e($item['product']['name']) ?></span>
                                    <span class="item-price"><?= formatMoney($item['price']) ?> x <?= $item['quantity'] ?></span>
                                </div>
                                <div class="item-total">
                                    <?= formatMoney($item['subtotal']) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-totals">
                            <div class="total-row">
                                <span>Tạm tính</span>
                                <span><?= formatMoney($subtotal) ?></span>
                            </div>
                            <div class="total-row shipping">
                                <span>Phí giao hàng</span>
                                <span class="<?= $shippingFee == 0 ? 'free' : '' ?>">
                                    <?= $shippingFee == 0 ? 'Miễn phí' : formatMoney($shippingFee) ?>
                                </span>
                            </div>
                            <?php if ($subtotal < 200000): ?>
                            <div class="shipping-note">
                                <i class="fas fa-info-circle"></i>
                                Mua thêm <?= formatMoney(200000 - $subtotal) ?> để được miễn phí ship
                            </div>
                            <?php endif; ?>
                            <div class="total-row final">
                                <span>Tổng cộng</span>
                                <span class="final-amount"><?= formatMoney($total) ?></span>
                            </div>
                        </div>

                        <button type="submit" class="btn-place-order">
                            <i class="fas fa-lock"></i>
                            Đặt hàng
                        </button>
                        
                        <a href="index.php?page=cart" class="btn-back-cart">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại giỏ hàng
                        </a>

                        <div class="secure-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Thanh toán an toàn & bảo mật</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.checkout-page {
    background: #f5f7fa;
    min-height: 100vh;
    padding: 30px 20px 60px;
}

/* Progress Steps */
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
    transition: all 0.3s ease;
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
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: #fff;
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.4);
}

.progress-step.active span {
    color: #ff5722;
    font-weight: 600;
}

.progress-line {
    flex: 1;
    height: 3px;
    background: #e0e0e0;
    margin: 0 15px;
    margin-bottom: 25px;
}

.progress-line.completed {
    background: linear-gradient(90deg, #4caf50, #ff5722);
}

/* Container */
.checkout-container {
    max-width: 1200px;
    margin: 0 auto;
}

.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 30px;
    align-items: start;
}

/* Sections */
.checkout-section {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 25px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px 25px;
    background: linear-gradient(135deg, #667eea15, #764ba215);
    border-bottom: 1px solid #eee;
}

.section-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.section-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.section-body {
    padding: 25px;
}

/* Form */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    font-size: 14px;
}

.form-group label i {
    color: #ff5722;
    font-size: 14px;
}

.required {
    color: #f44336;
}

.form-input {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #eee;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-input:focus {
    outline: none;
    border-color: #ff5722;
    box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.1);
}

.form-textarea {
    min-height: 100px;
    resize: vertical;
}

/* Payment Methods */
.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-option {
    cursor: pointer;
}

.payment-option input {
    display: none;
}

.payment-box {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 18px 20px;
    border: 2px solid #eee;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.payment-option input:checked + .payment-box {
    border-color: #ff5722;
    background: rgba(255, 87, 34, 0.05);
}

.payment-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.payment-icon.cod { background: #e8f5e9; color: #4caf50; }
.payment-icon.bank { background: #e3f2fd; color: #1976d2; }
.payment-icon.momo { background: #fce4ec; color: #e91e63; }

.payment-info {
    flex: 1;
}

.payment-name {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
}

.payment-desc {
    font-size: 13px;
    color: #888;
}

.payment-check {
    color: #ddd;
    font-size: 24px;
    transition: all 0.3s ease;
}

.payment-option input:checked + .payment-box .payment-check {
    color: #ff5722;
}

/* Bank Info */
.bank-info {
    margin-top: 20px;
}

.bank-card {
    background: #fff8e1;
    border: 1px solid #ffecb3;
    border-radius: 12px;
    padding: 20px;
}

.bank-card h4 {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f57c00;
    margin: 0 0 15px;
    font-size: 15px;
}

.bank-details p {
    margin: 8px 0;
    font-size: 14px;
    color: #555;
}

.bank-details .highlight {
    background: #ff5722;
    color: #fff;
    padding: 3px 10px;
    border-radius: 5px;
    font-weight: 600;
}

/* Order Summary */
.order-summary {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    position: sticky;
    top: 20px;
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: #fff;
}

.summary-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    font-size: 18px;
}

.item-count {
    background: rgba(255,255,255,0.2);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 13px;
}

.summary-items {
    max-height: 300px;
    overflow-y: auto;
    padding: 20px;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.summary-item:last-child {
    border-bottom: none;
}

.item-image {
    position: relative;
    width: 60px;
    height: 60px;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}

.item-qty {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 22px;
    height: 22px;
    background: #ff5722;
    color: #fff;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.item-details {
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

.item-total {
    font-weight: 700;
    color: #ff5722;
    font-size: 14px;
}

/* Totals */
.summary-totals {
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #eee;
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 14px;
    color: #666;
}

.total-row .free {
    color: #4caf50;
    font-weight: 600;
}

.shipping-note {
    background: #fff3e0;
    color: #f57c00;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 12px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.total-row.final {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px dashed #ddd;
    font-size: 16px;
    font-weight: 700;
    color: #333;
}

.final-amount {
    color: #ff5722;
    font-size: 22px;
}

/* Buttons */
.btn-place-order {
    width: calc(100% - 40px);
    margin: 20px;
    padding: 16px;
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(255, 107, 53, 0.3);
}

.btn-place-order:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
}

.btn-back-cart {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: calc(100% - 40px);
    margin: 0 20px 20px;
    padding: 14px;
    background: #f5f5f5;
    color: #666;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back-cart:hover {
    background: #eee;
}

.secure-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 15px;
    background: #e8f5e9;
    color: #4caf50;
    font-size: 13px;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 1024px) {
    .checkout-layout {
        grid-template-columns: 1fr;
    }
    
    .order-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .checkout-page {
        padding: 20px 15px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .checkout-progress {
        transform: scale(0.85);
    }
    
    .section-body {
        padding: 20px 15px;
    }
}
</style>

<script>
// Toggle bank info
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const bankInfo = document.getElementById('bankInfo');
        if (this.value === 'bank_transfer') {
            bankInfo.style.display = 'block';
        } else {
            bankInfo.style.display = 'none';
        }
    });
});

// Form validation
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const name = document.querySelector('input[name="customer_name"]').value.trim();
    const phone = document.querySelector('input[name="customer_phone"]').value.trim();
    const address = document.querySelector('textarea[name="shipping_address"]').value.trim();
    
    if (!name || !phone || !address) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin giao hàng!');
        return false;
    }
    
    // Validate phone
    if (!/^[0-9]{10,11}$/.test(phone)) {
        e.preventDefault();
        alert('Số điện thoại không hợp lệ!');
        return false;
    }
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>
