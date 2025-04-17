<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../frontend/db_connect.php'; // Assuming db_connect is now one level up after vendor renaming

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Delete Event
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $img_query = $conn->query("SELECT image FROM events WHERE id = $delete_id");
    if ($img_query->num_rows > 0) {
        $img_row = $img_query->fetch_assoc();
        $image_path = "../frontend/photos/" . $img_row['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $conn->query("DELETE FROM events WHERE id = $delete_id");
    echo "<script>alert('Event deleted successfully!'); window.location.href='events.php';</script>";
    exit();
}

// Initialize variables
$edit_id = "";
$event_name = $event_date = $venue = $description = $price = $available_seats = $image = "";

// Edit Event
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $result = $conn->query("SELECT * FROM events WHERE id = $edit_id");
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        $event_name = $event['event_name'];
        $event_date = $event['event_date'];
        $venue = $event['venue'];
        $description = $event['description'];
        $price = $event['price'];
        $available_seats = $event['available_seats'];
        $image = $event['image'];
    }
}

// Add Event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $venue = $_POST['venue'];
    $description = $_POST['description']; // Make sure 'description' exists in your form
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];
    $image = $_FILES['image']['name'];
    $target_file = "../frontend/photos/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO events (event_name, event_date, venue, description, price, available_seats, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdis", $event_name, $event_date, $venue, $description, $price, $available_seats, $image);

        if ($stmt->execute()) {
            echo "<script>alert('Event added successfully!'); window.location.href='events.php';</script>";
            exit();
        } else {
            echo "<script>alert('Database insert failed: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Image upload failed.');</script>";
    }
}


// Update Event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_event'])) {
    $edit_id = $_POST['edit_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $venue = $_POST['venue'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];

    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $new_image = $_FILES['image']['name'];
        $target_file = "../frontend/photos/" . basename($new_image);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            if (file_exists("../frontend/photos/" . $image)) {
                unlink("../frontend/photos/" . $image);
            }
            $image = $new_image;
        }
    }

    $sql = "UPDATE events SET event_name=?, event_date=?, venue=?, description=?, price=?, available_seats=?, image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdsisi", $event_name, $event_date, $venue, $description, $price, $available_seats, $image, $edit_id);
    if ($stmt->execute()) {
        echo "<script>alert('Event updated successfully!'); window.location.href='events.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update event.');</script>";
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
 
     <!-- Edit Event Form -->
     <?php if (!empty($edit_id)) { ?>
         <h3>Edit Event</h3>
         <form method="post" enctype="multipart/form-data">
             <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
             <input type="text" name="event_name" required value="<?php echo $event_name; ?>">
             <input type="date" name="event_date" required value="<?php echo $event_date; ?>">
             <input type="text" name="venue" required value="<?php echo $venue; ?>">
             <textarea name="description" required placeholder="Event Description"></textarea>
             <input type="number" name="price" required value="<?php echo $price; ?>">
             <input type="number" name="available_seats" required value="<?php echo $available_seats; ?>">
             <input type="file" name="image" accept="image/*">
             <?php if (!empty($image)) { ?>
                 <img src="../frontend/photos/<?php echo $image; ?>" width="100">
             <?php } ?>
             <button type="submit" name="update_event">Update Event</button>
         </form>
     <?php } ?>
 
     <!-- Add New Event Form -->
     <h3>Add New Event</h3>
     <form method="post" enctype="multipart/form-data">
    <input type="text" name="event_name" required placeholder="Event Name">
    <input type="date" name="event_date" required>
    <input type="text" name="venue" required placeholder="Venue">
    <textarea name="description" required placeholder="Event Description"></textarea>
    <input type="number" name="price" required placeholder="Price" step="0.01">
    <input type="number" name="available_seats" required placeholder="Available Seats" min="1">
    <input type="file" name="image" accept="image/*" required>
    <button type="submit" name="add_event">Add Event</button>
</form>

 
     <!-- Events Table -->
     <table border="1">
         <tr>
             <th>ID</th>
             <th>Event Name</th>
             <th>Date</th>
             <th>Venue</th>
             <th>Description</th>
             <th>Price</th>
             <th>Available Seats</th>
             <th>Image</th>
             <th>Actions</th>
         </tr>
         <?php while ($row = $events->fetch_assoc()) { ?>
             <tr>
                 <td><?php echo $row['id']; ?></td>
                 <td><?php echo $row['event_name']; ?></td>
                 <td><?php echo $row['event_date']; ?></td>
                 <td><?php echo $row['venue']; ?></td>
                 <td><?php echo $row['description']; ?></td>
                 <td>Rs<?php echo number_format($row['price'], 2); ?></td>
                 <td><?php echo $row['available_seats']; ?></td>
                 <td>
                     <img src="../frontend/photos/<?php echo $row['image']; ?>" alt="Event Image" width="100">
                 </td>
                 <td>
                     <a href="events.php?edit_id=<?php echo $row['id']; ?>">✏️ Edit</a> | 
                     <a href="events.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                 </td>
             </tr>
         <?php } ?>
     </table>
 </body>
 <style> 
     /* General Page Styling */
 body {
     font-family: 'Arial', sans-serif;
     background-color: #f4f4f4;
     margin: 0;
     padding: 20px;
 }
 
 /* Headings */
 h2, h3 {
     text-align: center;
     color: white;
     margin-bottom: 20px;
 }
 
 /* Form Styling */
 form {
     background: white;
     max-width: 500px;
     margin: 20px auto;
     padding: 20px;
     border-radius: 10px;
     box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
 }
 
 input, textarea {
     width: calc(100% - 20px);
     padding: 10px;
     margin: 10px auto;
     display: block;
     border: 1px solid #ddd;
     border-radius: 5px;
     font-size: 16px;
 }
 
 textarea {
     height: 100px;
     resize: none;
 }
 
 /* Buttons */
 button {
     display: block;
     width: 100%;
     padding: 12px;
     background-color: rgb(36, 77, 121);
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
 
 /* Events Table */
 table {
     width: 90%;
     margin: 20px auto;
     border-collapse: collapse;
     background: white;
     box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
     border-radius: 10px;
     overflow: hidden;
 }
 
 th, td {
     padding: 12px;
     text-align: left;
 }
 
 th {
     background-color: rgb(36, 77, 121);
     color: white;
     font-size: 16px;
 }
 
 td {
     border-bottom: 1px solid #ddd;
 }
 
 tr:nth-child(even) {
     background-color: #f9f9f9;
 }
 
 tr:hover {
     background-color: #e3f2fd;
 }
 
 /* Event Image */
 img {
     display: block;
     border-radius: 5px;
     box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
 }
 
 /* Action Links */
 a {
     text-decoration: none;
     color: white;
     padding: 5px 10px;
     border-radius: 5px;
     font-size: 14px;
 }
 
 a:hover {
     opacity: 0.8;
 }
 
 /* Edit and Delete Buttons */
 .edit-btn {
     background-color: #ffc107;
     padding: 6px 10px;
     color: black;
 }
 
 .delete-btn {
     background-color: #dc3545;
     padding: 6px 10px;
 }
 
 /* Responsive Design */
 @media (max-width: 768px) {
     table {
         font-size: 14px;
     }
 
     form {
         width: 90%;
     }
 
     input, textarea, button {
         font-size: 14px;
     }
 }
 
 </style>
 </html>