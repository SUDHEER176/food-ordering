<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.php");
  exit;
}

if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $image = $_POST['image'];
  mysqli_query($conn, "INSERT INTO food_items (name, price, image) VALUES ('$name', $price, '$image')");
}

if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  mysqli_query($conn, "DELETE FROM food_items WHERE id=$id");
}

$result = mysqli_query($conn, "SELECT * FROM food_items");
?>

<h2>Admin Panel</h2>
<form method="post">
  <input type="text" name="name" placeholder="Food Name" required>
  <input type="number" name="price" placeholder="Price" required>
  <input type="text" name="image" placeholder="Image URL" required>
  <button name="add">Add Item</button>
</form>

<h3>Food Items</h3>
<?php while($row = mysqli_fetch_assoc($result)): ?>
  <p><?= $row['name']; ?> - ₹<?= $row['price']; ?>
    <a href="?delete=<?= $row['id']; ?>">Delete</a>
  </p>
<?php endwhile; ?>
