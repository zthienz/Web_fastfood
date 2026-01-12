<?php require_once 'views/layouts/header.php'; ?>

<div class="review-container">
    <!-- Header -->
    <div class="review-header">
        <div class="header-content">
            <div class="back-btn">
                <a href="index.php?page=orders" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại
                </a>
            </div>
            <div class="header-title">
                <i class="fas fa-star"></i>
                <h1>Đánh giá đơn hàng</h1>
            </div>
        </div>
    </div>

    <!-- Order Info -->
    <div class="order-info-banner">
        <div class="order-info-content">
            <div class="order-info-item">
                <i class="fas fa-info-circle"></i>
                <span class="label">Mã đơn hàng:</span>
                <span class="value"><?= e($order['order_number']) ?></span>
            </div>
        </div>
    </div>

    <!-- Products List -->
    <div class="products-review-list">
        <?php foreach ($orderItems as $index => $item): ?>
            <div class="product-review-card <?= $item['already_reviewed'] ? 'already-reviewed' : '' ?>" data-order-item-id="<?= e($item['order_item_id']) ?>">
                <div class="product-info">
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
                    <div class="product-details">
                        <h3 class="product-name"><?= e($item['product_name']) ?></h3>
                        <div class="product-meta">
                            <span class="product-sku">SKU: <?= e($item['product_id']) ?></span>
                            <span class="product-quantity">Số lượng: <?= e($item['total_quantity']) ?></span>
                        </div>
                        <?php if ($item['already_reviewed']): ?>
                            <div class="reviewed-badge">
                                <i class="fas fa-check-circle"></i>
                                Đã đánh giá
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!$item['already_reviewed']): ?>
                    <!-- Comment Form -->
                    <form class="rating-form" data-product-id="<?= e($item['product_id']) ?>" data-order-item-id="<?= e($item['order_item_id']) ?>">
                        <div class="comment-section">
                            <label class="comment-label">Nội dung đánh giá:</label>
                            <textarea 
                                name="content_<?= $item['order_item_id'] ?>" 
                                class="comment-textarea"
                                placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."
                                rows="4"
                            ></textarea>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Already Reviewed Message -->
                    <div class="already-reviewed-message">
                        <i class="fas fa-info-circle"></i>
                        <p>Bạn đã đánh giá sản phẩm này rồi. Cảm ơn bạn đã chia sẻ!</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Submit Actions -->
    <div class="submit-actions">
        <button type="button" class="btn-submit" onclick="submitAllReviews()">
            <i class="fas fa-paper-plane"></i>
            Gửi đánh giá
        </button>
        <button type="button" class="btn-cancel" onclick="window.history.back()">
            <i class="fas fa-times"></i>
            Hủy
        </button>
    </div>
</div>

<style>
/* Review Container */
.review-container {
    max-width: 1000px;
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

/* Products Review List */
.products-review-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 30px;
}

.product-review-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.product-review-card.already-reviewed {
    background: #f8f9fa;
    border: 2px solid #28a745;
}

