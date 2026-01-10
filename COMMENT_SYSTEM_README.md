# Hệ thống bình luận cho đơn hàng đã giao

## Tổng quan
Hệ thống cho phép khách hàng bình luận và đánh giá món ăn sau khi đơn hàng đã được giao thành công.

## Tính năng chính

### 1. Điều kiện bình luận
- Chỉ khách hàng đã mua và nhận được hàng mới có thể bình luận
- Đơn hàng phải ở trạng thái "delivered" (đã giao)
- Mỗi khách hàng chỉ có thể bình luận 1 lần cho mỗi món ăn trong 1 đơn hàng

### 2. Nút bình luận
- Xuất hiện trong trang chi tiết đơn hàng (`views/orders/detail.php`)
- Chỉ hiển thị với đơn hàng đã giao
- Chuyển sang trạng thái "Đã đánh giá" sau khi bình luận

### 3. Form bình luận
- Đánh giá từ 1-5 sao
- Nội dung bình luận (tối đa 1000 ký tự)
- Hiển thị thông tin đơn hàng và món ăn
- Validation đầy đủ

### 4. Hiển thị bình luận
- Xuất hiện trong trang chi tiết món ăn (`views/menu/detail.php`)
- Hiển thị điểm đánh giá trung bình
- Danh sách bình luận với thông tin người dùng
- Đánh dấu "Đã mua hàng" cho bình luận từ đơn hàng

## Cấu trúc file

### Controllers
- `controllers/CommentController.php` - Xử lý logic bình luận
- `controllers/OrderController.php` - Cập nhật để hiển thị trạng thái bình luận
- `controllers/MenuController.php` - Cập nhật để hiển thị bình luận trong chi tiết món ăn

### Views
- `views/comments/order_comment_form.php` - Form bình luận
- `views/orders/detail.php` - Trang chi tiết đơn hàng với nút bình luận
- `views/menu/detail.php` - Trang chi tiết món ăn với phần bình luận

### Database
- Bảng `comments` đã được cập nhật với cột `order_id`
- Ràng buộc unique để tránh bình luận trùng lặp
- Foreign key liên kết với bảng orders

## Routing

### URL patterns
- `index.php?page=comments&action=form&order_id=X&product_id=Y` - Form bình luận
- `index.php?page=comments&action=submit` - Xử lý submit bình luận
- `index.php?page=orders&action=detail&id=X` - Chi tiết đơn hàng
- `index.php?page=menu&action=detail&id=Y` - Chi tiết món ăn

## Cách sử dụng

### Cho khách hàng:
1. Vào "Đơn hàng của tôi"
2. Chọn "Xem chi tiết" đơn hàng đã giao
3. Nhấn nút "Đánh giá" bên cạnh món ăn
4. Điền form đánh giá và gửi
5. Xem bình luận trong trang chi tiết món ăn

### Cho admin:
- Bình luận tự động được approve (status = 'approved')
- Có thể thay đổi trạng thái bình luận trong database nếu cần

## Database Schema

```sql
-- Cột mới trong bảng comments
ALTER TABLE comments ADD COLUMN order_id INT NULL AFTER product_id;
ALTER TABLE comments ADD FOREIGN KEY (order_id) REFERENCES orders(id);
ALTER TABLE comments ADD UNIQUE KEY (user_id, order_id, product_id);
```

## Validation Rules

1. **Đánh giá**: Bắt buộc, từ 1-5 sao
2. **Nội dung**: Bắt buộc, tối đa 1000 ký tự
3. **Đơn hàng**: Phải thuộc về user hiện tại và đã giao
4. **Sản phẩm**: Phải có trong đơn hàng
5. **Unique**: Mỗi user chỉ bình luận 1 lần/sản phẩm/đơn hàng

## Tính năng bảo mật

- Kiểm tra quyền sở hữu đơn hàng
- Sanitize input để tránh XSS
- Prepared statements để tránh SQL injection
- Validation đầy đủ trước khi lưu database

## Test

Chạy `php test_comment_system.php` để kiểm tra hệ thống.

## Lưu ý

- Bình luận chỉ hiển thị khi status = 'approved'
- Hệ thống tự động approve bình luận từ đơn hàng
- Có thể mở rộng thêm tính năng reply bình luận bằng cách sử dụng cột `parent_id`