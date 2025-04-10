<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

$username = $_SESSION['username'];
$result = mysqli_query($conn, "SELECT * FROM orders WHERE username='$username'");

echo "<h2>Your Order History</h2>";
while ($row = mysqli_fetch_assoc($result)) {
  echo "<p>Order #{$row['id']} - ₹{$row['total']} - Items: {$row['food_ids']}</p>";
}
?>
