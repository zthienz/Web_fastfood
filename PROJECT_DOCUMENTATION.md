# TÀI LIỆU DỰ ÁN FASTFOOD WEB APPLICATION

## 1. TỔNG QUAN DỰ ÁN

### 1.1 Giới thiệu
FastFood là một ứng dụng web thương mại điện tử bán đồ ăn nhanh, được xây dựng bằng PHP thuần với kiến trúc MVC đơn giản. Dự án hỗ trợ cả khách hàng (đặt hàng, thanh toán) và quản trị viên (quản lý sản phẩm, đơn hàng, người dùng).

### 1.2 Công nghệ sử dụng
- **Backend**: PHP 7.x/8.x (thuần, không framework)
- **Database**: MySQL với PDO
- **Frontend**: HTML, CSS, JavaScript
- **Authentication**: Session-based + Google OAuth 2.0
- **Server**: Apache với mod_rewrite

### 1.3 Cấu trúc thư mục
```
Web_fastfood/
├── config/                 # Cấu hình
│   ├── config.php         # Cấu hình chung (site name, Google OAuth)
│   └── database.php       # Kết nối database (Singleton pattern)
├── controllers/           # Controllers (19 files)
│   ├── HomeController.php
│   ├── AuthController.php
│   ├── MenuController.php
│   ├── CartController.php
│   ├── OrderController.php
│   ├── ProfileController.php
│   ├── PostController.php
│   ├── FavoritesController.php
│   ├── CommentController.php
│   ├── ContactController.php
│   ├── PolicyController.php
│   ├── AboutController.php
│   └── Admin Controllers (7 files)
├── helpers/
│   └── functions.php      # Hàm tiện ích (sanitize, redirect, flash...)
├── views/                 # Template files
├── public/
│   ├── css/              # Stylesheets
│   └── images/           # Hình ảnh sản phẩm, avatar
├── index.php             # Entry point & Router
├── .htaccess             # URL rewriting & Security headers
├── google-callback.php   # Google OAuth callback
└── fastfood_db.sql       # Database schema
```

---

## 2. KIẾN TRÚC VÀ CÁCH HOẠT ĐỘNG

### 2.1 Luồng xử lý Request

```
1. User Request → .htaccess (URL Rewrite) → index.php
2. index.php:
   - session_start()
   - Load config (database.php, config.php)
   - Load helpers (functions.php)
   - Load tất cả Controllers
   - Routing dựa trên $_GET['page'] và $_GET['action']
3. Controller xử lý logic → Query Database → Load View
4. View render HTML → Response to User
```

### 2.2 Routing System
Routing được xử lý trong `index.php` bằng switch-case:

```php
$page = $_GET['page'] ?? 'home';    // Xác định controller
$action = $_GET['action'] ?? 'index'; // Xác định method

// Ví dụ URL:
// index.php?page=menu                    → MenuController->index()
// index.php?page=menu&action=detail&id=1 → MenuController->detail()
// index.php?page=cart&action=add&id=5    → CartController->add()
// index.php?page=admin&section=products  → AdminProductController->index()
```

### 2.3 Database Connection (Singleton Pattern)
```php
// config/database.php
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        $this->conn = new PDO(...);
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}

// Sử dụng trong Controller:
$this->db = Database::getInstance()->getConnection();
```

### 2.4 Authentication Flow
```
ĐĂNG NHẬP THƯỜNG:
1. User submit form → AuthController->login()
2. Validate input → Query user by email
3. password_verify() → Set session variables
4. Redirect based on role (admin → dashboard, customer → home)

ĐĂNG NHẬP GOOGLE:
1. User click "Login with Google" → Redirect to Google OAuth
2. Google callback → google-callback.php
3. Verify token → Create/Update user → Set session
4. Redirect to home

SESSION VARIABLES:
$_SESSION['user_id']     - ID người dùng
$_SESSION['user_email']  - Email
$_SESSION['full_name']   - Họ tên
$_SESSION['role']        - 'admin' hoặc 'customer'
$_SESSION['login_method'] - 'normal' hoặc 'google'
```

---

## 3. CÁC TÍNH NĂNG CHÍNH

### 3.1 Tính năng Khách hàng

