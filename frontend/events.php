<?php
session_start();
include 'db_connect.php';

// Fetch events from the database
$sql = "SELECT * FROM events";
$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_event'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('You must be logged in to book an event.'); window.location.href='login.php';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $event_id = $_POST['event_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, name, email, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $event_id, $name, $email, $phone);

    if ($stmt->execute()) {
        echo "<script>alert('Booking successful!');</script>";
    } else {
        echo "<script>alert('Error booking event. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function showBookingForm(eventId, eventName) {
            document.getElementById("bookingForm").style.display = "flex";
            document.getElementById("event_id").value = eventId;
            document.getElementById("event_name_display").textContent = eventName;
        }

        function closeBookingForm() {
            document.getElementById("bookingForm").style.display = "none";
        }
    </script>
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
        <div class="event-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="event">
                        <img src="/frontend/photos/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['event_name']); ?>">
                        <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($row['event_date']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <button class="book-now-btn" onclick="showBookingForm('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['event_name']); ?>')">Book Now</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No events found.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Booking Form -->
    <div id="bookingForm" class="booking-form-container">
        <div class="booking-form">
            <h2>Book Event: <span id="event_name_display"></span></h2>
            <form method="POST">
                <input type="hidden" name="event_id" id="event_id">
                <input type="text" name="name" required placeholder="Your Name">
                <input type="email" name="email" required placeholder="Your Email">
                <input type="text" name="phone" required placeholder="Your Phone Number">
                <button type="submit" name="book_event">Confirm Booking</button>
                <button type="button" class="close-btn" onclick="closeBookingForm()">Cancel</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Royal Events. All rights reserved.</p>
    </footer>

    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Event Styling */
        .event {
            background: white;
            padding: 15px;
            margin: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .event img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .event h3 {
            margin-top: 10px;
        }

        .book-now-btn {
            background: #ff5722;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 16px;
        }

        .book-now-btn:hover {
            background: #e64a19;
        }

        /* Booking Form Styling */
        .booking-form-container {
            display: none;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }

        .booking-form {
            background: white;
            padding: 20px;
            border-radius: 12px;
            width: 400px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .booking-form h2 {
            margin-bottom: 15px;
            font-size: 22px;
            color: #333;
        }

        .booking-form input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .booking-form button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .booking-form button[type="submit"] {
            background: #4CAF50;
            color: white;
        }

        .booking-form button[type="submit"]:hover {
            background: #45a049;
        }

        .close-btn {
            background: #d32f2f;
            color: white;
        }

        .close-btn:hover {
            background: #b71c1c;
        }
    </style>
</body>
</html>
