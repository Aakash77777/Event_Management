<?php
// venue_reports.php
// Include database connection
require_once '../frontend/db_connect.php';

// Initialize filter values
$status      = isset($_GET['status']) ? trim($_GET['status']) : '';
$start_date  = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date    = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// Build base query with join to venues table (to get venue name)
$sql = "SELECT vb.id, vb.user_id, vb.venue_id, v.venue_name AS venue_name, vb.booking_date, vb.food_ids, 
               vb.total_price, vb.status, vb.guests
        FROM venue_booking vb
        JOIN venues v ON vb.venue_id = v.id
        WHERE 1";
$params = [];
$types  = '';

// Add filters
if ($status !== '') {
    $sql .= " AND vb.status = ?";
    $params[] = $status;
    $types  .= 's';
}
if ($start_date !== '') {
    $sql .= " AND DATE(vb.booking_date) >= ?";
    $params[] = $start_date;
    $types  .= 's';
}
if ($end_date !== '') {
    $sql .= " AND DATE(vb.booking_date) <= ?";
    $params[] = $end_date;
    $types  .= 's';
}

$sql .= " ORDER BY vb.booking_date DESC";

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
    <title>Venue Booking Report</title>
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
    <h1>Venue Bookings Report</h1>

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
                <th>Venue</th>
                <th>Booking Date</th>
                <th>Food IDs</th>
                <th>Status</th>
                <th>Guests</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($rows) > 0): ?>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= $r['user_id'] ?></td>
                    <td><?= htmlspecialchars($r['venue_name']) ?></td>
                    <td><?= $r['booking_date'] ?></td>
                    <td><?= htmlspecialchars($r['food_ids']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td><?= $r['guests'] ?></td>
                    <td><?= number_format($r['total_price'], 2) ?></td>

                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align: right;"><strong>Total Amount:</strong></td>
                <td colspan="1" style="color: green;"><strong><?= number_format($total_amount, 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>
    </div>
</body>
</html>
