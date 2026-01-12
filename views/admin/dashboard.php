<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị hệ thống - Admin</title>
    <link rel="stylesheet" href="<?= asset('public/css/admin.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'views/admin/layouts/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Quản trị hệ thống</h1>
            <div class="admin-user">
                <div class="user-avatar">A</div>
                <span>Administrator</span>
            </div>
        </div>

        <?php if (isset($stats['error'])): ?>
        <div class="alert alert-error">
            <strong>Lỗi:</strong> <?= e($stats['error']) ?>
        </div>
        <?php endif; ?>

        <?php if ($stats['total_orders'] == 0 && $stats['total_products'] == 0): ?>
        <div class="alert alert-info" style="background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;">
            <strong>Thông báo:</strong> Hệ thống chưa có dữ liệu. 
            <a href="test_dashboard_data.php" target="_blank" style="color: #0c5460; text-decoration: underline;">
                Nhấp vào đây để tạo dữ liệu mẫu
            </a> hoặc bắt đầu thêm sản phẩm và đơn hàng.
        </div>
        <?php endif; ?>

        <div class="content-container">
            <!-- Dashboard Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Khách hàng</h3>
                        <div class="stat-value"><?= number_format($stats['total_customers']) ?></div>
                        <span class="stat-badge">Tổng số người dùng</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Đơn hàng</h3>
                        <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
                        <span class="stat-badge">Tổng đơn hàng</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon customers">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Sản phẩm</h3>
                        <div class="stat-value"><?= number_format($stats['total_products']) ?></div>
                        <span class="stat-badge">Món ăn & thức uống</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon products">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Doanh thu</h3>
                        <div class="stat-value"><?= formatMoney($stats['total_revenue']) ?></div>
                        <span class="stat-badge">Tổng doanh thu</span>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="page" value="admin">
                    
                    <div class="filter-group">
                        <label>Bộ lọc thời gian:</label>
                        <select name="time_filter" class="filter-select">
                            <option value="today" <?= ($_GET['time_filter'] ?? '') === 'today' ? 'selected' : '' ?>>Hôm nay</option>
                            <option value="week" <?= ($_GET['time_filter'] ?? '') === 'week' ? 'selected' : '' ?>>7 ngày qua</option>
                            <option value="month" <?= ($_GET['time_filter'] ?? 'month') === 'month' ? 'selected' : '' ?>>30 ngày qua</option>
                            <option value="year" <?= ($_GET['time_filter'] ?? '') === 'year' ? 'selected' : '' ?>>12 tháng qua</option>
                            <option value="custom" <?= ($_GET['time_filter'] ?? '') === 'custom' ? 'selected' : '' ?>>Tùy chọn</option>
                        </select>
                    </div>

                    <div class="custom-date-range" style="display: <?= ($_GET['time_filter'] ?? '') === 'custom' ? 'flex' : 'none' ?>;">
                        <div class="date-input">
                            <label>Từ ngày:</label>
                            <input type="date" name="custom_from" value="<?= $_GET['custom_from'] ?? '' ?>" class="form-control">
                        </div>
                        <div class="date-input">
                            <label>Đến ngày:</label>
                            <input type="date" name="custom_to" value="<?= $_GET['custom_to'] ?? '' ?>" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                </form>
            </div>

            <!-- Charts -->
            <div class="chart-section">
                <h2>Biểu đồ doanh thu</h2>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Top Products Section -->
            <div class="top-products-section">
                <h2>Thống kê tổng quan</h2>
                <div class="products-grid">
                    <!-- Order Status Chart -->
                    <div class="chart-section">
                        <h3>Trạng thái đơn hàng</h3>
                        <div class="chart-container">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>

                    <!-- Stock Status Chart -->
                    <div class="chart-section">
                        <h3>Tình trạng tồn kho</h3>
                        <div class="chart-container">
                            <canvas id="stockStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Dữ liệu thống kê
        const orderStats = <?= json_encode($stats['order_status_stats'] ?? []) ?>;
        const stockStats = <?= json_encode($stats['stock_stats'] ?? []) ?>;
        const revenueData = <?= json_encode($stats['chart_data'] ?? []) ?>;

        // Xử lý hiển thị custom date range
        document.querySelector('select[name="time_filter"]').addEventListener('change', function() {
            const customRange = document.querySelector('.custom-date-range');
            if (this.value === 'custom') {
                customRange.style.display = 'flex';
            } else {
                customRange.style.display = 'none';
            }
        });

        // Biểu đồ doanh thu
        if (revenueData.length > 0) {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: revenueData.map(item => item.label || ''),
                    datasets: [{
                        label: 'Doanh thu',
                        data: revenueData.map(item => parseFloat(item.revenue) || 0),
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3498db',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('revenueChart').parentElement.innerHTML = '<p class="text-center">Không có dữ liệu doanh thu</p>';
        }

        // Biểu đồ trạng thái đơn hàng
        if (orderStats.length > 0) {
            const ctx1 = document.getElementById('orderStatusChart').getContext('2d');
            
            // Định nghĩa màu sắc theo trạng thái
            const statusColors = {
                'Chờ xử lý': '#ffc107',        // Vàng - đang chờ xác nhận
                'Đang chuẩn bị': '#fd7e14',   // Cam - đang chuẩn bị  
                'Đang giao': '#007bff',       // Xanh dương - đang giao
                'Hoàn thành': '#28a745',      // Xanh lá - hoàn thành
                'Đã hủy': '#dc3545'           // Đỏ - đã hủy
            };
            
            // Tạo mảng màu theo thứ tự của dữ liệu
            const colors = orderStats.map(item => statusColors[item.label] || '#6c757d');
            
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: orderStats.map(item => item.label),
                    datasets: [{
                        data: orderStats.map(item => item.count),
                        backgroundColor: colors,
                        borderWidth: 0,
                        cutout: '60%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('orderStatusChart').parentElement.innerHTML = '<p class="text-center">Không có dữ liệu đơn hàng</p>';
        }

        // Biểu đồ tình trạng tồn kho
        if (stockStats.length > 0) {
            const ctx2 = document.getElementById('stockStatusChart').getContext('2d');
            
            // Map màu theo trạng thái
            const statusColors = {
                'in_stock': '#27ae60',    // Xanh lá - Còn hàng
                'low_stock': '#f39c12',   // Cam - Sắp hết
                'out_of_stock': '#e74c3c' // Đỏ - Hết hàng
            };
            
            // Lấy màu tương ứng với từng trạng thái
            const colors = stockStats.map(item => statusColors[item.status] || '#95a5a6');
            
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: stockStats.map(item => item.label),
                    datasets: [{
                        data: stockStats.map(item => item.count),
                        backgroundColor: colors,
                        borderWidth: 0,
                        cutout: '60%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('stockStatusChart').parentElement.innerHTML = '<p class="text-center">Không có dữ liệu tồn kho</p>';
        }
    </script>
</body>
</html>