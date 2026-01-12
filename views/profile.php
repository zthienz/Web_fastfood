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
        
        <?php if ($editMode): ?>
        <!-- Form chỉnh sửa -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if (!empty($user['avatar']) && file_exists('public/images/avatars/' . $user['avatar'])): ?>
                        <img src="public/images/avatars/<?= e($user['avatar']) ?>?v=<?= time() ?>" alt="Avatar" class="avatar-image" id="avatarPreview">
                    <?php else: ?>
                        <div class="avatar-circle" id="avatarCircle">
                            <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="avatar-upload">
                        <label for="avatarInput" class="avatar-upload-btn">
                            <i class="fas fa-camera"></i>
                            Thay đổi ảnh
                        </label>
                    </div>
                </div>
                <h3>Chỉnh sửa thông tin</h3>
            </div>
            
            <form method="POST" action="index.php?page=profile&action=update" class="profile-form" enctype="multipart/form-data">
                <!-- Hidden file input - PHẢI nằm trong form -->
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">
                
                <!-- Debug info - sẽ xóa sau -->
                <div id="debugInfo" style="display: none; background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px;">
                    <small>Debug: File selected = <span id="fileSelected">None</span></small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Họ và tên <span class="required">*</span></label>
                    <input type="text" name="full_name" class="form-input" 
                           value="<?= e($user['full_name'] ?? '') ?>" required
                           placeholder="Nhập họ và tên">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" class="form-input" 
                           value="<?= e($user['email'] ?? '') ?>" disabled
                           style="background: #f5f5f5; color: #999;">
                    <small class="form-help">Email không thể thay đổi</small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Số điện thoại</label>
                    <input type="tel" name="phone" class="form-input" 
                           value="<?= e($user['phone'] ?? '') ?>"
                           placeholder="Nhập số điện thoại">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Địa chỉ</label>
                    <textarea name="address" class="form-input form-textarea"
                              placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố)"><?= e($user['address'] ?? '') ?></textarea>
                    <small class="form-help">Địa chỉ này sẽ được sử dụng làm địa chỉ giao hàng mặc định</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Lưu thay đổi
                    </button>
                    <a href="index.php?page=profile" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Hủy
                    </a>
                </div>
            </form>
        </div>
        
        <?php else: ?>
        <!-- Hiển thị thông tin -->
        <div class="profile-card">
            <div class="profile-avatar">
                <?php if (!empty($user['avatar']) && file_exists('public/images/avatars/' . $user['avatar'])): ?>
                    <img src="public/images/avatars/<?= e($user['avatar']) ?>?v=<?= time() ?>" alt="Avatar" class="avatar-image">
                <?php else: ?>
                    <div class="avatar-circle">
                        <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
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
                    <span class="address-text"><?= e($user['address'] ?? 'Chưa cập nhật') ?></span>
                </div>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div class="info-row" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
        
        <label style="font-weight: 600; color: #666; margin-bottom: 0;">Vai trò:</label>
        
        <div style="text-align: right;">
            <span class="badge badge-admin" style="display: inline-block; width: fit-content;">
                👑 Quản trị viên
            </span>
        </div>
        
    </div>
<?php endif; ?>
                
                
                <div class="info-row" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
    
                <label style="font-weight: 600; color: #666; margin-bottom: 0;">Phương thức đăng nhập:</label>

    <div style="text-align: right;">
        <?php if (isset($_SESSION['login_method']) && $_SESSION['login_method'] === 'google'): ?>
            <span class="badge badge-google" style="display: inline-flex; align-items: center; gap: 5px;">
                <svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 6.75c1.63 0 3.06.56 4.21 1.65l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84C6.71 7.41 9.14 5.75 12 5.75z" fill="#EA4335"/>
                </svg>
                Google Account
            </span>
        <?php else: ?>
            <span class="badge badge-normal" style="color: #333;">Tài khoản thường</span>
        <?php endif; ?>
    </div>
