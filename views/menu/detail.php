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
                    <a href="javascript:void(0)" onclick="addToCart(<?= $product['id'] ?>)" 
                       class="btn btn-primary">🛒 Thêm vào giỏ</a>
                <?php else: ?>
                    <a href="index.php?page=login" 
                       class="btn btn-login"
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
                        <h3><?= e($relatedProduct['name']) ?></h3>
                        <div class="price-wrapper">
                            <?php if (!empty($relatedProduct['sale_price'])): ?>
                                <span class="original-price"><?= formatMoney($relatedProduct['price']) ?></span>
                                <span class="sale-price"><?= formatMoney($relatedProduct['sale_price']) ?></span>
                            <?php else: ?>
                                <span class="price"><?= formatMoney($relatedProduct['price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
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
}

.products-grid .food-item {
    border: 1px solid #eee;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.products-grid .food-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.products-grid .food-item a {
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 15px;
}

.products-grid .food-item img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    margin-bottom: 10px;
    border-radius: 8px;
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
</script>

<?php require_once 'views/layouts/footer.php'; ?>