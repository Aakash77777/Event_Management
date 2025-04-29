<?php

session_start();
include 'db_connect.php';
// Include the DOMPDF autoload
require '../vendor/autoload.php'; // Adjust this path based on your directory structure

// Use DOMPDF namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// Initialize DOMPDF
$dompdf = new Dompdf();

// Get booking ID from query parameter
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : null;

// Ensure booking ID is provided
if ($booking_id) {

    $sql = "SELECT id, event_id, name, email, phone, created_at, quantity, total_price, status 
            FROM bookings 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If booking is found
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Generate HTML content with booking details
        $html = '
<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #1e3a8a; }
            p { font-size: 16px; margin-bottom: 10px; }
            .ticket-table { 
                width: 100%; 
                border-collapse: collapse; 
                table-layout: fixed; 
                margin-top: 20px;
            }
            .ticket-table th, .ticket-table td { 
                border: 1px solid #ddd; 
                padding: 10px; 
                text-align: left; 
                word-wrap: break-word;
            }
            .ticket-table th { 
                background-color: #f3f4f6; 
                white-space: nowrap;
            }
            .ticket-table tr:nth-child(even) { 
                background-color: #f9fafb; 
            }
            .ticket-table td, .ticket-table th {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            /* Optional: Reduce font size for better fitting */
            .ticket-table td, .ticket-table th {
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <h1>Event Ticket Details</h1>
        <p><strong>Event ID:</strong> ' . htmlspecialchars($row['event_id']) . '</p>
        <p><strong>Name:</strong> ' . htmlspecialchars($row['name']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>
        <p><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</p>
        <p><strong>Booking Date:</strong> ' . $row['created_at'] . '</p>
        <p><strong>Quantity:</strong> ' . $row['quantity'] . '</p>
        <p><strong>Total Price:</strong> Rs ' . number_format($row['total_price'], 2) . '</p>
        <p><strong>Status:</strong> ' . ucfirst($row['status']) . '</p>

        <h2>Booking Details:</h2>
        <table class="ticket-table">
            <tr>
                <th>Booking ID</th>
                <th>Event ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Booking Date</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>' . $row['id'] . '</td>
                <td>' . $row['event_id'] . '</td>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . htmlspecialchars($row['email']) . '</td>
                <td>' . htmlspecialchars($row['phone']) . '</td>
                <td>' . $row['created_at'] . '</td>
                <td>' . $row['quantity'] . '</td>
                <td>' . number_format($row['total_price'], 2) . '</td>
                <td>' . ucfirst($row['status']) . '</td>
            </tr>
        </table>
    </body>
</html>';


        // Load the HTML content into DOMPDF
        $dompdf->loadHtml($html);

        // (Optional) Set paper size
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF (first pass)
        $dompdf->render();

        // Output the generated PDF (streaming to browser)
        $dompdf->stream("ticket_{$row['id']}.pdf", array("Attachment" => 0));  // Attachment = 0 means open in browser
    } else {
        echo "Booking not found.";
    }
} else {
    echo "No booking ID provided.";
}
?>
