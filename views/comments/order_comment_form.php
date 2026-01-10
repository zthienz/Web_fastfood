<?php require_once 'views/layouts/header.php'; ?>

<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Header -->
            <div class="review-header mb-4">
                <div class="text-center">
                    <div class="review-icon mb-3">
                        <i class="fas fa-star"></i>
                    </div>
                    <h2 class="review-title">Đánh giá món ăn</h2>
                    <p class="review-subtitle">Chia sẻ trải nghiệm của bạn để giúp khách hàng khác</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="review-card">
                <!-- Order Info -->
                <div class="order-info-section">
                    <div class="order-info-header">
                        <i class="fas fa-receipt me-2"></i>
                        <h6 class="mb-0">Thông tin đơn hàng</h6>
                    </div>
                    <div class="order-info-content">
                        <div class="order-detail-item">
                            <span class="label">Mã đơn:</span>
                            <span class="value"><?= e($orderItem['order_number']) ?></span>
                        </div>
                        <div class="order-detail-item">
                            <span class="label">Món ăn:</span>
                            <span class="value"><?= e($orderItem['current_product_name'] ?: $orderItem['product_name']) ?></span>
                        </div>
                        <div class="order-detail-item">
                            <span class="label">Ngày giao:</span>
                            <span class="value"><?= date('d/m/Y H:i', strtotime($orderItem['delivered_at'] ?: $orderItem['updated_at'])) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Review Form -->
                <form method="POST" action="index.php?page=comments&action=submit" class="review-form">
                    <input type="hidden" name="order_id" value="<?= e($orderItem['id']) ?>">
                    <input type="hidden" name="product_id" value="<?= e($orderItem['product_id']) ?>">
                    
                    <!-- Rating Section -->
                    <div class="rating-section">
                        <label class="section-label">
                            <i class="fas fa-star me-2"></i>
                            Đánh giá của bạn
                        </label>
                        <div class="rating-input-container">
                            <div class="stars-container">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" required>
                                    <label for="star<?= $i ?>" class="star-label">
                                        <i class="fas fa-star"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <div class="rating-feedback">
                                <span id="rating-description" class="rating-text">Nhấn vào sao để đánh giá</span>
                            </div>
                        </div>
                    </div>

                    <!-- Comment Section -->
                    <div class="comment-section">
                        <label for="content" class="section-label">
                            <i class="fas fa-comment-alt me-2"></i>
                            Nội dung đánh giá
                        </label>
                        <div class="comment-input-container">
                            <textarea 
                                class="comment-textarea" 
                                id="content" 
                                name="content" 
                                rows="6" 
                                placeholder="Hãy chia sẻ cảm nhận của bạn về món ăn này: chất lượng, hương vị, cách trình bày, dịch vụ giao hàng..."
                                required
                                maxlength="1000"
                            ></textarea>
                            <div class="char-counter">
                                <span id="char-count">0</span>/1000 ký tự
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <a href="index.php?page=orders&action=detail&id=<?= e($orderItem['id']) ?>" 
                           class="btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Quay lại
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            Gửi đánh giá
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Review Form Styles */
.container-fluid {
    max-width: 1000px;
}

/* Header Section */
.review-header {
    text-align: center;
    margin-bottom: 2rem;
}

.review-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
}

.review-title {
    color: #2d3436;
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.review-subtitle {
    color: #636e72;
    font-size: 1.1rem;
    margin: 0;
}

/* Main Card */
.review-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* Order Info Section */
.order-info-section {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
}

.order-info-header {
    padding: 1.5rem 2rem 1rem;
    display: flex;
    align-items: center;
    color: #495057;
}

.order-info-header h6 {
    font-weight: 600;
    margin: 0;
}

.order-info-content {
    padding: 0 2rem 1.5rem;
}

.order-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #dee2e6;
}

.order-detail-item:last-child {
    border-bottom: none;
}

.order-detail-item .label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.95rem;
}

.order-detail-item .value {
    font-weight: 500;
    color: #2d3436;
    text-align: right;
}

/* Form Sections */
.review-form {
    padding: 2rem;
}

.section-label {
    display: flex;
    align-items: center;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3436;
    margin-bottom: 1.5rem;
}

/* Rating Section */
.rating-section {
    margin-bottom: 2.5rem;
}

.rating-input-container {
    text-align: center;
}

