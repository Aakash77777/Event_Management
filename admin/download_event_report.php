<?php
require_once '../frontend/db_connect.php'; // Database connection
require_once '../vendor/autoload.php'; // Autoload DomPDF

use Dompdf\Dompdf;
use Dompdf\Options;

// Fetch booking data from DB with event details
$sql = "SELECT b.id, b.user_id, b.event_id, e.event_name, b.name, b.email, b.phone, b.created_at, b.quantity, b.total_price, b.status
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        ORDER BY b.created_at DESC"; // Adjust column name for date if necessary
$result = $conn->query($sql);

// Start output buffering for HTML
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Event Booking Report</h2>
    <table>
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
            <?php 
            $total_amount = 0; // Initialize total amount
            while($row = $result->fetch_assoc()): 
                $total_amount += $row['total_price']; // Accumulate total price
            ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td><?= htmlspecialchars($row['event_name']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td><?= htmlspecialchars($row['quantity']) ?></td>
                <td><?= number_format($row['total_price'], 2) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Total Amount -->
    <p><strong>Total Amount: </strong>Rs. <?= number_format($total_amount, 2) ?></p>
</body>
</html>
<?php
$html = ob_get_clean();

// Setup DomPDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Load HTML content
$dompdf->loadHtml($html);

// Set paper size
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Stream PDF as download
$dompdf->stream("event_booking_report.pdf", ["Attachment" => 1]); // 1 forces download
exit;
