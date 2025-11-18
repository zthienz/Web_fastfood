<?php
session_start();

$error = '';
$username = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    }
    elseif ($username === 'admin' && $password === '123456') {
        $_SESSION['user'] = $username;
        $_SESSION['loggedin'] = true;
        header("Location: trangchu.php");
        exit();
    } else {
        $error = 'Sai tên đăng nhập hoặc mật khẩu!';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

        <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            text-align: center;
            margin-top: 60px;
        }

        table {
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            width: 350px;
        }

        table td {
            padding: 8px 5px;
            font-size: 16px;
        }

        input[type="text"],
        input[type="password"] {
            width: 95%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #ff5722;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #e64a19;
        }

        a {
            color: #ff5722;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
    
</head>
<body>

    <div class="login-container">
        <h2>Đăng nhập hệ thống</h2>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off" novalidate>
            <input type="hidden" name="login" value="1">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($username) ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit">Đăng nhập ngay</button>
        </form>

        <div class="extra-links">
            <a href="quen-mat-khau.php" class="forgot-password">Quên mật khẩu?</a>
        </div>

        <!-- PHÂN CÁCH VÀ NÚT GOOGLE -->
        <div class="social-divider">
            <span>Hoặc</span>
        </div>

        <a href="google-login.php" class="google-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 6.75c1.63 0 3.06.56 4.21 1.65l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84C6.71 7.41 9.14 5.75 12 5.75z" fill="#EA4335"/>
            </svg>
            Tiếp tục với Google
        </a>

        <div class="register-link">
            Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a>
        </div>
    </div>

</body>
</html>