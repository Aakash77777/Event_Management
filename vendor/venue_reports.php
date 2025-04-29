<?php
// venue_reports.php
require_once '../frontend/db_connect.php';

$status      = isset($_GET['status']) ? trim($_GET['status']) : '';
$start_date  = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date    = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

$sql = "SELECT vb.id, vb.user_id, vb.venue_id, v.venue_name AS venue_name, vb.booking_date, vb.food_ids, 
               vb.total_price, vb.status, vb.guests
        FROM venue_booking vb
        JOIN venues v ON vb.venue_id = v.id
        WHERE 1";
$params = [];
$types  = '';

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

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

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
    <title>Venue Booking Report</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0.5rem;
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
        .report-table tr:nth-child(even) {
            background-color: #ffffff;
        }
        .report-table tfoot td {
            background-color: #ffffff;
            font-weight: bold;
        }
        .report-container {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .download-section {
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }
        .download-section button {
            padding: 0.5em 1.2em;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .download-section button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h1>Venue Bookings Report</h1>

        <!-- Filter Form -->
        <form method="get" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="filter-form">
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
                    <td style="color: green;"><strong><?= number_format($total_amount, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- ✅ ONLY ONE Download Button Below Table -->
        <div class="download-section">
            <a href="download_venue_report.php?status=<?= urlencode($status) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
                <button type="button">Download PDF Report</button>
            </a>
        </div>

    </div>
</body>
</html>
