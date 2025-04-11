<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the form data
$venue_id = $_POST['venue_id'];
$booking_date = $_POST['booking_date'];
$food_ids = $_POST['food_ids']; // This will be an array of selected food IDs
$user_id = $_SESSION['user_id'];

// Insert the booking data into the venue_booking table
$sql = "INSERT INTO venue_booking (user_id, venue_id, booking_date, food_ids) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$food_ids_string = implode(',', $food_ids); // Convert array of food IDs to comma-separated string
$stmt->bind_param("iiss", $user_id, $venue_id, $booking_date, $food_ids_string);

if ($stmt->execute()) {
    $booking_id = $stmt->insert_id; // Get the ID of the newly created booking

    // Now insert the selected food items into the booking_foods table
    $food_sql = "INSERT INTO booking_foods (booking_id, food_id) VALUES (?, ?)";
    $food_stmt = $conn->prepare($food_sql);

    foreach ($food_ids as $food_id) {
        $food_stmt->bind_param("ii", $booking_id, $food_id);
        $food_stmt->execute();
    }

    // Close the prepared statements
    $food_stmt->close();
    $stmt->close();

    echo "<script>alert('Booking successful!'); window.location.href='venues.php';</script>";
} else {
    // Close the statement in case of an error
    $stmt->close();
    echo "<script>alert('Error making the booking. Please try again.');</script>";
}
?>
  

<style>
    
</style>