<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bài viết - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <!-- Nút thêm mới -->
            <div class="action-bar">
                <a href="index.php?page=admin&section=posts&action=create" class="btn btn-success">
                    ➕ Thêm bài viết mới
                </a>
            </div>
            
            <!-- Bộ lọc -->
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="section" value="posts">
                    
                    <input type="text" name="search" placeholder="Tìm kiếm bài viết..." 
                           value="<?= e($_GET['search'] ?? '') ?>" class="search-input">
                    
                    <select name="status" class="filter-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="published" <?= ($_GET['status'] ?? '') === 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
                        <option value="draft" <?= ($_GET['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
                        <option value="hidden" <?= ($_GET['status'] ?? '') === 'hidden' ? 'selected' : '' ?>>Đã ẩn</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Lọc</button>
                    <a href="index.php?page=admin&section=posts" class="btn btn-secondary">Xóa lọc</a>
                </form>
            </div>
            
            <!-- Bảng danh sách -->
            <div class="table-section">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Tác giả</th>
                            <th>Lượt xem</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có bài viết nào</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?= $post['id'] ?></td>
                                <td>
                                    <strong><?= e($post['title']) ?></strong>
                                    <?php if ($post['excerpt']): ?>
                                    <br><small class="text-muted"><?= e(substr($post['excerpt'], 0, 80)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($post['author_name']) ?></td>
                                <td><?= number_format($post['views']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'published' => 'status-active',
                                        'draft' => 'status-draft',
                                        'hidden' => 'status-inactive'
                                    ];
                                    $statusText = [
                                        'published' => 'Đã xuất bản',
                                        'draft' => 'Bản nháp',
                                        'hidden' => 'Đã ẩn'
                                    ];
                                    ?>
                                    <span class="status-badge <?= $statusClass[$post['status']] ?>">
                                        <?= $statusText[$post['status']] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></td>
                                <td class="action-buttons">
                                    <a href="index.php?page=admin&section=posts&action=edit&id=<?= $post['id'] ?>" 
                                       class="btn-icon btn-edit" title="Sửa">✏️</a>
                                    
                                    <form method="POST" action="index.php?page=admin&section=posts&action=delete" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Xóa" 
                                                onclick="return confirm('Bạn có chắc muốn xóa bài viết này?')">🗑️</button>
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
                    <a href="index.php?page=admin&section=posts&p=<?= $i ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>" 
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
