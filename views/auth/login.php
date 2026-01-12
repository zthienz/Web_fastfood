<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - FastFood</title>
    <link rel="stylesheet" href="<?= asset('public/css/style.css') ?>">
    
    <!-- Google Sign-In -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="login-page">

<div class="login-wrapper">
    <div class="login-box">
        <h2>Đăng nhập</h2>

        <?php if ($error = getFlash('error')): ?>
            <div class="error-message"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success = getFlash('success')): ?>
            <div class="success-message"><?= e($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login&action=submit">
            <div class="form-group">
                <label for="username">Tên đăng nhập hoặc Email</label>
                <input type="text" name="username" id="username" 
                       placeholder="Nhập tên đăng nhập hoặc email" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" 
                       placeholder="Nhập mật khẩu" required>
            </div>

            <button type="submit">Đăng nhập ngay</button>
        </form>

        <div class="extra-links">
            <a href="#" class="forgot-password">Quên mật khẩu?</a>
        </div>

        <div class="social-divider">
            <span>Hoặc</span>
        </div>

        <!-- Google Sign-In Button -->
        <div id="g_id_onload"
             data-client_id="<?= GOOGLE_CLIENT_ID ?>"
             data-callback="handleCredentialResponse"
             data-auto_prompt="false">
        </div>
        
        <div class="google-signin-wrapper">
            <div class="g_id_signin" 
                 data-type="standard"
                 data-size="large"
                 data-theme="outline"
                 data-text="signin_with"
                 data-shape="rectangular"
                 data-logo_alignment="left">
            </div>
        </div>

        <script>
        function handleCredentialResponse(response) {
            if (!response.credential) {
                alert('Lỗi: Không nhận được credential từ Google');
                return;
            }
            
            // Hiển thị loading
            const button = document.querySelector('.g_id_signin');
            if (button) {
                button.style.opacity = '0.5';
                button.style.pointerEvents = 'none';
            }
            
            // Gửi credential đến server
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= BASE_URL ?>google-callback.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'credential';
            input.value = response.credential;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        
        // Xử lý lỗi Google Sign-In
        window.addEventListener('load', function() {
            // Kiểm tra xem Google Sign-In có load được không
            setTimeout(function() {
                const googleButton = document.querySelector('.g_id_signin');
                if (googleButton && !googleButton.innerHTML.trim()) {
                    // Google Sign-In không load được - có thể log lỗi nếu cần
                }
            }, 3000);
        });
        </script>

        <!-- Fallback button nếu Google Sign-In không load -->
        <noscript>
            <a href="index.php?page=google-login" class="google-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 6.75c1.63 0 3.06.56 4.21 1.65l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84C6.71 7.41 9.14 5.75 12 5.75z" fill="#EA4335"/>
                </svg>
                Tiếp tục với Google
            </a>
        </noscript>

        <div class="register-link">
            Chưa có tài khoản? <a href="index.php?page=register">Đăng ký ngay</a>
        </div>
    </div>
</div>

</body>
</html>
