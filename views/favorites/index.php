<?php 
$pageTitle = 'Sản phẩm yêu thích - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <h2>Sản phẩm yêu thích</h2>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-error" style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>Lỗi:</strong> <?= e($error_message) ?>
            <br><br>
            <a href="create_favorites_table.php" class="btn" style="display: inline-block; margin-top: 10px;">
                Tạo bảng favorites ngay
            </a>
        </div>
    <?php elseif (!empty($favorites)): ?>
        <div class="favorites-grid">
            <?php foreach ($favorites as $product): ?>
                <div class="food-item favorite-item">
                    <div class="favorite-badge">
                        <button class="favorite-btn active" 
                                onclick="toggleFavorite(<?= $product['id'] ?>, this)"
                                title="Xóa khỏi yêu thích">
                            ❤️
                        </button>
                    </div>
                    
                    <a href="index.php?page=menu&action=detail&id=<?= $product['id'] ?>" class="product-link">
                        <img src="<?= getImageUrl($product['primary_image'] ?? '') ?>" 
                             alt="<?= e($product['name']) ?>">
                        
                        <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                            <div class="stock-badge out">Hết hàng</div>
                        <?php elseif ($product['stock_quantity'] <= 5): ?>
                            <div class="stock-badge low">Còn <?= $product['stock_quantity'] ?></div>
                        <?php endif; ?>
                        
                        <h3><?= e($product['name']) ?></h3>
                        <p class="category-badge"><?= e($product['category_name'] ?? '') ?></p>
                        <p class="description"><?= e(substr($product['description'] ?? '', 0, 100)) ?><?= strlen($product['description'] ?? '') > 100 ? '...' : '' ?></p>
                        
                        <div class="price-wrapper">
                            <?php if (!empty($product['sale_price'])): ?>
                                <span class="original-price"><?= formatMoney($product['price']) ?></span>
                                <span class="sale-price"><?= formatMoney($product['sale_price']) ?></span>
                            <?php else: ?>
                                <span class="price"><?= formatMoney($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="favorite-date">Yêu thích từ: <?= date('d/m/Y', strtotime($product['favorited_at'])) ?></p>
                    </a>
                    
                    <div class="product-actions">
                        <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                            <button class="btn btn-disabled" disabled>Hết hàng</button>
                        <?php elseif (isLoggedIn()): ?>
                            <a href="index.php?page=cart&action=add&id=<?= $product['id'] ?>" 
                               class="btn">🛒 Thêm vào giỏ</a>
                        <?php else: ?>
                            <a href="index.php?page=login" 
                               class="btn btn-login"
                               onclick="return confirm('Bạn cần đăng nhập để thêm món ăn vào giỏ hàng. Bạn có muốn đăng nhập ngay không?')">
                               🛒 Thêm vào giỏ
                            </a>
                        <?php endif; ?>
                        <a href="index.php?page=menu&action=detail&id=<?= $product['id'] ?>" 
                           class="btn btn-secondary">Xem chi tiết</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-favorites">
            <div class="empty-icon">💔</div>
            <h3>Chưa có sản phẩm yêu thích nào</h3>
            <p>Hãy khám phá thực đơn và thêm những món ăn yêu thích của bạn!</p>
            <a href="index.php?page=menu" class="btn">Xem thực đơn</a>
        </div>
    <?php endif; ?>
</div>

<style>
.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.favorite-item {
    position: relative;
    border: 1px solid #eee;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    background: white;
}

.favorite-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.favorite-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.favorite-btn {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.favorite-btn:hover {
    background: white;
    transform: scale(1.1);
}

.favorite-btn.active {
    color: #ff4757;
}

.product-link {
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 15px;
}

.favorite-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    margin-bottom: 15px;
    border-radius: 8px;
}

.favorite-item h3 {
    font-size: 18px;
    margin-bottom: 8px;
    color: #333;
}

.favorite-item .category-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
    margin-bottom: 8px;
}

.favorite-item .description {
    font-size: 14px;
    color: #666;
    line-height: 1.4;
    margin-bottom: 10px;
}

.favorite-item .price-wrapper {
    margin-bottom: 10px;
}

.favorite-date {
    font-size: 12px;
    color: #999;
    font-style: italic;
    margin-bottom: 0;
}

.product-actions {
    display: flex;
    gap: 10px;
    padding: 0 15px 15px;
}

.product-actions .btn {
    flex: 1;
    text-align: center;
    padding: 8px 12px;
    font-size: 14px;
}

.empty-favorites {
    text-align: center;
    padding: 80px 20px;
    color: #666;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.empty-favorites h3 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #333;
}

.empty-favorites p {
    font-size: 16px;
    margin-bottom: 30px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.stock-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
    color: white;
}

.stock-badge.out {
    background: #f44336;
}

.stock-badge.low {
    background: #ff9800;
}

@media (max-width: 768px) {
    .favorites-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .product-actions {
        flex-direction: column;
    }
}
</style>

<script>
function toggleFavorite(productId, button) {
    fetch('index.php?page=favorites&action=toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.action === 'removed') {
                // Xóa item khỏi trang yêu thích
                button.closest('.favorite-item').style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    button.closest('.favorite-item').remove();
                    
                    // Kiểm tra nếu không còn item nào
                    const remainingItems = document.querySelectorAll('.favorite-item');
                    if (remainingItems.length === 0) {
                        location.reload();
                    }
                }, 300);
            }
            
            // Hiển thị thông báo
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra!', 'error');
    });
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
        ${type === 'success' ? 'background: #4CAF50;' : 'background: #f44336;'}
    `;
    
    document.body.appendChild(notification);
    
    // Tự động xóa sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.8); }
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>

<?php require_once 'views/layouts/footer.php'; ?>