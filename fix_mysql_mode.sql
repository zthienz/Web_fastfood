-- Script để tắt chế độ ONLY_FULL_GROUP_BY trong MySQL
-- Chạy script này nếu vẫn gặp lỗi GROUP BY

-- Kiểm tra sql_mode hiện tại
SELECT @@sql_mode;

-- Tắt ONLY_FULL_GROUP_BY cho session hiện tại
SET sql_mode = (SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

-- Hoặc tắt hoàn toàn sql_mode (không khuyến khích)
-- SET sql_mode = '';

-- Để thay đổi vĩnh viễn, thêm vào file my.cnf hoặc my.ini:
-- [mysqld]
-- sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"

-- Kiểm tra lại sql_mode sau khi thay đổi
SELECT @@sql_mode;