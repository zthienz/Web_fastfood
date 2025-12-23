<?php
$pageTitle = 'Tất cả sản phẩm';
require_once 'views/layouts/header.php';
?>

<div class="container">
    <h1 class="page-title">Tất cả sản phẩm</h1>
    
    <div class="products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php 
                        $image = $product['primary_image'] ?? $product['image'] ?? 'public/images/products/default.jpg';
                        ?>
                        <img src="<?= asset($image) ?>" alt="<?= e($product['name']) ?>">
                        <?php if ($product['sale_price']): ?>
                            <span class="sale-badge">Giảm giá</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?= e($product['name']) ?></h3>
                        <p class="product-desc"><?= e(mb_substr($product['description'] ?? '', 0, 80)) ?>...</p>
                        <div class="product-price">
                            <?php if ($product['sale_price']): ?>
                                <span class="price-old"><?= formatMoney($product['price']) ?></span>
                                <span class="price-sale"><?= formatMoney($product['sale_price']) ?></span>
                            <?php else: ?>
                                <span class="price"><?= formatMoney($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="index.php?page=post&id=<?= $product['id'] ?>" class="btn btn-detail">Xem chi tiết</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-products">Chưa có sản phẩm nào.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.page-title {
    text-align: center;
    margin: 30px 0;
    color: #333;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    padding: 20px 0;
}

.product-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sale-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #e74c3c;
    color: #fff;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: bold;
}

.product-info {
    padding: 15px;
}

.product-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    line-height: 1.3;
}

.product-desc {
    font-size: 13px;
    color: #666;
    margin-bottom: 10px;
    line-height: 1.5;
    min-height: 40px;
}

.product-price {
    margin-bottom: 12px;
}

.price {
    font-size: 18px;
    font-weight: bold;
    color: #e74c3c;
}

.price-old {
    font-size: 14px;
    color: #999;
    text-decoration: line-through;
    margin-right: 8px;
}

.price-sale {
    font-size: 18px;
    font-weight: bold;
    color: #e74c3c;
}

.btn-detail {
    display: block;
    text-align: center;
    background: #ff6b35;
    color: #fff;
    padding: 10px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.3s;
}

.btn-detail:hover {
    background: #e55a2b;
}

.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px;
    color: #666;
}

/* Responsive */
@media (max-width: 992px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>
