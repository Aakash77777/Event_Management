<?php
session_start();
include '../frontend/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Fetch user role
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $role);
$stmt->fetch();
$stmt->close();

// Restrict access if not an admin
if ($role !== 'admin') {
    echo "<script>alert('Access denied. Admins only!'); window.location.href='../frontend/index.php';</script>";
    exit();
}

// Store username in session for display
$_SESSION['username'] = $username;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_styles.css"> 
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Icons -->
    <script src="../frontend/script.js"></script>
</body>
</html>

</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
        <li><a href=""><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="events.php"><i class="fas fa-calendar"></i> Events</a></li>
        <li><a href="venues.php"><i class="fas fa-map-marker-alt"></i> Venues</a></li>
        <li><a href="bookings.php"><i class="fas fa-ticket-alt"></i> Event Bookings</a></li>
        <li><a href="venuebooking.php"><i class="fas fa-building"></i> Venue Bookings</a></li>
        <li><a href="foods.php"><i class="fas fa-utensils"></i> Foods</a></li>
        <li><a href="vehicles.php"><i class="fas fa-car"></i> Vehicles</a></li>
        <li><a href="../frontend/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Dashboard</h1>
            <div class="user-profile">
                <img src="../frontend/photos/bipul.jpg" alt="Admin">
                <span>Admin <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </header>

        <!-- Empty Space for Future Content -->
        <div class="dashboard-placeholder">
            <p>Welcome to the Admin Dashboard. Select an option from the sidebar.</p>
        </div>

    </div>

</body>
</html>
