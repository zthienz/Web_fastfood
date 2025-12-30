<?php
$pageTitle = 'Blog';
require_once 'views/layouts/header.php';
?>

<div class="blog-container">
    <div class="container">
        <div class="blog-header">
            <h1 class="blog-title">Blog</h1>
            <p class="blog-subtitle">Khám phá những bài viết thú vị về ẩm thực và cuộc sống</p>
        </div>

        <div class="blog-grid">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <article class="blog-card">
                        <a href="index.php?page=post&id=<?php echo (int)$post['id']; ?>" class="blog-card-link">
                            <div class="blog-card-image">
                                <?php 
                                $image = !empty($post['featured_image']) ? $post['featured_image'] : 'public/images/products/default.jpg';
                                ?>
                                <img src="<?php echo asset($image); ?>" alt="<?php echo e($post['title']); ?>">
                                <div class="blog-card-overlay"></div>
                            </div>
                            
                            <div class="blog-card-content">
                                <?php if (!empty($post['category'])): ?>
                                    <div class="blog-category">
                                        <span class="category-tag"><?php echo e($post['category']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="blog-card-title"><?php echo e($post['title']); ?></h3>
                                
                                <p class="blog-card-excerpt">
                                    <?php echo e($post['excerpt'] ?? mb_substr(strip_tags($post['content']), 0, 120)); ?>...
                                </p>
                                
                                <div class="blog-card-meta">
                                    <span class="blog-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <?php echo date('d/m/Y', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-posts">
                    <i class="fas fa-newspaper"></i>
                    <h3>Chưa có bài viết nào</h3>
                    <p>Hãy quay lại sau để đọc những bài viết mới nhất!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.blog-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 40px 0 80px;
}

.blog-header {
    text-align: center;
    margin-bottom: 50px;
}

.blog-title {
    font-size: 42px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 15px 0;
    letter-spacing: -1px;
}

.blog-subtitle {
    font-size: 18px;
    color: #7f8c8d;
    margin: 0;
    font-weight: 400;
}

.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.blog-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
}

.blog-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.blog-card-link {
    text-decoration: none;
    display: block;
    color: inherit;
}

.blog-card-image {
    position: relative;
    width: 100%;
    height: 240px;
    overflow: hidden;
}

.blog-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.blog-card:hover .blog-card-image img {
    transform: scale(1.1);
}

.blog-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.1) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.blog-card:hover .blog-card-overlay {
    opacity: 1;
}

.blog-card-content {
    padding: 25px;
}

.blog-category {
    margin-bottom: 15px;
}

.category-tag {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.blog-card-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 15px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-card-excerpt {
    font-size: 14px;
    color: #7f8c8d;
    line-height: 1.6;
    margin: 0 0 20px 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 15px;
    border-top: 1px solid #ecf0f1;
}

.blog-date {
    font-size: 13px;
    color: #95a5a6;
    display: flex;
    align-items: center;
    gap: 6px;
}

.blog-date i {
    color: #3498db;
}

.no-posts {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    color: #7f8c8d;
}

.no-posts i {
    font-size: 64px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

.no-posts h3 {
    font-size: 24px;
    color: #2c3e50;
    margin: 0 0 10px 0;
}

.no-posts p {
    font-size: 16px;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .blog-container {
        padding: 20px 0 40px;
    }
    
    .blog-title {
        font-size: 32px;
    }
    
    .blog-subtitle {
        font-size: 16px;
    }
    
    .blog-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 0 15px;
    }
    
    .blog-card-image {
        height: 200px;
    }
    
    .blog-card-content {
        padding: 20px;
    }
    
    .blog-card-title {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .blog-title {
        font-size: 28px;
    }
    
    .blog-card-content {
        padding: 15px;
    }
    
    .blog-card-title {
        font-size: 16px;
    }
    
    .blog-card-excerpt {
        font-size: 13px;
    }
}

/* Animation cho category tags */
.category-tag {
    position: relative;
    overflow: hidden;
}

.category-tag::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.blog-card:hover .category-tag::before {
    left: 100%;
}

/* Hiệu ứng loading skeleton */
.blog-card.loading {
    background: #f8f9fa;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>