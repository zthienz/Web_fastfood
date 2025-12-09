<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <!-- Nút thêm mới -->
            <div class="action-bar">
                <a href="index.php?page=admin&section=products&action=create" class="btn btn-success">
                    ➕ Thêm sản phẩm mới
                </a>
            </div>
            
            <!-- Bộ lọc -->
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="section" value="products">
                    
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." 
                           value="<?= e($_GET['search'] ?? '') ?>" class="search-input">
                    
                    <select name="category" class="filter-select">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="status" class="filter-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang bán</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
                        <option value="out_of_stock" <?= ($_GET['status'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Hết hàng</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Lọc</button>
                    <a href="index.php?page=admin&section=products" class="btn btn-secondary">Xóa lọc</a>
                </form>
            </div>
            
            <!-- Bảng danh sách -->
            <div class="table-section">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có sản phẩm nào</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td>
                                    <img src="<?= getImageUrl($product['primary_image']) ?>" 
                                         alt="<?= e($product['name']) ?>" 
                                         class="product-thumb">
                                </td>
                                <td>
                                    <strong><?= e($product['name']) ?></strong>
                                    <?php if ($product['is_featured']): ?>
                                    <span class="badge badge-featured">⭐ Nổi bật</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($product['category_name']) ?></td>
                                <td>
                                    <?php if ($product['sale_price']): ?>
                                        <span class="price-sale"><?= formatMoney($product['sale_price']) ?></span>
                                        <span class="price-old"><?= formatMoney($product['price']) ?></span>
                                    <?php else: ?>
                                        <?= formatMoney($product['price']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="stock-badge <?= $product['stock_quantity'] > 0 ? 'in-stock' : 'out-stock' ?>">
                                        <?= $product['stock_quantity'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'active' => 'status-active',
                                        'inactive' => 'status-inactive',
                                        'out_of_stock' => 'status-out-stock'
                                    ];
                                    $statusText = [
                                        'active' => 'Đang bán',
                                        'inactive' => 'Ngừng bán',
                                        'out_of_stock' => 'Hết hàng'
                                    ];
                                    ?>
                                    <span class="status-badge <?= $statusClass[$product['status']] ?>">
                                        <?= $statusText[$product['status']] ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <a href="index.php?page=admin&section=products&action=edit&id=<?= $product['id'] ?>" 
                                       class="btn-icon btn-edit" title="Sửa">✏️</a>
                                    
                                    <form method="POST" action="index.php?page=admin&section=products&action=delete" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Xóa" 
                                                onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?page=admin&section=products&p=<?= $i ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>" 
                       class="page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
