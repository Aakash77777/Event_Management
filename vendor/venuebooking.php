<?php
session_start();
include '../frontend/db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

$sql = "SELECT vb.id, vb.user_id, vb.venue_id, vb.booking_date, vb.food_ids, vb.status, vb.total_price, vb.guests, v.venue_name, u.username 
        FROM venue_booking vb 
        JOIN venues v ON vb.venue_id = v.id 
        JOIN users u ON vb.user_id = u.id";
  // Get all bookings
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Bookings - Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <main>
        <section class="venue-bookings">
            <h2>All Venue Bookings</h2>
            <?php if ($result->num_rows > 0): ?>
                <table>
                <thead>
    <tr>
        <th>Booking ID</th>
        <th>User</th>
        <th>Venue</th>
        <th>Booking Date</th>
        <th>Guests</th>
        <th>Total Price (Rs)</th>
        <th>Food Selected</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['venue_name']); ?></td>
            <td><?php echo $row['booking_date']; ?></td>
            <td><?php echo $row['guests']; ?></td>
            <td><?php echo $row['total_price']; ?></td>
            <td>
                <?php
                $food_ids = explode(',', $row['food_ids']);
                $food_names = [];
                foreach ($food_ids as $food_id) {
                    $food_sql = "SELECT name FROM foods WHERE id = ?";
                    $food_stmt = $conn->prepare($food_sql);
                    $food_stmt->bind_param("i", $food_id);
                    $food_stmt->execute();
                    $food_result = $food_stmt->get_result();
                    if ($food_row = $food_result->fetch_assoc()) {
                        $food_names[] = $food_row['name'];
                    }
                    $food_stmt->close();
                }
                echo implode(", ", $food_names);
                ?>
            </td>
            <td>
                <?php
                if ($row['status'] === 'paid') {
                    echo "<span class='status-paid'>Paid</span>";
                } else {
                    echo "<span class='status-info'>" . ucfirst($row['status']) . "</span>";
                }
                ?>
            </td>
            <td>
                <?php if ($row['status'] == 'unpaid'): ?>
                    <a href="mark_paid.php?id=<?php echo $row['id']; ?>" class="btn btn-paid">Mark as Paid</a>
                <?php else: ?>
                    <span class="status-info"><?php echo ucfirst($row['status']); ?></span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

                </table>
            <?php else: ?>
                <p>No bookings found.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Royal Event. All rights reserved. For any help & support: 📱+977 9864791919 📧 RoyalEvents@gmail.com.</p>
    </footer>
</body>
</html>
<style>
    .status-paid {
    background-color: #22c55e;
    color: white;
    padding: 0.5rem 0.8rem;
    border-radius: 4px;
    font-weight: bold;
    display: inline-block;
}

.btn-paid {
    background-color: #0ea5e9;
}

.btn-paid:hover {
    background-color: #0284c7;
}

    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background: #f1f5f9;
    color: #333;
}

main {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color:rgb(36, 77, 121);
    margin-bottom: 1.5rem;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

thead {
    background-color:rgb(36, 77, 121);
    color: white;
}

th, td {
    padding: 1rem;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
}

tbody tr:hover {
    background-color: #f0f4ff;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    text-decoration: none;
    color: white;
    margin: 0 5px;
    transition: background 0.3s ease;
}

.btn-accept {
    background-color: #16a34a;
}

.btn-accept:hover {
    background-color: #15803d;
}

.btn-reject {
    background-color: #dc2626;
}

.btn-reject:hover {
    background-color: #b91c1c;
}

.status-info {
    padding: 0.5rem 0.8rem;
    border-radius: 4px;
    background-color: #e2e8f0;
    color: #1f2937;
    font-weight: bold;
    display: inline-block;
}

footer {
    text-align: center;
    margin-top: 3rem;
    padding: 1rem;
    background-color:rgb(36, 77, 121);
    color: white;
    font-size: 0.9rem;
    border-top: 2px solid rgb(36, 77, 121);
}

</style>
