<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm"] ?? "";

    if ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        $success = "Đăng ký thành công! Bạn có thể đăng nhập.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>
    <meta charset="utf-8">

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

    <h2>Đăng ký tài khoản</h2>

    <?php 
        if (!empty($error)) echo "<p style='color:red;'>$error</p>";
        if (!empty($success)) echo "<p style='color:green;'>$success</p>";
    ?>

    <form method="POST">
        <table>
            <tr>
                <td><label>Tên đăng nhập:</label></td>
                <td><input type="text" name="username" required></td>
            </tr>

            <tr>
                <td><label>Mật khẩu:</label></td>
                <td><input type="password" name="password" required></td>
            </tr>

            <tr>
                <td><label>Nhập lại mật khẩu:</label></td>
                <td><input type="password" name="confirm" required></td>
            </tr>

            <tr>
                <td colspan="2">
                    <button type="submit">Đăng ký</button>
                </td>
            </tr>
        </table>
    </form>

    <p>Đã có tài khoản?  
        <a href="dangnhap.php">Đăng nhập</a>
    </p>

</body>
</html>
