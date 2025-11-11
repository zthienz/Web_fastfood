<?php include('include/db_connect.php'); ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastFood - Trang chủ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Header -->
    <header>
        <h1>🍔 FastFood</h1>
        <nav>
            <a href="index.php">Trang chủ</a>
            <a href="#">Thực đơn</a>
            <a href="#">Giỏ hàng</a>
            <a href="#">Liên hệ</a>
        </nav>
    </header>

    <!-- Banner -->
    <div class="banner">
        Ăn nhanh - Ngon miệng - Giá rẻ!
    </div>

    <!-- Danh sách món ăn -->
    <div class="container">
        <h2>Thực đơn nổi bật</h2>
        <div class="menu">
            <?php
            // Hiển thị món ăn từ DB
            $sql = "SELECT * FROM monan LIMIT 6";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "
                    <div class='food-item'>
                        <img src='assets/monan/{$row['hinhAnh']}' alt='{$row['tenMon']}'>
                        <h3>{$row['tenMon']}</h3>
                        <p>Giá: {$row['gia']}đ</p>
                        <a href='#' class='btn'>Đặt ngay</a>
                    </div>
                    ";
                }
            } else {
                echo "<p>Chưa có món ăn nào!</p>";
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 FastFood. Thiết kế bởi Thanh Thiện.</p>
    </footer>

</body>
</html>