<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <div class="form-header">
                <h2>Sửa sản phẩm: <?= e($product['name']) ?></h2>
                <a href="index.php?page=admin&section=products" class="btn btn-secondary">← Quay lại</a>
            </div>
            
            <form method="POST" action="index.php?page=admin&section=products&action=update" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên sản phẩm <span class="required">*</span></label>
                        <input type="text" name="name" value="<?= e($product['name']) ?>" required class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Danh mục <span class="required">*</span></label>
                        <select name="category_id" required class="form-control">
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" rows="4" class="form-control"><?= e($product['description']) ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Giá gốc <span class="required">*</span></label>
                        <input type="number" name="price" value="<?= $product['price'] ?>" required min="0" step="1000" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Giá khuyến mãi</label>
                        <input type="number" name="sale_price" value="<?= $product['sale_price'] ?>" min="0" step="1000" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Số lượng tồn kho <span class="required">*</span></label>
                        <input type="number" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" required min="0" class="form-control">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Đang bán</option>
                            <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
                            <option value="out_of_stock" <?= $product['status'] === 'out_of_stock' ? 'selected' : '' ?>>Hết hàng</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
                            Sản phẩm nổi bật
                        </label>
                    </div>
                </div>
                
                <!-- Hiển thị ảnh hiện có -->
                <?php if (!empty($images)): ?>
                <div class="form-group">
                    <label>Hình ảnh hiện có</label>
                    <div class="image-gallery">
                        <?php foreach ($images as $img): ?>
                        <div class="image-item">
                            <img src="<?= asset($img['image_url']) ?>" alt="">
                            <?php if ($img['is_primary']): ?>
                            <span class="primary-badge">Ảnh chính</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Thêm hình ảnh mới</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="form-control">
                    <small class="form-hint">Có thể chọn nhiều ảnh.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">💾 Cập nhật</button>
                    <a href="index.php?page=admin&section=products" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
