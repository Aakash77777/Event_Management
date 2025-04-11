<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Handle venue deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Get image path
    $img_query = $conn->query("SELECT image FROM venues WHERE id = $delete_id");
    $img_row = $img_query->fetch_assoc();
    $image_path = "../frontend/uploads/venues/" . $img_row['image'];

    // Delete venue
    $conn->query("DELETE FROM venues WHERE id = $delete_id");

    // Delete image file
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    echo "<script>alert('Venue deleted successfully!'); window.location.href='venues.php';</script>";
    exit();
}

// Initialize variables
$edit_id = "";
$venue_name = "";
$location = "";
$description = "";
$image = "";

// Handle venue editing
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $result = $conn->query("SELECT * FROM venues WHERE id = $edit_id");

    if ($result->num_rows > 0) {
        $venue = $result->fetch_assoc();
        $venue_name = $venue['venue_name'];
        $location = $venue['location'];
        $description = $venue['description'];
        $image = $venue['image'];
    }
}

// Handle new venue addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_venue'])) {
    $venue_name = $_POST['venue_name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $image = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../frontend/uploads/venues/";

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

    $sql = "INSERT INTO venues (venue_name, location, image, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $venue_name, $location, $image, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Venue added successfully!'); window.location.href='venues.php';</script>";
    } else {
        echo "<script>alert('Error adding venue.');</script>";
    }
}

// Handle venue update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_venue'])) {
    $edit_id = $_POST['edit_id'];
    $venue_name = $_POST['venue_name'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    if (!empty($venue_name) && !empty($location) && !empty($description)) {
        if (!empty($_FILES['image']['name'])) {
            $new_image = $_FILES['image']['name'];
            $target_file = "../frontend/uploads/venues/" . basename($new_image);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                if (file_exists("../frontend/uploads/venues/" . $image)) {
                    unlink("../frontend/uploads/venues/" . $image);
                }
                $image = $new_image;
            }
        }

        $sql = "UPDATE venues SET venue_name=?, location=?, description=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $venue_name, $location, $description, $image, $edit_id);

        if ($stmt->execute()) {
            echo "<script>alert('Venue updated successfully!'); window.location.href='venues.php';</script>";
            exit();
        } else {
            echo "<p style='color: red;'>Error updating venue.</p>";
        }
    } else {
        echo "<p style='color: red;'>All fields are required.</p>";
    }
}

$venues = $conn->query("SELECT * FROM venues");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Venues</title>
    <link rel="stylesheet" href="../frontend/styles.css">
</head>
<body>
    <h2>Manage Venues</h2>

    <!-- Edit Venue Form -->
    <?php if (!empty($edit_id)) { ?>
        <h3>Edit Venue</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
            <input type="text" name="venue_name" required value="<?php echo $venue_name; ?>">
            <input type="text" name="location" required value="<?php echo $location; ?>">
            <textarea name="description" required><?php echo $description; ?></textarea>
            <input type="file" name="image" accept="image/*">
            <?php if (!empty($image)) { ?>
                <img src="../frontend/uploads/venues/<?php echo $image; ?>" width="100">
            <?php } ?>
            <button type="submit" name="update_venue">Update Venue</button>
        </form>
    <?php } ?>

    <!-- Add New Venue Form -->
<h3>Add New Venue</h3>
<form method="post" action="venues.php" enctype="multipart/form-data">
    <input type="text" name="venue_name" required placeholder="Venue Name">
    <input type="text" name="location" required placeholder="Location">
    <textarea name="description" required placeholder="Enter venue description"></textarea>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit" name="add_venue">Add Venue</button>
</form>


    <!-- Venues Table -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Venue Name</th>
            <th>Location</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $venues->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['venue_name']; ?></td>
                <td><?php echo $row['location']; ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><img src="../frontend/uploads/venues/<?php echo $row['image']; ?>" width="100"></td>
                <td>
                    <a href="venues.php?edit_id=<?php echo $row['id']; ?>">✏️ Edit</a> |
                    <a href="venues.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
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