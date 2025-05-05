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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4e3d7; /* Light brown background */
            margin: 0;
            padding: 0;
        }

        .reviews-section {
            max-width: 800px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #6d4c41;
        }

        .review {
            border-bottom: 1px solid #e0cfc2;
            padding: 20px 0;
        }

        .review:last-child {
            border-bottom: none;
        }

        .review h4 {
            margin: 0 0 5px 0;
            font-weight: 600;
            color: #4e342e;
        }

        .review .stars {
            color: #FFD700;
            font-size: 18px;
            margin: 5px 0;
        }

        .review p {
            margin: 5px 0;
            color: #5d4037;
            line-height: 1.5;
        }

        .review .date {
            font-size: 0.9em;
            color: #8d6e63;
        }

        @media (max-width: 600px) {
            .reviews-section {
                margin: 20px;
                padding: 20px;
            }

            .review .stars {
                font-size: 16px;
            }
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
