<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <div class="form-header">
                <h2>Chi tiết đơn hàng: <?= e($order['order_number']) ?></h2>
                <a href="index.php?page=admin&section=orders" class="btn btn-secondary">← Quay lại</a>
            </div>
            
            <div class="order-detail-grid">
                <!-- Thông tin đơn hàng -->
                <div class="detail-card">
                    <h3>Thông tin đơn hàng</h3>
                    <table class="info-table">
                        <tr>
                            <td><strong>Mã đơn hàng:</strong></td>
                            <td><?= e($order['order_number']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Ngày đặt:</strong></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Phương thức thanh toán:</strong></td>
                            <td>
                                <?php
                                $paymentMethods = [
                                    'cod' => 'Thanh toán khi nhận hàng',
                                    'bank_transfer' => 'Chuyển khoản ngân hàng',
                                    'momo' => 'Ví MoMo',
                                    'vnpay' => 'VNPay',
                                    'credit_card' => 'Thẻ tín dụng'
                                ];
                                echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Ghi chú:</strong></td>
                            <td><?= e($order['notes'] ?? 'Không có') ?></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Thông tin khách hàng -->
                <div class="detail-card">
                    <h3>Thông tin khách hàng</h3>
                    <table class="info-table">
                        <tr>
                            <td><strong>Họ tên:</strong></td>
                            <td><?= e($order['customer_name']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?= e($order['customer_email']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Số điện thoại:</strong></td>
                            <td><?= e($order['customer_phone']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Địa chỉ giao hàng:</strong></td>
                            <td><?= e($order['shipping_address']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Sản phẩm trong đơn -->
            <div class="detail-card">
                <h3>Sản phẩm đã đặt</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= getImageUrl($item['product_image']) ?>" 
                                     alt="<?= e($item['product_name']) ?>" 
                                     class="product-thumb">
                            </td>
                            <td><?= e($item['product_name']) ?></td>
                            <td><?= formatMoney($item['price']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><strong><?= formatMoney($item['subtotal']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Tạm tính:</strong></td>
                            <td><strong><?= formatMoney($order['subtotal']) ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Phí vận chuyển:</strong></td>
                            <td><?= formatMoney($order['shipping_fee']) ?></td>
                        </tr>
                        <?php if ($order['discount'] > 0): ?>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Giảm giá:</strong></td>
                            <td class="text-danger">-<?= formatMoney($order['discount']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="total-row">
                            <td colspan="4" class="text-right"><strong>TỔNG CỘNG:</strong></td>
                            <td><strong class="text-success"><?= formatMoney($order['total']) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Cập nhật trạng thái -->
            <div class="status-update-section">
                <div class="detail-card">
                    <h3>Cập nhật trạng thái đơn hàng</h3>
                    <form method="POST" action="index.php?page=admin&section=orders&action=update_status" class="status-form">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        
                        <div class="form-group">
                            <label>Trạng thái đơn hàng:</label>
                            <select name="status" class="form-control">
                                <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                <option value="confirmed" <?= $order['order_status'] === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                <option value="preparing" <?= $order['order_status'] === 'preparing' ? 'selected' : '' ?>>Đang chuẩn bị</option>
                                <option value="shipping" <?= $order['order_status'] === 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                                <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Đã giao hàng</option>
                                <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Ghi chú:</label>
                            <textarea name="note" rows="3" class="form-control" placeholder="Ghi chú về thay đổi trạng thái..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success">💾 Cập nhật trạng thái</button>
                    </form>
                </div>
                
                <div class="detail-card">
                    <h3>Cập nhật thanh toán</h3>
                    <form method="POST" action="index.php?page=admin&section=orders&action=update_payment_status" class="status-form">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        
                        <div class="form-group">
                            <label>Trạng thái thanh toán:</label>
                            <select name="payment_status" class="form-control">
                                <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Chờ thanh toán</option>
                                <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                                <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Thất bại</option>
                                <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success">💾 Cập nhật thanh toán</button>
                    </form>
                </div>
            </div>
            
            <!-- Lịch sử thay đổi -->
            <?php if (!empty($history)): ?>
            <div class="detail-card">
                <h3>Lịch sử thay đổi trạng thái</h3>
                <div class="history-timeline">
                    <?php foreach ($history as $h): ?>
                    <div class="history-item">
                        <div class="history-time"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></div>
                        <div class="history-content">
                            <strong>Trạng thái: <?= e($h['status']) ?></strong>
                            <?php if ($h['note']): ?>
                            <p><?= e($h['note']) ?></p>
                            <?php endif; ?>
                            <?php if ($h['admin_name']): ?>
                            <small>Bởi: <?= e($h['admin_name']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
