<?php require_once 'views/layouts/header.php'; ?>

<div class="single-review-container">
    <!-- Header -->
    <div class="review-header">
        <div class="header-content">
            <div class="back-btn">
                <a href="index.php?page=orders&action=detail&id=<?= e($orderItem['id']) ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
            </div>
            <div class="header-title">
                <i class="fas fa-star"></i>
                <h1>Đánh giá món ăn</h1>
            </div>
        </div>
    </div>

    <!-- Order Info -->
    <div class="order-info-banner">
        <div class="order-info-content">
            <div class="order-info-item">
                <i class="fas fa-info-circle"></i>
                <span class="label">Mã đơn hàng:</span>
                <span class="value"><?= e($orderItem['order_number']) ?></span>
            </div>
        </div>
    </div>

    <!-- Product Review Card -->
    <div class="single-product-review">
        <div class="product-info">
            <div class="product-image">
                <?php 
                $imageUrl = '';
                if ($orderItem['current_product_image']) {
                    $imageUrl = getImageUrl($orderItem['current_product_image']);
                } elseif ($orderItem['product_image']) {
                    $imageUrl = getImageUrl($orderItem['product_image']);
                }
                ?>
                <?php if ($imageUrl): ?>
                    <img src="<?= $imageUrl ?>" alt="<?= e($orderItem['product_name']) ?>">
                <?php else: ?>
                    <div class="product-placeholder">
                        <i class="fas fa-utensils"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="product-details">
                <h3 class="product-name"><?= e($orderItem['current_product_name'] ?: $orderItem['product_name']) ?></h3>
                <div class="product-meta">
                    <span class="product-sku">SKU: <?= e($orderItem['product_id']) ?></span>
                    <span class="product-quantity">Số lượng: <?= e($orderItem['quantity']) ?></span>
                </div>
            </div>
        </div>

        <!-- Comment Form -->
        <form method="POST" action="index.php?page=comments&action=submit" class="rating-form">
            <input type="hidden" name="order_id" value="<?= e($orderItem['id']) ?>">
            <input type="hidden" name="product_id" value="<?= e($orderItem['product_id']) ?>">
            <input type="hidden" name="rating" value="5">
            
            <div class="comment-section">
                <label class="comment-label">Nội dung đánh giá:</label>
                <textarea 
                    name="content" 
                    class="comment-textarea"
                    placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."
                    rows="6"
                    required
                ></textarea>
            </div>

            <div class="submit-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Gửi đánh giá
                </button>
                <button type="button" class="btn-cancel" onclick="window.history.back()">
                    <i class="fas fa-times"></i>
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Single Review Container */
.single-review-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
    min-height: 100vh;
}

/* Header */
.review-header {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.header-content {
    padding: 20px 30px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.btn-back {
    background: #6c757d;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.header-title {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #333;
}

.header-title i {
    font-size: 24px;
    color: #ff6b35;
}

.header-title h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

/* Order Info Banner */
.order-info-banner {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.order-info-content {
    padding: 15px 30px;
}

.order-info-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.order-info-item i {
    font-size: 16px;
}

.order-info-item .label {
    font-weight: 500;
}

.order-info-item .value {
    font-weight: 600;
    font-size: 16px;
}

/* Single Product Review */
.single-product-review {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.product-info {
    padding: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.product-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #f0f0f0;
}

.product-placeholder {
    width: 80px;
    height: 80px;
    background: #f0f0f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 24px;
}

.product-details {
    flex: 1;
}

.product-name {
    margin: 0 0 12px 0;
    font-size: 22px;
    font-weight: 600;
    color: #333;
}

.product-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.product-sku {
    font-size: 14px;
    color: #999;
    text-transform: uppercase;
}

.product-quantity {
    font-size: 16px;
    color: #666;
}

/* Rating Form */
.rating-form {
    padding: 30px;
}

.comment-section {
    margin-bottom: 30px;
}

.comment-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    font-size: 18px;
}

.comment-textarea {
    width: 100%;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    font-size: 16px;
    line-height: 1.6;
    resize: vertical;
    transition: all 0.3s ease;
    font-family: inherit;
    min-height: 120px;
}

.comment-textarea:focus {
    outline: none;
    border-color: #ff6b35;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
}

.comment-textarea::placeholder {
    color: #adb5bd;
}

/* Submit Actions */
.submit-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    padding-top: 20px;
    border-top: 1px solid #f0f0f0;
}

.btn-submit, .btn-cancel {
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 16px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-width: 140px;
    justify-content: center;
}

.btn-submit {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.btn-submit:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
}

.btn-cancel {
    background: #6c757d;
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-cancel:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

/* Responsive Design */
@media (max-width: 768px) {
    .single-review-container {
        padding: 15px;
    }
    
    .header-content {
        padding: 15px 20px;
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .order-info-content {
        padding: 15px 20px;
    }
    
    .product-info {
        padding: 20px;
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .product-meta {
        align-items: center;
    }
    
    .rating-form {
        padding: 20px;
    }
    
    .submit-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-submit, .btn-cancel {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>