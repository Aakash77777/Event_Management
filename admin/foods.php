<?php
session_start();
include '../frontend/db_connect.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM foods WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Food item deleted successfully!'); window.location='foods.php';</script>";
    } else {
        echo "<script>alert('Failed to delete food item.');</script>";
    }
}

// Handle Add & Edit Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $food_name = $_POST['food_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $food_id = $_POST['food_id'] ?? null;
    $image = "";

    // File Upload
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
            echo "<script>alert('Image upload failed.');</script>";
        }
    }

    // Check if it's an update or new insert
    if (!empty($food_id)) {
        if (!empty($image)) {
            $sql = "UPDATE foods SET food_name=?, price=?, description=?, image=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdssi", $food_name, $price, $description, $image, $food_id);
        } else {
            $sql = "UPDATE foods SET food_name=?, price=?, description=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsi", $food_name, $price, $description, $food_id);
        }
    } else {
        $sql = "INSERT INTO foods (food_name, price, description, image) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdss", $food_name, $price, $description, $image);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Food item saved successfully!'); window.location='foods.php';</script>";
    } else {
        echo "<script>alert('Failed to save food item.');</script>";
    }
}

// Fetch all food items
$foods = $conn->query("SELECT * FROM foods");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Catering Foods</title>
    <link rel="stylesheet" href="../frontend/styles.css">
</head>
<body>

<h2>Manage Catering Foods</h2>

<!-- Food Form -->
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="food_id" id="food_id">
    <label>Food Name:</label>
    <input type="text" name="food_name" id="food_name" required placeholder="Food Name">
    
    <label>Price:</label>
    <input type="number" name="price" id="price" required placeholder="Price in Rupees" step="0.01">
    
    <label>Description:</label>
    <textarea name="description" id="description" required placeholder="Enter food description"></textarea>
    
    <label>Image:</label>
    <input type="file" name="image" id="image">
    
    <button type="submit">Save Food</button>
</form>

<!-- Food Table -->
<table border="1">
    <tr>
        <th>ID</th>
        <th>Food Name</th>
        <th>Price</th>
        <th>Description</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $foods->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['food_name']); ?></td>
            <td>Rs<?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><img src="../frontend/uploads/foods/<?php echo $row['image']; ?>" alt="Food Image" width="100"></td>
            <td>
                <button onclick="editFood(
                    <?php echo $row['id']; ?>, 
                    '<?php echo htmlspecialchars($row['food_name'], ENT_QUOTES); ?>', 
                    <?php echo $row['price']; ?>, 
                    '<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>'
                )">Edit</button>
                <a href="foods.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this food item?');">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<script>
function editFood(id, name, price, description) {
    document.getElementById('food_id').value = id;
    document.getElementById('food_name').value = name;
    document.getElementById('price').value = price;
    document.getElementById('description').value = description;
}
</script>

</body>
</html>

<!-- CSS -->
 <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
}

h2 {
    text-align: center;
    color: white;
}

form {
    background: white;
    max-width: 500px;
    margin: 0 auto 20px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

input, textarea {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
}

button {
    padding: 8px 12px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    text-align: center;
    font-size: 14px;
}

button:hover {
    background-color: #0056b3;
}

table {
    width: 100%;
    background: white;
    border-collapse: collapse;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

th, td {
    padding: 12px;
    text-align: left;
}

th {
    background-color: #007bff;
    color: white;
}

td {
    border-bottom: 1px solid #ddd;
}

/* Fix alignment of buttons */
td.actions {
    display: flex;
    gap: 10px; /* Space between buttons */
    align-items: center;
}

/* Edit button */
.edit-btn {
    background-color: #007bff;
    color: white;
    padding: 5px 12px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.edit-btn:hover {
    background-color: #0056b3;
}

/* Delete button */
.delete-btn {
    background-color: red;
    color: white;
    padding: 5px 12px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.delete-btn:hover {
    background-color: darkred;
}

/* Image Styling */
img {
    border-radius: 5px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
    width: 80px; /* Set a fixed width for consistency */
    height: auto;
}
</style>