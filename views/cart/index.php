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
                                    <button type="submit" class="btn-update">Cập nhật</button>
                                </form>
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
                        <td colspan="4" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                        <td colspan="2"><strong style="color: #ff5722; font-size: 20px;"><?= formatMoney($total) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="cart-actions">
                <a href="index.php?page=menu" class="btn btn-continue">Tiếp tục mua hàng</a>
                <a href="index.php?page=cart&action=checkout" class="btn btn-orange">Đặt hàng</a>
            </div>
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
}

.cart-table th {
    background: #f5f5f5;
    font-weight: 600;
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

.btn-update {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
    font-size: 12px;
}

.btn-update:hover {
    background: linear-gradient(135deg, #45a049, #3d8b40);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
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

<?php require_once 'views/layouts/footer.php'; ?>
