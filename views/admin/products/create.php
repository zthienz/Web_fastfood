<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <div class="form-header">
                <h2>Thêm sản phẩm mới</h2>
                <a href="index.php?page=admin&section=products" class="btn btn-secondary">← Quay lại</a>
            </div>
            
            <form method="POST" action="index.php?page=admin&section=products&action=store" enctype="multipart/form-data" class="admin-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên sản phẩm <span class="required">*</span></label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Danh mục <span class="required">*</span></label>
                        <select name="category_id" required class="form-control">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" rows="4" class="form-control"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Giá gốc <span class="required">*</span></label>
                        <input type="number" name="price" required min="0" step="1000" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Giá khuyến mãi</label>
                        <input type="number" name="sale_price" min="0" step="1000" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Số lượng tồn kho <span class="required">*</span></label>
                        <input type="number" name="stock_quantity" required min="0" value="0" class="form-control">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="active">Đang bán</option>
                            <option value="inactive">Ngừng bán</option>
                            <option value="out_of_stock">Hết hàng</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1">
                            Sản phẩm nổi bật
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Hình ảnh sản phẩm</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="form-control">
                    <small class="form-hint">Có thể chọn nhiều ảnh. Ảnh đầu tiên sẽ là ảnh chính.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">💾 Lưu sản phẩm</button>
                    <a href="index.php?page=admin&section=products" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
