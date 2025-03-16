<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';

    if (!empty($event_name) && !empty($event_date) && !empty($venue) && !empty($description) && !empty($price) && isset($_FILES['image'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "../frontend/photos/";
        $target_file = $target_dir . basename($image);

        // Check if file upload is successful
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO events (event_name, event_date, venue, description, price, image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdss", $event_name, $event_date, $venue, $description, $price, $image);
            
            if ($stmt->execute()) {
                echo "<p>Event added successfully!</p>";
            } else {
                echo "<p>Error adding event.</p>";
            }
        } else {
            echo "<p>Error uploading image.</p>";
        }
    } else {
        echo "<p>All fields are required.</p>";
    }
}

// Fetch all events
$events = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Events</title>
    <link rel="stylesheet" href="../frontend/styles.css">
</head>
<body>
    <h2>Manage Events</h2>
    
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="event_name" required placeholder="Event Name">
        <input type="date" name="event_date" required>
        <input type="text" name="venue" required placeholder="Venue">
        <textarea name="description" required placeholder="Event Description"></textarea>
        <input type="number" name="price" required placeholder="Price" step="0.01">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Add Event</button>
    </form>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Event Name</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Description</th>
            <th>Price</th>
            <th>Image</th>
        </tr>
        <?php while ($row = $events->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['event_name']; ?></td>
                <td><?php echo $row['event_date']; ?></td>
                <td><?php echo $row['venue']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>$<?php echo number_format($row['price'], 2); ?></td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="../frontend/photos/<?php echo $row['image']; ?>" alt="Event Image" width="100">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
