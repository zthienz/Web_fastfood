<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'FastFood - Ăn nhanh, ngon miệng' ?></title>
    <link rel="stylesheet" href="<?= asset('public/css/style.css') ?>?v=<?= time() ?>">
</head>
<body>
    <header>
        <div class="logo">🍔 FastFood</div>
        <nav>
            <a href="index.php">Trang chủ</a>
            <a href="index.php?page=menu">Thực đơn</a>
            <a href="index.php?page=post&action=show&id=1">Bài viết</a>
            <?php if (isLoggedIn()): ?>
                <a href="index.php?page=favorites" class="favorites-link">Yêu thích</a>
            <?php endif; ?>
            <a href="index.php?page=cart">Giỏ hàng</a>
            <a href="#">Liên hệ</a>
        </nav>
        <div class="nav-right">
            <?php if (isLoggedIn()): ?>
                <div class="user-menu">
                    <button class="user-btn" onclick="toggleUserMenu()">
                        <span class="user-avatar">
                            <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
                        </span>
                        <span class="user-name"><?= e($_SESSION['full_name'] ?? 'User') ?></span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="white">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="index.php?page=profile">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            Thông tin tài khoản
                        </a>
                        <a href="index.php?page=orders">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                            </svg>
                            Đơn hàng của tôi
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="index.php?page=logout" class="logout-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                            </svg>
                            Đăng xuất
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="index.php?page=register">Đăng ký</a>
                <a href="index.php?page=login">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </header>

    <?php 
    // Hiển thị flash messages
    if ($error = getFlash('error')): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success = getFlash('success')): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <script>
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Đóng dropdown khi click bên ngoài
        window.onclick = function(event) {
            if (!event.target.matches('.user-btn') && !event.target.closest('.user-btn')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        }
    </script>
