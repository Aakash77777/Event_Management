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
    $food_ids = $_POST['food_ids'] ?? [];

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

    // Calculate venue price
    $venue_price = $price_per_person * $guests;

    // Calculate total food price
    $total_food_price = 0;
    $food_ids_string = ''; // Initialize the food_ids string
    if (!empty($food_ids)) {
        $food_sql = "SELECT price, id FROM foods WHERE id IN (" . implode(",", array_fill(0, count($food_ids), "?")) . ")";
        $stmt = $conn->prepare($food_sql);
        $stmt->bind_param(str_repeat('i', count($food_ids)), ...$food_ids);
        $stmt->execute();
        $stmt->bind_result($food_price, $food_id);
        while ($stmt->fetch()) {
            $total_food_price += $food_price;
            $food_ids_string .= $food_id . ",";  // Concatenate food IDs
        }
        $stmt->close();
    }

    // Remove the trailing comma
    $food_ids_string = rtrim($food_ids_string, ',');

    // Calculate total price (venue price + food price)
    $total_price = $venue_price + $total_food_price;

    // Insert booking into venue_booking table
    $insert_sql = "INSERT INTO venue_booking (user_id, venue_id, booking_date, guests, total_price, food_ids) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iisiis", $user_id, $venue_id, $booking_date, $guests, $total_price, $food_ids_string);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // Link selected foods to booking
    if (!empty($food_ids)) {
        $food_stmt = $conn->prepare("INSERT INTO booking_foods (booking_id, food_id) VALUES (?, ?)");
        foreach ($food_ids as $food_id) {
            $food_stmt->bind_param("ii", $booking_id, $food_id);
            $food_stmt->execute();
        }
        $food_stmt->close();
    }

    echo "<script>alert('Venue booked successfully!'); window.location.href='venues.php';</script>";
} else {
    echo "Invalid request.";
}
?>
