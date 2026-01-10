<?php 
$pageTitle = 'Cập nhật địa chỉ - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="address-required-page">
    <div class="container">
        <div class="address-form-container">
            <div class="form-header">
                <div class="header-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h2>Cập nhật thông tin giao hàng</h2>
                <p>Để hoàn tất đặt hàng, vui lòng cập nhật thông tin giao hàng của bạn</p>
            </div>
            
            <form method="POST" action="index.php?page=cart&action=updateAddress" class="address-form">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Họ và tên <span class="required">*</span></label>
                    <input type="text" name="full_name" class="form-input" 
                           value="<?= e($user['full_name'] ?? '') ?>" required
                           placeholder="Nhập họ và tên đầy đủ">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Số điện thoại <span class="required">*</span></label>
                    <input type="tel" name="phone" class="form-input" 
                           value="<?= e($user['phone'] ?? '') ?>" required
                           placeholder="Nhập số điện thoại">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng <span class="required">*</span></label>
                    <textarea name="address" class="form-input form-textarea" required
                              placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố)"><?= e($user['address'] ?? '') ?></textarea>
                    <div class="form-help">
                        <i class="fas fa-info-circle"></i>
                        Địa chỉ này sẽ được lưu lại cho các đơn hàng tiếp theo. Bạn có thể thay đổi trong trang thông tin cá nhân.
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Lưu và tiếp tục đặt hàng
                    </button>
                    <a href="index.php?page=cart" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại giỏ hàng
                    </a>
                </div>
            </form>
            
            <div class="order-preview">
                <h3><i class="fas fa-shopping-bag"></i> Đơn hàng của bạn</h3>
                <div class="preview-items">
                    <?php 
                    $previewTotal = 0;
                    $ids = array_map('intval', $selectedItems);
                    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                    $stmt = $this->db->prepare("
                        SELECT p.*, pi.image_url as primary_image
                        FROM products p
                        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
                        WHERE p.id IN ($placeholders)
                    ");
                    $stmt->execute($ids);
                    $previewProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($previewProducts as $product): 
                        $quantity = $cart[$product['id']] ?? 0;
                        if ($quantity > 0):
                            $price = $product['sale_price'] ?? $product['price'];
                            $itemSubtotal = $price * $quantity;
                            $previewTotal += $itemSubtotal;
                    ?>
                    <div class="preview-item">
                        <div class="item-image">
                            <?php $image = $product['primary_image'] ?? $product['image'] ?? ''; ?>
                            <img src="<?= getImageUrl($image) ?>" alt="<?= e($product['name']) ?>">
                            <span class="item-qty"><?= $quantity ?></span>
                        </div>
                        <div class="item-info">
                            <span class="item-name"><?= e($product['name']) ?></span>
                            <span class="item-price"><?= formatMoney($itemSubtotal) ?></span>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <div class="preview-total">
                    <span>Tạm tính: <strong><?= formatMoney($previewTotal) ?></strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.address-required-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 40px 20px;
}

.address-form-container {
    max-width: 800px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 30px;
    align-items: start;
}

.form-header {
    grid-column: 1 / -1;
    text-align: center;
    color: white;
    margin-bottom: 30px;
}

.header-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin: 0 auto 20px;
    backdrop-filter: blur(10px);
}

.form-header h2 {
    font-size: 28px;
    margin: 0 0 10px;
    font-weight: 700;
}

.form-header p {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
}

.address-form {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
    font-size: 15px;
}

.form-group label i {
    color: #667eea;
    font-size: 16px;
}

.required {
    color: #f44336;
}

.form-input {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    font-family: inherit;
    background: #fafafa;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    background: white;
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-help {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-top: 8px;
    font-size: 13px;
    color: #666;
    background: #f0f4ff;
    padding: 12px 15px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.form-help i {
    color: #667eea;
    margin-top: 2px;
    flex-shrink: 0;
}

.form-actions {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 16px 24px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 15px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #f5f5f5;
    color: #666;
    border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
    background: #eee;
    border-color: #ccc;
}

.order-preview {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.order-preview h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 20px;
    color: #333;
    font-size: 18px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.order-preview h3 i {
    color: #667eea;
}

.preview-items {
    margin-bottom: 20px;
}

.preview-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.preview-item:last-child {
    border-bottom: none;
}

.item-image {
    position: relative;
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.item-qty {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 20px;
    height: 20px;
    background: #667eea;
    color: white;
    border-radius: 50%;
    font-size: 10px;
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
    font-size: 13px;
    color: #667eea;
    font-weight: 600;
}

.preview-total {
    padding-top: 15px;
    border-top: 2px dashed #e0e0e0;
    text-align: right;
    font-size: 16px;
    color: #333;
}

.preview-total strong {
    color: #667eea;
    font-size: 18px;
}

@media (max-width: 1024px) {
    .address-form-container {
        grid-template-columns: 1fr;
        max-width: 600px;
    }
    
    .order-preview {
        position: static;
    }
}

@media (max-width: 768px) {
    .address-required-page {
        padding: 20px 15px;
    }
    
    .address-form {
        padding: 25px 20px;
    }
    
    .form-header h2 {
        font-size: 24px;
    }
    
    .form-actions {
        gap: 12px;
    }
}
</style>

<script>
// Form validation
document.querySelector('.address-form').addEventListener('submit', function(e) {
    const name = document.querySelector('input[name="full_name"]').value.trim();
    const phone = document.querySelector('input[name="phone"]').value.trim();
    const address = document.querySelector('textarea[name="address"]').value.trim();
    
    if (!name || !phone || !address) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin!');
        return false;
    }
    
    // Validate phone
    if (!/^[0-9]{10,11}$/.test(phone)) {
        e.preventDefault();
        alert('Số điện thoại không hợp lệ!');
        return false;
    }
    
    if (address.length < 10) {
        e.preventDefault();
        alert('Vui lòng nhập địa chỉ chi tiết hơn!');
        return false;
    }
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>