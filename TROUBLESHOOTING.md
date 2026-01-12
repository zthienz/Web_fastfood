# Hướng dẫn khắc phục lỗi Dashboard

## Lỗi hiện tại: "Có lỗi xảy ra khi tải dữ liệu"

### Bước 1: Chạy script debug
```
http://localhost/Web_fastfood/debug_dashboard.php
```
Script này sẽ kiểm tra:
- Kết nối database
- Cấu trúc bảng
- Dữ liệu có sẵn
- SQL mode
- Queries cụ thể

### Bước 2: Khắc phục lỗi ONLY_FULL_GROUP_BY
Nếu debug hiển thị lỗi GROUP BY, chạy:
```
http://localhost/Web_fastfood/fix_mysql_mode.sql
```
Hoặc trong phpMyAdmin:
```sql
SET sql_mode = (SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
```

### Bước 3: Thêm dữ liệu mẫu (nếu database trống)
```
http://localhost/Web_fastfood/insert_sample_data.php
```
Script này sẽ tạo:
- Categories mẫu
- Products mẫu  
- Users mẫu
- Orders mẫu (30 ngày qua)
- Order items mẫu

### Bước 4: Kiểm tra lại Dashboard
```
http://localhost/Web_fastfood/index.php?page=admin&section=dashboard
```

## Các lỗi thường gặp và cách khắc phục

### 1. Lỗi kết nối Database
**Triệu chứng:** "Lỗi kết nối database"
**Khắc phục:**
- Kiểm tra XAMPP MySQL đã chạy chưa
- Kiểm tra thông tin database trong `config/database.php`
- Đảm bảo database `fastfood_db` đã được tạo

### 2. Lỗi ONLY_FULL_GROUP_BY
**Triệu chứng:** "Expression #X of SELECT list is not in GROUP BY clause"
**Khắc phục:**
- Chạy script `fix_mysql_mode.sql`
- Hoặc tắt trong file `my.ini` của XAMPP

### 3. Không có dữ liệu
**Triệu chứng:** Biểu đồ hiển thị "Không có dữ liệu để hiển thị"
**Khắc phục:**
- Chạy `insert_sample_data.php` để tạo dữ liệu mẫu
- Hoặc tạo đơn hàng thực tế với `payment_status = 'paid'`

### 4. Lỗi JavaScript
**Triệu chứng:** Biểu đồ không hiển thị
**Khắc phục:**
- Mở Developer Tools (F12) kiểm tra Console
- Đảm bảo Chart.js được load từ CDN
- Kiểm tra dữ liệu JSON có hợp lệ không

### 5. Lỗi quyền truy cập
**Triệu chứng:** Redirect về trang login
**Khắc phục:**
- Đảm bảo đã đăng nhập với tài khoản admin
- Kiểm tra session có `role = 'admin'` không

## Cấu trúc dữ liệu cần thiết

### Bảng orders
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- total (DECIMAL)
- order_status (ENUM: pending, confirmed, preparing, shipping, delivered, cancelled)
- payment_status (ENUM: pending, paid, failed, refunded)
- created_at (TIMESTAMP)
```

### Bảng order_items
```sql
- id (PRIMARY KEY)
- order_id (FOREIGN KEY)
- product_id (FOREIGN KEY)
- product_name (VARCHAR)
- quantity (INT)
- price (DECIMAL)
- subtotal (DECIMAL)
```

### Bảng products
```sql
- id (PRIMARY KEY)
- category_id (FOREIGN KEY)
- name (VARCHAR)
- price (DECIMAL)
- stock_quantity (INT)
- status (ENUM: active, inactive, out_of_stock)
```

## Debug mode

Để bật debug mode và xem lỗi chi tiết:
1. Mở `config/config.php`
2. Đảm bảo có dòng: `define('DEBUG', true);`
3. Refresh dashboard để xem lỗi chi tiết

## Liên hệ hỗ trợ

Nếu vẫn gặp lỗi sau khi thực hiện các bước trên:
1. Chụp màn hình lỗi
2. Copy nội dung từ `debug_dashboard.php`
3. Kiểm tra log lỗi PHP trong XAMPP
4. Gửi thông tin chi tiết để được hỗ trợ