<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <?php include 'views/admin/layouts/header.php'; ?>
        
        <div class="dashboard-container">
            <h1>Tổng quan hệ thống</h1>
            
            <!-- Thống kê tổng quan -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon revenue">💰</div>
                    <div class="stat-info">
                        <h3>Tổng doanh thu</h3>
                        <p class="stat-value"><?= formatMoney($stats['total_revenue']) ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon orders">📦</div>
                    <div class="stat-info">
                        <h3>Tổng đơn hàng</h3>
                        <p class="stat-value"><?= number_format($stats['total_orders']) ?></p>
                        <span class="stat-badge"><?= $stats['pending_orders'] ?> đơn chờ xử lý</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon customers">👥</div>
                    <div class="stat-info">
                        <h3>Khách hàng</h3>
                        <p class="stat-value"><?= number_format($stats['total_customers']) ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon products">🍔</div>
                    <div class="stat-info">
                        <h3>Sản phẩm</h3>
                        <p class="stat-value"><?= number_format($stats['total_products']) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Biểu đồ doanh thu -->
            <div class="chart-section">
                <h2>Doanh thu 12 tháng gần nhất</h2>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            
            <!-- Sản phẩm bán chạy -->
            <div class="top-products-section">
                <h2>Top 5 sản phẩm bán chạy</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng đã bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['top_products'] as $product): ?>
                        <tr>
                            <td><?= e($product['name']) ?></td>
                            <td><?= number_format($product['total_sold']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Dữ liệu biểu đồ
        const monthlyData = <?= json_encode($stats['monthly_revenue']) ?>;
        const labels = monthlyData.map(item => item.month);
        const data = monthlyData.map(item => item.revenue);
        
        // Vẽ biểu đồ
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: data,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
