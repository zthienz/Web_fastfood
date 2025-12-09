<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - FastFood</title>
    <link rel="stylesheet" href="<?= asset('public/css/style.css') ?>">
    
    <!-- Google Sign-In -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="login-page">

<div class="login-wrapper">
    <div class="login-box">
        <h2>Đăng ký tài khoản</h2>

        <?php if ($error = getFlash('error')): ?>
            <div class="error-message"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=register&action=submit">
            <div class="form-group">
                <label for="name">Họ và tên</label>
                <input type="text" name="name" id="name" 
                       placeholder="Nhập họ và tên" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" 
                       placeholder="Nhập email" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="tel" name="phone" id="phone" 
                       placeholder="Nhập số điện thoại (không bắt buộc)">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" 
                       placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" required>
            </div>

            <div class="form-group">
                <label for="confirm">Nhập lại mật khẩu</label>
                <input type="password" name="confirm" id="confirm" 
                       placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit">Đăng ký</button>
        </form>

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
                 data-text="signup_with"
                 data-shape="rectangular"
                 data-logo_alignment="left">
            </div>
        </div>

        <script>
        function handleCredentialResponse(response) {
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
        </script>

        <div class="register-link">
            Đã có tài khoản? <a href="index.php?page=login">Đăng nhập</a>
        </div>
    </div>
</div>

</body>
</html>
