<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Fetch all reviews
$sql = "SELECT r.id, r.rating, r.message, r.created_at, u.username 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reviews - Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .reviews-section {
            max-width: 900px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
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
        }

        .review .date {
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="reviews-section">
        <h2>User Reviews</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
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
