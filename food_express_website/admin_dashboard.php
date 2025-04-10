<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db.php';

// Function to get the actual date column name in orders table
function getOrderDateColumn($conn) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE '%date%'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['Field'];
    }
    return 'created_at'; // default fallback
}

$date_column = getOrderDateColumn($conn);

// Get stats with error handling
$total_orders = 0;
$total_revenue = 0;
$total_users = 0;

$result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders");
if ($result) {
    $total_orders = mysqli_fetch_assoc($result)['count'] ?? 0;
}

$result = mysqli_query($conn, "SELECT SUM(total) AS sum FROM orders");
if ($result) {
    $total_revenue = mysqli_fetch_assoc($result)['sum'] ?? 0;
}

$result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM users");
if ($result) {
    $total_users = mysqli_fetch_assoc($result)['count'] ?? 0;
}

// Get recent orders with error handling
$recent_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY $date_column DESC LIMIT 5") or die(mysqli_error($conn));

// Get monthly revenue data for chart
$monthly_revenue = mysqli_query($conn, "SELECT 
    DATE_FORMAT($date_column, '%Y-%m') AS month, 
    SUM(total) AS revenue 
    FROM orders 
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 6") or die(mysqli_error($conn));

$chart_labels = [];
$chart_data = [];
while ($row = mysqli_fetch_assoc($monthly_revenue)) {
    $chart_labels[] = date("M Y", strtotime($row['month']));
    $chart_data[] = $row['revenue'];
}
$chart_labels = array_reverse($chart_labels);
$chart_data = array_reverse($chart_data);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard | Food Express</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #FBA518;
            --primary-dark: #FBA518;;
            --secondary: #607D8B;
            --danger: #F44336;
            --warning: #FFC107;
            --info: #2196F3;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
        }
        
        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
        }
        
        .brand {
            display: flex;
            align-items: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .brand i {
            font-size: 24px;
            margin-right: 10px;
        }
        
        .brand h2 {
            font-size: 20px;
            font-weight: 600;
        }
        
        .nav-menu {
            margin-top: 20px;
        }
        
        .nav-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.1);
            border-left: 3px solid white;
        }
        
        .nav-item a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
        }
        
        .nav-item i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        /* Main Content */
        .main-content {
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .profile {
            display: flex;
            align-items: center;
        }
        
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .profile-name {
            font-weight: 500;
        }
        
        .logout-btn {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .logout-btn:hover {
            background: #D32F2F;
        }
        
        .logout-btn i {
            margin-right: 5px;
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary);
        }
        
        .stat-icon {
            font-size: 30px;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .stat-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .stat-change {
            font-size: 12px;
            color: #4CAF50;
        }
        
        .stat-change.negative {
            color: var(--danger);
        }
        
        /* Charts */
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .chart-title {
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .chart-title i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        /* Recent Orders */
        .recent-orders {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .section-title {
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 500;
            color: #555;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status.pending {
            background: #FFF3E0;
            color: #E65100;
        }
        
        .status.completed {
            background: #E8F5E9;
            color: #2E7D32;
        }
        
        .status.processing {
            background: #E3F2FD;
            color: #1565C0;
        }
        
        .view-all {
            display: block;
            text-align: right;
            margin-top: 15px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
                <i class="fas fa-utensils"></i>
                <h2>Food Express</h2>
            </div>
            
            <div class="nav-menu">
                <div class="nav-item active">
                    <a href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="admin_orders.php">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Orders</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="admin_manage_food.php">
                        <i class="fas fa-hamburger"></i>
                        <span>Food Items</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="admin_customers.php">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="admin_reports.php">
                        <i class="fas fa-chart-line"></i>
                        <span>Reports</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="admin_settings.php">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Dashboard Overview</h1>
                <div class="profile">
                    <div class="profile-img">A</div>
                    <span class="profile-name">Admin</span>
                    <button class="logout-btn" onclick="location.href='admin_logout.php'">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card animate delay-1">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-title">Total Orders</div>
                    <div class="stat-value"><?= $total_orders ?></div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i> 12% from last month
                    </div>
                </div>
                
                <div class="stat-card animate delay-2">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-title">Total Revenue</div>
                    <div class="stat-value">₹<?= number_format($total_revenue, 2) ?></div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i> 8% from last month
                    </div>
                </div>
                
                <div class="stat-card animate delay-3">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-title">Registered Users</div>
                    <div class="stat-value"><?= $total_users ?></div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i> 5% from last month
                    </div>
                </div>
                
                <div class="stat-card animate delay-4">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-title">Conversion Rate</div>
                    <div class="stat-value">3.2%</div>
                    <div class="stat-change negative">
                        <i class="fas fa-arrow-down"></i> 0.5% from last month
                    </div>
                </div>
            </div>
            
            <!-- Revenue Chart -->
            <div class="chart-container animate">
                <div class="chart-title">
                    <i class="fas fa-chart-line"></i>
                    Monthly Revenue
                </div>
                <canvas id="revenueChart" height="300"></canvas>
            </div>
            
            <!-- Recent Orders -->
            <div class="recent-orders animate">
                <div class="section-title">
                    <i class="fas fa-clock"></i>
                    Recent Orders
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = mysqli_fetch_assoc($recent_orders)): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($order['id'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (isset($order['customer_name'])): ?>
                                    <?= htmlspecialchars($order['customer_name']) ?>
                                <?php elseif (isset($order['user_id'])): ?>
                                    Customer <?= htmlspecialchars($order['user_id']) ?>
                                <?php else: ?>
                                    Guest Customer
                                <?php endif; ?>
                            </td>
                            <td><?= isset($order[$date_column]) ? date("M d, Y", strtotime($order[$date_column])) : 'N/A' ?></td>
                            <td>₹<?= number_format($order['total'] ?? 0, 2) ?></td>
                            <td>
                                <span class="status <?= 
                                    isset($order['status']) ? 
                                    ($order['status'] == 'completed' ? 'completed' : 
                                    ($order['status'] == 'processing' ? 'processing' : 'pending')) 
                                    : 'pending'
                                ?>">
                                    <?= isset($order['status']) ? ucfirst($order['status']) : 'Pending' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <a href="admin_orders.php" class="view-all">View All Orders →</a>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?= json_encode($chart_data) ?>,
                    backgroundColor: '#FF9800',
                    borderColor: '#F57C00',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Add animation class to elements
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.animate');
            elements.forEach(el => {
                el.style.opacity = '0';
            });
        });
    </script>
</body>
</html>