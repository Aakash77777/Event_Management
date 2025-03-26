<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Handle Venue Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_venue'])) {
    $venue_name = $_POST['venue_name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $image = "";

    // Upload Image
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
    $stmt->execute();
    header("Location: venues.php");
}

// Handle Venue Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Get Image Name Before Deletion
    $result = $conn->query("SELECT image FROM venues WHERE id = $id");
    $venue = $result->fetch_assoc();

    // Delete Image File
    if ($venue['image']) {
        unlink("../frontend/uploads/venues/" . $venue['image']);
    }

    // Delete Venue from Database
    $conn->query("DELETE FROM venues WHERE id = $id");
    header("Location: venues.php");
}

// Handle Venue Editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_venue'])) {
    $id = $_POST['venue_id'];
    $venue_name = $_POST['venue_name'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    $update_image = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../frontend/uploads/venues/";
        $image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $update_image = ", image='$image_name'";
        }
    }

    $sql = "UPDATE venues SET venue_name=?, location=?, description=? $update_image WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $venue_name, $location, $description, $id);
    $stmt->execute();
    header("Location: venues.php");
}

$venues = $conn->query("SELECT * FROM venues");
?>

<h2>Manage Venues</h2>

<!-- Add Venue Form -->
<form method="post" enctype="multipart/form-data">
    <input type="text" name="venue_name" required placeholder="Venue Name">
    <input type="text" name="location" required placeholder="Location">
    <textarea name="description" required placeholder="Enter venue description"></textarea>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit" name="add_venue">Add Venue</button>
</form>

<!-- Edit Venue Form (Hidden by Default) -->
<div id="editFormContainer" style="display: none;">
    <h3>Edit Venue</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="venue_id" id="editVenueId">
        <input type="text" name="venue_name" id="editVenueName" required>
        <input type="text" name="location" id="editVenueLocation" required>
        <textarea name="description" id="editVenueDescription" required></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="edit_venue">Update Venue</button>
    </form>
</div>

<!-- Venue Table -->
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
            <td><img src="../frontend/uploads/venues/<?php echo $row['image']; ?>" alt="Venue Image" width="100"></td>
            <td>
                <button onclick="editVenue(<?php echo $row['id']; ?>, '<?php echo $row['venue_name']; ?>', '<?php echo $row['location']; ?>', '<?php echo htmlspecialchars($row['description']); ?>')">Edit</button>
                <a href="venues.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this venue?')">
                    <button style="background-color: red;">Delete</button>
                </a>
            </td>
        </tr>
    <?php } ?>
</table>

<!-- JavaScript for Edit Feature -->
<script>
    function editVenue(id, name, location, description) {
        document.getElementById('editVenueId').value = id;
        document.getElementById('editVenueName').value = name;
        document.getElementById('editVenueLocation').value = location;
        document.getElementById('editVenueDescription').value = description;
        document.getElementById('editFormContainer').style.display = 'block';
    }
</script>

<style>
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
}

h2, h3 {
    text-align: center;
    color: white;
}

/* Form Styling */
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
    font-size: 16px;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    font-size: 18px;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 10px;
}

button:hover {
    background-color: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
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

tr:hover {
    background-color: #e3f2fd;
}

img {
    border-radius: 5px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
}
</style>
