<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db.php';

// Create images folder if not exists
if (!is_dir('images')) {
    mkdir('images', 0755, true);
}

// Handle food item operations
$message = '';
if (isset($_POST['add'])) {
    // Add new food item
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imageType = $_FILES['image']['type'];
    $targetDir = "images/";
    $targetFile = $targetDir . uniqid() . '_' . basename($imageName);

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    if (in_array($imageType, $allowedTypes)) {
        if ($_FILES['image']['size'] <= $maxFileSize) {
            if (move_uploaded_file($imageTmp, $targetFile)) {
                $stmt = $conn->prepare("INSERT INTO food_items (name, price, category, image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sdss", $name, $price, $category, $targetFile);
                $stmt->execute();
                $message = "<div class='alert success'>🍕 Food item added successfully!</div>";
            } else {
                $message = "<div class='alert error'>❌ Failed to upload image</div>";
            }
        } else {
            $message = "<div class='alert error'>❌ Image size exceeds 2MB limit</div>";
        }
    } else {
        $message = "<div class='alert error'>❌ Only JPG, PNG, or GIF files are allowed</div>";
    }
} elseif (isset($_GET['delete'])) {
    // Delete food item
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = "<div class='alert success'>🗑️ Food item deleted successfully</div>";
} elseif (isset($_POST['update'])) {
    // Update food item
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $current_image = mysqli_real_escape_string($conn, $_POST['current_image']);
    $imagePath = $current_image;

    if ($_FILES['image']['error'] === 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageType = $_FILES['image']['type'];
        $targetDir = "images/";
        $targetFile = $targetDir . uniqid() . '_' . basename($imageName);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if (in_array($imageType, $allowedTypes) && $_FILES['image']['size'] <= $maxFileSize) {
            if (move_uploaded_file($imageTmp, $targetFile)) {
                $imagePath = $targetFile;
                // Delete old image file
                if (file_exists($current_image)) {
                    unlink($current_image);
                }
            }
        }
    }

    $stmt = $conn->prepare("UPDATE food_items SET name=?, price=?, category=?, image=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $category, $imagePath, $id);
    $stmt->execute();
    $message = "<div class='alert success'>✏️ Food item updated successfully!</div>";
}

$items = $conn->query("SELECT * FROM food_items ORDER BY category, name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Food Items | Food Express</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2A9D8F;
            --primary-dark: #264653;
            --secondary: #E9C46A;
            --accent-light: #F4A261;
            --accent-dark: #E76F51;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--primary-dark);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h2 {
            color: var(--primary-dark);
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--secondary);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin: 15px 0;
            text-align: center;
            font-weight: 500;
        }
        
        .alert.success {
            background-color: rgba(42, 157, 143, 0.2);
            color: var(--primary-dark);
            border-left: 4px solid var(--primary);
        }
        
        .alert.error {
            background-color: rgba(231, 111, 81, 0.2);
            color: var(--accent-dark);
            border-left: 4px solid var(--accent-dark);
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            color: var(--primary-dark);
            text-decoration: none;
            margin-bottom: 20px;
            padding: 8px 15px;
            border-radius: 4px;
            background-color: rgba(233, 196, 106, 0.3);
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background-color: rgba(233, 196, 106, 0.5);
        }
        
        .back-btn i {
            margin-right: 8px;
        }
        
        .add-form {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin: 25px auto;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }
        
        .add-form h3 {
            margin-top: 0;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .add-form h3 i {
            margin-right: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-dark);
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.2);
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #238f82;
        }
        
        .btn-danger {
            background-color: var(--accent-dark);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #d45b3d;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: var(--primary-dark);
            color: white;
            font-weight: 500;
        }
        
        tr:hover {
            background-color: rgba(233, 196, 106, 0.1);
        }
        
        .food-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        
        .action-btns {
            display: flex;
            gap: 10px;
        }
        
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            background-color: rgba(233, 196, 106, 0.3);
            color: #b68f1a;
        }
        
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
            
            .action-btns {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <h2><i class="fas fa-utensils"></i> Manage Food Menu</h2>
        
        <?php if (!empty($message)) echo $message; ?>
        
        <div class="add-form">
            <h3><i class="fas fa-plus-circle"></i> Add New Food Item</h3>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Food Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Food Image (Max 2MB)</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
                </div>
                
                <button type="submit" name="add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($item['image']) ?>">
                        
                        <td><?= htmlspecialchars($item['id']) ?></td>
                        
                        <td>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
                        </td>
                        
                        <td>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($item['price']) ?>" required>
                        </td>
                        
                        <td>
                            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($item['category']) ?>" required>
                        </td>
                        
                        <td>
                            <img class="food-img" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </td>
                        
                        <td class="action-btns">
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save
                            </button>
                            <a href="?delete=<?= $item['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    // Client-side file validation
    document.querySelectorAll("input[type='file']").forEach(input => {
        input.addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!validTypes.includes(file.type)) {
                    alert("Only JPG, PNG, or GIF files are allowed.");
                    e.target.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    alert("Image size should be less than 2MB.");
                    e.target.value = '';
                }
            }
        });
    });
    
    // Confirm before delete
    document.querySelectorAll('.btn-danger').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>