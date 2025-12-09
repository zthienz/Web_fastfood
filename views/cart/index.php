<?php 
$pageTitle = 'Giỏ hàng - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <h2>Giỏ hàng của bạn</h2>
    
    <?php if (!empty($cartItems)): ?>
        <div class="cart-container">
            <table class="cart-table">
                <thead>
                    <tr>
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
                                <form method="POST" action="index.php?page=cart&action=update" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $item['product']['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                           min="1" style="width: 60px; padding: 5px;">
                                    <button type="submit" class="btn-small">Cập nhật</button>
                                </form>
                            </td>
                            <td><strong><?= formatMoney($item['subtotal']) ?></strong></td>
                            <td>
                                <a href="index.php?page=cart&action=remove&id=<?= $item['product']['id'] ?>" 
                                   class="btn-remove"
                                   onclick="return confirm('Bạn có chắc muốn xóa món này?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                        <td colspan="2"><strong style="color: #ff5722; font-size: 20px;"><?= formatMoney($total) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="cart-actions">
                <a href="index.php?page=menu" class="btn btn-secondary">Tiếp tục mua hàng</a>
                <a href="index.php?page=cart&action=checkout" class="btn">Đặt hàng</a>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <p>Giỏ hàng của bạn đang trống!</p>
            <a href="index.php?page=menu" class="btn">Xem thực đơn</a>
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
}

.cart-table th {
    background: #f5f5f5;
    font-weight: 600;
}

.btn-small {
    padding: 5px 10px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-remove {
    color: #f44336;
    text-decoration: none;
}

.btn-remove:hover {
    text-decoration: underline;
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.btn-secondary {
    background: #757575;
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
</style>

<?php require_once 'views/layouts/footer.php'; ?>
