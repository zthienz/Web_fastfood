<?php
$pageTitle = $post['name'] ?? 'Chi tiết sản phẩm';
require_once 'views/layouts/header.php';
?>

<div class="container">
    <div class="article-card">
        <h1><?= e($post['name']) ?></h1>
        <div class="article-meta">
            <span class="category"><?= e($post['category_name'] ?? '') ?></span>
            <?php if ($post['sale_price']): ?>
                <span class="price-old"><?= formatMoney($post['price']) ?></span>
                <span class="price"><?= formatMoney($post['sale_price']) ?></span>
            <?php else: ?>
                <span class="price">Giá: <?= formatMoney($post['price']) ?></span>
            <?php endif; ?>
        </div>
        <div class="article-image">
            <?php 
            $image = $post['primary_image'] ?? $post['image'] ?? 'public/images/products/default.jpg';
            ?>
            <img src="<?= asset($image) ?>" alt="<?= e($post['name']) ?>">
        </div>
        <div class="article-content">
            <p><?= e($post['description']) ?></p>
        </div>
        <div class="article-actions">
            <a href="index.php?page=posts" class="btn">← Quay lại</a>
            <a href="index.php?page=menu" class="btn btn-primary">Xem thực đơn</a>
        </div>
    </div>
</div>

<style>
.article-card {
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    padding: 30px;
}

.article-card h1 {
    color: #333;
    margin-bottom: 15px;
}

.article-meta {
    margin-bottom: 20px;
}

.article-meta .category {
    background: #f0f0f0;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 13px;
    color: #666;
    margin-right: 15px;
}

.article-meta .price {
    font-size: 20px;
    font-weight: bold;
    color: #e74c3c;
}

.article-meta .price-old {
    font-size: 16px;
    color: #999;
    text-decoration: line-through;
    margin-right: 10px;
}

.article-image {
    margin-bottom: 20px;
    border-radius: 8px;
    overflow: hidden;
}

.article-image img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
}

.article-content {
    line-height: 1.8;
    color: #555;
    margin-bottom: 25px;
}

.article-actions {
    display: flex;
    gap: 15px;
}

.article-actions .btn {
    padding: 12px 25px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
}

.article-actions .btn-primary {
    background: #ff6b35;
    color: #fff;
}

.article-actions .btn-primary:hover {
    background: #e55a2b;
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>
