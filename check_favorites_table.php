<?php
// File kiểm tra và tạo bảng favorites nếu cần
require_once 'config/database.php';

function checkFavoritesTable() {
    try {
        $db = Database::getInstance()->getConnection();
        $checkTable = $db->query("SHOW TABLES LIKE 'favorites'");
        return $checkTable->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Nếu được gọi trực tiếp, hiển thị kết quả
if (basename($_SERVER['PHP_SELF']) === 'check_favorites_table.php') {
    if (checkFavoritesTable()) {
        echo "✅ Bảng favorites đã tồn tại!";
    } else {
        echo "❌ Bảng favorites chưa tồn tại. <a href='create_favorites_table.php'>Tạo ngay</a>";
    }
}
?>