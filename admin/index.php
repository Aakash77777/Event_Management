<?php
session_start();

// Redirect to login if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../frontend/login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body { display: flex; min-height: 100vh; font-family: Arial; background: #f4f4f4; }
        .sidebar { width: 230px; background: #2c3e50; color: white; padding-top: 30px; position: fixed; height: 100vh; }
        .sidebar h2 { text-align: center; font-size: 22px; margin-bottom: 30px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 15px 25px; }
        .sidebar ul li a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .sidebar ul li:hover { background-color: #34495e; }
        .main-content { margin-left: 230px; padding: 20px; width: calc(100% - 230px); }
        header { background: #ecf0f1; padding: 15px 20px; display: flex; justify-content: space-between; border-radius: 10px; }
        .user-profile { display: flex; align-items: center; gap: 10px; }
        .user-profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #2c3e50; }
        .dashboard-placeholder { background: white; padding: 30px; border-radius: 10px; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="../frontend/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Dashboard</h1>
            <div class="user-profile">
                <img src="../frontend/photos/admin.png" alt="Admin">
                <span>Admin <?php echo htmlspecialchars($username); ?></span>
            </div>
        </header>

        <div class="dashboard-placeholder">
            <p>Welcome to the Admin Dashboard. Use the sidebar to manage users, reviews, and logout.</p>
        </div>
    </div>

</body>
</html>
