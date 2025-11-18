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
    <link rel="stylesheet" href="css/login.css">
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
                <input type="password" name="	password" id="password" required>
            </div>

            <button type="submit">Đăng nhập ngay</button>
        </form>

        <div class="extra-links">
            <a href="quen-mat-khau.php" class="forgot-password">Quên mật khẩu?</a>
        </div>

        <div class="register-link">
            Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a>
        </div>
    </div>

</body>
</html>