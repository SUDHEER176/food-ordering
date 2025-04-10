<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $delivery_time = mysqli_real_escape_string($conn, $_POST['delivery_time']);
    $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $promotion = mysqli_real_escape_string($conn, $_POST['promotion']);
    $free_delivery = isset($_POST['free_delivery']) ? 1 : 0;
    
    // Handle image upload
    $image_name = 'default-restaurant-' . rand(1, 10) . '.jpg'; // Default fallback
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'images/restaurants/';
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        
        // Check if file is an image
        $image_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($image_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                // File uploaded successfully
            } else {
                $image_name = 'default-restaurant-' . rand(1, 10) . '.jpg';
            }
        } else {
            $image_name = 'default-restaurant-' . rand(1, 10) . '.jpg';
        }
    }
    
    // Insert into database
    $query = "INSERT INTO restaurants (name, image, rating, delivery_time, cuisine, price, promotion, free_delivery) 
              VALUES ('$name', '$image_name', '$rating', '$delivery_time', '$cuisine', '$price', '$promotion', '$free_delivery')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Restaurant added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Restaurant - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .admin-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        
        .checkbox-group input {
            margin-right: 10px;
        }
        
        button {
            background-color: #ff9800;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #f57c00;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #ff9800;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1><i class="fas fa-plus-circle"></i> Add New Restaurant</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Restaurant Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="image">Restaurant Image</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small>Leave empty to use default image</small>
            </div>
            
            <div class="form-group">
                <label for="rating">Rating (1-5)</label>
                <input type="number" id="rating" name="rating" min="1" max="5" step="0.1" required>
            </div>
            
            <div class="form-group">
                <label for="delivery_time">Delivery Time</label>
                <input type="text" id="delivery_time" name="delivery_time" placeholder="e.g. 25-30 min" required>
            </div>
            
            <div class="form-group">
                <label for="cuisine">Cuisine Type</label>
                <input type="text" id="cuisine" name="cuisine" placeholder="e.g. Italian, Continental" required>
            </div>
            
            <div class="form-group">
                <label for="price">Price Range</label>
                <input type="text" id="price" name="price" placeholder="e.g. ₹500 for two" required>
            </div>
            
            <div class="form-group">
                <label for="promotion">Promotion (Optional)</label>
                <input type="text" id="promotion" name="promotion" placeholder="e.g. 20% OFF">
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="free_delivery" name="free_delivery">
                <label for="free_delivery">Free Delivery</label>
            </div>
            
            <button type="submit">Add Restaurant</button>
        </form>
        
        <a href="admin-dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</body>
</html>