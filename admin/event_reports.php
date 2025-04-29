<?php
// bookings_report.php
// Include database connection

session_start();
require_once '../frontend/db_connect.php';

// Initialize filter values
$status      = isset($_GET['status']) ? trim($_GET['status']) : '';
$start_date  = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date    = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// Build base query with join to events table (to get event name)
$sql = "SELECT b.id, b.user_id, b.event_id, e.event_name AS event_name, b.name, b.email, b.phone,
               b.created_at, b.quantity, b.total_price, b.status
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        WHERE 1";
$params = [];
$types  = '';

// Add filters
if ($status !== '') {
    $sql .= " AND b.status = ?";
    $params[] = $status;
    $types  .= 's';
}
if ($start_date !== '') {
    $sql .= " AND DATE(b.created_at) >= ?";
    $params[] = $start_date;
    $types  .= 's';
}
if ($end_date !== '') {
    $sql .= " AND DATE(b.created_at) <= ?";
    $params[] = $end_date;
    $types  .= 's';
}

$sql .= " ORDER BY b.created_at DESC";

// Prepare and execute
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Calculate total
$total_amount = 0;
$rows = [];
while ($row = $result->fetch_assoc()) {
    $total_amount += $row['total_price'];
    $rows[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Report</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Filter form styling */
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
        }
        .filter-form label {
            font-weight: bold;
        }
        .filter-form select,
        .filter-form input[type="date"] {
            padding: 0.5em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .filter-form button {
            padding: 0.5em 1em;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #0056b3;
        }

        /* Report table styling */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background-color: #ffffff;
        }
        .report-table th,
        .report-table td {
            border: 1px solid #ddd;
            padding: 0.75em;
            text-align: left;
        }
        .report-table th {
            background-color: #007bff;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 0.9em;
        }
        /* remove alternate row colors */
        .report-table tr:nth-child(even) {
            background-color: #ffffff;
        }
        .report-table tfoot td {
            background-color: #ffffff;
            font-weight: bold;
        }
        /* Container for heading, filters, and table */
        .report-container {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="report-container">
    <h1>Event Bookings Report</h1>

    <!-- Filter Form -->
    <form method="get" action="<?php echo basename(__FILE__); ?>" class="filter-form">
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="">All</option>
            <option value="paid" <?= $status==='paid' ? 'selected' : '' ?>>Paid</option>
            <option value="unpaid" <?= $status==='unpaid' ? 'selected' : '' ?>>Unpaid</option>
        </select>

        <label for="start_date">From:</label>
        <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($start_date) ?>">

        <label for="end_date">To:</label>
        <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($end_date) ?>">

        <button type="submit">Filter</button>
    </form>

    <!-- Report Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Event</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Booked At</th>
                <th>Qty</th>
                <th>Total Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($rows) > 0): ?>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= $r['user_id'] ?></td>
                    <td><?= htmlspecialchars($r['event_name']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td><?= $r['created_at'] ?></td>
                    <td><?= $r['quantity'] ?></td>
                    <td><?= number_format($r['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" style="text-align: right;"><strong>Total Amount:</strong></td>
                <td colspan="2" style="color: green;"><strong><?= number_format($total_amount, 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>
    </div>
</body>
</html>
