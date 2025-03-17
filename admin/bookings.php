<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Fetch all bookings from the database
$sql = "SELECT b.id, b.user_id, b.event_id, e.event_name, b.name, b.email, b.phone, 
               b.quantity, b.total_price, b.status, b.created_at 
        FROM bookings b
        JOIN events e ON b.event_id = e.id 
        ORDER BY b.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main>
        <h2>All Bookings</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Booked By</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Tickets</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Booking Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td>$<?php echo number_format($row['total_price'], 2); ?></td>
                            <td>
                                <span class="<?php echo ($row['status'] == 'paid') ? 'status-paid' : 'status-unpaid'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <?php if ($row['status'] == 'unpaid'): ?>
                                    <form method="POST" action="update_booking_status.php" style="display:inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="mark_paid">Mark as Paid</button>
                                    </form>
                                <?php else: ?>
                                    <button disabled>Paid</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="10">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy; 2025 Royal Events. All rights reserved.</p>
    </footer>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }
        th {
            background: #333;
            color: white;
        }
        .status-paid {
            color: green;
            font-weight: bold;
        }
        .status-unpaid {
            color: red;
            font-weight: bold;
        }
        button {
            padding: 5px 10px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        button[disabled] {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</body>
</html>
