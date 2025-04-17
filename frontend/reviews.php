<?php
session_start();
include 'db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO reviews (user_id, rating, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $rating, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Review submitted successfully!'); window.location.href='reviews.php';</script>";
        exit();
    } else {
        $error = "Failed to submit review.";
    }
    $stmt->close();
}

// Fetch all reviews
$review_sql = "SELECT r.id, r.rating, r.message, r.created_at, u.username 
               FROM reviews r 
               JOIN users u ON r.user_id = u.id 
               ORDER BY r.created_at DESC";
$review_result = $conn->query($review_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Review - Royal Events</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .review-form {
            max-width: 500px;
            margin: 30px auto;
            background-color: #2c2f35;
            padding: 20px;
            border-radius: 12px;
            color: #fff;
            box-sizing: border-box;
        }

        .stars {
            display: flex;
            justify-content: space-between;
            flex-direction: row-reverse;
            margin-bottom: 15px;
            padding: 0 5px;
        }

        .stars input[type="radio"] {
            display: none;
        }

        .stars label {
            font-size: 2em;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s ease-in-out;
        }

        .stars input:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label {
            color: #FFD700;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: none;
            margin-bottom: 10px;
            resize: none;
            box-sizing: border-box;
            font-size: 1rem;
        }

        .submit-btn {
            padding: 10px 20px;
            background-color: #FFD700;
            color: #000;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .message {
            text-align: center;
            margin-top: 10px;
        }

        .reviews-section {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
        }

        .reviews-section h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .review {
            border-bottom: 1px solid #ccc;
            padding: 15px 0;
        }

        .review h4 {
            margin: 0;
            font-weight: bold;
        }

        .review .stars {
            color: #FFD700;
            font-size: 1.2rem;
            padding: 5px 0;
        }

        .review .date {
            font-size: 0.9em;
            color: #777;
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="reviews.php">Review</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Review Form -->
<div class="review-form">
    <h2>Leave a Review</h2>
    <?php if (isset($success)) echo "<p class='message' style='color: lightgreen;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='message' style='color: red;'>$error</p>"; ?>

    <form method="POST">
        <div class="stars">
            <input type="radio" name="rating" value="5" id="star5" required><label for="star5">&#9733;</label>
            <input type="radio" name="rating" value="4" id="star4"><label for="star4">&#9733;</label>
            <input type="radio" name="rating" value="3" id="star3"><label for="star3">&#9733;</label>
            <input type="radio" name="rating" value="2" id="star2"><label for="star2">&#9733;</label>
            <input type="radio" name="rating" value="1" id="star1"><label for="star1">&#9733;</label>
        </div>

        <textarea name="message" rows="5" placeholder="Write your review here..." required></textarea>
        <button class="submit-btn" type="submit">Submit Review</button>
    </form>
</div>

<!-- Display Reviews -->
<div class="reviews-section">
    <h2>User Reviews</h2>
    <?php if ($review_result->num_rows > 0): ?>
        <?php while ($row = $review_result->fetch_assoc()): ?>
            <div class="review">
                <h4><?php echo htmlspecialchars($row['username']); ?></h4>
                <div class="stars"><?php echo str_repeat("★", $row['rating']) . str_repeat("☆", 5 - $row['rating']); ?></div>
                <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                <p class="date">Submitted on <?php echo date('F j, Y', strtotime($row['created_at'])); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No reviews submitted yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
