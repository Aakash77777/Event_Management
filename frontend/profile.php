<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);

    $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $new_username, $new_email, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['username'] = $new_username;
        $user['username'] = $new_username;
        $user['email'] = $new_email;
        $message = "Profile updated successfully!";
    } else {
        $error = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Royal Events</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Royal Events</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="venues.php">Venues</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="profile-section">
            <h2>Profile</h2>

            <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

            <form method="post" class="profile-form">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <button type="submit">Update Profile</button>
            </form>

            <p><a href="logout.php" class="logout-btn">Logout</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Royal Events. All rights reserved.</p>
    </footer>
</body>
</html>
