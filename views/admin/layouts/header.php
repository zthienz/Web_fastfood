<div class="admin-header">
    <div class="header-left">
        <h1><?php
            $section = $_GET['section'] ?? 'dashboard';
            $titles = [
                'dashboard' => 'Tổng quan',
                'users' => 'Quản lý khách hàng',
                'products' => 'Quản lý sản phẩm',
                'orders' => 'Quản lý đơn hàng',
                'posts' => 'Quản lý bài viết',
                'revenue' => 'Tổng doanh thu'
            ];
            echo $titles[$section] ?? 'Quản trị hệ thống';
        ?></h1>
    </div>
    
    <div class="header-right">
        <div class="admin-user">
            <span class="admin-name"><?= e($_SESSION['full_name'] ?? 'Admin') ?></span>
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)) ?>
            </div>
        </div>
    </div>
</div>

<?php if ($message = getFlash('success')): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<?php if ($message = getFlash('error')): ?>
<div class="alert alert-error"><?= e($message) ?></div>
<?php endif; ?>