#### A. Xem Menu & Tìm kiếm
- Hiển thị danh sách sản phẩm với phân loại
- Lọc theo: danh mục, khoảng giá, trạng thái (còn hàng/hết hàng/giảm giá)
- Sắp xếp: tên, giá tăng/giảm, mới nhất, phổ biến
- Tìm kiếm theo tên sản phẩm
- Xem chi tiết sản phẩm với nhiều hình ảnh

#### B. Giỏ hàng (Session-based)
```php
// Cấu trúc giỏ hàng trong session:
$_SESSION['cart'] = [
    product_id => quantity,
    // Ví dụ: 1 => 2, 3 => 1
];

// Các thao tác:
- add(): Thêm sản phẩm (kiểm tra tồn kho)
- update(): Cập nhật số lượng
- remove(): Xóa sản phẩm
- checkout(): Chuyển đến thanh toán
```

#### C. Đặt hàng & Thanh toán
```
Quy trình đặt hàng:
1. Xem giỏ hàng → Checkout
2. Nhập/Xác nhận địa chỉ giao hàng
3. Chọn phương thức thanh toán:
   - COD (Thanh toán khi nhận hàng)
   - Bank Transfer (Chuyển khoản)
   - MoMo, VNPay, Credit Card (chưa tích hợp API)
4. Xác nhận đơn hàng → Tạo order + order_items
5. Trừ tồn kho sản phẩm
6. Xóa giỏ hàng → Redirect success page

Trạng thái đơn hàng:
pending → confirmed → preparing → shipping → delivered
                                           ↘ cancelled
```

#### D. Yêu thích (Favorites)
- Toggle yêu thích sản phẩm (AJAX)
- Xem danh sách yêu thích
- Yêu cầu đăng nhập

#### E. Đánh giá & Bình luận
- Đánh giá sản phẩm sau khi đơn hàng delivered
- Rating 1-5 sao + nội dung
- Admin có thể reply bình luận

#### F. Hồ sơ cá nhân
- Cập nhật thông tin (tên, SĐT, địa chỉ)
- Upload avatar (max 2MB, JPG/PNG/GIF)
- Xem lịch sử đơn hàng

### 3.2 Tính năng Admin

#### A. Dashboard
- Thống kê doanh thu (theo ngày/tuần/tháng/năm/tùy chỉnh)
- Số đơn hàng theo trạng thái
- Sản phẩm sắp hết hàng
- Biểu đồ doanh thu

#### B. Quản lý Sản phẩm
- CRUD sản phẩm
- Upload nhiều hình ảnh/sản phẩm
- Đặt ảnh chính (is_primary)
- Quản lý tồn kho
- Đánh dấu sản phẩm nổi bật

#### C. Quản lý Đơn hàng
- Xem danh sách đơn hàng (filter theo trạng thái)
- Cập nhật trạng thái đơn hàng
- Cập nhật trạng thái thanh toán
- Xem chi tiết đơn hàng

#### D. Quản lý Người dùng
- Xem danh sách users
- Thay đổi trạng thái (active/inactive/banned)
- Xóa user

#### E. Quản lý Bài viết
- CRUD bài viết/tin tức
- Upload ảnh đại diện
- Trạng thái: published/draft/hidden

#### F. Quản lý Liên hệ
- Xem tin nhắn từ khách hàng
- Đánh dấu đã đọc/đã trả lời
- Xóa liên hệ

#### G. Báo cáo Doanh thu
- Doanh thu theo thời gian
- Top sản phẩm bán chạy
- Doanh thu theo danh mục

---

## 4. CẤU TRÚC DATABASE

### 4.1 Sơ đồ quan hệ (ERD)

```
users (1) ──────< orders (N)
  │                  │
  │                  └──< order_items (N) >── products (1)
  │                                              │
  │                                              │
  └──< favorites (N) >─────────────────────────┘
  │
  └──< comments (N) >── products/posts
  │
  └──< cart (N) >── products

categories (1) ──< products (N) ──< product_images (N)
```

### 4.2 Chi tiết các bảng

| Bảng | Mô tả | Quan hệ |
|------|-------|---------|
| `users` | Người dùng (admin/customer) | - |
| `categories` | Danh mục món ăn | 1-N với products |
| `products` | Sản phẩm/Món ăn | N-1 với categories |
| `product_images` | Hình ảnh sản phẩm | N-1 với products |
| `orders` | Đơn hàng | N-1 với users |
| `order_items` | Chi tiết đơn hàng | N-1 với orders, products |
| `order_status_history` | Lịch sử trạng thái | N-1 với orders |
| `payments` | Thanh toán | N-1 với orders |
| `cart` | Giỏ hàng (DB) | N-1 với users, products |
| `favorites` | Yêu thích | N-1 với users, products |
| `comments` | Bình luận/Đánh giá | N-1 với users, products, posts |
| `posts` | Bài viết | N-1 với users |
| `contacts` | Liên hệ | - |

