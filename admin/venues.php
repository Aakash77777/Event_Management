<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $venue_name = $_POST['venue_name'];
    $location = $_POST['location'];
    $description = $_POST['description']; // Get description from form
    $image = "";

    // Check if an image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../frontend/uploads/venues/";

        // Create directory if not exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        // Move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image_name; // Store only the filename in the database
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
        }
    }

    $sql = "INSERT INTO venues (venue_name, location, image, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $venue_name, $location, $image, $description);
    $stmt->execute();
}

$venues = $conn->query("SELECT * FROM venues");
?>

<h2>Manage Venues</h2>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="venue_name" required placeholder="Venue Name">
    <input type="text" name="location" required placeholder="Location">
    <textarea name="description" required placeholder="Enter venue description"></textarea>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit">Add Venue</button>
</form>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Venue Name</th>
        <th>Location</th>
        <th>Description</th>
        <th>Image</th>
    </tr>
    <?php while ($row = $venues->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['venue_name']; ?></td>
            <td><?php echo $row['location']; ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><img src="../frontend/uploads/venues/<?php echo $row['image']; ?>" alt="Venue Image" width="100"></td>
        </tr>
    <?php } ?>
</table>
