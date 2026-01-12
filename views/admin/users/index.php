<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách hàng - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <!-- Bộ lọc -->
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="section" value="users">
                    
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên, email, SĐT..." 
                           value="<?= e($_GET['search'] ?? '') ?>" class="search-input">
                    
                    <select name="role" class="filter-select">
                        <option value="">Tất cả vai trò</option>
                        <option value="customer" <?= ($_GET['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Khách hàng</option>
                        <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    
                    <select name="status" class="filter-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                        <option value="banned" <?= ($_GET['status'] ?? '') === 'banned' ? 'selected' : '' ?>>Bị khóa</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Lọc</button>
                    <a href="index.php?page=admin&section=users" class="btn btn-secondary">Xóa lọc</a>
                </form>
            </div>
            
            <!-- Bảng danh sách -->
            <div class="table-section">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có dữ liệu</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= e($user['full_name']) ?></td>
                                <td><?= e($user['email']) ?></td>
                                <td><?= e($user['phone'] ?? 'Chưa cập nhật') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'active' => 'status-active',
                                        'inactive' => 'status-inactive',
                                        'banned' => 'status-banned'
                                    ];
                                    $statusText = [
                                        'active' => 'Hoạt động',
                                        'inactive' => 'Không hoạt động',
                                        'banned' => 'Bị khóa'
                                    ];
                                    ?>
                                    <span class="status-badge <?= $statusClass[$user['status']] ?>">
                                        <?= $statusText[$user['status']] ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button onclick="viewUser(<?= $user['id'] ?>)" class="btn-icon btn-view" title="Xem chi tiết">👁️</button>
                                    
                                    <?php if ($user['status'] === 'active'): ?>
                                    <form method="POST" action="index.php?page=admin&section=users&action=update_status" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="status" value="banned">
                                        <button type="submit" class="btn-icon btn-ban" title="Khóa tài khoản" 
                                                onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">🔒</button>
                                    </form>
                                    <?php else: ?>
                                    <form method="POST" action="index.php?page=admin&section=users&action=update_status" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn-icon btn-unlock" title="Mở khóa">🔓</button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" action="index.php?page=admin&section=users&action=delete" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Xóa" 
                                                onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">🗑️</button>
                                    </form>
                                    <?php endif; ?>
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
                    <a href="index.php?page=admin&section=users&p=<?= $i ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['role']) ? '&role=' . $_GET['role'] : '' ?><?= isset($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>" 
                       class="page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function viewUser(userId) {
            // Có thể mở modal hoặc chuyển trang chi tiết
            alert('Xem chi tiết user ID: ' + userId);
        }
    </script>
</body>
</html>
