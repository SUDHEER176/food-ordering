<?php 
session_start(); 
include 'db.php';  

if (!isset($_SESSION['username']) || empty($_SESSION['cart'])) { 
    header("Location: index.php"); 
    exit; 
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $username = $_SESSION['username']; 
    $address = $_POST['address']; 
    $phone = $_POST['phone']; 
    $email = $_POST['email']; 
    $payment_method = $_POST['payment_method']; 
    $payment_status = 'paid'; 

    $ids = implode(",", $_SESSION['cart']); 
    $result = mysqli_query($conn, "SELECT * FROM food_items WHERE id IN ($ids)"); 
    $total = 0; 
    $items = []; 

    while ($row = mysqli_fetch_assoc($result)) { 
        $total += $row['price']; 
        $items[] = $row['name'] . " - ₹" . $row['price']; 
    } 

    mysqli_query($conn, "INSERT INTO orders (username, food_ids, total, address, phone, email, payment_method, payment_status) 
                        VALUES ('$username', '$ids', $total, '$address', '$phone', '$email', '$payment_method', '$payment_status')"); 

    $_SESSION['cart'] = []; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #FF6B6B;
            --secondary: #4ECDC4;
            --dark: #292F36;
            --light: #F7FFF7;
            --accent: #FFE66D;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.8s ease-out;
        }
        
        .confirmation-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .confirmation-header h2 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .confirmation-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .confirmation-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            display: inline-block;
            animation: bounce 1.5s infinite;
        }
        
        .confirmation-body {
            padding: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .order-details, .payment-details {
            background: var(--light);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        
        .order-details:hover, .payment-details:hover {
            transform: translateY(-5px);
        }
        
        .section-title {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.3rem;
            border-bottom: 2px solid var(--secondary);
            padding-bottom: 10px;
        }
        
        .detail-item {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--dark);
        }
        
        .detail-value {
            color: #555;
        }
        
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #ddd;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            padding: 0 30px 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-download {
            background-color: var(--secondary);
            color: white;
        }
        
        .btn-download:hover {
            background-color: #3dbeb6;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
        }
        
        .btn-home {
            background-color: var(--dark);
            color: white;
        }
        
        .btn-home:hover {
            background-color: #1e2328;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(41, 47, 54, 0.4);
        }
        
        .eta-badge {
            display: inline-block;
            background-color: var(--accent);
            color: var(--dark);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 10px;
            animation: pulse 2s infinite;
        }
        
        @keyframes fadeInUp {
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
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        @media (max-width: 768px) {
            .confirmation-body {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <div class="confirmation-icon">🎉</div>
            <h2>Order Confirmed!</h2>
            <p>Thank you for your purchase. Your payment was successful.</p>
        </div>
        
        <div class="confirmation-body">
            <div class="order-details">
                <h3 class="section-title">Order Details</h3>
                
                <div class="detail-item">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value"><?= htmlspecialchars($username) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Delivery Address:</span>
                    <span class="detail-value"><?= htmlspecialchars($address) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Contact:</span>
                    <span class="detail-value"><?= htmlspecialchars($phone) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?= htmlspecialchars($email) ?></span>
                </div>
                
                <div class="eta-badge animate__animated animate__pulse animate__infinite">
                    ⏱️ Delivery ETA: 30-45 mins
                </div>
            </div>
            
            <div class="payment-details">
                <h3 class="section-title">Payment Summary</h3>
                
                <?php foreach($items as $item): ?>
                <div class="detail-item">
                    <span class="detail-label">Item:</span>
                    <span class="detail-value"><?= htmlspecialchars($item) ?></span>
                </div>
                <?php endforeach; ?>
                
                <div class="detail-item">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value"><?= htmlspecialchars($payment_method) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value" style="color: #4CAF50; font-weight: 600;">Paid</span>
                </div>
                
                <div class="total-amount">
                    Total: ₹<?= number_format($total, 2) ?>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <button onclick="generateBill()" class="btn btn-download">
                <span>🧾</span> Download Bill
            </button>
            <a href="index.php" class="btn btn-home">
                <span>🏠</span> Back to Home
            </a>
        </div>
    </div>

    <script>
        const { jsPDF } = window.jspdf;
        const items = <?= json_encode($items) ?>;
        const total = <?= $total ?>;
        const username = <?= json_encode($username) ?>;
        const phone = <?= json_encode($phone) ?>;
        const email = <?= json_encode($email) ?>;
        const address = <?= json_encode($address) ?>;
        const paymentMethod = <?= json_encode($payment_method) ?>;
        const date = new Date().toLocaleDateString();

        function generateBill() {
            const doc = new jsPDF();
            
            // Header
            doc.setFontSize(22);
            doc.setTextColor(255, 107, 107);
            doc.text("Food Express", 105, 20, { align: 'center' });
            
            doc.setFontSize(12);
            doc.setTextColor(100, 100, 100);
            doc.text("123 Food Street, Foodville", 105, 30, { align: 'center' });
            doc.text("Invoice Date: " + date, 105, 40, { align: 'center' });
            
            // Divider
            doc.setDrawColor(78, 205, 196);
            doc.setLineWidth(0.5);
            doc.line(20, 45, 190, 45);
            
            // Customer Info
            doc.setFontSize(14);
            doc.setTextColor(41, 47, 54);
            doc.text("Customer Information", 20, 55);
            
            doc.setFontSize(10);
            doc.text("Name: " + username, 20, 65);
            doc.text("Phone: " + phone, 20, 75);
            doc.text("Email: " + email, 20, 85);
            doc.text("Address: " + address, 20, 95);
            doc.text("Payment Method: " + paymentMethod, 20, 105);
            
            // Order Items
            doc.setFontSize(14);
            doc.text("Order Items", 20, 120);
            
            doc.setFontSize(10);
            let y = 130;
            items.forEach(item => {
                if (y > 250) {
                    doc.addPage();
                    y = 20;
                }
                doc.text("- " + item, 25, y);
                y += 10;
            });
            
            // Total
            doc.setFontSize(14);
            doc.text("Total Amount: ₹" + total.toFixed(2), 20, y + 20);
            
            // Footer
            doc.setFontSize(10);
            doc.setTextColor(100, 100, 100);
            doc.text("Thank you for your order!", 105, 280, { align: 'center' });
            doc.text("© Food Express " + new Date().getFullYear(), 105, 285, { align: 'center' });
            
            doc.save("FoodExpress_Invoice_" + date.replace(/\//g, '-') + ".pdf");
        }
    </script>
</body>
</html>

<?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Food Express</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary:rgb(227, 174, 27);
            --secondary: #4ECDC4;
            --dark: #292F36;
            --light: #F7FFF7;
            --accent: #FFE66D;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .checkout-container {
            max-width: 1000px;
            margin: 50px auto;
            animation: fadeIn 0.5s ease-out;
        }
        
        .checkout-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .checkout-header h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .checkout-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .checkout-form, .order-summary {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .checkout-form:hover, .order-summary:hover {
            transform: translateY(-5px);
        }
        
        .section-title {
            color: var(--primary);
            margin-bottom: 25px;
            font-size: 1.5rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--secondary);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        input, textarea, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.2);
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-submit:hover {
            background: #ff5252;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px dashed #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: 500;
        }
        
        .item-price {
            color: var(--primary);
            font-weight: 600;
        }
        
        .order-total {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid var(--secondary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total-label {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .payment-options {
            margin-top: 20px;
        }
        
        .payment-option {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
        }
        
        .payment-option:hover {
            border-color: var(--secondary);
        }
        
        .payment-option input {
            width: auto;
            margin-right: 15px;
        }
        
        .payment-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }
        
        .payment-label {
            flex: 1;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            
            .checkout-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-header animate__animated animate__fadeIn">
            <h1>🛒 Checkout</h1>
            <p>Complete your order with secure payment</p>
        </div>
        
        <div class="checkout-grid">
            <form method="POST" class="checkout-form animate__animated animate__fadeInLeft">
                <h2 class="section-title">Delivery Information</h2>
                
                <div class="form-group">
                    <label for="email">📧 Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="address">🏠 Delivery Address</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="phone">📞 Phone Number</label>
                    <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required>
                </div>
                
                <h2 class="section-title" style="margin-top: 40px;">Payment Method</h2>
                
                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Card" checked>
                        <span class="payment-icon">💳</span>
                        <span class="payment-label">Credit/Debit Card</span>
                    </label>
                    
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="UPI">
                        <span class="payment-icon">📱</span>
                        <span class="payment-label">UPI Payment</span>
                    </label>
                    
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Cash on Delivery">
                        <span class="payment-icon">💰</span>
                        <span class="payment-label">Cash on Delivery</span>
                    </label>
                </div>
                
                <button type="submit" class="btn-submit">
                    <span>🔒</span> Complete Payment
                </button>
            </form>
            
            <div class="order-summary animate__animated animate__fadeInRight">
                <h2 class="section-title">Order Summary</h2>
                
                <?php
                $ids = implode(",", $_SESSION['cart']);
                $result = mysqli_query($conn, "SELECT * FROM food_items WHERE id IN ($ids)");
                $total = 0;
                
                while ($row = mysqli_fetch_assoc($result)): 
                    $total += $row['price'];
                ?>
                <div class="order-item">
                    <span class="item-name"><?= htmlspecialchars($row['name']) ?></span>
                    <span class="item-price">₹<?= number_format($row['price'], 2) ?></span>
                </div>
                <?php endwhile; ?>
                
                <div class="order-total">
                    <span class="total-label">Total Amount:</span>
                    <span class="total-amount">₹<?= number_format($total, 2) ?></span>
                </div>
                
                <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <p style="color: #666; text-align: center;">
                        Your order will be delivered within 30-45 minutes after confirmation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
