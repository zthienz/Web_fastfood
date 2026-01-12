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
            <a href="index.php?page=posts">Bài viết</a>
            <a href="index.php?page=contact">Liên hệ</a>
            <?php if (isLoggedIn()): ?>
                <a href="index.php?page=favorites" class="favorites-link">Yêu thích</a>
            <?php endif; ?>
            <a href="index.php?page=cart" class="cart-link">
                Giỏ hàng
                <?php if (isLoggedIn() && !empty($_SESSION['cart'])): ?>
                    <span class="cart-badge"><?= array_sum($_SESSION['cart']) ?></span>
                <?php endif; ?>
            </a>
        </nav>
        <div class="nav-right">
            <?php if (isLoggedIn()): ?>
                <div class="user-menu">
                    <button class="user-btn" onclick="toggleUserMenu()">
                        <span class="user-avatar">
                            <?php 
                            // Lấy thông tin avatar từ database với cache busting
                            $stmt = Database::getInstance()->getConnection()->prepare("SELECT avatar FROM users WHERE id = ?");
                            $stmt->execute([$_SESSION['user_id']]);
                            $userAvatar = $stmt->fetch();
                            
                            if (!empty($userAvatar['avatar']) && file_exists('public/images/avatars/' . $userAvatar['avatar'])): ?>
                                <img src="public/images/avatars/<?= e($userAvatar['avatar']) ?>?v=<?= time() ?>" alt="Avatar" class="header-avatar-img">
                            <?php else: ?>
                                <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
                            <?php endif; ?>
                        </span>
                        <span class="user-name"><?= e($_SESSION['full_name'] ?? 'User') ?></span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="white">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <!-- Menu cho Admin -->
                            <a href="index.php?page=admin">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                Tổng quan
                            </a>
                            <a href="index.php?page=admin&section=users">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-1c0-2.66 5.33-4 8-4s8 1.34 8 4v1H4zM12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/>
                                </svg>
                                Quản lý khách hàng
                            </a>
                            <a href="index.php?page=admin&section=products">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.20-1.10-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13l1.47-1.47z"/>
                                </svg>
                                Quản lý sản phẩm
                            </a>
                            <a href="index.php?page=admin&section=orders">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 4V2C7 1.45 7.45 1 8 1h8c.55 0 1 .45 1 1v2h5v2h-2v13c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2V6H2V4h5zM9 3v1h6V3H9zm0 5v9h2V8H9zm4 0v9h2V8h-2z"/>
                                </svg>
                                Quản lý đơn hàng
                            </a>
                            <a href="index.php?page=admin&section=posts">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                                </svg>
                                Quản lý bài viết
                            </a>
                            <a href="index.php?page=admin&section=contacts">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                                Quản lý liên hệ
                            </a>
                            <a href="index.php?page=admin&section=revenue">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/>
                                </svg>
                                Tổng doanh thu
                            </a>
                            <a href="index.php?page=home" target="_blank">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/>
                                </svg>
                                Xem trang chủ
                            </a>
                        <?php else: ?>
                            <!-- Menu cho User thông thường -->
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
                        <?php endif; ?>
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