.stars-container {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stars-container input[type="radio"] {
    display: none;
}

.star-label {
    cursor: pointer;
    font-size: 3rem;
    color: #e9ecef;
    transition: all 0.3s ease;
    transform-origin: center;
}

.star-label:hover,
.star-label:hover ~ .star-label,
.stars-container input[type="radio"]:checked ~ .star-label {
    color: #ffc107;
    transform: scale(1.1);
}

.star-label:hover {
    transform: scale(1.2);
}

.rating-feedback {
    min-height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rating-text {
    font-size: 1.1rem;
    color: #6c757d;
    font-style: italic;
    transition: all 0.3s ease;
}

.rating-text.selected {
    color: #ff6b35;
    font-weight: 600;
    font-style: normal;
}

/* Comment Section */
.comment-section {
    margin-bottom: 2.5rem;
}

.comment-input-container {
    position: relative;
}

.comment-textarea {
    width: 100%;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 1.5rem;
    font-size: 1rem;
    line-height: 1.6;
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
    font-style: italic;
}

.char-counter {
    text-align: right;
    margin-top: 0.75rem;
    font-size: 0.9rem;
}

#char-count {
    font-weight: 600;
    transition: color 0.3s ease;
}

.char-counter.warning #char-count {
    color: #ffc107;
}

.char-counter.danger #char-count {
    color: #dc3545;
}

/* Action Buttons */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.btn-secondary, .btn-primary {
    padding: 0.875rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    min-width: 140px;
    justify-content: center;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #495057, #343a40);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

.btn-primary {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: white;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #e55a2b, #e8851e);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.btn-primary:disabled {
    background: #adb5bd;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .review-form {
        padding: 1.5rem;
    }
    
    .order-info-header,
    .order-info-content {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    
    .review-title {
        font-size: 1.5rem;
    }
    
    .review-subtitle {
        font-size: 1rem;
    }
    
    .star-label {
        font-size: 2.5rem;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-secondary, .btn-primary {
        width: 100%;
    }
    
    .order-detail-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .order-detail-item .value {
        text-align: center;
    }
}

/* Animation for star rating */
@keyframes starPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.star-label.animate {
    animation: starPulse 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingDescription = document.getElementById('rating-description');
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('char-count');
    const charCounter = document.querySelector('.char-counter');
    const submitButton = document.querySelector('.btn-primary');
    
    const ratingTexts = {
        1: '⭐ Rất không hài lòng - Món ăn không đạt yêu cầu',
        2: '⭐⭐ Không hài lòng - Cần cải thiện nhiều', 
        3: '⭐⭐⭐ Bình thường - Ở mức trung bình',
        4: '⭐⭐⭐⭐ Hài lòng - Món ăn ngon, sẽ đặt lại',
        5: '⭐⭐⭐⭐⭐ Rất hài lòng - Xuất sắc, đáng đồng tiền'
    };
    
    // Xử lý thay đổi rating
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            const starLabel = document.querySelector(`label[for="star${this.value}"]`);
            starLabel.classList.add('animate');
            
            ratingDescription.textContent = ratingTexts[this.value];
            ratingDescription.classList.add('selected');
            
            setTimeout(() => {
                starLabel.classList.remove('animate');
            }, 300);
            
            updateSubmitButton();
        });
    });
    
    // Đếm ký tự và validation
    contentTextarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        // Reset classes
        charCounter.classList.remove('warning', 'danger');
        
        if (length > 900) {
            charCounter.classList.add('danger');
        } else if (length > 800) {
            charCounter.classList.add('warning');
        }
        
        updateSubmitButton();
    });
    
    // Cập nhật trạng thái nút submit
    function updateSubmitButton() {
        const hasRating = document.querySelector('input[name="rating"]:checked');
        const hasContent = contentTextarea.value.trim().length > 0;
        
        if (hasRating && hasContent) {
            submitButton.disabled = false;
            submitButton.textContent = 'Gửi đánh giá';
        } else {
            submitButton.disabled = true;
            if (!hasRating) {
                submitButton.innerHTML = '<i class="fas fa-star me-2"></i>Vui lòng chọn số sao';
            } else if (!hasContent) {
                submitButton.innerHTML = '<i class="fas fa-comment-alt me-2"></i>Vui lòng nhập nội dung';
            }
        }
    }
    
    // Khởi tạo trạng thái ban đầu
    updateSubmitButton();
    
    // Xử lý submit form
    document.querySelector('.review-form').addEventListener('submit', function(e) {
        const hasRating = document.querySelector('input[name="rating"]:checked');
        const hasContent = contentTextarea.value.trim().length > 0;
        
        if (!hasRating || !hasContent) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin đánh giá!');
            return false;
        }
        
        // Hiển thị loading
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
    });
    
    // Auto-resize textarea
    contentTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 200) + 'px';
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>