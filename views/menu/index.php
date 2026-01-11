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
            
            <!-- Tìm kiếm -->
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="q" placeholder="Tìm kiếm món ăn..." 
                           value="<?= e($_GET['q'] ?? '') ?>"
                           class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Bộ lọc -->
            <div class="filters-container">
                <div class="filter-grid">
                    <!-- Lọc theo danh mục -->
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-list"></i>
                            DANH MỤC:
                        </label>
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
                        <label class="filter-label">
                            <i class="fas fa-info-circle"></i>
                            TRẠNG THÁI:
                        </label>
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
                        <label class="filter-label">
                            <i class="fas fa-sort"></i>
                            SẮP XẾP:
                        </label>
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
                    <label class="filter-label">
                        <i class="fas fa-dollar-sign"></i>
                        KHOẢNG GIÁ:
                    </label>
                    <div class="price-range">
                        <input type="number" name="min_price" placeholder="Từ" 
                               value="<?= e($_GET['min_price'] ?? '') ?>" 
                               class="price-input" min="0">
                        <span class="price-separator">-</span>
                        <input type="number" name="max_price" placeholder="Đến" 
                               value="<?= e($_GET['max_price'] ?? '') ?>" 
                               class="price-input" min="0">
                        <span class="price-unit">VNĐ</span>
                    </div>
                </div>
                
                <!-- Nút hành động -->
                <div class="filter-actions">
                    <button type="submit" class="btn btn-filter">
                        <i class="fas fa-filter"></i>
                        LỌC
                    </button>
                    <a href="index.php?page=menu" class="btn btn-clear">
                        <i class="fas fa-times"></i>
                        XÓA BỘ LỌC
                    </a>
                </div>
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
                    </a>
                    
                    <div class="content">
                        <div class="content-body">
                            <a href="index.php?page=menu&action=detail&id=<?= $product['id'] ?>" class="product-link">
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
                        </div>
                        
                        <?php if ($product['status'] === 'out_of_stock' || $product['stock_quantity'] <= 0): ?>
                            <button class="btn btn-disabled" disabled>Hết hàng</button>
                        <?php elseif (isLoggedIn()): ?>
                            <button onclick="addToCartAjax(<?= $product['id'] ?>, 1, this)" 
                                    class="btn btn-orange add-to-cart-btn">Thêm vào giỏ</button>
                        <?php else: ?>
                            <a href="index.php?page=login" 
                               class="btn btn-orange"
                               onclick="return confirm('Bạn cần đăng nhập để thêm món ăn vào giỏ hàng. Bạn có muốn đăng nhập ngay không?')">
                               Thêm vào giỏ
                            </a>
                        <?php endif; ?>
                    </div>
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
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

/* Search Container */
.search-container {
    margin-bottom: 25px;
}

