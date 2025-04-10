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
        <p>&copy;  2025 Royal Event. All rights reserved. For any help & support: 📱+977 9864791919 📧 RoyalEvents@gmail.com.</p>
    </footer>
</body>
</html>
<style> 
  /* Profile Section (Matches the card-style look in your screenshot) */
.profile-section {
    max-width: 600px;
    margin: 3rem auto;
    padding: 2rem;
    background: #ffffff; /* white card */
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    color: #1f2937; /* dark gray text */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.profile-section h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    color: #111827;
}

.profile-form {
    display: flex;
    flex-direction: column;
}

.profile-form label {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.profile-form input {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
    background-color: #f9fafb;
    color: #111827;
}

.profile-form input:focus {
    outline: 2px solid #2563eb;
}

button[type="submit"] {
    margin-top: 2rem;
    padding: 0.75rem;
    background-color: #1e3a8a;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s;
}

button[type="submit"]:hover {
    background-color: #1d4ed8;
}

.logout-btn {
    display: inline-block;
    margin-top: 1.5rem;
    padding: 0.6rem 1.2rem;
    background-color: #dc2626;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background 0.3s ease;
}

.logout-btn:hover {
    background-color: #b91c1c;
}

/* Messages */
.success, .error {
    padding: 0.8rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-weight: bold;
    text-align: center;
}

.success {
    background-color: #d1fae5;
    color: #065f46;
}

.error {
    background-color: #fee2e2;
    color: #991b1b;
}

</style>

