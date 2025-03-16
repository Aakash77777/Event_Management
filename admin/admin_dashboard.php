<?php
session_start();
include '../frontend/db_connect.php';

// Check if the user is logged in (assuming only admins can access this)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .logout {
            float: right;
            margin-top: -30px;
        }
        
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome, Admin <?php echo $_SESSION['username']; ?></h1>
    <a href="../frontend/logout.php" class="logout">Logout</a>

    <!-- Events Section -->
    <h2>Events</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Event Name</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Actions</th>
        </tr>
        <?php
        $events = $conn->query("SELECT * FROM events");
        while ($row = $events->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['event_name']}</td>
                <td>{$row['event_date']}</td>
                <td>{$row['venue']}</td>
                <td><a href='#'>Edit</a> | <a href='#'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>

    <!-- Venues Section -->
    <h2>Venues</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Venue Name</th>
            <th>Location</th>
            <th>Actions</th>
        </tr>
        <?php
        $venues = $conn->query("SELECT * FROM venues");
        while ($row = $venues->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['venue_name']}</td>
                <td>{$row['location']}</td>
                <td><a href='#'>Edit</a> | <a href='#'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>

    <!-- Users Section -->
    <h2>Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        $users = $conn->query("SELECT * FROM users");
        while ($row = $users->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td><a href='#'>Edit</a> | <a href='#'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>
</div>


</body>
</html>