.search-box {
    position: relative;
    max-width: 500px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    background: white;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.search-box:focus-within {
    border-color: #ff6b35;
    box-shadow: 0 4px 20px rgba(255, 107, 53, 0.2);
}

.search-icon {
    position: absolute;
    left: 20px;
    color: #999;
    font-size: 16px;
    z-index: 2;
}

.search-input {
    flex: 1;
    padding: 15px 20px 15px 50px;
    border: none;
    font-size: 16px;
    outline: none;
    background: transparent;
    color: #333;
}

.search-input::placeholder {
    color: #999;
}

.search-btn {
    padding: 15px 25px;
    background: linear-gradient(135deg, #ff5722, #f50057);
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    min-width: 120px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.search-btn:hover {
    background: linear-gradient(135deg, #f50057, #e91e63);
    transform: translateX(-2px);
    box-shadow: 0 4px 15px rgba(245, 0, 87, 0.3);
}

.search-btn i {
    margin-right: 5px;
}

.search-btn::after {
    content: 'TÌM KIẾM';
    margin-left: 8px;
    font-size: 12px;
    font-weight: 700;
}

/* Filters Container */
.filters-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-label i {
    color: #ff6b35;
    font-size: 14px;
}

.filter-select {
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    outline: none;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-select:focus,
.filter-select:hover {
    border-color: #ff6b35;
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

/* Price Filter */
.price-filter {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 20px;
}

.price-filter .filter-label {
    margin-bottom: 12px;
}

.price-range {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.price-input {
    flex: 1;
    min-width: 120px;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
}

.price-input:focus {
    border-color: #ff6b35;
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.price-separator {
    font-weight: bold;
    color: #6c757d;
    font-size: 16px;
}

.price-unit {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
    white-space: nowrap;
}

/* Filter Actions */
.filter-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 140px;
    justify-content: center;
}

.btn-filter {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: white;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-filter:hover {
    background: linear-gradient(135deg, #ff5722, #e64a19);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.btn-clear {
    background: #6c757d;
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-clear:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
}

/* Active Filter Indicator */
.filter-select.active-filter,
.filter-select:not([value=""]) {
    border-color: #ff6b35;
    background: rgba(255, 107, 53, 0.05);
}

.price-input.active-filter,
.price-input:not(:placeholder-shown) {
    border-color: #ff6b35;
    background: rgba(255, 107, 53, 0.05);
}

.search-input.active-filter {
    background: rgba(255, 107, 53, 0.05);
}

/* Loading State */
.filter-loading {
    opacity: 0.7;
    pointer-events: none;
}

.filter-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #ff6b35;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .filter-section {
        padding: 20px 15px;
    }
    
    .search-box {
        max-width: 100%;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .price-range {
        flex-direction: column;
        align-items: stretch;
    }
    
    .price-input {
        min-width: auto;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .btn {
        min-width: auto;
        width: 100%;
    }
}

@media (max-width: 480px) {
    .search-input {
        padding: 12px 15px 12px 45px;
        font-size: 14px;
    }
    
    .search-btn {
        padding: 12px 20px;
    }
    
    .search-icon {
        left: 15px;
        font-size: 14px;
    }
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
    
    // Kiểm tra xem form có tồn tại không
    if (!filterForm) {
        console.error('Filter form not found');
        return;
    }
    
    const filterSelects = filterForm.querySelectorAll('.filter-select');
    const searchInput = filterForm.querySelector('.search-input');
    const priceInputs = filterForm.querySelectorAll('.price-input');
    const actionInput = filterForm.querySelector('input[name="action"]');
    
    // Kiểm tra các elements cần thiết
    if (!searchInput || !actionInput) {
        console.error('Required form elements not found');
        return;
    }
    
    console.log('Filter form initialized successfully');
    
    // Auto-submit when filter selects change
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            console.log('Filter select changed:', this.name, this.value);
            
            // If search is being used, set action to search
            if (searchInput.value.trim()) {
                actionInput.value = 'search';
            } else {
                actionInput.value = '';
            }
            
            // Submit form
            filterForm.submit();
        });
    });
    
    // Handle search input with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        console.log('Search input changed:', this.value);
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            if (this.value.trim()) {
                actionInput.value = 'search';
                filterForm.submit();
            } else {
                actionInput.value = '';
                // Optionally submit to clear search results
                filterForm.submit();
            }
        }, 500); // Increased timeout for better UX
    });
    
    // Handle price inputs with debounce
    let priceTimeout;
    priceInputs.forEach(input => {
        input.addEventListener('input', function() {
            console.log('Price input changed:', this.name, this.value);
            clearTimeout(priceTimeout);
            
            priceTimeout = setTimeout(() => {
                // If search is being used, set action to search
                if (searchInput.value.trim()) {
                    actionInput.value = 'search';
                } else {
                    actionInput.value = '';
                }
                
                filterForm.submit();
            }, 1000); // Submit after 1 second of no typing
        });
    });
    
    // Handle manual form submission
    filterForm.addEventListener('submit', function(e) {
        console.log('Form submitted manually');
        
        const searchValue = searchInput.value.trim();
        if (searchValue) {
            actionInput.value = 'search';
        } else {
            actionInput.value = '';
        }
        
        // Let the form submit normally
    });
    
    // Add visual feedback for active filters
    updateActiveFilters();
    
    function updateActiveFilters() {
        // Highlight active filters
        filterSelects.forEach(select => {
            if (select.value && select.value !== '') {
                select.classList.add('active-filter');
            } else {
                select.classList.remove('active-filter');
            }
        });
        
        priceInputs.forEach(input => {
            if (input.value && input.value !== '') {
                input.classList.add('active-filter');
            } else {
                input.classList.remove('active-filter');
            }
        });
        
        if (searchInput.value.trim()) {
            searchInput.classList.add('active-filter');
        } else {
            searchInput.classList.remove('active-filter');
        }
    }
    
    // Update active filters on change
    [...filterSelects, ...priceInputs, searchInput].forEach(element => {
        element.addEventListener('change', updateActiveFilters);
        element.addEventListener('input', updateActiveFilters);
    });
    
    // Test filter functionality
    console.log('Filter elements found:', {
        filterSelects: filterSelects.length,
        searchInput: !!searchInput,
        priceInputs: priceInputs.length,
        actionInput: !!actionInput
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
        max-width: 350px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        ${type === 'success' ? 'background: #4CAF50;' : 'background: #f44336;'}
    `;
    
    document.body.appendChild(notification);
    
    // Tự động xóa sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

function addToCartAjax(productId, quantity, buttonElement) {
    // Disable button để tránh click nhiều lần
    const originalText = buttonElement.textContent;
    buttonElement.disabled = true;
    buttonElement.textContent = 'Đang thêm...';
    buttonElement.style.opacity = '0.7';
    
    fetch('index.php?page=cart&action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Cập nhật số lượng giỏ hàng trong header nếu có
            updateCartCount(data.cart_count);
            
            // Hiệu ứng thành công cho button
            buttonElement.style.background = '#4CAF50';
            buttonElement.textContent = '✓ Đã thêm';
            
            setTimeout(() => {
                buttonElement.style.background = '';
                buttonElement.textContent = originalText;
                buttonElement.disabled = false;
                buttonElement.style.opacity = '1';
            }, 1500);
            
        } else {
            showNotification(data.message, 'error');
            
            // Reset button
            buttonElement.disabled = false;
            buttonElement.textContent = originalText;
            buttonElement.style.opacity = '1';
            
            // Nếu cần redirect (như đăng nhập)
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');
        
        // Reset button
        buttonElement.disabled = false;
        buttonElement.textContent = originalText;
        buttonElement.style.opacity = '1';
    });
}

function updateCartCount(count) {
    // Cập nhật số lượng trong header cart badge
    const cartBadges = document.querySelectorAll('.cart-badge');
    cartBadges.forEach(badge => {
        badge.textContent = count;
        if (count > 0) {
            badge.style.display = 'flex';
            // Thêm animation pulse
            badge.style.animation = 'pulse 0.3s ease-out';
        } else {
            badge.style.display = 'none';
        }
    });
    
    // Nếu chưa có badge, tạo mới
    if (cartBadges.length === 0 && count > 0) {
        const cartLink = document.querySelector('.cart-link');
        if (cartLink) {
            const badge = document.createElement('span');
            badge.className = 'cart-badge';
            badge.textContent = count;
            cartLink.appendChild(badge);
        }
    }
}

// Test filter functionality when page loads
window.addEventListener('load', function() {
    console.log('Page loaded, testing filter functionality...');
    
    // Test if form elements exist
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        console.log('✓ Filter form found');
        
        const filterSelects = filterForm.querySelectorAll('.filter-select');
        const searchInput = filterForm.querySelector('.search-input');
        const priceInputs = filterForm.querySelectorAll('.price-input');
        
        console.log('Filter elements:', {
            selects: filterSelects.length,
            searchInput: !!searchInput,
            priceInputs: priceInputs.length
        });
        
        // Test a filter change
        if (filterSelects.length > 0) {
            console.log('✓ Filter selects available for testing');
        }
    } else {
        console.error('✗ Filter form not found!');
    }
});

function testFilter() {
    console.log('=== FILTER TEST ===');
    
    const filterForm = document.getElementById('filterForm');
    if (!filterForm) {
        alert('❌ Filter form not found!');
        return;
    }
    
    console.log('✓ Filter form found');
    
    // Test form elements
    const filterSelects = filterForm.querySelectorAll('.filter-select');
    const searchInput = filterForm.querySelector('.search-input');
    const priceInputs = filterForm.querySelectorAll('.price-input');
    const actionInput = filterForm.querySelector('input[name="action"]');
    
    console.log('Form elements:', {
        filterSelects: filterSelects.length,
        searchInput: !!searchInput,
        priceInputs: priceInputs.length,
        actionInput: !!actionInput
    });
    
    // Test current values
    const currentValues = {
        category: filterForm.querySelector('select[name="category"]')?.value || '',
        status: filterForm.querySelector('select[name="status"]')?.value || '',
        sort: filterForm.querySelector('select[name="sort"]')?.value || '',
        min_price: filterForm.querySelector('input[name="min_price"]')?.value || '',
        max_price: filterForm.querySelector('input[name="max_price"]')?.value || '',
        search: searchInput?.value || ''
    };
    
    console.log('Current filter values:', currentValues);
    
    // Test form action
    console.log('Form action:', filterForm.action);
    console.log('Form method:', filterForm.method);
    
    // Test submit
    const hasActiveFilters = Object.values(currentValues).some(value => value !== '');
    
    if (hasActiveFilters) {
        console.log('✓ Has active filters, testing submit...');
        
        // Show confirmation
        if (confirm('Test filter submit? This will reload the page with current filter values.')) {
            console.log('Submitting form...');
            filterForm.submit();
        }
    } else {
        alert('ℹ️ No active filters to test. Try selecting a category or entering a search term first.');
    }
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
    
    .add-to-cart-btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .add-to-cart-btn:disabled {
        cursor: not-allowed;
    }
    
    .add-to-cart-btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        margin: -8px 0 0 -8px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>

<?php require_once 'views/layouts/footer.php'; ?>
