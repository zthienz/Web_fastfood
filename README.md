# FastFood - Website Bán Đồ Ăn Nhanh

## Mô tả dự án
Website bán đồ ăn nhanh được xây dựng bằng PHP thuần, sử dụng kiến trúc MVC đơn giản.

## Tính năng chính

### Khách hàng:
- Duyệt menu và sản phẩm
- Thêm sản phẩm vào giỏ hàng
- Đặt hàng và thanh toán
- Theo dõi đơn hàng
- Đăng nhập/đăng ký (hỗ trợ Google OAuth)
- Quản lý thông tin cá nhân và avatar
- Danh sách sản phẩm yêu thích
- Bình luận và đánh giá sản phẩm
- Đọc bài viết/tin tức
- Liên hệ với nhà hàng

### Admin:
- Dashboard thống kê doanh thu
- Quản lý sản phẩm và danh mục
- Quản lý đơn hàng
- Quản lý người dùng
- Quản lý bài viết/tin tức
- Quản lý liên hệ từ khách hàng
- Báo cáo doanh thu chi tiết

## Cấu trúc thư mục

```
Web_fastfood/
├── config/                 # Cấu hình database và chung
├── controllers/            # Các controller xử lý logic
├── helpers/               # Hàm tiện ích
├── views/                 # Giao diện người dùng
├── public/               # Tài nguyên tĩnh (CSS, images)
├── vendor/               # Thư viện bên thứ 3
├── index.php             # File entry point
├── google-callback.php   # Xử lý Google OAuth
├── fastfood_db.sql      # Database schema
└── .htaccess            # Cấu hình Apache
```

## Cài đặt

### Yêu cầu hệ thống:
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- Composer (cho Google OAuth)

### Các bước cài đặt:

1. **Clone dự án:**
   ```bash
   git clone [repository-url]
   cd Web_fastfood
   ```

2. **Cài đặt dependencies:**
   ```bash
   composer install
   ```

3. **Tạo database:**
   - Tạo database mới tên `fastfood_db`
   - Import file `fastfood_db.sql`

4. **Cấu hình database:**
   - Chỉnh sửa file `config/database.php`
   - Cập nhật thông tin kết nối database

5. **Cấu hình Google OAuth (tùy chọn):**
   - Tạo project trên Google Cloud Console
   - Cập nhật `GOOGLE_CLIENT_ID` và `GOOGLE_CLIENT_SECRET` trong `config/config.php`

6. **Cấu hình Apache:**
   - Đảm bảo mod_rewrite được bật
   - Document root trỏ đến thư mục dự án

## Sử dụng

### Tài khoản mặc định:
- **Admin:** admin@fastfood.com / admin123
- **Customer:** customer@example.com / customer123

### URL quan trọng:
- Trang chủ: `http://localhost/Web_fastfood/`
- Admin: `http://localhost/Web_fastfood/?page=admin`
- Đăng nhập: `http://localhost/Web_fastfood/?page=login`

## Database Schema

Dự án sử dụng 14 bảng chính:
- `users` - Người dùng (admin/customer)
- `categories` - Danh mục sản phẩm
- `products` - Sản phẩm
- `product_images` - Hình ảnh sản phẩm
- `orders` - Đơn hàng
- `order_items` - Chi tiết đơn hàng
- `cart` - Giỏ hàng
- `favorites` - Danh sách yêu thích
- `posts` - Bài viết/tin tức
- `comments` - Bình luận/đánh giá
- `payments` - Thanh toán
- `order_status_history` - Lịch sử trạng thái đơn hàng
- `contacts` - Liên hệ từ khách hàng

## Bảo mật

- Sử dụng PDO với prepared statements
- Hash password bằng bcrypt
- Escape output để tránh XSS
- Validation input từ người dùng
- Session management an toàn

## Đóng góp

1. Fork dự án
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit thay đổi (`git commit -m 'Add some AmazingFeature'`)
4. Push lên branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## License

Dự án này được phát hành dưới giấy phép MIT.