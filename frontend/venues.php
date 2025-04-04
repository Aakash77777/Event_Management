<?php
session_start();
include 'db_connect.php';

// Fetch venues from the database
$sql = "SELECT * FROM venues";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome to Royal Events</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="venues.php">Venues</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="venues">
            <h2>Our Venues</h2>
            <p>Explore our top event venues for your next occasion.</p>
            <div class="venue-list">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="venue">
                            <img src="/frontend/photos/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['venue_name']); ?>">
                            <h3><?php echo htmlspecialchars($row['venue_name']); ?></h3>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No venues available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy;  2025 Royal Event. All rights reserved. For any help & support: 📱+977 9864791919 📧 RoyalEvents@gmail.com.</p>
    </footer>
</body>
</html>
