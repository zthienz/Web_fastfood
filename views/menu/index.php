<?php 
$pageTitle = 'Thực đơn - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <h2>Thực đơn</h2>
    
    <!-- Tìm kiếm -->
    <div style="margin: 20px 0;">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="menu">
            <input type="hidden" name="action" value="search">
            <input type="text" name="q" placeholder="Tìm kiếm món ăn..." 
                   value="<?= e($_GET['q'] ?? '') ?>"
                   style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" class="btn" style="margin: 0;">Tìm kiếm</button>
        </form>
    </div>

    <div class="menu">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="food-item <?= ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0) ? 'out-of-stock' : '' ?>">
                    <img src="<?= getImageUrl($product['primary_image'] ?? $product['image'] ?? '') ?>" 
                         alt="<?= e($product['name']) ?>">
                    <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                        <div class="stock-badge out">Hết hàng</div>
                    <?php elseif ($product['stock_quantity'] <= 5): ?>
                        <div class="stock-badge low">Còn <?= $product['stock_quantity'] ?></div>
                    <?php endif; ?>
                    <h3><?= e($product['name']) ?></h3>
                    <p class="category-badge"><?= e($product['category_name'] ?? '') ?></p>
                    <p><?= e($product['description'] ?? '') ?></p>
                    <div class="price-wrapper">
                        <?php if (!empty($product['sale_price'])): ?>
                            <span class="original-price"><?= formatMoney($product['price']) ?></span>
                            <span class="sale-price"><?= formatMoney($product['sale_price']) ?></span>
                        <?php else: ?>
                            <span class="price"><?= formatMoney($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                        <button class="btn btn-disabled" disabled>Hết hàng</button>
                    <?php elseif (isLoggedIn()): ?>
                        <a href="index.php?page=cart&action=add&id=<?= $product['id'] ?>" 
                           class="btn">Thêm vào giỏ</a>
                    <?php else: ?>
                        <a href="index.php?page=login" 
                           class="btn btn-login"
                           onclick="return confirm('Bạn cần đăng nhập để thêm món ăn vào giỏ hàng. Bạn có muốn đăng nhập ngay không?')">
                           Thêm vào giỏ
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Không tìm thấy món ăn nào!</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
