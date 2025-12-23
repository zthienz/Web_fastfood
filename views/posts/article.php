<?php
$pageTitle = $post['title'] ?? 'Bài viết';
require_once 'views/layouts/header.php';
?>

<div class="container">
    <div class="article-card">
        <h1><?= e($post['title']) ?></h1>
        <div class="article-meta">
            <span class="price">Giá: <?= formatMoney($post['price'] ?? 0) ?></span>
        </div>
        <div class="article-image">
            <img src="<?= asset($post['image']) ?>" alt="<?= e($post['title']) ?>">
        </div>
        <div class="article-content">
            <p><?= e($post['description']) ?></p>
        </div>
        <a href="index.php?page=menu" class="btn">Xem thực đơn</a>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
