<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết liên hệ - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <div class="page-header">
                <h2>Chi tiết liên hệ #<?= $contact['id'] ?></h2>
                <div class="header-actions">
                    <a href="index.php?page=admin&section=contacts" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="contact-detail-container">
                <div class="contact-detail-main">
                    <!-- Thông tin liên hệ -->
                    <div class="contact-card">
                        <div class="contact-header">
                            <div class="contact-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="contact-basic-info">
                                <h3><?= e($contact['name']) ?></h3>
                                <p><?= e($contact['email']) ?></p>
                                <?php if ($contact['phone']): ?>
                                    <p><i class="fas fa-phone"></i> <?= e($contact['phone']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="contact-status">
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
                            </div>
                        </div>

                        <?php if ($contact['subject']): ?>
                            <div class="contact-subject">
                                <h4>Chủ đề:</h4>
                                <span class="subject-tag"><?= e($contact['subject']) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="contact-message">
                            <h4>Nội dung:</h4>
                            <div class="message-content">
                                <?= nl2br(e($contact['message'])) ?>
                            </div>
                        </div>

                        <div class="contact-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Ngày gửi: <?= date('d/m/Y H:i', strtotime($contact['created_at'])) ?></span>
                            </div>
                            <?php if ($contact['updated_at'] !== $contact['created_at']): ?>
                                <div class="meta-item">
                                    <i class="fas fa-edit"></i>
                                    <span>Cập nhật: <?= date('d/m/Y H:i', strtotime($contact['updated_at'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="contact-detail-sidebar">
                    <!-- Cập nhật trạng thái -->
                    <div class="action-card">
                        <h4>Cập nhật trạng thái</h4>
                        <form method="POST" action="index.php?page=admin&section=contacts&action=updateStatus">
                            <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                            <div class="form-group">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="new" <?= $contact['status'] === 'new' ? 'selected' : '' ?>>Mới</option>
                                    <option value="read" <?= $contact['status'] === 'read' ? 'selected' : '' ?>>Đã đọc</option>
                                    <option value="replied" <?= $contact['status'] === 'replied' ? 'selected' : '' ?>>Đã phản hồi</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Thao tác nhanh -->
                    <div class="action-card">
                        <h4>Thao tác</h4>
                        <div class="quick-actions">
                            <a href="mailto:<?= e($contact['email']) ?>?subject=Re: <?= e($contact['subject'] ?? 'Liên hệ') ?>" 
                               class="btn btn-primary btn-block">
                                <i class="fas fa-reply"></i> Phản hồi qua Email
                            </a>
                            
                            <?php if ($contact['phone']): ?>
                                <a href="tel:<?= e($contact['phone']) ?>" class="btn btn-success btn-block">
                                    <i class="fas fa-phone"></i> Gọi điện
                                </a>
                            <?php endif; ?>
                            
                            <button onclick="deleteContact(<?= $contact['id'] ?>)" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Xóa liên hệ
                            </button>
                        </div>
                    </div>

                    <!-- Thông tin thêm -->
                    <div class="info-card">
                        <h4>Thông tin thêm</h4>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">ID:</span>
                                <span class="info-value">#<?= $contact['id'] ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Trạng thái:</span>
                                <span class="info-value">
                                    <span class="status-badge status-<?= $contact['status'] ?>">
                                        <?= $statusText[$contact['status']] ?>
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ngày tạo:</span>
                                <span class="info-value"><?= date('d/m/Y H:i:s', strtotime($contact['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form xóa ẩn -->
    <form id="deleteForm" method="POST" action="index.php?page=admin&section=contacts&action=delete" style="display: none;">
        <input type="hidden" name="id" value="<?= $contact['id'] ?>">
    </form>

    <script>
    function deleteContact(id) {
        if (confirm('Bạn có chắc chắn muốn xóa liên hệ này?')) {
            document.getElementById('deleteForm').submit();
        }
    }
    </script>

    <style>
    .contact-detail-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }

    .contact-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .contact-header {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .contact-avatar {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }

    .contact-basic-info {
        flex: 1;
    }

    .contact-basic-info h3 {
        margin: 0 0 5px 0;
        font-size: 24px;
        font-weight: 600;
    }

    .contact-basic-info p {
        margin: 0;
        opacity: 0.9;
        font-size: 14px;
    }

    .contact-subject {
        padding: 20px 30px;
        border-bottom: 1px solid #e9ecef;
    }

    .contact-subject h4 {
        margin: 0 0 10px 0;
        color: #495057;
        font-size: 16px;
    }

    .subject-tag {
        background: #e9ecef;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        color: #495057;
        font-weight: 500;
    }

    .contact-message {
        padding: 30px;
    }

    .contact-message h4 {
        margin: 0 0 15px 0;
        color: #495057;
        font-size: 16px;
    }

    .message-content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #667eea;
        line-height: 1.6;
        color: #495057;
    }

    .contact-meta {
        padding: 20px 30px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        color: #6c757d;
        font-size: 14px;
    }

    .meta-item:last-child {
        margin-bottom: 0;
    }

    .meta-item i {
        color: #667eea;
        width: 16px;
    }

    .action-card, .info-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 25px;
        margin-bottom: 20px;
    }

    .action-card h4, .info-card h4 {
        margin: 0 0 20px 0;
        color: #495057;
        font-size: 18px;
        font-weight: 600;
    }

    .quick-actions .btn {
        margin-bottom: 10px;
    }

    .quick-actions .btn:last-child {
        margin-bottom: 0;
    }

    .btn-block {
        width: 100%;
        display: block;
        text-align: center;
    }

    .info-list {
        space-y: 15px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
    }

    .info-value {
        color: #6c757d;
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

    @media (max-width: 768px) {
        .contact-detail-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .contact-header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }
        
        .contact-avatar {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }
    }
    </style>
</body>
</html>