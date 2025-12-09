<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm bài viết - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <div class="form-header">
                <h2>Thêm bài viết mới</h2>
                <a href="index.php?page=admin&section=posts" class="btn btn-secondary">← Quay lại</a>
            </div>
            
            <form method="POST" action="index.php?page=admin&section=posts&action=store" enctype="multipart/form-data" class="admin-form">
                <div class="form-group">
                    <label>Tiêu đề <span class="required">*</span></label>
                    <input type="text" name="title" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Mô tả ngắn</label>
                    <textarea name="excerpt" rows="3" class="form-control" placeholder="Mô tả ngắn gọn về bài viết..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Nội dung <span class="required">*</span></label>
                    <textarea name="content" rows="15" required class="form-control"></textarea>
                    <small class="form-hint">Hỗ trợ HTML</small>
                </div>
                
                <div class="form-group">
                    <label>Ảnh đại diện</label>
                    <input type="file" name="featured_image" accept="image/*" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="draft">Bản nháp</option>
                        <option value="published">Xuất bản ngay</option>
                        <option value="hidden">Ẩn</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">💾 Lưu bài viết</button>
                    <a href="index.php?page=admin&section=posts" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
