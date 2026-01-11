<?php
$pageTitle = 'Liên hệ với chúng tôi';
require_once 'views/layouts/header.php';
?>

<div class="contact-container">
    <!-- Hero Section -->
    <div class="contact-hero">
        <div class="container">
            <h1 class="contact-title">Liên hệ với chúng tôi</h1>
            <p class="contact-subtitle">
                Chúng tôi luôn sẵn sàng lắng nghe ý kiến và phục vụ bạn một cách tốt nhất.<br>
                Vui lòng gửi thông tin liên hệ, chúng tôi sẽ phản hồi trong thời gian sớm nhất.
            </p>
        </div>
    </div>

    <div class="container">
        <!-- Contact Form Section -->
        <div class="contact-form-section">
            <h2 class="section-title">Gửi tin nhắn cho chúng tôi</h2>
            <p class="section-subtitle">Điền thông tin chi tiết và chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất</p>
            
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

            <form method="POST" action="index.php?page=contact&action=store" class="contact-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Họ và tên *</label>
                        <input type="text" id="name" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required class="form-control">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="subject">Chủ đề</label>
                        <select id="subject" name="subject" class="form-control">
                            <option value="">Chọn chủ đề</option>
                            <option value="Thông tin sản phẩm">Thông tin sản phẩm</option>
                            <option value="Đặt hàng">Đặt hàng</option>
                            <option value="Khiếu nại">Khiếu nại</option>
                            <option value="Hợp tác">Hợp tác</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message">Nội dung *</label>
                    <textarea id="message" name="message" rows="6" required class="form-control" 
                              placeholder="Nhập nội dung chi tiết về yêu cầu của bạn..."></textarea>
                </div>
                
                <div class="form-group">
                    <small class="form-note">
                        Bằng việc gửi thông tin này, bạn đồng ý với việc chúng tôi liên hệ lại với bạn qua thông tin đã cung cấp.
                    </small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Gửi tin nhắn
                </button>
            </form>
        </div>

        <!-- Working Hours Section -->
        <div class="working-hours-section">
            <h2 class="section-title">Giờ làm việc</h2>
            <div class="working-hours-grid">
                <div class="working-day">
                    <span class="day">Thứ Hai - Thứ Sáu</span>
                    <span class="time">8:00 AM - 10:00 PM</span>
                </div>
                <div class="working-day">
                    <span class="day">Thứ Bảy</span>
                    <span class="time">9:00 AM - 11:00 PM</span>
                </div>
                <div class="working-day">
                    <span class="day">Chủ Nhật</span>
                    <span class="time">Nghỉ</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-container {
    background: #f5f5f5;
    min-height: 100vh;
}

.contact-hero {
    background: linear-gradient(135deg, #ff5722, #f50057);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.contact-title {
    font-size: 48px;
    font-weight: 700;
    margin: 0 0 20px 0;
    letter-spacing: -1px;
}

.contact-subtitle {
    font-size: 18px;
    line-height: 1.6;
    margin: 0;
    opacity: 0.9;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.contact-form-section {
    background: white;
    padding: 60px;
    margin: -40px auto 60px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 2;
}

.section-title {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    text-align: center;
    margin: 0 0 15px 0;
}

.section-subtitle {
    font-size: 16px;
    color: #666;
    text-align: center;
    margin: 0 0 40px 0;
}

.contact-form {
    max-width: 800px;
    margin: 0 auto;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #fff;
}

.form-control:focus {
    outline: none;
    border-color: #ff5722;
    box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
}

.form-control::placeholder {
    color: #adb5bd;
}

.form-note {
    color: #6c757d;
    font-size: 13px;
    line-height: 1.4;
}

.btn-submit {
    background: linear-gradient(135deg, #ff5722, #f50057);
    color: white;
    border: none;
    padding: 18px 40px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 20px auto 0;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(255, 87, 34, 0.3);
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
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

.working-hours-section {
    background: white;
    padding: 60px;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin: 60px 0;
}

.working-hours-grid {
    max-width: 600px;
    margin: 0 auto;
}

.working-day {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    border-bottom: 1px solid #e9ecef;
}

.working-day:last-child {
    border-bottom: none;
}

.day {
    font-weight: 600;
    color: #333;
    font-size: 16px;
}

.time {
    color: #ff5722;
    font-weight: 600;
    font-size: 16px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .contact-hero {
        padding: 60px 0;
    }
    
    .contact-title {
        font-size: 36px;
    }
    
    .contact-subtitle {
        font-size: 16px;
    }
    
    .contact-form-section {
        padding: 40px 30px;
        margin: -20px 20px 40px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .section-title {
        font-size: 28px;
    }
    
    .working-hours-section {
        padding: 40px 30px;
        margin: 40px 20px;
    }
}

@media (max-width: 480px) {
    .contact-title {
        font-size: 28px;
    }
    
    .contact-form-section {
        padding: 30px 20px;
    }
    
    .section-title {
        font-size: 24px;
    }
    
    .working-hours-section {
        padding: 30px 20px;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>