<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Sanitize and fetch user
  $email = mysqli_real_escape_string($conn, $email);
  $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

  if (mysqli_num_rows($res) == 1) {
    $user = mysqli_fetch_assoc($res);
    if (password_verify($password, $user['password'])) {
      $_SESSION['username'] = $user['username'];
      $_SESSION['user_id'] = $user['id'];
      header("Location: index.php");
      exit;
    } else {
      $error = "Invalid email or password.";
    }
  } else {
    $error = "Invalid email or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Express - Welcome Back!</title>
  <style>
     * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

body {
    min-height: 100vh;
    background-image: url("https://images.unsplash.com/photo-1543353071-087092ec393a?q=80&w=1920&auto=format&fit=crop");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.overlay {
    min-height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.container {
    background: white;
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 440px;
    animation: slideUp 0.5s ease-out;
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 32px;
}

.logo {
    color: #4361ee;
    font-size: 28px;
}

.logo-text {
    font-size: 24px;
    font-weight: 600;
    color: #1a1a1a;
}

h2 {
    color: #1a1a1a;
    font-size: 24px;
    margin-bottom: 8px;
}

.subtitle {
    color: #666;
    margin-bottom: 32px;
    font-size: 15px;
}

.form-group {
    margin-bottom: 24px;
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
    padding: 14px;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
}

input:focus {
    outline: none;
    border-color: #4361ee;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.remember-forgot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0 24px;
}

.remember {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.remember input {
    width: auto;
}

.forgot-link {
    color: #4361ee;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
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
    transition: background-color 0.3s ease;
}

button:hover {
    background: #3651d4;
}

.signup-link {
    text-align: center;
    margin-top: 24px;
    color: #666;
    font-size: 14px;
}

.signup-link a {
    color: #4361ee;
    text-decoration: none;
    font-weight: 500;
}

.error {
    color: #dc2626;
    text-align: center;
    margin-top: 16px;
    padding: 12px;
    background: #fef2f2;
    border-radius: 8px;
    font-size: 14px;
    animation: shake 0.5s ease-in-out;
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

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
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
      <h2>Welcome Back!</h2>
      <p class="subtitle">Sign in to order your favorite meals</p>

      <form method="post">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>

        <div class="remember-forgot">
          <label class="remember">
            <input type="checkbox" name="remember">
            Remember me
          </label>
          <a href="#" class="forgot-link">Forgot Password?</a>
        </div>

        <button type="submit">Sign in</button>

        <div class="signup-link">
          Don't have an account? <a href="register.php">Sign up</a>
        </div>
      </form>
      <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
  </div>
</body>
</html>
