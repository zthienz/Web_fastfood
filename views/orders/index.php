<?php 
$pageTitle = 'Đơn hàng của tôi - FastFood';
require_once 'views/layouts/header.php'; 
?>

<div class="container" style="margin-top: 30px;">
    <h2>Đơn hàng của tôi</h2>
    
    <!-- Flash Messages -->
    <?php if (hasFlash('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= getFlash('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= getFlash('error') ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($orders)): ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <strong>Đơn hàng <?= e($order['order_number']) ?></strong>
                            <span class="order-date"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div>
                            <span class="order-status status-<?= $order['order_status'] ?>">
                                <?php
                                $statusText = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'preparing' => 'Đang chuẩn bị',
                                    'shipping' => 'Đang giao',
                                    'delivered' => 'Đã giao',
                                    'cancelled' => 'Đã hủy'
                                ];
                                echo $statusText[$order['order_status']] ?? 'Không xác định';
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-body">
                        <p><strong>Số lượng món:</strong> <?= $order['total_items'] ?? 0 ?> món (<?= $order['total_quantity'] ?? 0 ?> sản phẩm)</p>
                        <p><strong>Tổng tiền:</strong> <span style="color: #ff5722; font-size: 18px; font-weight: bold;"><?= formatMoney($order['total']) ?></span></p>
                        <p><strong>Thanh toán:</strong> 
                            <span class="payment-status status-<?= $order['payment_status'] ?>">
                                <?php
                                $paymentText = [
                                    'pending' => 'Chưa thanh toán',
                                    'paid' => 'Đã thanh toán',
                                    'failed' => 'Thất bại',
                                    'refunded' => 'Đã hoàn tiền'
                                ];
                                echo $paymentText[$order['payment_status']] ?? 'Không xác định';
                                ?>
                            </span>
                        </p>
                        <?php if (!empty($order['notes'])): ?>
                            <p><strong>Ghi chú:</strong> <?= e($order['notes']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="order-footer">
                        <a href="index.php?page=orders&action=detail&id=<?= $order['id'] ?>" class="btn-link">Xem chi tiết</a>
                        
                        <?php if ($order['order_status'] === 'pending'): ?>
                            <button type="button" class="btn-cancel" onclick="cancelOrder(<?= $order['id'] ?>, '<?= e($order['order_number']) ?>')">
                                <i class="fas fa-times"></i>
                                Hủy đơn
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($order['order_status'] === 'delivered'): ?>
                            <a href="index.php?page=comments&action=order_review&order_id=<?= $order['id'] ?>" class="btn-review">
                                <i class="fas fa-star"></i>
                                Đánh giá
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-orders">
            <p>Bạn chưa có đơn hàng nào!</p>
            <a href="index.php?page=menu" class="btn">Đặt hàng ngay</a>
        </div>
    <?php endif; ?>
</div>

<!-- Form ẩn để hủy đơn hàng -->
<form id="cancelOrderForm" method="POST" action="index.php?page=orders&action=cancel" style="display: none;">
    <input type="hidden" name="order_id" id="cancelOrderId">
</form>

<script>
function cancelOrder(orderId, orderNumber) {
    if (confirm(`Bạn có chắc chắn muốn hủy đơn hàng ${orderNumber}?\n\nSau khi hủy, số lượng sản phẩm sẽ được hoàn lại vào kho và bạn không thể khôi phục đơn hàng này.`)) {
        document.getElementById('cancelOrderId').value = orderId;
        document.getElementById('cancelOrderForm').submit();
    }
}
</script>

<style>
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}

.alert-error {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}

.order-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
}

.order-date {
    color: #666;
    font-size: 14px;
    margin-left: 10px;
}

.order-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

.status-pending {
    background: #fff3e0;
    color: #f57c00;
}

.status-confirmed {
    background: #e3f2fd;
    color: #1976d2;
}

.status-preparing {
    background: #f3e5f5;
    color: #7b1fa2;
}

.status-shipping {
    background: #e1f5fe;
    color: #0277bd;
}

.status-delivered {
    background: #e8f5e9;
    color: #388e3c;
}

.status-cancelled {
    background: #ffebee;
    color: #d32f2f;
}

.status-paid {
    background: #e8f5e9;
    color: #388e3c;
}

.status-failed {
    background: #ffebee;
    color: #d32f2f;
}

.payment-status {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 13px;
}

.order-footer {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 15px;
    align-items: center;
}

.btn-link {
    color: #ff5722;
    text-decoration: none;
    font-weight: 500;
}

.btn-link:hover {
    text-decoration: underline;
}

.btn-cancel {
    background: linear-gradient(135deg, #f44336, #d32f2f);
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
}

.btn-cancel:hover {
    background: linear-gradient(135deg, #d32f2f, #c62828);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(244, 67, 54, 0.4);
}

.btn-cancel:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-review {
    background: linear-gradient(135deg, #ff6b35, #ff5722);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
}

.btn-review:hover {
    background: linear-gradient(135deg, #ff5722, #e64a19);
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.4);
}

.empty-orders {
    text-align: center;
    padding: 60px 20px;
}

.empty-orders p {
    font-size: 18px;
    color: #666;
    margin-bottom: 20px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .orders-list {
        gap: 15px;
    }
    
    .order-card {
        padding: 15px;
    }
    
    .order-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .order-footer {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
    
    .btn-review {
        text-align: center;
        justify-content: center;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>
