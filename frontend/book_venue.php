<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to book a venue.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $venue_id = $_POST['venue_id'];
    $booking_date = $_POST['booking_date'];
    $guests = $_POST['guests'];
    $food_id = $_POST['food_id'] ?? null;

    // Check if the date is already booked
    $check_sql = "SELECT COUNT(*) FROM venue_booking WHERE venue_id = ? AND booking_date = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("is", $venue_id, $booking_date);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "<script>alert('This venue is already booked on the selected date.'); window.history.back();</script>";
        exit;
    }

    // Fetch venue price per person
    $venue_sql = "SELECT price_per_person FROM venues WHERE id = ?";
    $stmt = $conn->prepare($venue_sql);
    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
    $stmt->bind_result($price_per_person);
    $stmt->fetch();
    $stmt->close();

    $venue_price = $price_per_person * $guests;
    $total_price = $venue_price;

    // Insert booking
    $insert_sql = "INSERT INTO venue_booking (user_id, venue_id, booking_date, guests, total_price, food_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iisiid", $user_id, $venue_id, $booking_date, $guests, $total_price, $food_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Venue booked successfully!'); window.location.href='venues.php';</script>";
} else {
    echo "Invalid request.";
}
?>
