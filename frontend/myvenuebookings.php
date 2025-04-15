<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch venue bookings for the logged-in user
$sql = "SELECT id, venue_id, booking_date, food_ids, status 
        FROM venue_booking 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Venue Bookings - Royal Events</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            max-width: 900px;
            margin: 3rem auto;
            padding: 2rem;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 12px;
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #1e3a8a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 0.75rem;
            text-align: center;
        }

        th {
            background-color: #f3f4f6;
            color: #111827;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .status-confirmed {
            color: green;
            font-weight: bold;
        }

        .status-cancelled {
            color: red;
            font-weight: bold;
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

    <main class="container">
        <h2>My Venue Bookings</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Venue ID</th>
                        <th>Booking Date</th>
                        <th>Food IDs</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['venue_id']; ?></td>
                            <td><?php echo $row['booking_date']; ?></td>
                            <td><?php echo htmlspecialchars($row['food_ids']); ?></td>
                            <td class="status-<?php echo strtolower($row['status']); ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No venue bookings found.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Royal Event. All rights reserved. For any help & support: 📱+977 9864791919 📧 RoyalEvents@gmail.com.</p>
    </footer>
</body>
</html>