.product-info {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.already-reviewed .product-info {
    border-bottom: 1px solid #e9ecef;
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

.product-details {
    flex: 1;
}

.product-name {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.product-meta {
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

.reviewed-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #28a745;
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 8px;
}

.already-reviewed-message {
    padding: 20px;
    text-align: center;
    color: #28a745;
    background: #d4edda;
    border-top: 1px solid #c3e6cb;
}

.already-reviewed-message i {
    font-size: 24px;
    margin-bottom: 10px;
    display: block;
}

.already-reviewed-message p {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
}

/* Rating Form */
.rating-form {
    padding: 20px;
}

.comment-section {
    margin-bottom: 0;
}

.comment-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    font-size: 16px;
}

.comment-textarea {
    width: 100%;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    line-height: 1.5;
    resize: vertical;
    transition: all 0.3s ease;
    font-family: inherit;
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
    padding: 20px 0;
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
    .review-container {
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
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .product-meta {
        align-items: center;
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

<script>
function submitAllReviews() {
    const forms = document.querySelectorAll('.rating-form');
    const reviews = [];
    
    // Kiểm tra xem có sản phẩm nào cần đánh giá không
    if (forms.length === 0) {
        alert('Tất cả sản phẩm trong đơn hàng này đã được đánh giá!');
        window.location.href = 'index.php?page=orders';
        return;
    }
    
    // Kiểm tra từng form một cách tuần tự
    for (let i = 0; i < forms.length; i++) {
        const form = forms[i];
        const productId = form.dataset.productId;
        const orderItemId = form.dataset.orderItemId;
        
        // Tìm textarea bằng order_item_id để đảm bảo tính duy nhất
        let contentTextarea = form.querySelector(`textarea[name="content_${orderItemId}"]`);
        
        if (!contentTextarea) {
            // Thử cách khác nếu không tìm thấy
            contentTextarea = form.querySelector('textarea');
        }
        
        if (!contentTextarea || !contentTextarea.value.trim()) {
            alert('Vui lòng nhập nội dung đánh giá cho sản phẩm!');
            // Focus vào textarea có vấn đề và highlight
            if (contentTextarea) {
                contentTextarea.focus();
                contentTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                contentTextarea.classList.add('error-highlight');
                setTimeout(() => contentTextarea.classList.remove('error-highlight'), 3000);
            }
            return;
        }
        
        const reviewData = {
            product_id: productId,
            order_item_id: orderItemId,
            rating: 5, // Mặc định 5 sao
            content: contentTextarea.value.trim()
        };
        
        reviews.push(reviewData);
    }
    
    // Nếu không có sản phẩm nào cần đánh giá
    if (reviews.length === 0) {
        alert('Tất cả sản phẩm trong đơn hàng này đã được đánh giá!');
        window.location.href = 'index.php?page=orders';
        return;
    }
    
    // Disable button and show loading
    const submitBtn = document.querySelector('.btn-submit');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    // Submit reviews
    const formData = new FormData();
    formData.append('order_id', '<?= e($order['id']) ?>');
    formData.append('reviews', JSON.stringify(reviews));
    
    fetch('index.php?page=comments&action=submit_order_reviews', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đánh giá đã được gửi thành công!');
            window.location.href = 'index.php?page=orders';
        } else {
            alert('Có lỗi xảy ra: ' + (data.message || 'Vui lòng thử lại'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi đánh giá';
        }
    })
    .catch(error => {
        alert('Có lỗi xảy ra khi gửi đánh giá!');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi đánh giá';
    });
}

// Kiểm tra và ẩn nút submit nếu tất cả sản phẩm đã được đánh giá
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.rating-form');
    const submitActions = document.querySelector('.submit-actions');
    
    if (forms.length === 0) {
        // Tất cả sản phẩm đã được đánh giá, ẩn nút submit và hiển thị thông báo
        submitActions.innerHTML = `
            <div class="all-reviewed-message">
                <i class="fas fa-check-circle"></i>
                <p>Tất cả sản phẩm trong đơn hàng này đã được đánh giá!</p>
                <a href="index.php?page=orders" class="btn-back-to-orders">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại danh sách đơn hàng
                </a>
            </div>
        `;
    }
    
    // Thêm event listeners để clear error highlighting
    forms.forEach(form => {
        const orderItemId = form.dataset.orderItemId;
        
        // Clear error khi nhập content
        const contentTextarea = form.querySelector(`textarea[name="content_${orderItemId}"]`);
        if (contentTextarea) {
            contentTextarea.addEventListener('input', function() {
                this.classList.remove('error-highlight');
            });
        }
    });
});
</script>

<style>
.all-reviewed-message {
    text-align: center;
    padding: 30px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    color: #28a745;
}

.all-reviewed-message i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}

.all-reviewed-message p {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 20px 0;
}

.btn-back-to-orders {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-back-to-orders:hover {
    background: linear-gradient(135deg, #20c997, #17a2b8);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

/* Error highlighting */
.error-highlight {
    animation: errorShake 0.5s ease-in-out;
    border: 2px solid #dc3545 !important;
    box-shadow: 0 0 10px rgba(220, 53, 69, 0.3) !important;
}

.comment-textarea.error-highlight {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

@keyframes errorShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>