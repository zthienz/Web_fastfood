<?php 
$pageTitle = e($product['name']) . ' - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="index.php">Trang chủ</a>
        <span>/</span>
        <a href="index.php?page=menu">Thực đơn</a>
        <span>/</span>
        <span><?= e($product['name']) ?></span>
    </nav>

    <div class="product-detail">
        <div class="product-images">
            <?php if (!empty($images)): ?>
                <div class="main-image">
                    <img id="mainImage" src="<?= getImageUrl($images[0]['image_url']) ?>" 
                         alt="<?= e($product['name']) ?>">
                    <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                        <div class="stock-overlay">Hết hàng</div>
                    <?php endif; ?>
                </div>
                
                <?php if (count($images) > 1): ?>
                <div class="image-thumbnails">
                    <?php foreach ($images as $index => $image): ?>
                        <img src="<?= getImageUrl($image['image_url']) ?>" 
                             alt="<?= e($product['name']) ?>"
                             onclick="changeMainImage('<?= getImageUrl($image['image_url']) ?>')"
                             class="<?= $index === 0 ? 'active' : '' ?>">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="main-image">
                    <img src="<?= getImageUrl('') ?>" alt="<?= e($product['name']) ?>">
                </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <h1><?= e($product['name']) ?></h1>
            
            <div class="category-badge">
                <span><?= e($product['category_name'] ?? '') ?></span>
            </div>

            <div class="price-section">
                <?php if (!empty($product['sale_price'])): ?>
                    <span class="original-price"><?= formatMoney($product['price']) ?></span>
                    <span class="sale-price"><?= formatMoney($product['sale_price']) ?></span>
                    <span class="discount-badge">
                        -<?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>%
                    </span>
                <?php else: ?>
                    <span class="current-price"><?= formatMoney($product['price']) ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($product['description'])): ?>
            <div class="description">
                <h3>Mô tả sản phẩm</h3>
                <p><?= nl2br(e($product['description'])) ?></p>
            </div>
            <?php endif; ?>

            <div class="stock-info">
                <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                    <span class="stock-status out">❌ Hết hàng</span>
                <?php elseif ($product['stock_quantity'] <= 5): ?>
                    <span class="stock-status low">⚠️ Chỉ còn <?= $product['stock_quantity'] ?> sản phẩm</span>
                <?php else: ?>
                    <span class="stock-status available">✅ Còn hàng</span>
                <?php endif; ?>
            </div>

            <div class="product-actions">
                <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                    <button class="btn btn-disabled" disabled>Hết hàng</button>
                <?php elseif (isLoggedIn()): ?>
                    <div class="quantity-selector">
                        <button type="button" onclick="decreaseQuantity()">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                        <button type="button" onclick="increaseQuantity()">+</button>
                    </div>
                    <a href="javascript:void(0)" onclick="addToCartAjax(<?= $product['id'] ?>, document.getElementById('quantity').value, this)" 
                       class="btn btn-orange add-to-cart-btn">Thêm vào giỏ</a>
                <?php else: ?>
                    <a href="index.php?page=login" 
                       class="btn btn-orange"
                       onclick="return confirm('Bạn cần đăng nhập để thêm món ăn vào giỏ hàng. Bạn có muốn đăng nhập ngay không?')">
                       🛒 Thêm vào giỏ
                    </a>
                <?php endif; ?>
            </div>

            <div class="product-meta">
                <p><strong>Lượt xem:</strong> <?= number_format($product['views']) ?></p>
                <p><strong>Mã sản phẩm:</strong> SP<?= str_pad($product['id'], 4, '0', STR_PAD_LEFT) ?></p>
            </div>
        </div>
    </div>

    <!-- Phần đánh giá và bình luận -->
    <div class="product-reviews">
        <div class="reviews-header">
            <h2>Đánh giá sản phẩm</h2>
            <?php if ($totalComments > 0): ?>
                <div class="rating-summary">
                    <span class="total-reviews">(<?= $totalComments ?> đánh giá)</span>
                </div>
            <?php else: ?>
                <p class="no-reviews">Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($comments)): ?>
            <div class="reviews-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="review-item" data-comment-id="<?= $comment['id'] ?>">
                        <div class="reviewer-info">
                            <div class="reviewer-details">
                                <h4><?= e($comment['full_name']) ?></h4>
                                <div class="review-meta">
                                    <span class="review-date"><?= date('d/m/Y', strtotime($comment['created_at'])) ?></span>
                                    <?php if (isset($comment['rating']) && $comment['rating']): ?>
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $comment['rating'] ? 'filled' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="review-content">
                            <p><?= nl2br(e($comment['content'])) ?></p>
                        </div>
                        
                        <!-- Admin Reply Section -->
                        <?php if (isLoggedIn() && $_SESSION['role'] === 'admin'): ?>
                            <div class="admin-reply-section">
                                <button type="button" class="btn-reply" onclick="toggleReplyForm(<?= $comment['id'] ?>)">
                                    <i class="fas fa-reply"></i> Phản hồi
                                </button>
                                
                                <div class="reply-form" id="replyForm<?= $comment['id'] ?>" style="display: none;">
                                    <textarea 
                                        id="replyContent<?= $comment['id'] ?>" 
                                        placeholder="Nhập phản hồi của bạn..."
                                        rows="3"
                                    ></textarea>
                                    <div class="reply-actions">
                                        <button type="button" class="btn-submit-reply" onclick="submitReply(<?= $comment['id'] ?>)">
                                            <i class="fas fa-paper-plane"></i> Gửi phản hồi
                                        </button>
                                        <button type="button" class="btn-cancel-reply" onclick="cancelReply(<?= $comment['id'] ?>)">
                                            <i class="fas fa-times"></i> Hủy
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Display Existing Replies -->
                        <?php if (isset($comment['replies']) && !empty($comment['replies'])): ?>
                            <div class="replies-section">
                                <?php foreach ($comment['replies'] as $reply): ?>
                                    <div class="reply-item">
                                        <div class="reply-header">
                                            <strong class="admin-badge">
                                                <i class="fas fa-shield-alt"></i> <?= e($reply['full_name']) ?>
                                            </strong>
                                            <span class="reply-date"><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?></span>
                                        </div>
                                        <div class="reply-content">
                                            <p><?= nl2br(e($reply['content'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sản phẩm liên quan -->
    <?php if (!empty($relatedProducts)): ?>
    <div class="related-products">
        <h2>Sản phẩm liên quan</h2>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $relatedProduct): ?>
                <div class="food-item">
                    <a href="index.php?page=menu&action=detail&id=<?= $relatedProduct['id'] ?>">
                        <img src="<?= getImageUrl($relatedProduct['primary_image'] ?? '') ?>" 
                             alt="<?= e($relatedProduct['name']) ?>">
                    </a>
                    
                    <div class="content">
                        <div class="content-body">
                            <a href="index.php?page=menu&action=detail&id=<?= $relatedProduct['id'] ?>">
                                <h3><?= e($relatedProduct['name']) ?></h3>
                            </a>
                            <div class="price-wrapper">
                                <?php if (!empty($relatedProduct['sale_price'])): ?>
                                    <span class="original-price"><?= formatMoney($relatedProduct['price']) ?></span>
                                    <span class="sale-price"><?= formatMoney($relatedProduct['sale_price']) ?></span>
                                <?php else: ?>
                                    <span class="price"><?= formatMoney($relatedProduct['price']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.breadcrumb {
    margin-bottom: 20px;
    font-size: 14px;
}

.breadcrumb a {
    color: #ff6b35;
    text-decoration: none;
}

.breadcrumb span {
    margin: 0 8px;
    color: #666;
}

.product-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 50px;
}

.product-images {
    position: sticky;
    top: 20px;
}

.main-image {
    position: relative;
    margin-bottom: 15px;
}

.main-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stock-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 0, 0, 0.9);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 18px;
}

