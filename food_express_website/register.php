<?php
include 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $error = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email address.";
  } elseif ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } else {
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
      $error = "Username or Email already exists.";
    } else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $insert = mysqli_query($conn, "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')");
      if ($insert) {
        $success = "Registration successful! Redirecting to login...";
        header("refresh:2;url=login.php");
      } else {
        $error = "Registration failed. Please try again.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Food Express</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    body {
      min-height: 100vh;
      background-image: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .container {
      background: white;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      width: 100%;
      max-width: 450px;
      animation: slideUp 0.4s ease-out;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 30px;
    }

    .logo {
      font-size: 32px;
      color: #4361ee;
    }

    .logo-text {
      font-size: 24px;
      font-weight: 700;
      color: #1a1a1a;
    }

    h2 {
      color: #1a1a1a;
      font-size: 24px;
      margin-bottom: 8px;
      text-align: center;
    }

    .subtitle {
      color: #666;
      margin-bottom: 30px;
      text-align: center;
      font-size: 15px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      color: #1a1a1a;
      margin-bottom: 8px;
      font-weight: 500;
      font-size: 14px;
    }

    input {
      width: 100%;
      padding: 14px 16px;
      border: 1px solid #e1e1e1;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s ease;
      background-color: #f9fafb;
    }

    input:focus {
      outline: none;
      border-color: #4361ee;
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
      background-color: white;
    }

    button {
      width: 100%;
      padding: 14px;
      background: #4361ee;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
    }

    button:hover {
      background: #3651d4;
      transform: translateY(-1px);
    }

    .login-link {
      text-align: center;
      margin-top: 24px;
      color: #666;
      font-size: 14px;
    }

    .login-link a {
      color: #4361ee;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    .error {
      color: #dc2626;
      text-align: center;
      margin: 16px 0;
      padding: 12px;
      background: #fef2f2;
      border-radius: 8px;
      font-size: 14px;
      animation: shake 0.5s ease-in-out;
    }

    .success {
      color: #16a34a;
      text-align: center;
      margin: 16px 0;
      padding: 12px;
      background: #f0fdf4;
      border-radius: 8px;
      font-size: 14px;
      animation: fadeIn 0.5s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    @media (max-width: 480px) {
      .container {
        padding: 30px 20px;
        border-radius: 12px;
      }
      
      .logo-container {
        margin-bottom: 25px;
      }
      
      h2 {
        font-size: 22px;
      }
      
      .subtitle {
        font-size: 14px;
        margin-bottom: 25px;
      }
      
      input, button {
        padding: 12px 14px;
      }
    }
  </style>
</head>
<body>
  <div class="overlay">
    <div class="container">
      <div class="logo-container">
        <span class="logo">🍽️</span>
        <span class="logo-text">Food Express</span>
      </div>
      
      <h2>Create Your Account</h2>
      <p class="subtitle">Join us to enjoy delicious food delivery</p>
      
      <form method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter your username" required>
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Create a password" required>
        </div>
        
        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
        </div>
        
        <button type="submit">Sign Up</button>
        
        <?php if ($error): ?>
          <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
          <div class="success"><?= $success ?></div>
        <?php endif; ?>
      </form>
      
      <div class="login-link">
        Already have an account? <a href="login.php">Log in</a>
      </div>
    </div>
  </div>
</body>
</html>