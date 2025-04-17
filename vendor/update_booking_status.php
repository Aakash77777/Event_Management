<?php
session_start();
include '../frontend/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_paid'])) {
    $booking_id = $_POST['booking_id'];

    // Update booking status to "paid"
    $stmt = $conn->prepare("UPDATE bookings SET status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "<script>alert('Booking marked as Paid.'); window.location.href='bookings.php';</script>";
    } else {
        echo "<script>alert('Error updating status. Try again.');</script>";
    }
}
?>
