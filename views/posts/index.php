<?php
$pageTitle = 'Bài đăng';
require_once 'views/layouts/header.php';
?>

<div class="container">
    <div class="posts-grid">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <a href="index.php?page=post&amp;id=<?php echo (int)$post['id']; ?>" class="post-link">
                        <div class="post-image">
                            <?php 
                            $image = !empty($post['featured_image']) ? $post['featured_image'] : 'public/images/products/default.jpg';
                            ?>
                            <img src="<?php echo asset($image); ?>" alt="<?php echo e($post['title']); ?>">
                        </div>
                        <div class="post-content">
                            <h3 class="post-title"><?php echo e($post['title']); ?></h3>
                            <p class="post-excerpt"><?php echo e($post['excerpt'] ?? mb_substr(strip_tags($post['content']), 0, 100)); ?>...</p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-posts">Chưa có bài đăng nào.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.posts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    padding: 40px 0;
}

.post-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: box-shadow 0.3s ease;
}

.post-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.post-link {
    text-decoration: none;
    display: block;
}

.post-image {
    width: 100%;
    height: 220px;
    overflow: hidden;
}

.post-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-card:hover .post-image img {
    transform: scale(1.05);
}

.post-content {
    padding: 20px;
}

.post-title {
    font-size: 18px;
    font-weight: 700;
    color: #c8102e;
    margin: 0 0 12px 0;
    line-height: 1.4;
    text-transform: uppercase;
}

.post-excerpt {
    font-size: 14px;
    color: #555;
    line-height: 1.6;
    margin: 0;
}

.no-posts {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #888;
    font-size: 16px;
}

@media (max-width: 992px) {
    .posts-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (max-width: 576px) {
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 20px 0;
    }
    
    .post-image {
        height: 180px;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>
