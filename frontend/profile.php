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
    /* ==== GLOBAL STYLES ==== */
body {
    font-family: 'Poppins', sans-serif;
    background: #121212;
    color: #e0e0e0;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* ==== HEADER ==== */
header {
    width: 100%;
    background: #1e1e1e;
    padding: 15px 0;
    text-align: center;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
}

header h1 {
    color: #f39c12;
    margin: 0;
    font-size: 26px;
}

/* ==== NAVIGATION ==== */
nav ul {
    list-style: none;
    padding: 0;
    margin: 10px 0 0;
    display: flex;
    justify-content: center;
    gap: 15px;
}

nav ul li {
    display: inline;
}

nav ul li a {
    text-decoration: none;
    color: #e0e0e0;
    font-size: 16px;
    padding: 8px 15px;
    border-radius: 6px;
    transition: 0.3s;
}

nav ul li a:hover {
    background: rgba(243, 156, 18, 0.2);
    color: #f39c12;
}

/* ==== MAIN CONTAINER ==== */
main {
    width: 90%;
    max-width: 500px;
    margin-top: 40px;
    padding: 25px;
    background: #1a1a1a;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    text-align: center;
}

/* ==== PROFILE SECTION ==== */
.profile-section h2 {
    font-size: 24px;
    font-weight: 600;
    color: #f39c12;
    margin-bottom: 20px;
}

/* ==== SUCCESS & ERROR MESSAGES ==== */
.success {
    color: #2ecc71;
    background: rgba(46, 204, 113, 0.2);
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}

.error {
    color: #e74c3c;
    background: rgba(231, 76, 60, 0.2);
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}

/* ==== FORM STYLES ==== */
.profile-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    text-align: left;
}

.profile-form label {
    font-size: 14px;
    font-weight: 500;
    color: #e0e0e0;
}

.profile-form input {
    width: 100%;
    padding: 12px;
    border: 1px solid #444;
    border-radius: 8px;
    background: #222;
    color: #e0e0e0;
    font-size: 16px;
    transition: 0.3s;
}

.profile-form input:focus {
    outline: none;
    border-color: #f39c12;
    box-shadow: 0 0 10px rgba(243, 156, 18, 0.5);
}

/* ==== BUTTONS ==== */
button {
    padding: 12px;
    border: none;
    background: #f39c12;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #d87d0f;
    transform: scale(1.05);
}

/* ==== LOGOUT BUTTON ==== */
.logout-btn {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #e74c3c;
    font-weight: bold;
    transition: 0.3s;
}

.logout-btn:hover {
    color: #ff6b5c;
}

/* ==== FOOTER ==== */
footer {
    margin-top: 20px;
    padding: 15px;
    width: 100%;
    text-align: center;
    background: #222222;
    color: white;
    font-size: 14px;
    font-weight: 500;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

</style>

