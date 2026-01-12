<div class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Administrator</h2>
    </div>
    
    <nav class="sidebar-nav">
        <a href="index.php?page=admin" class="nav-item <?= (!isset($_GET['section']) ? 'active' : '') ?>">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
            <span>Tổng quan</span>
        </a>
        
        <a href="index.php?page=admin&section=users" class="nav-item <?= (($_GET['section'] ?? '') === 'users' ? 'active' : '') ?>">
            <span class="nav-icon"><i class="fas fa-users"></i></span>
            <span>Quản lý khách hàng</span>
        </a>
        
        <a href="index.php?page=admin&section=products" class="nav-item <?= (($_GET['section'] ?? '') === 'products' ? 'active' : '') ?>">
            <span class="nav-icon"><i class="fas fa-utensils"></i></span>
            <span>Quản lý sản phẩm</span>
        </a>
        
        <a href="index.php?page=admin&section=orders" class="nav-item <?= (($_GET['section'] ?? '') === 'orders' ? 'active' : '') ?>">
            <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span>
            <span>Quản lý đơn hàng</span>
        </a>
        
        <a href="index.php?page=admin&section=posts" class="nav-item <?= (($_GET['section'] ?? '') === 'posts' ? 'active' : '') ?>">
            <span class="nav-icon"><i class="fas fa-newspaper"></i></span>
            <span>Quản lý bài viết</span>
        </a>
        
        <a href="index.php?page=admin&section=contacts" class="nav-item <?= (($_GET['section'] ?? '') === 'contacts' ? 'active' : '') ?>">
            <span class="nav-icon"><i class="fas fa-envelope"></i></span>
            <span>Quản lý liên hệ</span>
        </a>
        
        <a href="index.php?page=admin&section=revenue" class="nav-item <?= (($_GET['section'] ?? '') === 'revenue' ? 'active' : '') ?>">
            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
            <span>Tổng doanh thu</span>
        </a>
        
        <a href="index.php?page=home" class="nav-item" target="_blank">
            <span class="nav-icon"><i class="fas fa-external-link-alt"></i></span>
            <span>Xem trang chủ</span>
        </a>
        
        <a href="index.php?page=logout" class="nav-item logout">
            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
            <span>Đăng xuất</span>
        </a>
    </nav>
</div>
