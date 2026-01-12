<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                        <div class="image-item" data-image-id="<?= $img['id'] ?>">
                            <img src="<?= asset($img['image_url']) ?>" alt="">
                            <div class="image-controls">
                                <?php if ($img['is_primary']): ?>
                                <span class="primary-badge">Ảnh chính</span>
                                <?php else: ?>
                                <button type="button" class="btn btn-sm btn-primary set-primary-btn" 
                                        data-image-id="<?= $img['id'] ?>" 
                                        data-product-id="<?= $product['id'] ?>">
                                    Đặt làm ảnh chính
                                </button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-danger delete-image-btn" 
                                        data-image-id="<?= $img['id'] ?>" 
                                        data-product-id="<?= $product['id'] ?>"
                                        data-is-primary="<?= $img['is_primary'] ? '1' : '0' ?>">
                                    🗑️ Xóa
                                </button>
                            </div>
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

    <script>
    // Xử lý xóa ảnh
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-image-btn')) {
            const imageId = e.target.dataset.imageId;
            const productId = e.target.dataset.productId;
            const isPrimary = e.target.dataset.isPrimary === '1';
            
            if (isPrimary) {
                if (!confirm('Đây là ảnh chính của sản phẩm. Bạn có chắc chắn muốn xóa?')) {
                    return;
                }
            } else {
                if (!confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
                    return;
                }
            }
            
            // Gửi request xóa ảnh
            fetch('index.php?page=admin&section=products&action=delete_image', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `image_id=${imageId}&product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Xóa element khỏi DOM
                    e.target.closest('.image-item').remove();
                    
                    // Hiển thị thông báo
                    alert('Xóa ảnh thành công!');
                    
                    // Reload trang để cập nhật ảnh chính mới nếu cần
                    if (isPrimary) {
                        location.reload();
                    }
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xóa ảnh'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa ảnh');
            });
        }
        
        // Xử lý đặt ảnh chính
        if (e.target.classList.contains('set-primary-btn')) {
            const imageId = e.target.dataset.imageId;
            const productId = e.target.dataset.productId;
            
            if (!confirm('Đặt ảnh này làm ảnh chính?')) {
                return;
            }
            
            // Gửi request đặt ảnh chính
            fetch('index.php?page=admin&section=products&action=set_primary_image', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `image_id=${imageId}&product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đặt ảnh chính thành công!');
                    location.reload(); // Reload để cập nhật giao diện
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể đặt ảnh chính'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi đặt ảnh chính');
            });
        }
    });
    </script>
</body>
</html>
