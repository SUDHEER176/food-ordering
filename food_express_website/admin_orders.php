<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db.php';

$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Modern CSS with animations */
        :root {
            --primary: #2A9D8F;
            --primary-dark: #264653;
            --secondary: #E9C46A;
            --accent-light: #F4A261;
            --accent-dark: #E76F51;
            --light: #f8f9fa;
            --dark: #343a40;
            --success: #4CAF50;
            --warning: #FFC107;
            --danger: #F44336;
            --info: #2196F3;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--primary-dark);
            padding: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        /* Header styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--secondary);
            position: relative;
        }
        
        h2 {
            color: var(--primary-dark);
            font-size: 28px;
            position: relative;
            display: inline-block;
            padding-left: 40px;
        }
        
        h2::before {
            content: "📦";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 30px;
            animation: bounce 2s infinite;
        }
        
        .logout {
            background-color: var(--accent-dark);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .logout:hover {
            background-color: #d45b3d;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .logout i {
            margin-right: 8px;
        }
        
        /* Table styles */
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            animation: slideUp 0.5s ease-out;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 0 auto;
            background: white;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: var(--primary-dark);
            color: white;
            font-weight: 500;
            position: sticky;
            top: 0;
        }
        
        tr {
            transition: all 0.3s ease;
        }
        
        tr:hover {
            background-color: rgba(233, 196, 106, 0.1);
            transform: translateX(5px);
        }
        
        tr:nth-child(even) {
            background-color: rgba(0,0,0,0.02);
        }
        
        /* Status styling */
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
            border: 1px solid rgba(255, 193, 7, 0.4);
        }
        
        .status-completed {
            background-color: rgba(76, 175, 80, 0.2);
            color: #155724;
            border: 1px solid rgba(76, 175, 80, 0.4);
        }
        
        .status-cancelled {
            background-color: rgba(244, 67, 54, 0.2);
            color: #721c24;
            border: 1px solid rgba(244, 67, 54, 0.4);
        }
        
        .status-processing {
            background-color: rgba(33, 150, 243, 0.2);
            color: #004085;
            border: 1px solid rgba(33, 150, 243, 0.4);
        }
        
        /* Payment method styling */
        .payment {
            display: flex;
            align-items: center;
        }
        
        .payment-icon {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .payment-status {
            margin-left: 5px;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 3px;
        }
        
        .payment-paid {
            background-color: rgba(76, 175, 80, 0.2);
            color: #155724;
        }
        
        .payment-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        /* Price styling */
        .price {
            font-weight: 600;
            color: var(--primary);
        }
        
        /* Responsive design */
        @media (max-width: 1200px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .logout {
                align-self: flex-end;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(30px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(-50%); }
            50% { transform: translateY(-65%); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Row animation on load */
        tr {
            opacity: 0;
            animation: fadeInRow 0.5s ease-out forwards;
        }
        
        @keyframes fadeInRow {
            from { 
                opacity: 0;
                transform: translateX(-20px);
            }
            to { 
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Apply different delays to rows */
        <?php
        $delay = 0.1;
        for ($i = 1; $i <= 20; $i++) {
            echo "tr:nth-child($i) { animation-delay: " . ($delay * $i) . "s; }\n";
        }
        ?>
        
        /* Dashboard navigation */
        .dashboard-nav {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .nav-item {
            padding: 10px 20px;
            background-color: white;
            border-radius: 5px;
            text-decoration: none;
            color: var(--primary-dark);
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }
        
        .nav-item:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .nav-item i {
            margin-right: 8px;
        }
        
        .nav-item.active {
            background-color: var(--primary);
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>All Orders (Admin View)</h2>
        <a href="admin_logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    
    <div class="dashboard-nav">
        <a href="admin_dashboard.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#" class="nav-item active"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="admin_food.php" class="nav-item"><i class="fas fa-utensils"></i> Food Items</a>
        <a href="admin_users.php" class="nav-item"><i class="fas fa-users"></i> Users</a>
    </div>
    
    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Email</th>
                <th>Food IDs</th>
                <th>Total</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php 
            $count = 0;
            while ($row = mysqli_fetch_assoc($result)) { 
                $count++;
                
                // Determine status class
                $statusClass = 'status-pending';
                if (isset($row['order_status'])) {
                    if ($row['order_status'] == 'Completed') {
                        $statusClass = 'status-completed';
                    } else if ($row['order_status'] == 'Cancelled') {
                        $statusClass = 'status-cancelled';
                    } else if ($row['order_status'] == 'Processing') {
                        $statusClass = 'status-processing';
                    }
                }
                
                // Determine payment icon
                $paymentIcon = 'fa-money-bill';
                if ($row['payment_method'] == 'Credit Card') {
                    $paymentIcon = 'fa-credit-card';
                } else if ($row['payment_method'] == 'PayPal') {
                    $paymentIcon = 'fa-paypal';
                } else if ($row['payment_method'] == 'UPI') {
                    $paymentIcon = 'fa-mobile-alt';
                }
                
                // Determine payment status class
                $paymentStatusClass = 'payment-pending';
                if ($row['payment_status'] == 'Paid') {
                    $paymentStatusClass = 'payment-paid';
                }
            ?>
            <tr style="animation-delay: <?= $count * 0.1 ?>s;">
                <td>#<?= $row['id'] ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['food_ids'] ?></td>
                <td class="price">₹<?= $row['total'] ?></td>
                <td><?= $row['address'] ?></td>
                <td><?= $row['phone'] ?></td>
                <td>
                    <div class="payment">
                        <i class="fas <?= $paymentIcon ?> payment-icon"></i>
                        <?= $row['payment_method'] ?>
                        <span class="payment-status <?= $paymentStatusClass ?>"><?= $row['payment_status'] ?></span>
                    </div>
                </td>
                <td><span class="status <?= $statusClass ?>"><?= $row['order_status'] ?? 'Pending' ?></span></td>
                <td>
                    <a href="edit_order.php?id=<?= $row['id'] ?>" style="color: var(--info); margin-right: 10px;" title="Edit Order">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="delete_order.php?id=<?= $row['id'] ?>" style="color: var(--danger);" title="Delete Order" 
                       onclick="return confirm('Are you sure you want to delete this order?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>
            
            <?php if ($count == 0) { ?>
            <tr>
                <td colspan="10" style="text-align: center; padding: 30px;">
                    <i class="fas fa-box-open" style="font-size: 40px; color: #ccc; margin-bottom: 15px; display: block;"></i>
                    <p>No orders found</p>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html> 