<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Set your admin credentials here
    $admin_user = 'admin';
    $admin_pass = 'admin123';

    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "❌ Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login | Food Express</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Montserrat:wght@700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: url('https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 400px;
            max-width: 90%;
            text-align: center;
            transform: translateY(-20px);
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
            position: relative;
            z-index: 1;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .logo-text {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color:#FBA518;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .logo-icon {
            font-size: 2rem;
            margin-right: 10px;
            color:#FBA518;
        }
        
        h2 {
            color: #333;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .welcome-text {
            color: #666;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }
        
        .form-group {
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        input:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
            outline: none;
        }
        
        button {
            background:rgb(240, 160, 13);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }
        
        button:hover {
            background:rgb(255, 226, 82);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
        
        .error-message {
            color: #ff4757;
            margin-top: 1rem;
            font-size: 0.9rem;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        .footer-text {
            margin-top: 1.5rem;
            color: #999;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <span class="logo-icon">🍔</span>
            <span class="logo-text">FOOD EXPRESS</span>
        </div>
        
        <h2>🔐 Admin Portal</h2>
        <p class="welcome-text">Welcome back to Food Express Admin Dashboard. Please enter your credentials to access the management system.</p>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter admin username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        
        <p class="footer-text">© 2025 Food Express. All rights reserved.</p>
    </div>
</body>
</html>