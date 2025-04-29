<?php
include '../frontend/db_connect.php';

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    $stmt = $conn->prepare("UPDATE venue_booking SET status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    if ($stmt->execute()) {
        header("Location: venuebooking.php"); // change to your actual admin file
    } else {
        echo "Error updating status.";
    }
    $stmt->close();
}
?>