.image-thumbnails {
    display: flex;
    gap: 10px;
    overflow-x: auto;
}

.image-thumbnails img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.3s;
}

.image-thumbnails img.active,
.image-thumbnails img:hover {
    border-color: #ff6b35;
}

.product-info h1 {
    font-size: 28px;
    margin-bottom: 10px;
    color: #333;
}

.category-badge {
    margin-bottom: 15px;
}

.category-badge span {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.price-section {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.original-price {
    text-decoration: line-through;
    color: #999;
    font-size: 18px;
}

.sale-price,
.current-price {
    font-size: 24px;
    font-weight: bold;
    color: #ff6b35;
}

.discount-badge {
    background: #ff4444;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.description {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.description h3 {
    margin-bottom: 10px;
    color: #333;
}

.stock-info {
    margin-bottom: 20px;
}

.stock-status {
    font-weight: 500;
    padding: 5px 0;
}

.stock-status.out {
    color: #dc3545;
}

.stock-status.low {
    color: #ffc107;
}

.stock-status.available {
    color: #28a745;
}

.product-actions {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 20px;
}

.quantity-selector {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
}

.quantity-selector button {
    background: #f8f9fa;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
}

.quantity-selector button:hover {
    background: #e9ecef;
}

.quantity-selector input {
    border: none;
    padding: 8px 12px;
    width: 60px;
    text-align: center;
    font-size: 16px;
}

.product-meta {
    padding-top: 20px;
    border-top: 1px solid #eee;
    font-size: 14px;
    color: #666;
}

.product-meta p {
    margin-bottom: 5px;
}

/* Orange Button Styles */
.btn-orange {
    background: linear-gradient(135deg, #ff6b35, #ff5722) !important;
    color: white !important;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
    font-size: 16px;
}

.btn-orange:hover {
    background: linear-gradient(135deg, #ff5722, #e64a19) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
    color: white !important;
}

.btn-orange:active {
    transform: translateY(0);
}

.related-products {
    margin-top: 50px;
}

.related-products h2 {
    margin-bottom: 20px;
    color: #333;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    align-items: stretch; /* Make all grid items same height */
}

.products-grid .food-item {
    border: 1px solid #eee;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.products-grid .food-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.products-grid .food-item a {
    text-decoration: none;
    color: inherit;
}

.products-grid .food-item img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.products-grid .food-item .content {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 15px;
}

.products-grid .food-item .content-body {
    flex: 1;
}

.products-grid .food-item h3 {
    font-size: 16px;
    margin-bottom: 8px;
    color: #333;
}

@media (max-width: 768px) {
    .product-detail {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .product-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .quantity-selector {
        justify-content: center;
    }
}

/* Reviews Styles */
.product-reviews {
    margin-top: 50px;
    padding-top: 30px;
    border-top: 2px solid #f0f0f0;
}

.reviews-header {
    margin-bottom: 30px;
}

.reviews-header h2 {
    color: #333;
    margin-bottom: 15px;
}

.rating-summary {
    display: flex;
    align-items: center;
    gap: 20px;
}

.total-reviews {
    color: #666;
    font-size: 1rem;
    font-weight: 500;
}

.no-reviews {
    color: #666;
    font-style: italic;
    margin: 0;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.review-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    border-left: 4px solid #ff6b35;
}

.reviewer-info {
    margin-bottom: 15px;
}

.reviewer-details h4 {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 1.1rem;
    font-weight: 600;
}

.review-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.review-date {
    color: #666;
    font-size: 0.85rem;
}

.review-rating {
    display: flex;
    gap: 2px;
}

.review-rating .fas.fa-star {
    font-size: 0.9rem;
    color: #ddd;
}

.review-rating .fas.fa-star.filled {
    color: #ffc107;
}

.review-content p {
    margin: 0;
    line-height: 1.6;
    color: #444;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #ff6b35;
}

/* Admin Reply Styles */
.admin-reply-section {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.btn-reply {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background-color 0.3s;
}

.btn-reply:hover {
    background: #0056b3;
}

.reply-form {
    margin-top: 15px;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.reply-form textarea {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 10px;
    font-family: inherit;
    font-size: 0.9rem;
    resize: vertical;
    margin-bottom: 10px;
}

.reply-actions {
    display: flex;
    gap: 10px;
}

.btn-submit-reply {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background-color 0.3s;
}

.btn-submit-reply:hover {
    background: #1e7e34;
}

.btn-cancel-reply {
    background: #6c757d;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background-color 0.3s;
}

.btn-cancel-reply:hover {
    background: #545b62;
}

/* Replies Display Styles */
.replies-section {
    margin-top: 20px;
    padding-left: 20px;
    border-left: 3px solid #007bff;
}

.reply-item {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.reply-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.admin-badge {
    color: #007bff;
    font-size: 0.95rem;
}

.admin-badge i {
    margin-right: 5px;
}

.reply-date {
    color: #666;
    font-size: 0.85rem;
}

.reply-content p {
    margin: 0;
    line-height: 1.6;
    color: #333;
    background: none;
    padding: 0;
    border: none;
}

@media (max-width: 768px) {
    .rating-summary {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .review-meta {
        justify-content: flex-start;
    }
    
    .reply-actions {
        flex-direction: column;
    }
    
    .replies-section {
        padding-left: 10px;
    }
}
</style>

<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
    
    // Update active thumbnail
    const thumbnails = document.querySelectorAll('.image-thumbnails img');
    thumbnails.forEach(thumb => {
        thumb.classList.remove('active');
        if (thumb.src === src) {
            thumb.classList.add('active');
        }
    });
}

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}

function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    
    // Redirect to cart controller with product ID and quantity
    window.location.href = `index.php?page=cart&action=add&id=${productId}&quantity=${quantity}`;
}

function addToCartAjax(productId, quantity, buttonElement) {
    // Disable button để tránh click nhiều lần
    const originalText = buttonElement.textContent;
    buttonElement.disabled = true;
    buttonElement.textContent = 'Đang thêm...';
    buttonElement.style.opacity = '0.7';
    
    fetch('index.php?page=cart&action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Cập nhật số lượng giỏ hàng trong header nếu có
            updateCartCount(data.cart_count);
            
            // Hiệu ứng thành công cho button
            buttonElement.style.background = '#4CAF50';
            buttonElement.textContent = '✓ Đã thêm';
            
            setTimeout(() => {
                buttonElement.style.background = '';
                buttonElement.textContent = originalText;
                buttonElement.disabled = false;
                buttonElement.style.opacity = '1';
            }, 1500);
            
        } else {
            showNotification(data.message, 'error');
            
            // Reset button
            buttonElement.disabled = false;
            buttonElement.textContent = originalText;
            buttonElement.style.opacity = '1';
            
            // Nếu cần redirect (như đăng nhập)
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');
        
        // Reset button
        buttonElement.disabled = false;
        buttonElement.textContent = originalText;
        buttonElement.style.opacity = '1';
    });
}

function updateCartCount(count) {
    // Cập nhật số lượng trong header cart badge
    const cartBadges = document.querySelectorAll('.cart-badge');
    cartBadges.forEach(badge => {
        badge.textContent = count;
        if (count > 0) {
            badge.style.display = 'flex';
            // Thêm animation pulse
            badge.style.animation = 'pulse 0.3s ease-out';
        } else {
            badge.style.display = 'none';
        }
    });
    
    // Nếu chưa có badge, tạo mới
    if (cartBadges.length === 0 && count > 0) {
        const cartLink = document.querySelector('.cart-link');
        if (cartLink) {
            const badge = document.createElement('span');
            badge.className = 'cart-badge';
            badge.textContent = count;
            cartLink.appendChild(badge);
        }
    }
}

function showNotification(message, type) {
    // Tạo notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
        max-width: 350px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        ${type === 'success' ? 'background: #4CAF50;' : 'background: #f44336;'}
    `;
    
    document.body.appendChild(notification);
    
    // Tự động xóa sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .add-to-cart-btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .add-to-cart-btn:disabled {
        cursor: not-allowed;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// Admin Reply Functions
function toggleReplyForm(commentId) {
    const form = document.getElementById('replyForm' + commentId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
        document.getElementById('replyContent' + commentId).focus();
    } else {
        form.style.display = 'none';
    }
}

function cancelReply(commentId) {
    const form = document.getElementById('replyForm' + commentId);
    const textarea = document.getElementById('replyContent' + commentId);
    
    form.style.display = 'none';
    textarea.value = '';
}

function submitReply(commentId) {
    const textarea = document.getElementById('replyContent' + commentId);
    const content = textarea.value.trim();
    
    if (!content) {
        alert('Vui lòng nhập nội dung phản hồi!');
        textarea.focus();
        return;
    }
    
    // Disable form để tránh submit nhiều lần
    const submitBtn = document.querySelector(`#replyForm${commentId} .btn-submit-reply`);
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    // Submit reply
    fetch('index.php?page=comments&action=admin_reply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `comment_id=${commentId}&content=${encodeURIComponent(content)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page để hiển thị reply mới
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + (data.message || 'Vui lòng thử lại'));
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        alert('Có lỗi xảy ra khi gửi phản hồi!');
        
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>