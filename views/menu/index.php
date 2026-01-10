<?php 
$pageTitle = 'Thực đơn - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <h2>Thực đơn</h2>
    
    <!-- Bộ lọc và tìm kiếm -->
    <div class="filter-section">
        <form method="GET" action="index.php" id="filterForm">
            <input type="hidden" name="page" value="menu">
            <input type="hidden" name="action" value="<?= isset($_GET['action']) && $_GET['action'] === 'search' ? 'search' : '' ?>">
            
            <div class="filter-row">
                <!-- Tìm kiếm -->
                <div class="search-box">
                    <input type="text" name="q" placeholder="Tìm kiếm món ăn..." 
                           value="<?= e($_GET['q'] ?? '') ?>"
                           class="search-input">
                    <button type="submit" class="search-btn">🔍</button>
                </div>
                
                <!-- Lọc theo danh mục -->
                <div class="filter-group">
                    <label>Danh mục:</label>
                    <select name="category" class="filter-select">
                        <option value="">Tất cả danh mục</option>
                        <?php if (isset($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= e($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Lọc theo trạng thái -->
                <div class="filter-group">
                    <label>Trạng thái:</label>
                    <select name="status" class="filter-select">
                        <option value="">Tất cả</option>
                        <option value="available" <?= (isset($_GET['status']) && $_GET['status'] === 'available') ? 'selected' : '' ?>>
                            Còn hàng
                        </option>
                        <option value="out_of_stock" <?= (isset($_GET['status']) && $_GET['status'] === 'out_of_stock') ? 'selected' : '' ?>>
                            Hết hàng
                        </option>
                        <option value="sale" <?= (isset($_GET['status']) && $_GET['status'] === 'sale') ? 'selected' : '' ?>>
                            Đang giảm giá
                        </option>
                    </select>
                </div>
                
                <!-- Sắp xếp -->
                <div class="filter-group">
                    <label>Sắp xếp:</label>
                    <select name="sort" class="filter-select">
                        <option value="name" <?= (isset($_GET['sort']) && $_GET['sort'] === 'name') ? 'selected' : '' ?>>
                            Tên A-Z
                        </option>
                        <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'price_asc') ? 'selected' : '' ?>>
                            Giá thấp đến cao
                        </option>
                        <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'price_desc') ? 'selected' : '' ?>>
                            Giá cao đến thấp
                        </option>
                        <option value="newest" <?= (isset($_GET['sort']) && $_GET['sort'] === 'newest') ? 'selected' : '' ?>>
                            Mới nhất
                        </option>
                        <option value="popular" <?= (isset($_GET['sort']) && $_GET['sort'] === 'popular') ? 'selected' : '' ?>>
                            Phổ biến
                        </option>
                    </select>
                </div>
            </div>
            
            <!-- Lọc theo giá -->
            <div class="price-filter">
                <label>Khoảng giá:</label>
                <div class="price-range">
                    <input type="number" name="min_price" placeholder="Từ" 
                           value="<?= e($_GET['min_price'] ?? '') ?>" 
                           class="price-input" min="0">
                    <span>-</span>
                    <input type="number" name="max_price" placeholder="Đến" 
                           value="<?= e($_GET['max_price'] ?? '') ?>" 
                           class="price-input" min="0">
                    <span>VNĐ</span>
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="index.php?page=menu" class="btn btn-secondary">Xóa bộ lọc</a>
            </div>
        </form>
    </div>

    <!-- Hiển thị kết quả -->
    <div class="results-info">
        <?php 
        $totalProducts = count($products ?? []);
        $activeFilters = [];
        if (!empty($_GET['category'])) $activeFilters[] = 'danh mục';
        if (!empty($_GET['status'])) $activeFilters[] = 'trạng thái';
        if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) $activeFilters[] = 'giá';
        if (!empty($_GET['q'])) $activeFilters[] = 'từ khóa';
        ?>
        
        <p>Hiển thị <?= $totalProducts ?> món ăn
        <?php if (!empty($activeFilters)): ?>
            (đã lọc theo: <?= implode(', ', $activeFilters) ?>)
        <?php endif; ?>
        </p>
    </div>

    <!-- Danh sách món ăn -->
    <div class="menu">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="food-item <?= ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0) ? 'out-of-stock' : '' ?>">
                    <?php if (isLoggedIn()): ?>
                        <div class="favorite-badge">
                            <button class="favorite-btn <?= isset($product['is_favorite']) && $product['is_favorite'] ? 'active' : '' ?>" 
                                    onclick="toggleFavorite(<?= $product['id'] ?>, this)"
                                    title="<?= isset($product['is_favorite']) && $product['is_favorite'] ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' ?>">
                                ❤️
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Sale badge -->
                    <?php if (!empty($product['sale_price'])): ?>
                        <div class="sale-badge">SALE</div>
                    <?php endif; ?>
                    
                    <a href="index.php?page=menu&action=detail&id=<?= $product['id'] ?>" class="product-link">
                        <img src="<?= getImageUrl($product['primary_image'] ?? $product['image'] ?? '') ?>" 
                             alt="<?= e($product['name']) ?>">
                        <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                            <div class="stock-badge out">Hết hàng</div>
                        <?php elseif ($product['stock_quantity'] <= 5): ?>
                            <div class="stock-badge low">Còn <?= $product['stock_quantity'] ?></div>
                        <?php endif; ?>
                        <h3><?= e($product['name']) ?></h3>
                    </a>
                    <p class="category-badge"><?= e($product['category_name'] ?? '') ?></p>
                    <p><?= e($product['description'] ?? '') ?></p>
                    <div class="price-wrapper">
                        <?php if (!empty($product['sale_price'])): ?>
                            <span class="original-price"><?= formatMoney($product['price']) ?></span>
                            <span class="sale-price"><?= formatMoney($product['sale_price']) ?></span>
                        <?php else: ?>
                            <span class="price"><?= formatMoney($product['price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                        <button class="btn btn-disabled" disabled>Hết hàng</button>
                    <?php elseif (isLoggedIn()): ?>
                        <a href="index.php?page=cart&action=add&id=<?= $product['id'] ?>" 
                           class="btn btn-orange">Thêm vào giỏ</a>
                    <?php else: ?>
                        <a href="index.php?page=login" 
                           class="btn btn-orange"
                           onclick="return confirm('Bạn cần đăng nhập để thêm món ăn vào giỏ hàng. Bạn có muốn đăng nhập ngay không?')">
                           Thêm vào giỏ
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <p>Không tìm thấy món ăn nào phù hợp với bộ lọc!</p>
                <a href="index.php?page=menu" class="btn btn-primary">Xem tất cả món ăn</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Filter Section Styles */
.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: end;
    margin-bottom: 15px;
}

.search-box {
    display: flex;
    min-width: 300px;
    flex: 1;
}

.search-input {
    flex: 1;
    padding: 10px 15px;
    border: 2px solid #ddd;
    border-radius: 25px 0 0 25px;
    border-right: none;
    font-size: 14px;
    outline: none;
}

.search-input:focus {
    border-color: #ff6b35;
}

.search-btn {
    padding: 10px 15px;
    background: #ff6b35;
    color: white;
    border: 2px solid #ff6b35;
    border-radius: 0 25px 25px 0;
    cursor: pointer;
    font-size: 16px;
}

.search-btn:hover {
    background: #e55a2b;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 150px;
}

.filter-group label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
    font-size: 14px;
}