</div>
                
                <div class="info-row">
                    <label>Ngày đăng ký:</label>
                    <span><?= isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?></span>
                </div>
            </div>
            
            <div class="profile-actions">
                <a href="index.php?page=profile&edit=1" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Chỉnh sửa thông tin
                </a>
                <a href="index.php?page=logout" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Đăng xuất
                </a>
            </div>
        </div>
        <?php endif; ?>
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

.profile-header {
    text-align: center;
    margin-bottom: 30px;
}

.profile-header h3 {
    margin: 15px 0 0;
    color: #333;
    font-size: 24px;
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

.avatar-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.avatar-upload {
    margin-top: 15px;
}

.avatar-upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #667eea;
    color: white;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

.avatar-upload-btn:hover {
    background: #5a6fd8;
    transform: translateY(-1px);
}

.profile-info {
    margin-bottom: 30px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row label {
    font-weight: 600;
    color: #666;
    min-width: 120px;
}

.info-row span {
    color: #333;
    flex: 1;
    text-align: right;
}

.address-text {
    max-width: 300px;
    word-wrap: break-word;
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
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.btn-secondary {
    background: #f5f5f5;
    color: #666;
    border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
    background: #eee;
    border-color: #ccc;
    color: #666;
    text-decoration: none;
}

.btn-logout {
    background: #f44336;
    color: white;
}

.btn-logout:hover {
    background: #d32f2f;
    color: white;
    text-decoration: none;
}

/* Form styles */
.profile-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    font-size: 15px;
}

.form-group label i {
    color: #667eea;
    font-size: 16px;
}

.required {
    color: #f44336;
}

.form-input {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    font-family: inherit;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-textarea {
    min-height: 100px;
    resize: vertical;
}

.form-help {
    font-size: 12px;
    color: #888;
    margin-top: 5px;
    display: block;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

@media (max-width: 768px) {
    .profile-card {
        padding: 25px 20px;
    }
    
    .info-row {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }
    
    .info-row span {
        text-align: left;
    }
    
    .profile-actions,
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>

<script>
// Form validation cho profile
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.querySelector('.profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="full_name"]').value.trim();
            const phone = document.querySelector('input[name="phone"]').value.trim();
            
            if (!name) {
                e.preventDefault();
                alert('Vui lòng nhập họ và tên!');
                return false;
            }
            
            if (phone && !/^[0-9]{10,11}$/.test(phone)) {
                e.preventDefault();
                alert('Số điện thoại không hợp lệ! Vui lòng nhập 10-11 chữ số.');
                return false;
            }
        });
    }
    
    // Xử lý preview avatar
    const avatarInput = document.getElementById('avatarInput');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const debugInfo = document.getElementById('debugInfo');
            const fileSelected = document.getElementById('fileSelected');
            
            if (file) {
                // Debug info
                if (debugInfo && fileSelected) {
                    fileSelected.textContent = file.name + ' (' + (file.size/1024).toFixed(1) + 'KB)';
                    debugInfo.style.display = 'block';
                }
                
                // Kiểm tra kích thước file (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Kích thước ảnh không được vượt quá 2MB!');
                    this.value = '';
                    if (debugInfo) debugInfo.style.display = 'none';
                    return;
                }
                
                // Kiểm tra định dạng file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Chỉ chấp nhận file ảnh định dạng JPG, PNG, GIF!');
                    this.value = '';
                    if (debugInfo) debugInfo.style.display = 'none';
                    return;
                }
                
                // Preview ảnh
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarCircle = document.getElementById('avatarCircle');
                    const avatarPreview = document.getElementById('avatarPreview');
                    
                    if (avatarPreview) {
                        avatarPreview.src = e.target.result;
                    } else if (avatarCircle) {
                        // Tạo img element mới thay thế avatar circle
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Avatar Preview';
                        img.className = 'avatar-image';
                        img.id = 'avatarPreview';
                        avatarCircle.parentNode.replaceChild(img, avatarCircle);
                    }
                };
                reader.readAsDataURL(file);
            } else {
                if (debugInfo) debugInfo.style.display = 'none';
            }
        });
    }
});
</script>