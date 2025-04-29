<?php
include 'db_connect.php';

$venue_id = $_GET['venue_id'] ?? 0;

$sql = "SELECT booking_date FROM bookings WHERE venue_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $venue_id);
$stmt->execute();
$result = $stmt->get_result();

$bookedDates = [];
while ($row = $result->fetch_assoc()) {
    $bookedDates[] = date('Y-m-d', strtotime($row['booking_date']));
}

header('Content-Type: application/json');
echo json_encode(['bookedDates' => $bookedDates]);
?>
