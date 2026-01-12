# Tính năng Upload Avatar

## Mô tả
Tính năng cho phép khách hàng thay đổi ảnh đại diện (avatar) trong trang chỉnh sửa thông tin cá nhân.

## Tính năng chính

### 1. Upload Avatar
- Hỗ trợ định dạng: JPG, JPEG, PNG, GIF
- Kích thước tối đa: 2MB
- Tự động resize và tối ưu hóa
- Preview trước khi lưu

### 2. Hiển thị Avatar
- Hiển thị trong header (thay thế chữ cái đầu tên)
- Hiển thị trong trang profile
- Fallback về chữ cái đầu nếu không có avatar

### 3. Bảo mật
- Kiểm tra định dạng file nghiêm ngặt
- Kiểm tra kích thước file
- Tên file được mã hóa unique
- Thư mục được bảo vệ bằng .htaccess

## Cách sử dụng

### Cho khách hàng:
1. Đăng nhập vào tài khoản
2. Vào "Thông tin tài khoản" 
3. Nhấn "Chỉnh sửa thông tin"
4. Nhấn nút "Thay đổi ảnh"
5. Chọn file ảnh từ máy tính
6. Xem preview và nhấn "Lưu thay đổi"
7. Avatar sẽ hiển thị ngay lập tức

### Cho developer:
- Avatar được lưu trong `public/images/avatars/`
- Tên file format: `avatar_{user_id}_{timestamp}.{extension}`
- Database: cột `avatar` trong bảng `users`

## Files đã thay đổi

### 1. Views
- `views/profile.php`: Thêm form upload và preview
- `views/layouts/header.php`: Hiển thị avatar trong header

### 2. Controllers  
- `controllers/ProfileController.php`: Xử lý upload và validation

### 3. CSS
- `public/css/style.css`: Style cho avatar

### 4. Directories
- `public/images/avatars/`: Thư mục lưu avatar
- `public/images/avatars/.htaccess`: Bảo mật thư mục

## Validation Rules

### Client-side (JavaScript):
- Kiểm tra kích thước file (max 2MB)
- Kiểm tra định dạng file
- Preview ảnh trước khi upload

### Server-side (PHP):
- Kiểm tra `$_FILES['avatar']['error']`
- Validate MIME type bằng `mime_content_type()`
- Kiểm tra kích thước file
- Tạo tên file unique để tránh conflict

## Error Handling
- File quá lớn: "Kích thước ảnh không được vượt quá 2MB!"
- Sai định dạng: "Chỉ chấp nhận file ảnh định dạng JPG, PNG, GIF!"
- Lỗi upload: "Có lỗi xảy ra khi upload ảnh!"
- Thành công: "Cập nhật thông tin thành công!"

## Testing
Chạy `php test_avatar_upload.php` để kiểm tra:
- ✅ Thư mục avatars tồn tại và có quyền ghi
- ✅ Cột avatar trong database
- ✅ Cấu hình server

## Browser Support
- Chrome, Firefox, Safari, Edge (modern browsers)
- Yêu cầu JavaScript enabled cho preview
- Fallback graceful nếu không có JavaScript