### 4.3 Các trạng thái quan trọng

```sql
-- User status
ENUM('active', 'inactive', 'banned')

-- Product status
ENUM('active', 'inactive', 'out_of_stock')

-- Order status
ENUM('pending', 'confirmed', 'preparing', 'shipping', 'delivered', 'cancelled')

-- Payment status
ENUM('pending', 'paid', 'failed', 'refunded')

-- Post status
ENUM('published', 'draft', 'hidden')
```

---

## 5. CÁC LỖI VÀ VẤN ĐỀ BẢO MẬT

### 5.1 Lỗi Nghiêm trọng (CRITICAL)

#### ⚠️ 1. Hardcoded Database Credentials
**File**: `config/database.php`
```php
private $password = 'Thien@160504'; // ❌ Mật khẩu lộ trong code
```
**Khắc phục**: Sử dụng environment variables hoặc file config ngoài web root.

#### ⚠️ 2. Google OAuth Credentials Exposed
**File**: `config/config.php`
```php
define('GOOGLE_CLIENT_ID', '297138230092-...');
define('GOOGLE_CLIENT_SECRET', 'GOCSPX-...');
```
**Khắc phục**: Di chuyển sang environment variables.

#### ⚠️ 3. Thiếu CSRF Protection
**Vấn đề**: Không có CSRF token trên các form POST.
**Rủi ro**: Attacker có thể thực hiện các hành động thay mặt user.
**Khắc phục**: Thêm CSRF token generation và validation.

### 5.2 Lỗi Cao (HIGH)

#### ⚠️ 4. Thiếu Rate Limiting
**Vấn đề**: Không giới hạn số lần request.
**Rủi ro**: Brute force attack trên login, spam contact form.
**Khắc phục**: Implement rate limiting (ví dụ: 5 lần/phút cho login).

#### ⚠️ 5. Weak Password Requirements
**File**: `controllers/AuthController.php`
```php
if (strlen($password) < 6) { // Chỉ yêu cầu 6 ký tự
```
**Khắc phục**: Yêu cầu mật khẩu mạnh hơn (chữ hoa, số, ký tự đặc biệt).

### 5.3 Lỗi Trung bình (MEDIUM)

#### ⚠️ 6. Session Fixation Risk
**Vấn đề**: Không regenerate session ID sau login.
**Khắc phục**: Thêm `session_regenerate_id(true)` sau login thành công.

#### ⚠️ 7. File Upload Validation
**File**: `controllers/ProfileController.php`
**Vấn đề**: Chỉ kiểm tra extension và MIME type, không kiểm tra nội dung file.
**Khắc phục**: Sử dụng `getimagesize()` để verify file là ảnh thật.

#### ⚠️ 8. Missing Input Length Validation
**Vấn đề**: Không giới hạn độ dài input.
**Khắc phục**: Thêm validation max length cho tất cả input.

### 5.4 Lỗi Code Quality

#### 1. Code Duplication
**File**: `controllers/MenuController.php`
- `index()` và `search()` có ~90% code giống nhau.
- **Khắc phục**: Extract common logic thành private method.

#### 2. SQL Mode Workaround
**File**: `controllers/AdminController.php`
```php
$this->db->exec("SET sql_mode = (SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
```
**Vấn đề**: Đây là workaround, không phải fix đúng.
**Khắc phục**: Viết lại các query GROUP BY cho đúng chuẩn.

#### 3. Hardcoded Values
```php
$shippingFee = $subtotal >= 200000 ? 0 : 30000; // Hardcoded
$limit = 20; // Hardcoded pagination
```
**Khắc phục**: Di chuyển sang config hoặc database.

---

## 6. HELPER FUNCTIONS

### 6.1 Danh sách hàm trong `helpers/functions.php`

