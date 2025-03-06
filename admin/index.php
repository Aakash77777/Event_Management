<?php
session_start();
include '../frontend/db_connect.php';

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
</head>
<body>
    <h1>Welcome, Admin <?php echo $_SESSION['username']; ?></h1>
    <a href="../frontend/logout.php">Logout</a>
    
    <ul>
        <li><a href="events.php">Manage Events</a></li>
        <li><a href="venues.php">Manage Venues</a></li>
        <li><a href="users.php">Manage Users</a></li>
    </ul>
</body>
</html>
