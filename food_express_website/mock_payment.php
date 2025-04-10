<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
?>

<h2>Mock Payment</h2>
<p>Total: ₹<?= $_GET['total'] ?? '0'; ?></p>
<form method="post" action="payment_success.php">
  <input type="text" name="card_number" placeholder="Card Number" required><br>
  <button type="submit">Pay Now</button>
</form>
