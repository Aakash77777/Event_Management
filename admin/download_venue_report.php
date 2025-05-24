<?php
// download_venue_report.php
// Include database connection
require_once '../frontend/db_connect.php';
require_once '../vendor/autoload.php'; // Autoload DomPDF

use Dompdf\Dompdf;
use Dompdf\Options;

// Initialize filter values
$status      = isset($_GET['status']) ? trim($_GET['status']) : '';
$start_date  = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date    = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// Build base query with join to venues table (to get venue name)
$sql = "SELECT vb.id, vb.user_id, vb.venue_id, v.venue_name AS venue_name, vb.booking_date, vb.food_id, 
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

// Start output buffering for HTML
ob_start();
?>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Venue Booking Report</h2>
    <table>
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
            <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= $r['user_id'] ?></td>
                <td><?= htmlspecialchars($r['venue_name']) ?></td>
                <td><?= $r['booking_date'] ?></td>
                <td><?= htmlspecialchars($r['food_id']) ?></td>
                <td><?= htmlspecialchars($r['status']) ?></td>
                <td><?= $r['guests'] ?></td>
                <td><?= number_format($r['total_price'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align: right;"><strong>Total Amount:</strong></td>
                <td colspan="1" style="color: green;"><strong><?= number_format($total_amount, 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
<?php
$html = ob_get_clean();

// Setup DomPDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Load HTML to DomPDF
$dompdf->loadHtml($html);

// Set paper size
$dompdf->setPaper('A4', 'portrait');

// Render PDF (first pass)
$dompdf->render();

// Output the generated PDF to Browser (force download)
$dompdf->stream("venue_booking_report.pdf", ["Attachment" => 1]);
exit;
?>
