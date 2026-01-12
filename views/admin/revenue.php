<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng doanh thu - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                    <input type="hidden" name="section" value="revenue">
                    
                    <label>Từ ngày:</label>
                    <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? date('Y-m-01') ?>" class="form-control">
                    
                    <label>Đến ngày:</label>
                    <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? date('Y-m-d') ?>" class="form-control">
                    
                    <button type="submit" class="btn btn-primary">Xem báo cáo</button>
                </form>
            </div>
            
            <!-- Thống kê tổng quan -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Tổng doanh thu</h3>
                    <p class="stat-value"><?= formatMoney($stats['total_revenue']) ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Tổng đơn hàng</h3>
                    <p class="stat-value"><?= number_format($stats['total_orders']) ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Giá trị TB/đơn</h3>
                    <p class="stat-value"><?= formatMoney($stats['avg_order_value']) ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Đơn thành công</h3>
                    <p class="stat-value"><?= number_format($stats['completed_orders']) ?></p>
                </div>
            </div>
            
            <!-- Biểu đồ doanh thu theo ngày -->
            <div class="chart-section">
                <h2>Doanh thu theo ngày</h2>
                <div class="chart-container">
                    <canvas id="dailyRevenueChart"></canvas>
                </div>
            </div>
            
            <!-- Doanh thu theo danh mục -->
            <div class="revenue-by-category">
                <h2>Doanh thu theo danh mục sản phẩm</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Danh mục</th>
                            <th>Số đơn</th>
                            <th>Số lượng bán</th>
                            <th>Doanh thu</th>
                            <th>% Tổng doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoryRevenue as $cat): ?>
                        <tr>
                            <td><?= e($cat['category_name']) ?></td>
                            <td><?= number_format($cat['total_orders']) ?></td>
                            <td><?= number_format($cat['total_quantity']) ?></td>
                            <td><strong><?= formatMoney($cat['revenue']) ?></strong></td>
                            <td>
                                <?php 
                                $percentage = $stats['total_revenue'] > 0 ? ($cat['revenue'] / $stats['total_revenue'] * 100) : 0;
                                ?>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                                    <span><?= number_format($percentage, 1) ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Top sản phẩm bán chạy -->
            <div class="top-products">
                <h2>Top 10 sản phẩm bán chạy</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng bán</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProducts as $index => $product): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= e($product['product_name']) ?></td>
                            <td><?= number_format($product['total_quantity']) ?></td>
                            <td><strong><?= formatMoney($product['revenue']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const dailyData = <?= json_encode($dailyRevenue) ?>;
        const labels = dailyData.map(item => item.date);
        const data = dailyData.map(item => item.revenue);
        
        const ctx = document.getElementById('dailyRevenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: data,
                    backgroundColor: '#4CAF50',
                    borderColor: '#45a049',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
