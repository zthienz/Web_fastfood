<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="content-container">
            <!-- Bộ lọc -->
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="section" value="orders">
                    
                    <input type="text" name="search" placeholder="Tìm mã đơn, tên KH, SĐT..." 
                           value="<?= e($_GET['search'] ?? '') ?>" class="search-input">
                    
                    <select name="order_status" class="filter-select">
                        <option value="">Tất cả trạng thái đơn</option>
                        <option value="pending" <?= ($_GET['order_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                        <option value="confirmed" <?= ($_GET['order_status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                        <option value="preparing" <?= ($_GET['order_status'] ?? '') === 'preparing' ? 'selected' : '' ?>>Đang chuẩn bị</option>
                        <option value="shipping" <?= ($_GET['order_status'] ?? '') === 'shipping' ? 'selected' : '' ?>>Đang giao</option>
                        <option value="delivered" <?= ($_GET['order_status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Đã giao</option>
                        <option value="cancelled" <?= ($_GET['order_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                    
                    <select name="payment_status" class="filter-select">
                        <option value="">Tất cả thanh toán</option>
                        <option value="pending" <?= ($_GET['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Chờ thanh toán</option>
                        <option value="paid" <?= ($_GET['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                        <option value="failed" <?= ($_GET['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Thất bại</option>
                        <option value="refunded" <?= ($_GET['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Lọc</button>
                    <a href="index.php?page=admin&section=orders" class="btn btn-secondary">Xóa lọc</a>
                </form>
            </div>
            
            <!-- Bảng danh sách -->
            <div class="table-section">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>SĐT</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái đơn</th>
                            <th>Ngày đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có đơn hàng nào</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?= e($order['order_number']) ?></strong></td>
                                <td><?= e($order['customer_name']) ?></td>
                                <td><?= e($order['customer_phone']) ?></td>
                                <td><strong><?= formatMoney($order['total']) ?></strong></td>
                                <td>
                                    <?php
                                    $paymentClass = [
                                        'pending' => 'payment-pending',
                                        'paid' => 'payment-paid',
                                        'failed' => 'payment-failed',
                                        'refunded' => 'payment-refunded'
                                    ];
                                    $paymentText = [
                                        'pending' => 'Chờ thanh toán',
                                        'paid' => 'Đã thanh toán',
                                        'failed' => 'Thất bại',
                                        'refunded' => 'Đã hoàn tiền'
                                    ];
                                    ?>
                                    <span class="status-badge <?= $paymentClass[$order['payment_status']] ?>">
                                        <?= $paymentText[$order['payment_status']] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $orderClass = [
                                        'pending' => 'order-pending',
                                        'confirmed' => 'order-confirmed',
                                        'preparing' => 'order-preparing',
                                        'shipping' => 'order-shipping',
                                        'delivered' => 'order-delivered',
                                        'cancelled' => 'order-cancelled'
                                    ];
                                    $orderText = [
                                        'pending' => 'Chờ xác nhận',
                                        'confirmed' => 'Đã xác nhận',
                                        'preparing' => 'Đang chuẩn bị',
                                        'shipping' => 'Đang giao',
                                        'delivered' => 'Đã giao',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    ?>
                                    <span class="status-badge <?= $orderClass[$order['order_status']] ?>">
                                        <?= $orderText[$order['order_status']] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                <td class="action-buttons">
                                    <a href="index.php?page=admin&section=orders&action=detail&id=<?= $order['id'] ?>" 
                                       class="btn-icon btn-view" title="Xem chi tiết">👁️</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?page=admin&section=orders&p=<?= $i ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['order_status']) ? '&order_status=' . $_GET['order_status'] : '' ?><?= isset($_GET['payment_status']) ? '&payment_status=' . $_GET['payment_status'] : '' ?>" 
                       class="page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