| Hàm | Mô tả | Sử dụng |
|-----|-------|---------|
| `e($string)` | Escape HTML (XSS prevention) | `<?= e($user['name']) ?>` |
| `formatMoney($amount)` | Format tiền VND | `formatMoney(50000)` → "50.000 đ" |
| `isLoggedIn()` | Kiểm tra đăng nhập | `if (isLoggedIn()) {...}` |
| `redirect($url)` | Redirect và exit | `redirect('index.php')` |
| `setFlash($key, $msg)` | Set flash message | `setFlash('success', 'Thành công!')` |
| `getFlash($key)` | Get và xóa flash message | `$msg = getFlash('error')` |
| `hasFlash($key)` | Kiểm tra có flash không | `if (hasFlash('success'))` |
| `isValidEmail($email)` | Validate email | `isValidEmail('test@mail.com')` |
| `sanitize($data)` | Sanitize input | `$name = sanitize($_POST['name'])` |

---

## 7. API ENDPOINTS (URL Patterns)

### 7.1 Public Routes

| URL | Method | Mô tả |
|-----|--------|-------|
| `index.php` | GET | Trang chủ |
| `index.php?page=menu` | GET | Danh sách menu |
| `index.php?page=menu&action=detail&id={id}` | GET | Chi tiết sản phẩm |
| `index.php?page=menu&action=search&q={keyword}` | GET | Tìm kiếm |
| `index.php?page=login` | GET | Form đăng nhập |
| `index.php?page=login&action=submit` | POST | Xử lý đăng nhập |
| `index.php?page=register` | GET | Form đăng ký |
| `index.php?page=register&action=submit` | POST | Xử lý đăng ký |
| `index.php?page=logout` | GET | Đăng xuất |
| `index.php?page=posts` | GET | Danh sách bài viết |
| `index.php?page=post&id={id}` | GET | Chi tiết bài viết |
| `index.php?page=contact` | GET | Form liên hệ |
| `index.php?page=contact&action=store` | POST | Gửi liên hệ |
| `index.php?page=policy&type={type}` | GET | Trang chính sách |
| `index.php?page=about` | GET | Giới thiệu |

### 7.2 Authenticated Routes (Yêu cầu đăng nhập)

| URL | Method | Mô tả |
|-----|--------|-------|
| `index.php?page=cart` | GET | Xem giỏ hàng |
| `index.php?page=cart&action=add&id={id}` | GET/POST | Thêm vào giỏ |
| `index.php?page=cart&action=update` | POST | Cập nhật giỏ |
| `index.php?page=cart&action=remove&id={id}` | GET | Xóa khỏi giỏ |
| `index.php?page=cart&action=checkout` | GET | Trang checkout |
| `index.php?page=cart&action=placeOrder` | POST | Đặt hàng |
| `index.php?page=orders` | GET | Lịch sử đơn hàng |
| `index.php?page=orders&action=detail&id={id}` | GET | Chi tiết đơn |
| `index.php?page=orders&action=cancel` | POST | Hủy đơn hàng |
| `index.php?page=profile` | GET | Hồ sơ cá nhân |
| `index.php?page=profile&action=update` | POST | Cập nhật hồ sơ |
| `index.php?page=favorites` | GET | Danh sách yêu thích |
| `index.php?page=favorites&action=toggle&id={id}` | GET | Toggle yêu thích |
| `index.php?page=comments&action=submit_order_reviews` | POST | Gửi đánh giá |

### 7.3 Admin Routes (Yêu cầu role=admin)

| URL | Method | Mô tả |
|-----|--------|-------|
| `index.php?page=admin` | GET | Dashboard |
| `index.php?page=admin&section=users` | GET | Quản lý users |
| `index.php?page=admin&section=users&action=update_status` | POST | Cập nhật status |
| `index.php?page=admin&section=products` | GET | Quản lý sản phẩm |
| `index.php?page=admin&section=products&action=create` | GET | Form thêm SP |
| `index.php?page=admin&section=products&action=store` | POST | Lưu SP mới |
| `index.php?page=admin&section=products&action=edit&id={id}` | GET | Form sửa SP |
| `index.php?page=admin&section=products&action=update&id={id}` | POST | Cập nhật SP |
| `index.php?page=admin&section=products&action=delete&id={id}` | POST | Xóa SP |
| `index.php?page=admin&section=orders` | GET | Quản lý đơn hàng |
| `index.php?page=admin&section=orders&action=detail&id={id}` | GET | Chi tiết đơn |
| `index.php?page=admin&section=orders&action=update_status` | POST | Cập nhật trạng thái |
| `index.php?page=admin&section=posts` | GET | Quản lý bài viết |
| `index.php?page=admin&section=contacts` | GET | Quản lý liên hệ |
| `index.php?page=admin&section=revenue` | GET | Báo cáo doanh thu |

