<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

$username = $_SESSION['username'];
$edit_mode = isset($_GET['edit']);

// Handle form submission for profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  
  // Update the latest order with new info (temporary until users table is created)
  $update_query = "UPDATE orders SET email='$email', phone='$phone', address='$address' 
                  WHERE username='$username' ORDER BY id DESC LIMIT 1";
  mysqli_query($conn, $update_query);
  
  header("Location: profile.php");
  exit;
}

// Get user info from latest order
$user_query = mysqli_query($conn, "SELECT * FROM orders WHERE username='$username' ORDER BY id DESC LIMIT 1");
$user_data = mysqli_fetch_assoc($user_query);

// Get order history
$order_query = mysqli_query($conn, "SELECT * FROM orders WHERE username='$username' ORDER BY created_at DESC");

// Count total orders and calculate total spent
$stats_query = mysqli_query($conn, "SELECT COUNT(*) as order_count, SUM(total) as total_spent FROM orders WHERE username='$username'");
$stats = mysqli_fetch_assoc($stats_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>My Profile - Food Express</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #FF6B6B;
      --primary-dark: #e05555;
      --secondary: #4ECDC4;
      --dark: #292F36;
      --light: #F7FFF7;
      --gray: #6C757D;
      --light-gray: #F1F3F5;
      --border-radius: 12px;
      --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background-color: var(--light-gray);
      color: var(--dark);
      line-height: 1.6;
    }

    .container {
      max-width: 1100px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .profile-card {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      overflow: hidden;
      margin-bottom: 2rem;
    }

    .profile-header {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 2rem;
      text-align: center;
      position: relative;
    }

    .profile-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      color: var(--primary);
      font-size: 2.5rem;
      font-weight: bold;
      border: 3px solid white;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .profile-username {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }

    .profile-stats {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-top: 1.5rem;
    }

    .stat-item {
      text-align: center;
    }

    .stat-value {
      font-size: 1.5rem;
      font-weight: bold;
    }

    .stat-label {
      font-size: 0.9rem;
      opacity: 0.9;
    }

    .profile-body {
      padding: 2rem;
    }

    .section-title {
      font-size: 1.25rem;
      margin-bottom: 1.5rem;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .section-title i {
      font-size: 1.1rem;
    }

    .user-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .info-item {
      background: var(--light-gray);
      padding: 1rem;
      border-radius: 8px;
    }

    .info-label {
      font-size: 0.85rem;
      color: var(--gray);
      margin-bottom: 0.25rem;
    }

    .info-value {
      font-weight: 500;
    }

    .edit-form {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-family: inherit;
      transition: var(--transition);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      transition: var(--transition);
      font-family: inherit;
    }

    .btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background: rgba(255, 107, 107, 0.1);
    }

    .btn-group {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }

    .order-card {
      border: 1px solid #eee;
      border-radius: var(--border-radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      transition: var(--transition);
    }

    .order-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--box-shadow);
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #eee;
    }

    .order-id {
      font-weight: bold;
      color: var(--primary);
    }

    .order-date {
      color: var(--gray);
      font-size: 0.9rem;
    }

    .order-details {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .order-status {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-pending {
      background-color: #FFF3BF;
      color: #E67700;
    }

    .status-completed {
      background-color: #D3F9D8;
      color: #2B8A3E;
    }

    .status-failed {
      background-color: #FFE3E3;
      color: #C92A2A;
    }

    .order-actions {
      display: flex;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .empty-state {
      text-align: center;
      padding: 3rem;
      color: var(--gray);
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #ddd;
    }

    .empty-state p {
      margin-bottom: 1.5rem;
    }

    .action-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
      .profile-stats {
        flex-direction: column;
        gap: 1rem;
      }
      
      .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
      
      .action-bar {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="action-bar">
      <a href="index.php" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Home
      </a>
      <a href="logout.php" class="btn btn-outline">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>

    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-avatar">
          <?= strtoupper(substr($username, 0, 1)) ?>
        </div>
        <h2 class="profile-username"><?= htmlspecialchars($username) ?></h2>
        
        <div class="profile-stats">
          <div class="stat-item">
            <div class="stat-value"><?= $stats['order_count'] ?? 0 ?></div>
            <div class="stat-label">Total Orders</div>
          </div>
          <div class="stat-item">
            <div class="stat-value">₹<?= number_format($stats['total_spent'] ?? 0, 2) ?></div>
            <div class="stat-label">Total Spent</div>
          </div>
          <div class="stat-item">
            <div class="stat-value"><?= mysqli_num_rows($order_query) > 0 ? date('M Y', strtotime(mysqli_fetch_assoc(mysqli_query($conn, "SELECT created_at FROM orders WHERE username='$username' ORDER BY created_at ASC LIMIT 1"))['created_at'])) : 'N/A' ?></div>
            <div class="stat-label">Member Since</div>
          </div>
        </div>
      </div>

      <div class="profile-body">
        <div class="user-details">
          <h3 class="section-title"><i class="fas fa-user-circle"></i> Personal Details</h3>
          
          <?php if (!$edit_mode): ?>
            <div class="user-info-grid">
              <div class="info-item">
                <div class="info-label">Username</div>
                <div class="info-value"><?= htmlspecialchars($username) ?></div>
              </div>
              <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value"><?= htmlspecialchars($user_data['email'] ?? 'Not provided') ?></div>
              </div>
              <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value"><?= htmlspecialchars($user_data['phone'] ?? 'Not provided') ?></div>
              </div>
              <div class="info-item">
                <div class="info-label">Address</div>
                <div class="info-value"><?= htmlspecialchars($user_data['address'] ?? 'Not provided') ?></div>
              </div>
            </div>
            
            <a href="profile.php?edit=1" class="btn">
              <i class="fas fa-edit"></i> Edit Profile
            </a>
          <?php else: ?>
            <form method="POST" action="profile.php">
              <div class="edit-form">
                <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($username) ?>" disabled>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user_data['address'] ?? '') ?></textarea>
                </div>
              </div>
              
              <div class="btn-group">
                <button type="submit" name="update_profile" class="btn">
                  <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="profile.php" class="btn btn-outline">
                  <i class="fas fa-times"></i> Cancel
                </a>
              </div>
            </form>
          <?php endif; ?>
        </div>

        <div class="order-history">
          <h3 class="section-title"><i class="fas fa-history"></i> Order History</h3>
          
          <?php if (mysqli_num_rows($order_query) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($order_query)): 
              // Determine status class
              $status_class = 'status-pending';
              if (strtolower($order['payment_status']) === 'completed') {
                $status_class = 'status-completed';
              } elseif (strtolower($order['payment_status']) === 'failed') {
                $status_class = 'status-failed';
              }
            ?>
              <div class="order-card">
                <div class="order-header">
                  <div class="order-id">Order #<?= $order['id'] ?></div>
                  <div class="order-date"><?= date("F j, Y \a\\t g:i A", strtotime($order['created_at'])) ?></div>
                </div>
                
                <div class="order-details">
                  <div>
                    <div class="info-label">Items</div>
                    <div class="info-value"><?= str_replace(',', ', ', $order['food_ids']) ?></div>
                  </div>
                  <div>
                    <div class="info-label">Total Amount</div>
                    <div class="info-value">₹<?= number_format($order['total'], 2) ?></div>
                  </div>
                  <div>
                    <div class="info-label">Payment Method</div>
                    <div class="info-value"><?= ucfirst($order['payment_method']) ?></div>
                  </div>
                  <div>
                    <div class="info-label">Status</div>
                    <div class="order-status <?= $status_class ?>">
                      <?= ucfirst($order['payment_status']) ?>
                    </div>
                  </div>
                </div>
                
                <div class="order-actions">
                  <a href="#" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                    <i class="fas fa-redo"></i> Reorder
                  </a>
                  <a href="#" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                    <i class="fas fa-question-circle"></i> Help
                  </a>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-shopping-bag"></i>
              <h3>No Orders Yet</h3>
              <p>You haven't placed any orders with us yet.</p>
              <a href="index.php" class="btn">
                <i class="fas fa-utensils"></i> Start Ordering
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>