<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Handle food deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Get image path
    $img_query = $conn->query("SELECT picture FROM foods WHERE id = $delete_id");
    $img_row = $img_query->fetch_assoc();
    $image_path = "../frontend/uploads/foods/" . $img_row['picture'];

    // Delete food
    $conn->query("DELETE FROM foods WHERE id = $delete_id");

    // Delete image file
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    echo "<script>alert('Food deleted successfully!'); window.location.href='foods.php';</script>";
    exit();
}

// Initialize variables
$edit_id = "";
$name = "";
$description = "";
$price = "";
$image = "";

// Handle food editing
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $result = $conn->query("SELECT * FROM foods WHERE id = $edit_id");

    if ($result->num_rows > 0) {
        $food = $result->fetch_assoc();
        $name = $food['name'];
        $description = $food['description'];
        $price = $food['price'];
        $image = $food['picture'];
    }
}

// Handle new food addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_food'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../frontend/uploads/foods/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image_name;
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
        }
    }

    $sql = "INSERT INTO foods (name, description, price, picture) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $name, $description, $price, $image);

    if ($stmt->execute()) {
        echo "<script>alert('Food added successfully!'); window.location.href='foods.php';</script>";
    } else {
        echo "<script>alert('Error adding food.');</script>";
    }
}

// Handle food update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_food'])) {
    $edit_id = $_POST['edit_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    if (!empty($name) && !empty($description) && !empty($price)) {
        if (!empty($_FILES['image']['name'])) {
            $new_image = $_FILES['image']['name'];
            $target_file = "../frontend/uploads/foods/" . basename($new_image);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                if (file_exists("../frontend/uploads/foods/" . $image)) {
                    unlink("../frontend/uploads/foods/" . $image);
                }
                $image = $new_image;
            }
        }

        $sql = "UPDATE foods SET name=?, description=?, price=?, picture=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $name, $description, $price, $image, $edit_id);

        if ($stmt->execute()) {
            echo "<script>alert('Food updated successfully!'); window.location.href='foods.php';</script>";
            exit();
        } else {
            echo "<p style='color: red;'>Error updating food.</p>";
        }
    } else {
        echo "<p style='color: red;'>All fields are required.</p>";
    }
}

$foods = $conn->query("SELECT * FROM foods");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Foods</title>
    <link rel="stylesheet" href="../frontend/styles.css">
</head>
<body>
    <h2>Manage Foods</h2>

    <!-- Edit Food Form -->
    <?php if (!empty($edit_id)) { ?>
        <h3>Edit Food</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
            <input type="text" name="name" required value="<?php echo $name; ?>" placeholder="Food Name">
            <textarea name="description" required placeholder="Food Description"><?php echo $description; ?></textarea>
            <input type="number" name="price" required value="<?php echo $price; ?>" step="0.01" placeholder="Price">
            <input type="file" name="image" accept="image/*">
            <?php if (!empty($image)) { ?>
                <img src="../frontend/uploads/foods/<?php echo $image; ?>" width="100">
            <?php } ?>
            <button type="submit" name="update_food">Update Food</button>
        </form>
    <?php } ?>

    <!-- Add New Food Form -->
    <h3>Add New Food</h3>
    <form method="post" action="foods.php" enctype="multipart/form-data">
        <input type="text" name="name" required placeholder="Food Name">
        <textarea name="description" required placeholder="Enter food description"></textarea>
        <input type="number" name="price" required step="0.01" placeholder="Price">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="add_food">Add Food</button>
    </form>

    <!-- Foods Table -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $foods->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><img src="../frontend/uploads/foods/<?php echo $row['picture']; ?>" width="100"></td>
                <td>
                    <a href="foods.php?edit_id=<?php echo $row['id']; ?>">✏️ Edit</a> |
                    <a href="foods.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
<style>
    /* Reset and Base Styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        padding: 40px 20px;
        color: #333;
    }

    /* Headings */
    h2, h3 {
        text-align: center;
        margin-bottom: 20px;
    }

    /* Form Containers */
    .form-container {
        max-width: 500px;
        margin: 0 auto 40px auto;
        background-color: #fff;
        padding: 25px 20px;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    /* Form Elements */
    form input[type="text"],
    form input[type="number"],
    form input[type="file"],
    form textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    form textarea {
        resize: vertical;
        min-height: 90px;
    }

    form img {
        margin-bottom: 15px;
        border-radius: 8px;
        width: 100px;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #1e4d7b;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #163d63;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    th, td {
        padding: 14px;
        text-align: left;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    th {
        background-color: #1e4d7b;
        color: white;
    }

    td img {
        width: 100px;
        object-fit: cover;
        border-radius: 8px;
    }

    /* Action links */
    td a {
        text-decoration: none;
        font-weight: 500;
        margin-right: 10px;
    }

    td a:hover {
        text-decoration: underline;
    }

    td a[href*="edit_id"] {
        color: #e67e22;
    }

    td a[href*="delete_id"] {
        color: #c0392b;
    }
</style>
