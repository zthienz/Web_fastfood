<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa bài viết - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <div class="form-header">
                <h2>Sửa bài viết: <?= e($post['title']) ?></h2>
                <a href="index.php?page=admin&section=posts" class="btn btn-secondary">← Quay lại</a>
            </div>
            
            <form method="POST" action="index.php?page=admin&section=posts&action=update" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                
                <div class="form-group">
                    <label>Tiêu đề <span class="required">*</span></label>
                    <input type="text" name="title" value="<?= e($post['title']) ?>" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Mô tả ngắn</label>
                    <textarea name="excerpt" rows="3" class="form-control"><?= e($post['excerpt']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Phân loại bài viết <span class="required">*</span></label>
                    <select name="category" required class="form-control">
                        <option value="">-- Chọn phân loại --</option>
                        <option value="ĐÁNH GIÁ SẢN PHẨM" <?= ($post['category'] ?? '') === 'ĐÁNH GIÁ SẢN PHẨM' ? 'selected' : '' ?>>ĐÁNH GIÁ SẢN PHẨM</option>
                        <option value="HƯỚNG DẪN" <?= ($post['category'] ?? '') === 'HƯỚNG DẪN' ? 'selected' : '' ?>>HƯỚNG DẪN</option>
                        <option value="TIN TỨC" <?= ($post['category'] ?? '') === 'TIN TỨC' ? 'selected' : '' ?>>TIN TỨC</option>
                        <option value="KHUYẾN MÃI" <?= ($post['category'] ?? '') === 'KHUYẾN MÃI' ? 'selected' : '' ?>>KHUYẾN MÃI</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nội dung <span class="required">*</span></label>
                    <textarea name="content" rows="15" required class="form-control"><?= e($post['content']) ?></textarea>
                    <small class="form-hint">Hỗ trợ HTML</small>
                </div>
                
                <?php if ($post['featured_image']): ?>
                <div class="form-group">
                    <label>Ảnh đại diện hiện tại</label>
                    <div class="current-image">
                        <img src="<?= asset($post['featured_image']) ?>" alt="" style="max-width: 300px;">
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Thay đổi ảnh đại diện</label>
                    <input type="file" name="featured_image" accept="image/*" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
                        <option value="hidden" <?= $post['status'] === 'hidden' ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">💾 Cập nhật</button>
                    <a href="index.php?page=admin&section=posts" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
