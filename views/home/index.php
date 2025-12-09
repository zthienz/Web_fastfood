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
                    <img src="<?= getImageUrl($product['primary_image'] ?? $product['image'] ?? '') ?>" 
                         alt="<?= e($product['name']) ?>">
                    <h3><?= e($product['name']) ?></h3>
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

<?php require_once 'views/layouts/footer.php'; ?>
