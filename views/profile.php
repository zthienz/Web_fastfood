<?php 
if (!isLoggedIn()) {
    redirect('index.php?page=login');
}

$pageTitle = 'Thông tin tài khoản - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <div class="profile-container">
        <h2>Thông tin tài khoản</h2>
        
        <div class="profile-card">
            <div class="profile-avatar">
                <div class="avatar-circle">
                    <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
                </div>
            </div>
            
            <div class="profile-info">
                <div class="info-row">
                    <label>Họ và tên:</label>
                    <span><?= e($user['full_name'] ?? $_SESSION['full_name'] ?? 'Chưa cập nhật') ?></span>
                </div>
                
                <div class="info-row">
                    <label>Email:</label>
                    <span><?= e($user['email'] ?? $_SESSION['user_email'] ?? 'Chưa cập nhật') ?></span>
                </div>
                
                <div class="info-row">
                    <label>Số điện thoại:</label>
                    <span><?= e($user['phone'] ?? 'Chưa cập nhật') ?></span>
                </div>
                
                <div class="info-row">
                    <label>Địa chỉ:</label>
                    <span><?= e($user['address'] ?? 'Chưa cập nhật') ?></span>
                </div>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="info-row">
                    <label>Vai trò:</label>
                    <span class="badge badge-admin">
                        👑 Quản trị viên
                    </span>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['login_method']) && $_SESSION['login_method'] === 'google'): ?>
                    <div class="info-row">
                        <label>Phương thức đăng nhập:</label>
                        <span class="badge badge-google">
                            <svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 6.75c1.63 0 3.06.56 4.21 1.65l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84C6.71 7.41 9.14 5.75 12 5.75z" fill="#EA4335"/>
                            </svg>
                            Google Account
                        </span>
                    </div>
                <?php else: ?>
                    <div class="info-row">
                        <label>Phương thức đăng nhập:</label>
                        <span class="badge badge-normal">Tài khoản thường</span>
                    </div>
                <?php endif; ?>
                
                <div class="info-row">
                    <label>Ngày đăng ký:</label>
                    <span><?= isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?></span>
                </div>
            </div>
            
            <div class="profile-actions">
                <a href="index.php?page=logout" class="btn btn-logout">Đăng xuất</a>
            </div>
        </div>
    </div>
</div>

<style>
.profile-container {
    max-width: 600px;
    margin: 0 auto;
}

.profile-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.profile-avatar {
    text-align: center;
    margin-bottom: 30px;
}

.avatar-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff5722, #f50057);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
}

.profile-info {
    margin-bottom: 30px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row label {
    font-weight: 600;
    color: #666;
}

.info-row span {
    color: #333;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

.badge-google {
    background: #f5f5f5;
    color: #333;
}

.badge-normal {
    background: #e3f2fd;
    color: #1976d2;
}

.badge-admin {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.profile-actions {
    text-align: center;
}

.btn-logout {
    background: #f44336;
    padding: 12px 40px;
}

.btn-logout:hover {
    background: #d32f2f;
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>
