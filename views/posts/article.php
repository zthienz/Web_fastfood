<?php
$pageTitle = isset($post['title']) ? $post['title'] : 'Chi tiết bài đăng';
require_once 'views/layouts/header.php';
?>

<div class="container">
    <div class="article-wrapper">
        <article class="article-detail">
            <h1 class="article-title"><?php echo e($post['title']); ?></h1>
            
            <div class="article-meta">
                <span class="author">
                    <i class="fas fa-user"></i> <?php echo e($post['author_name'] ?? 'Admin'); ?>
                </span>
                <span class="date">
                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                </span>
                <span class="views">
                    <i class="fas fa-eye"></i> <?php echo number_format($post['views']); ?> lượt xem
                </span>
            </div>

            <?php if (!empty($post['featured_image'])): ?>
            <div class="article-image">
                <img src="<?php echo asset($post['featured_image']); ?>" alt="<?php echo e($post['title']); ?>">
            </div>
            <?php endif; ?>

            <div class="article-content">
                <?php echo nl2br(e($post['content'])); ?>
            </div>

            <div class="article-footer">
                <a href="index.php?page=posts" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </article>
    </div>
</div>

<style>
.article-wrapper {
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 20px;
}

.article-detail {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    padding: 40px;
}

.article-title {
    font-size: 28px;
    font-weight: 700;
    color: #c8102e;
    margin: 0 0 20px 0;
    line-height: 1.3;
}

.article-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.article-meta span {
    font-size: 14px;
    color: #666;
}

.article-meta i {
    margin-right: 6px;
    color: #c8102e;
}

.article-image {
    margin-bottom: 25px;
    border-radius: 8px;
    overflow: hidden;
}

.article-image img {
    width: 100%;
    height: auto;
    max-height: 500px;
    object-fit: cover;
}

.article-content {
    font-size: 16px;
    line-height: 1.8;
    color: #444;
}

.article-content p {
    margin-bottom: 15px;
}

.article-footer {
    margin-top: 40px;
    padding-top: 25px;
    border-top: 1px solid #eee;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #c8102e;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.3s ease;
}

.btn-back:hover {
    background: #a00d25;
    color: #fff;
}

@media (max-width: 768px) {
    .article-wrapper {
        padding: 20px 15px;
    }
    
    .article-detail {
        padding: 25px 20px;
    }
    
    .article-title {
        font-size: 22px;
    }
    
    .article-meta {
        gap: 12px;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>
