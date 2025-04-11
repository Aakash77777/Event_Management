<?php
session_start();
include '../frontend/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

// Check if the admin clicked "Accept" and passed the booking ID
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // Update the booking status to 'accepted'
    $sql = "UPDATE venue_booking SET status = 'accepted' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "<script>alert('Booking accepted successfully!'); window.location.href='venuebooking.php';</script>";
    } else {
        echo "<script>alert('Error accepting the booking. Please try again.');</script>";
    }
} else {
    echo "<script>alert('Invalid booking ID.'); window.location.href='venuebooking.php';</script>";
}
?>
