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
    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Vehicle deleted successfully!'); window.location='vehicles.php';</script>";
    } else {
        echo "<script>alert('Failed to delete vehicle.');</script>";
    }
}

// Handle Add & Edit Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_name = $_POST['vehicle_name'];
    $available_seats = $_POST['available_seats'];
    $rental_price = $_POST['rental_price'];
    $image = "";

    // File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../frontend/uploads/vehicles/";

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
    if (isset($_POST['vehicle_id']) && !empty($_POST['vehicle_id'])) {
        $vehicle_id = $_POST['vehicle_id'];
        if (!empty($image)) {
            $sql = "UPDATE vehicles SET vehicle_name=?, available_seats=?, rental_price=?, image=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissi", $vehicle_name, $available_seats, $rental_price, $image, $vehicle_id);
        } else {
            $sql = "UPDATE vehicles SET vehicle_name=?, available_seats=?, rental_price=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sisi", $vehicle_name, $available_seats, $rental_price, $vehicle_id);
        }
    } else {
        $sql = "INSERT INTO vehicles (vehicle_name, available_seats, rental_price, image) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siss", $vehicle_name, $available_seats, $rental_price, $image);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Vehicle saved successfully!'); window.location='vehicles.php';</script>";
    } else {
        echo "<script>alert('Failed to save vehicle.');</script>";
    }
}

// Fetch all vehicles
$vehicles = $conn->query("SELECT * FROM vehicles");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles</title>
    <link rel="stylesheet" href="../frontend/styles.css">
</head>
<body>

<h2>Manage Vehicles</h2>

<!-- Vehicle Form -->
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="vehicle_id" id="vehicle_id">
    <input type="text" name="vehicle_name" id="vehicle_name" required placeholder="Vehicle Name">
    <input type="number" name="available_seats" id="available_seats" required placeholder="Available Seats">
    <input type="number" name="rental_price" id="rental_price" required placeholder="Rental Price" step="0.01">
    <input type="file" name="image" id="image">
    <button type="submit">Save Vehicle</button>
</form>

<!-- Vehicles Table -->
<table border="1">
    <tr>
        <th>ID</th>
        <th>Vehicle Name</th>
        <th>Seats</th>
        <th>Rental Price</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $vehicles->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
            <td><?php echo $row['available_seats']; ?></td>
            <td>Rs<?php echo number_format($row['rental_price'], 2); ?></td>
            <td><img src="../frontend/uploads/vehicles/<?php echo $row['image']; ?>" alt="Vehicle Image" width="100"></td>
            <td>
                <button onclick="editVehicle(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['vehicle_name']); ?>', <?php echo $row['available_seats']; ?>, <?php echo $row['rental_price']; ?>, '<?php echo $row['image']; ?>')">Edit</button>
                <a href="vehicles.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<script>
function editVehicle(id, name, seats, price, image) {
    document.getElementById('vehicle_id').value = id;
    document.getElementById('vehicle_name').value = name;
    document.getElementById('available_seats').value = seats;
    document.getElementById('rental_price').value = price;
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

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
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

img {
    border-radius: 5px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
}

.delete-btn {
    color: white;
    background: red;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
}
</style>
