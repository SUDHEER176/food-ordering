<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart'])) {
    $food_id = $_POST['food_id'];
    if (!in_array($food_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $food_id;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Express - Your Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
            * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-color:rgb(243, 151, 32);
            --secondary-color: #FFB6B6;
            --text-color: #333;
            --bg-color: #F8F9FA;
            --white: #FFFFFF;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        .header {
            background: var(--white);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo img {
            height: 40px;
        }

        .logo h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 700;
        }

        .search-bar {
            flex: 1;
            max-width: 600px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #eee;
            border-radius: 30px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--secondary-color);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--text-color);
            transition: color 0.3s ease;
        }

        .nav-item:hover {
            color: var(--primary-color);
        }

        .nav-item i {
            font-size: 20px;
        }

        /* Cart Section */
        .cart-section {
            padding: 40px 0;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .section-title h2 {
            font-size: 28px;
            color: var(--text-color);
        }

        .section-title span {
            color: var(--primary-color);
        }

        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .cart-items {
            background: var(--white);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            animation: fadeIn 0.5s ease;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            margin-right: 20px;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-restaurant {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-btn {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .quantity-btn:hover {
            transform: scale(1.1);
        }

        .quantity {
            font-weight: 600;
        }

        .item-price {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .remove-item {
            color:rgb(241, 175, 32);
            cursor: pointer;
            margin-left: 20px;
            transition: transform 0.2s ease;
        }

        .remove-item:hover {
            transform: scale(1.1);
        }

        /* Cart Summary */
        .cart-summary {
            background: var(--white);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            height: fit-content;
        }

        .summary-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .coupon-input {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .coupon-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .apply-btn {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .apply-btn:hover {
            background:rgb(228, 178, 13);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .summary-item:last-child {
            border-bottom: none;
            margin-bottom: 20px;
        }

        .total {
            font-size: 20px;
            font-weight: 600;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            background: var(--primary-color);
            color: var(--white);
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s ease;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
        }

        .empty-cart {
            text-align: center;
            padding: 40px;
        }

        .empty-cart i {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-container {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
            }

            .nav-actions {
                width: 100%;
                justify-content: space-around;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <a href="/" class="logo">
                <i class="fas fa-utensils"></i>
                <h1>Food Express</h1>
            </a>
            
            <div class="search-bar">
                <input type="text" placeholder="Search for food, cuisines, restaurants...">
            </div>
            
            <div class="nav-actions">
                <a href="#" class="nav-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Location</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cart</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
        </div>
    </header>

    <main class="cart-section">
        <div class="container">
            <div class="section-title">
                <h2>Your <span>Cart</span></h2>
            </div>

            <div class="cart-container">
                <div class="cart-items">
                    <?php
                    $total = 0;
                    
                    if (!empty($_SESSION['cart'])) {
                        $item_counts = array_count_values($_SESSION['cart']);
                        $unique_ids = array_unique($_SESSION['cart']);
                        $ids = implode(",", $unique_ids);
                        
                        $result = mysqli_query($conn, "SELECT * FROM food_items WHERE id IN ($ids)");
                        
                        if ($result && mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $quantity = $item_counts[$row['id']];
                                $item_total = $row['price'] * $quantity;
                                $total += $item_total;

                                // Check if image_url exists, use fallback if not
                                $image = isset($row['image_url']) ? $row['image_url'] : 'default.jpg';
                                
                                echo '<div class="cart-item">';
                                echo '<div class="item-image">';
                                echo '<img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($row['name']) . '">';
                                echo '</div>';
                                echo '<div class="item-details">';
                                echo '<h3 class="item-name">' . htmlspecialchars($row['name']) . '</h3>';
                                
                                if (isset($row['restaurant'])) {
                                    echo '<p class="item-restaurant">' . htmlspecialchars($row['restaurant']) . '</p>';
                                }
                                
                                echo '<div class="quantity-controls">';
                                echo '<button class="quantity-btn">-</button>';
                                echo '<span class="quantity">' . $quantity . '</span>';
                                echo '<button class="quantity-btn">+</button>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="item-price">₹' . number_format($item_total, 2) . '</div>';
                                echo '<div class="remove-item"><i class="fas fa-trash"></i></div>';
                                echo '</div>';
                            }
                        }
                    } else {
                        echo '<div class="empty-cart">';
                        echo '<i class="fas fa-shopping-cart"></i>';
                        echo '<p>Your cart is empty</p>';
                        echo '</div>';
                    }
                    ?>
                </div>

                <div class="cart-summary">
                    <h3 class="summary-title">Cart Total</h3>
                    <div class="coupon-input">
                        <input type="text" placeholder="Enter coupon code">
                        <button class="apply-btn">Apply</button>
                    </div>
                    
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>₹<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <div class="summary-item">
                        <span>Delivery Fee</span>
                        <span>₹40.00</span>
                    </div>
                    
                    <div class="summary-item total">
                        <span>Total</span>
                        <span>₹<?php echo number_format($total + 40, 2); ?></span>
                    </div>
                    
                    <a href="checkout.php" class="checkout-btn">
                        Proceed To Checkout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php mysqli_close($conn); ?>
</body>
</html>
