<?php 
$pageTitle = 'Giỏ hàng - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <h2>Giỏ hàng của bạn</h2>
    
    <?php if (!empty($cartItems)): ?>
        <div class="cart-container">
            <form id="cartForm" method="POST" action="index.php?page=cart&action=checkout">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="select-all-container">
                                    <label class="custom-checkbox">
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label for="selectAll" class="select-all-label">Chọn tất cả</label>
                                </div>
                            </th>
                            <th>Hình ảnh</th>
                            <th>Tên món</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <?php 
                            $price = $item['product']['sale_price'] ?? $item['product']['price'];
                            $image = $item['product']['primary_image'] ?? $item['product']['image'] ?? '';
                        ?>
                        <tr>
                            <td>
                                <div class="checkbox-container">
                                    <label class="custom-checkbox">
                                        <input type="checkbox" 
                                               name="selected_items[]" 
                                               value="<?= $item['product']['id'] ?>"
                                               class="item-checkbox"
                                               data-price="<?= $item['subtotal'] ?>"
                                               onchange="updateTotal()"
                                               checked>
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <img src="<?= getImageUrl($image) ?>" 
                                     alt="<?= e($item['product']['name']) ?>"
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                            </td>
                            <td><?= e($item['product']['name']) ?></td>
                            <td>
                                <?php if (!empty($item['product']['sale_price'])): ?>
                                    <span style="text-decoration: line-through; color: #999; font-size: 12px;">
                                        <?= formatMoney($item['product']['price']) ?>
                                    </span><br>
                                    <span style="color: #ff5722; font-weight: bold;">
                                        <?= formatMoney($item['product']['sale_price']) ?>
                                    </span>
                                <?php else: ?>
                                    <?= formatMoney($item['product']['price']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="number" 
                                       name="quantity" 
                                       value="<?= $item['quantity'] ?>" 
                                       min="1" 
                                       style="width: 60px; padding: 5px;"
                                       data-product-id="<?= $item['product']['id'] ?>"
                                       onchange="updateQuantity(this)">
                            </td>
                            <td><strong><?= formatMoney($item['subtotal']) ?></strong></td>
                            <td>
                                <a href="index.php?page=cart&action=remove&id=<?= $item['product']['id'] ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Bạn có chắc muốn xóa món này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                        <td colspan="2"><strong style="color: #ff5722; font-size: 20px;" id="totalAmount"><?= formatMoney($total) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="cart-actions">
                <a href="index.php?page=menu" class="btn btn-continue">Tiếp tục mua hàng</a>
                <button type="submit" class="btn btn-orange" id="checkoutBtn">Đặt hàng các món đã chọn</button>
            </div>
        </form>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <p>Giỏ hàng của bạn đang trống!</p>
            <a href="index.php?page=menu" class="btn btn-orange">Xem thực đơn</a>
        </div>
    <?php endif; ?>
</div>

<style>
.cart-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.cart-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.cart-table th,
.cart-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.cart-table th:first-child,
.cart-table td:first-child {
    text-align: center;
    width: 80px;
}

.cart-table th {
    background: #f5f5f5;
    font-weight: 600;
}

/* Custom Checkbox Styles */
.checkbox-container {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.custom-checkbox {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.custom-checkbox input[type="checkbox"] {
    opacity: 0;
    position: absolute;
    width: 0;
    height: 0;
}

.checkmark {
    position: relative;
    display: inline-block;
    width: 22px;
    height: 22px;
    background: #fff;
    border: 2px solid #ddd;
    border-radius: 6px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
    left: 7px;
    top: 3px;
    width: 6px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.custom-checkbox input:checked ~ .checkmark {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    border-color: #ff5722;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
}

.custom-checkbox input:checked ~ .checkmark:after {
    display: block;
    animation: checkmark 0.3s ease-in-out;
}

@keyframes checkmark {
    0% {
        opacity: 0;
        transform: rotate(45deg) scale(0);
    }
    50% {
        opacity: 1;
        transform: rotate(45deg) scale(1.2);
    }
    100% {
        opacity: 1;
        transform: rotate(45deg) scale(1);
    }
}

.custom-checkbox:hover .checkmark {
    border-color: #ff5722;
    box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
}

.custom-checkbox input:indeterminate ~ .checkmark {
    background: linear-gradient(135deg, #ff9800, #f57c00);
    border-color: #ff9800;
}

.custom-checkbox input:indeterminate ~ .checkmark:after {
    display: block;
    left: 4px;
    top: 9px;
    width: 12px;
    height: 2px;
    border: none;
    background: white;
    border-radius: 1px;
    transform: none;
}

.select-all-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.select-all-label {
    font-weight: 600;
    color: inherit;
    cursor: pointer;
    user-select: none;
    font-size: inherit;
    white-space: nowrap;
}

.select-all-label:hover {
    color: #ff5722;
}

/* Orange Button Styles */
.btn-orange {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
    font-size: 16px;
}

.btn-orange:hover {
    background: linear-gradient(135deg, #ff5722, #e64a19);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
    color: white;
    text-decoration: none;
}

.btn-orange:disabled {
    background: linear-gradient(135deg, #ccc, #999) !important;
    cursor: not-allowed !important;
    transform: none !important;
    box-shadow: none !important;
    opacity: 0.6 !important;
}

.btn-orange:disabled:hover {
    background: linear-gradient(135deg, #ccc, #999) !important;
    transform: none !important;
    box-shadow: none !important;
    opacity: 0.6 !important;
}

.btn-continue {
    background: linear-gradient(135deg, #757575, #616161);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(117, 117, 117, 0.3);
    font-size: 16px;
}

.btn-continue:hover {
    background: linear-gradient(135deg, #616161, #424242);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(117, 117, 117, 0.4);
    color: white;
    text-decoration: none;
}

.btn-delete {
    background: linear-gradient(135deg, #f44336, #d32f2f);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 15px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
    font-size: 12px;
    display: inline-block;
}

.btn-delete:hover {
    background: linear-gradient(135deg, #d32f2f, #c62828);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(244, 67, 54, 0.4);
    color: white;
    text-decoration: none;
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    gap: 15px;
}

.empty-cart {
    text-align: center;
    padding: 60px 20px;
}

.empty-cart p {
    font-size: 18px;
    color: #666;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .cart-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .cart-table {
        font-size: 14px;
    }
    
    .cart-table th,
    .cart-table td {
        padding: 10px 5px;
    }
}
</style>

<script>
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateTotal();
}

function updateTotal() {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    const selectAllCheckbox = document.getElementById('selectAll');
    const totalAmountElement = document.getElementById('totalAmount');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    let total = 0;
    itemCheckboxes.forEach(checkbox => {
        total += parseFloat(checkbox.dataset.price);
    });
    
    // Cập nhật hiển thị tổng tiền
    totalAmountElement.textContent = formatMoney(total);
    
    // Cập nhật trạng thái checkbox "Chọn tất cả"
    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    
    if (checkedCheckboxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
        checkoutBtn.disabled = true;
        checkoutBtn.textContent = 'Vui lòng chọn món để đặt hàng';
        checkoutBtn.style.opacity = '0.6';
        checkoutBtn.style.cursor = 'not-allowed';
    } else if (checkedCheckboxes.length === allCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
        checkoutBtn.disabled = false;
        checkoutBtn.textContent = 'Đặt hàng các món đã chọn';
        checkoutBtn.style.opacity = '1';
        checkoutBtn.style.cursor = 'pointer';
    } else {
        selectAllCheckbox.indeterminate = true;
        selectAllCheckbox.checked = false;
        checkoutBtn.disabled = false;
        checkoutBtn.textContent = 'Đặt hàng các món đã chọn';
        checkoutBtn.style.opacity = '1';
        checkoutBtn.style.cursor = 'pointer';
    }
}

function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount).replace('₫', 'đ');
}

// Cập nhật số lượng sản phẩm bằng AJAX
function updateQuantity(input) {
    const productId = input.dataset.productId;
    const quantity = parseInt(input.value);
    
    if (quantity < 1) {
        input.value = 1;
        return;
    }
    
    // Hiển thị loading
    input.disabled = true;
    
    // Gửi AJAX request
    fetch('index.php?page=cart&action=update', {
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
            // Reload trang để cập nhật giá tiền
            window.location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
            input.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật số lượng!');
        input.disabled = false;
    });
}

// Ngăn chặn submit form nếu không có món nào được chọn
document.getElementById('cartForm').addEventListener('submit', function(e) {
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    if (checkedItems.length === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất một món để đặt hàng!');
    }
});

// Khởi tạo trạng thái ban đầu
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>