.filter-select {
    padding: 8px 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    outline: none;
}

.filter-select:focus {
    border-color: #ff6b35;
}

.price-filter {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.price-filter label {
    font-weight: 600;
    color: #333;
}

.price-range {
    display: flex;
    align-items: center;
    gap: 8px;
}

.price-input {
    width: 100px;
    padding: 8px 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
}

.price-input:focus {
    border-color: #ff6b35;
}

.filter-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-primary {
    background: #ff6b35;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}

.btn-primary:hover {
    background: #e55a2b;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}

.btn-secondary:hover {
    background: #5a6268;
}

/* Results Info */
.results-info {
    margin-bottom: 20px;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.results-info p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

/* Sale Badge */
.sale-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    z-index: 5;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-results p {
    font-size: 18px;
    margin-bottom: 20px;
}

/* Favorite Button Styles */
.favorite-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}

.favorite-btn {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    color: #ccc;
}

.favorite-btn:hover {
    background: white;
    transform: scale(1.1);
}

.favorite-btn.active {
    color: #ff4757;
    background: white;
}

/* Product Link Styles */
.product-link {
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform 0.3s;
}

.product-link:hover {
    transform: translateY(-2px);
}

.product-link:hover h3 {
    color: #ff6b35;
}

/* Responsive Design */
@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        min-width: auto;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .price-filter {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .price-range {
        width: 100%;
        justify-content: space-between;
    }
    
    .price-input {
        flex: 1;
        max-width: 120px;
    }
}

/* Auto-submit on change */
.filter-select, .price-input {
    transition: border-color 0.3s;
}
</style>

<script>
// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const filterSelects = filterForm.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });
    
    // Debounce for price inputs
    const priceInputs = filterForm.querySelectorAll('.price-input');
    let priceTimeout;
    
    priceInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(priceTimeout);
            priceTimeout = setTimeout(() => {
                filterForm.submit();
            }, 1000); // Submit after 1 second of no typing
        });
    });
});

function toggleFavorite(productId, button) {
    fetch('index.php?page=favorites&action=toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.action === 'added') {
                button.classList.add('active');
                button.title = 'Xóa khỏi yêu thích';
            } else if (data.action === 'removed') {
                button.classList.remove('active');
                button.title = 'Thêm vào yêu thích';
            }
            
            // Hiển thị thông báo
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra!', 'error');
    });
}

function showNotification(message, type) {
    // Tạo notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
        ${type === 'success' ? 'background: #4CAF50;' : 'background: #f44336;'}
    `;
    
    document.body.appendChild(notification);
    
    // Tự động xóa sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>

<?php require_once 'views/layouts/footer.php'; ?>
