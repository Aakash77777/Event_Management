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
                            <td>Rs.<?php echo number_format($row['total_price'], 2); ?></td>
                            <td>
                                <span class="<?php echo ($row['status'] == 'paid') ? 'status-paid' : 'status-unpaid'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
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
/* ==== GLOBAL STYLES ==== */
body {
    font-family: 'Poppins', sans-serif;
    background: #ffffff; /* White background */
    color: #000000; /* Black text */
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* ==== MAIN CONTAINER ==== */
main {
    width: 95%;
    max-width: 1400px; /* Increased width */
    margin-top: 40px;
    padding: 25px;
    background: #f9f9f9; /* Light gray background */
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* ==== TITLE ==== */
h2 {
    text-align: center;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #333333; /* Dark gray */
}

/* ==== TABLE STYLES ==== */
table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

thead {
    background: #f1f1f1; /* Lighter gray */
    color: #333333; /* Dark gray */
    text-transform: uppercase;
}

th{
    padding: 16px; /* Increased padding */
    text-align: left;
    border-bottom: 1px solid #dddddd;
    font-size: 16px;
    background-color: #007bff;
    background-color: rgb(36, 77, 121);
    color: white;
}
td {
    padding: 16px; /* Increased padding */
    text-align: left;
    border-bottom: 1px solid #dddddd;
    font-size: 16px;
}

tbody tr {
    transition: 0.3s ease-in-out;
}

tbody tr:hover {
    background: rgba(0, 123, 255, 0.1); /* Subtle blue hover */
}

/* ==== STATUS LABELS ==== */
.status-paid {
    background: #28a745; /* Green */
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
}

.status-unpaid {
    background: #dc3545; /* Red */
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
}

/* ==== BUTTON STYLES ==== */
button {
    padding: 12px 16px;
    border: none;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s ease-in-out;
}

button[name="mark_paid"] {
    background: rgb(36, 77, 121); /* Blue */
    color: white;
}

button[name="mark_paid"]:hover {
    background: rgb(36, 77, 121);
    transform: scale(1.05);
}

button[disabled] {
    background: gray;
    cursor: not-allowed;
}

/* ==== FOOTER ==== */
footer {
    margin-top: 20px;
    padding: 15px;
    width: 100%;
    text-align: center;
    background: #f1f1f1;
    color: #333;
    font-size: 14px;
    font-weight: 500;
    border-top: 1px solid #ddd;
}

    </style>
</body>
</html>
