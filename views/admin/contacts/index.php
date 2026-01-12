<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý liên hệ - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <!-- Flash Messages -->
            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= getFlash('success') ?>
                </div>
            <?php endif; ?>
            
            <?php if (hasFlash('error')): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= getFlash('error') ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <h2>Quản lý liên hệ</h2>
                <div class="header-actions">
                    <span class="total-count">Tổng: <?= $total ?> liên hệ</span>
                </div>
            </div>

            <!-- Thống kê -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon new">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['new_count'] ?></h3>
                        <p>Mới</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon read">
                        <i class="fas fa-envelope-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['read_count'] ?></h3>
                        <p>Đã đọc</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon replied">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['replied_count'] ?></h3>
                        <p>Đã phản hồi</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['total'] ?></h3>
                        <p>Tổng cộng</p>
                    </div>
                </div>
            </div>

            <!-- Bộ lọc -->
            <div class="filters">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="section" value="contacts">
                    
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Tìm kiếm theo tên, email, chủ đề..." 
                               value="<?= e($_GET['search'] ?? '') ?>" class="form-control">
                    </div>
                    
                    <div class="filter-group">
                        <select name="status" class="form-control">
                            <option value="">Tất cả trạng thái</option>
                            <option value="new" <?= ($_GET['status'] ?? '') === 'new' ? 'selected' : '' ?>>Mới</option>
                            <option value="read" <?= ($_GET['status'] ?? '') === 'read' ? 'selected' : '' ?>>Đã đọc</option>
                            <option value="replied" <?= ($_GET['status'] ?? '') === 'replied' ? 'selected' : '' ?>>Đã phản hồi</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Lọc
                    </button>
                    
                    <a href="index.php?page=admin&section=contacts" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Đặt lại
                    </a>
                </form>
            </div>

            <!-- Danh sách liên hệ -->
            <div class="table-container">
                <?php if (!empty($contacts)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Người gửi</th>
                                <th>Email</th>
                                <th>Chủ đề</th>
                                <th>Trạng thái</th>
                                <th>Ngày gửi</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr class="<?= $contact['status'] === 'new' ? 'new-contact' : '' ?>">
                                    <td><?= $contact['id'] ?></td>
                                    <td>
                                        <div class="contact-info">
                                            <strong><?= e($contact['name']) ?></strong>
                                            <?php if ($contact['phone']): ?>
                                                <br><small><?= e($contact['phone']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= e($contact['email']) ?></td>
                                    <td>
                                        <?php if ($contact['subject']): ?>
                                            <span class="subject-tag"><?= e($contact['subject']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Không có</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $contact['status'] ?>">
                                            <?php
                                            $statusText = [
                                                'new' => 'Mới',
                                                'read' => 'Đã đọc',
                                                'replied' => 'Đã phản hồi'
                                            ];
                                            echo $statusText[$contact['status']];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            <?= date('d/m/Y', strtotime($contact['created_at'])) ?>
                                            <br><small><?= date('H:i', strtotime($contact['created_at'])) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="index.php?page=admin&section=contacts&action=show&id=<?= $contact['id'] ?>" 
                                               class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Dropdown thay đổi trạng thái -->
                                            <div class="status-dropdown">
                                                <select onchange="updateStatus(<?= $contact['id'] ?>, this.value)" 
                                                        class="status-select status-<?= $contact['status'] ?>">
                                                    <option value="new" <?= $contact['status'] === 'new' ? 'selected' : '' ?>>Mới</option>
                                                    <option value="read" <?= $contact['status'] === 'read' ? 'selected' : '' ?>>Đã đọc</option>
                                                    <option value="replied" <?= $contact['status'] === 'replied' ? 'selected' : '' ?>>Đã phản hồi</option>
                                                </select>
                                            </div>
                                            
                                            <button onclick="deleteContact(<?= $contact['id'] ?>)" 
                                                    class="btn btn-sm btn-delete" title="Xóa liên hệ">
                                                <i class="fas fa-trash-alt"></i>
                                                <span>Xóa</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Phân trang -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php
                            $currentPage = $page;
                            $queryParams = $_GET;
                            
                            for ($i = 1; $i <= $totalPages; $i++):
                                $queryParams['p'] = $i;
                                $url = 'index.php?' . http_build_query($queryParams);
                                $activeClass = ($i == $currentPage) ? 'active' : '';
                            ?>
                                <a href="<?= $url ?>" class="page-link <?= $activeClass ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Chưa có liên hệ nào</h3>
                        <p>Các liên hệ từ khách hàng sẽ hiển thị tại đây.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Form xóa ẩn -->
    <form id="deleteForm" method="POST" action="index.php?page=admin&section=contacts&action=delete" style="display: none;">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <!-- Form cập nhật trạng thái ẩn -->
    <form id="updateStatusForm" method="POST" action="index.php?page=admin&section=contacts&action=updateStatus" style="display: none;">
        <input type="hidden" name="id" id="statusId">
        <input type="hidden" name="status" id="statusValue">
    </form>

    <script>
    function deleteContact(id) {
        if (confirm('Bạn có chắc chắn muốn xóa liên hệ này?')) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }
    }

    function updateStatus(id, status) {
        if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái liên hệ này?')) {
            document.getElementById('statusId').value = id;
            document.getElementById('statusValue').value = status;
            document.getElementById('updateStatusForm').submit();
        }
    }
    </script>

    <style>
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .stat-icon.new { background: #e74c3c; }
    .stat-icon.read { background: #f39c12; }
    .stat-icon.replied { background: #27ae60; }
    .stat-icon.total { background: #3498db; }

    .stat-info h3 {
        font-size: 28px;
        font-weight: bold;
        margin: 0;
        color: #2c3e50;
    }

    .stat-info p {
        margin: 0;
        color: #7f8c8d;
        font-size: 14px;
    }

    .new-contact {
        background-color: #fff3cd !important;
    }

    .contact-info strong {
        color: #2c3e50;
    }

    .subject-tag {
        background: #e9ecef;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        color: #495057;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-new {
        background: #fee;
        color: #e74c3c;
    }

    .status-read {
        background: #fff3cd;
        color: #f39c12;
    }

    .status-replied {
        background: #d4edda;
        color: #27ae60;
    }

    .date-info {
        font-size: 14px;
    }

    .date-info small {
        color: #6c757d;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
        border: 1px solid #dc3545;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-delete:hover {
        background: #c82333;
        border-color: #bd2130;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .btn-delete i {
        font-size: 11px;
    }

    .status-dropdown {
        position: relative;
    }

    .status-select {
        padding: 4px 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        cursor: pointer;
        min-width: 90px;
    }

    .status-select.status-new {
        background: #fee;
        color: #e74c3c;
        border-color: #e74c3c;
    }

    .status-select.status-read {
        background: #fff3cd;
        color: #f39c12;
        border-color: #f39c12;
    }

    .status-select.status-replied {
        background: #d4edda;
        color: #27ae60;
        border-color: #27ae60;
    }

    .status-select:hover {
        opacity: 0.8;
    }

    .status-select option {
        background: white;
        color: #333;
        font-weight: normal;
        text-transform: none;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        color: #dee2e6;
    }

    .empty-state h3 {
        margin: 0 0 10px 0;
        color: #495057;
    }
    </style>
</body>
</html>