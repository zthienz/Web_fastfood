<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm"] ?? "";

    if ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Tạm thời chỉ thông báo (chưa lưu vào database)
        $success = "Đăng ký thành công! Bạn có thể đăng nhập.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>
    <meta charset="utf-8">
</head>
<body>
    <h2>Đăng ký</h2>

    <?php 
    if (!empty($error)) echo "<p style='color:red;'>$error</p>";
    if (!empty($success)) echo "<p style='color:green;'>$success</p>";
    ?>

    <form method="POST">
        <label>Tên đăng nhập:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Mật khẩu:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Nhập lại mật khẩu:</label><br>
        <input type="password" name="confirm" required><br><br>

        <button type="submit">Đăng ký</button>
    </form>

    <p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a></p>
</body>
</html>
