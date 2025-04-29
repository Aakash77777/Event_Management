<?php
// vendor/venues.php
session_start();
include '../frontend/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Handle venue deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $img_query = $conn->query("SELECT image FROM venues WHERE id = $delete_id");
    $img_row = $img_query->fetch_assoc();
    $image_path = "../frontend/uploads/venues/" . $img_row['image'];
    $conn->query("DELETE FROM venues WHERE id = $delete_id");
    if (file_exists($image_path)) unlink($image_path);
    echo "<script>alert('Venue deleted successfully!'); window.location.href='venues.php';</script>";
    exit();
}

// Initialize variables for add/edit
$edit_id = '';
$venue_name = '';
$location = '';
$description = '';
$image = '';
$price_per_person = '';

// Handle venue editing: load existing record
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $result = $conn->query("SELECT * FROM venues WHERE id = $edit_id");
    if ($result->num_rows > 0) {
        $venue = $result->fetch_assoc();
        $venue_name = $venue['venue_name'];
        $location = $venue['location'];
        $description = $venue['description'];
        $image = $venue['image'];
        $price_per_person = $venue['price_per_person'];
    }
}

// Handle venue addition
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_venue'])) {
    $venue_name = $_POST['venue_name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price_per_person = $_POST['price_per_person'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../frontend/uploads/venues/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_name = basename($_FILES['image']['name']); // Using the actual file name
        $target_file = $target_dir . $image_name;
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image_name;
        }
    }

    if (!empty($image)) {
        $sql = "INSERT INTO venues (venue_name, location, image, description, price_per_person)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $venue_name, $location, $image, $description, $price_per_person);

        if ($stmt->execute()) {
            echo "<script>alert('Venue added successfully!'); window.location.href='venues.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error adding venue.');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload image. Please try again.');</script>";
    }
}


// Handle venue update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_venue'])) {
    $edit_id = (int)$_POST['edit_id'];
    $venue_name = $_POST['venue_name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price_per_person = $_POST['price_per_person'];

    $venue_image_query = $conn->query("SELECT image FROM venues WHERE id = $edit_id");
    $venue_image_row = $venue_image_query->fetch_assoc();
    $current_image = $venue_image_row['image'];

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../frontend/uploads/venues/";
        $new_image = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $new_image;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            if (file_exists($target_dir . $current_image)) unlink($target_dir . $current_image);
            $image = $new_image;
        } else {
            $image = $current_image; // fallback to existing image
        }
    } else {
        $image = $current_image;
    }

    $sql = "UPDATE venues SET venue_name=?, location=?, description=?, image=?, price_per_person=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $venue_name, $location, $description, $image, $price_per_person, $edit_id);

    if ($stmt->execute()) {
        echo "<script>alert('Venue updated successfully!'); window.location.href='venues.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating venue.');</script>";
    }
}

// Fetch all venues for display
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

    <?php if (!empty($edit_id)): ?>
    <h3>Edit Venue</h3>
    <form method="post" action="venues.php" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
        <input type="text" name="venue_name" required value="<?= htmlspecialchars($venue_name) ?>">
        <input type="text" name="location" required value="<?= htmlspecialchars($location) ?>">
        <textarea name="description" required><?= htmlspecialchars($description) ?></textarea>
        <label>Current Image:</label>
        <?php if ($image): ?><img src="../frontend/uploads/venues/<?= htmlspecialchars($image) ?>" width="100"><?php endif; ?>
        <input type="file" name="image" accept="image/*">
        <label for="price_per_person">Price per person (Rs.)</label>
        <input type="number" name="price_per_person" id="price_per_person" step="0.01" min="0" required value="<?= htmlspecialchars($price_per_person) ?>">
        <button type="submit" name="update_venue">Update Venue</button>
    </form>
    <?php endif; ?>

    <h3>Add New Venue</h3>
    <form method="post" action="venues.php" enctype="multipart/form-data">
        <input type="text" name="venue_name" required placeholder="Venue Name">
        <input type="text" name="location" required placeholder="Location">
        <textarea name="description" required placeholder="Enter venue description"></textarea>
        <input type="file" name="image" accept="image/*" required>
        <label for="price_per_person">Price per person (Rs.)</label>
        <input type="number" name="price_per_person" id="price_per_person" step="0.01" min="0" required>
        <button type="submit" name="add_venue">Add Venue</button>
    </form>

    <table>
        <tr><th>ID</th><th>Name</th><th>Location</th><th>Rate</th><th>Description</th><th>Image</th><th>Actions</th></tr>
        <?php while($row = $venues->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['venue_name']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td>Rs. <?= number_format($row['price_per_person'],2) ?></td>
            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
            <td><?php if($row['image']): ?><img src="../frontend/uploads/venues/<?= htmlspecialchars($row['image']) ?>" width="100"><?php endif; ?></td>
            <td>
                <a href="venues.php?edit_id=<?= $row['id'] ?>">Edit</a> |
                <a href="venues.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this venue?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
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
    color: white;
}
form{
    background: white;
     max-width: 500px;
     margin: 20px auto;
     padding: 20px;
     border-radius: 10px;
     box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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