---

## 8. CÂU HỎI THƯỜNG GẶP (FAQ)

### Q1: Dự án sử dụng framework gì?
**A**: Không sử dụng framework, PHP thuần với kiến trúc MVC tự xây dựng.

### Q2: Routing hoạt động như thế nào?
**A**: Sử dụng GET parameters (`$_GET['page']`, `$_GET['action']`) và switch-case trong `index.php`. Không sử dụng URL rewriting phức tạp.

### Q3: Authentication được xử lý như thế nào?
**A**: Session-based authentication. Sau khi login thành công, thông tin user được lưu trong `$_SESSION`. Hỗ trợ cả đăng nhập thường và Google OAuth.

### Q4: Giỏ hàng lưu ở đâu?
**A**: Giỏ hàng lưu trong `$_SESSION['cart']` (session-based), không lưu database. Điều này có nghĩa giỏ hàng sẽ mất khi session hết hạn.

### Q5: Làm sao phân biệt admin và customer?
**A**: Dựa vào `$_SESSION['role']`. Admin controllers kiểm tra role trong constructor và redirect nếu không phải admin.

### Q6: Database connection được quản lý như thế nào?
**A**: Sử dụng Singleton pattern trong `Database` class. Chỉ tạo 1 connection duy nhất trong suốt request.

### Q7: Làm sao xử lý flash messages?
**A**: Sử dụng `setFlash('key', 'message')` để set và `getFlash('key')` để get (tự động xóa sau khi get).

### Q8: File upload được xử lý như thế nào?
**A**: Upload vào thư mục `public/images/`. Kiểm tra extension, MIME type, và kích thước file. Avatar max 2MB, product images max 5MB.

### Q9: Thanh toán online đã tích hợp chưa?
**A**: Chưa. Các phương thức MoMo, VNPay, Credit Card chỉ là placeholder, chưa tích hợp API thực tế.

### Q10: Làm sao deploy dự án?
**A**: 
1. Upload code lên server có Apache + PHP + MySQL
2. Import `fastfood_db.sql` vào MySQL
3. Cập nhật credentials trong `config/database.php`
4. Cập nhật `BASE_URL` trong `config/config.php`
5. Đảm bảo `mod_rewrite` được enable
6. Set permissions cho thư mục `public/images/`

---

## 9. HƯỚNG DẪN PHÁT TRIỂN

### 9.1 Thêm Controller mới
```php
// 1. Tạo file controllers/NewController.php
class NewController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        // Logic
        require_once 'views/new/index.php';
    }
}

// 2. Thêm require trong index.php
require_once 'controllers/NewController.php';

// 3. Thêm route trong switch-case
case 'new':
    $controller = new NewController();
    $controller->index();
    break;
```

### 9.2 Thêm bảng Database mới
```sql
-- 1. Tạo bảng trong fastfood_db.sql
CREATE TABLE IF NOT EXISTS new_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Chạy SQL trên database
```

### 9.3 Thêm Helper Function
```php
// Thêm vào helpers/functions.php
function newHelper($param) {
    // Logic
    return $result;
}
```

---

## 10. TÓM TẮT

### Điểm mạnh:
✅ Cấu trúc MVC rõ ràng, dễ hiểu
✅ Sử dụng Prepared Statements (chống SQL Injection)
✅ Hỗ trợ Google OAuth
✅ Đầy đủ tính năng e-commerce cơ bản
✅ Admin dashboard với thống kê
✅ Database design tốt với indexes và foreign keys

### Điểm cần cải thiện:
❌ Hardcoded credentials (cần dùng env variables)
❌ Thiếu CSRF protection
❌ Thiếu rate limiting
❌ Session-based cart (không persistent)
❌ Code duplication trong một số controllers
❌ Chưa tích hợp payment gateway thực tế
❌ Thiếu unit tests

### Khuyến nghị trước khi Production:
1. Di chuyển credentials sang environment variables
2. Implement CSRF protection
3. Thêm rate limiting cho authentication
4. Implement proper error logging
5. Enable HTTPS
6. Thêm Content Security Policy headers
