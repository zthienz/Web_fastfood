<?php 
$pageTitle = 'FastFood - Trang chủ';
require_once 'views/layouts/header.php'; 
?>

<!-- Banner -->
<div class="banner">
    <h1>Ăn nhanh - Ngon miệng - Giá rẻ!</h1>
    <p>Thưởng thức món ăn nhanh chất lượng cao</p>
</div>

<!-- Danh sách món ăn -->
<div class="container">
    <h2>Thực đơn nổi bật</h2>
    <div class="menu">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="food-item">
                    <?php if (isLoggedIn()): ?>
                        <div class="favorite-badge">
                            <button class="favorite-btn <?= isset($product['is_favorite']) && $product['is_favorite'] ? 'active' : '' ?>" 
                                    onclick="toggleFavorite(<?= $product['id'] ?>, this)"
                                    title="<?= isset($product['is_favorite']) && $product['is_favorite'] ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' ?>">
                                ❤️
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <a href="index.php?page=menu&action=detail&id=<?= $product['id'] ?>" class="product-link">
                        <img src="<?= getImageUrl($product['primary_image'] ?? $product['image'] ?? '') ?>" 
                             alt="<?= e($product['name']) ?>">
                        <h3><?= e($product['name']) ?></h3>
                    </a>
                    <p><?= e($product['description'] ?? '') ?></p>
                    <div class="price-wrapper">
                        <?php if (!empty($product['sale_price'])): ?>
                            <span class="original-price"><?= formatMoney($product['price']) ?></span>
                            <span class="sale-price"><?= formatMoney($product['sale_price']) ?></span>
                        <?php else: ?>
                            <span class="price"><?= formatMoney($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="index.php?page=cart&action=add&id=<?= $product['id'] ?>" 
                       class="btn">Đặt ngay</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Chưa có món ăn nào!</p>
        <?php endif; ?>
    </div>
</div>

<style>
/* Favorite Button Styles */
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
    color: #ccc;
}

.favorite-btn:hover {
    background: white;
    transform: scale(1.1);
}

.favorite-btn.active {
    color: #ff4757;
    background: white;
}

.favorites-link {
    display: flex;
    align-items: center;
    gap: 5px;
}

.heart-icon {
    font-size: 16px;
}

/* Product Link Styles */
.product-link {
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform 0.3s;
}

.product-link:hover {
    transform: translateY(-2px);
}

.product-link:hover h3 {
    color: #ff6b35;
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
            if (data.action === 'added') {
                button.classList.add('active');
                button.title = 'Xóa khỏi yêu thích';
            } else if (data.action === 'removed') {
                button.classList.remove('active');
                button.title = 'Thêm vào yêu thích';
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
