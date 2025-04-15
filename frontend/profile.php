<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT username, email, password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update Profile
    if (!isset($_POST['change_password'])) {
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

    // Change Password
    if (isset($_POST['change_password'])) {
        $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_new_password = mysqli_real_escape_string($conn, $_POST['confirm_new_password']);

        if ($new_password !== $confirm_new_password) {
            $password_error = "New password and confirmation do not match.";
        } else {
            // Verify the current password
            if (password_verify($current_password, $user['password'])) {
                // Hash the new password before storing it
                $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update password in the database
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_new_password, $user_id);

                if ($update_stmt->execute()) {
                    $password_message = "Password updated successfully!";
                } else {
                    $password_error = "Error updating password.";
                }
            } else {
                $password_error = "Current password is incorrect.";
            }
        }
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
    <style>
        .profile-booking-wrapper {
            max-width: 900px;
            margin: 3rem auto;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .profile-section {
            padding: 2rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            color: #1f2937;
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

        .booking-links {
            background-color: #1e3a8a;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-around;
            gap: 1.5rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .booking-link {
            color: #1e3a8a;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.8rem 1rem;
            background-color: #f3f4f6;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            flex: 1;
        }

        .booking-link:hover {
            background-color: #e0e7ff;
            color: #111827;
            transform: translateY(-2px);
        }

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
                <li><a href="reviews.php">Review</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="profile-booking-wrapper">

            <!-- Bookings section on top -->
            <section class="booking-links">
                <a href="myeventbookings.php" class="booking-link">My Event Bookings</a>
                <a href="myvenuebookings.php" class="booking-link">My Venue Bookings</a>
            </section>

            <!-- Profile update form below -->
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

                <!-- Change Password Section -->
                <section class="change-password-section">
                    <h3>Change Password</h3>

                    <?php if (isset($password_message)) echo "<p class='success'>$password_message</p>"; ?>
                    <?php if (isset($password_error)) echo "<p class='error'>$password_error</p>"; ?>

                    <form method="post" class="profile-form">
                        <label>Current Password:</label>
                        <input type="password" name="current_password" required>

                        <label>New Password:</label>
                        <input type="password" name="new_password" required>

                        <label>Confirm New Password:</label>
                        <input type="password" name="confirm_new_password" required>

                        <button type="submit" name="change_password">Change Password</button>
                    </form>
                </section>

                <p><a href="logout.php" class="logout-btn">Logout</a></p>
            </section>

        </div>
    </main>

    <footer>
        <p>&copy; 2025 Royal Event. All rights reserved. For any help & support: 📱+977 9864791919 📧 RoyalEvents@gmail.com.</p>
    </footer>
</body>
</html>
