<?php
include 'db_connect.php';

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['order_id']) && isset($data['status'])) {
    $order_id = intval($data['order_id']);
    $status = $data['status'];

    // First, update booking status
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        if ($status == 'paid') {
            // Fetch the quantity booked and event_id
            $stmt2 = $conn->prepare("SELECT event_id, quantity FROM bookings WHERE id = ?");
            $stmt2->bind_param("i", $order_id);
            $stmt2->execute();
            $result = $stmt2->get_result();
            $booking = $result->fetch_assoc();

            if ($booking) {
                $event_id = $booking['event_id'];
                $quantity = $booking['quantity'];

                // Decrease available seats
                $updateEvent = $conn->prepare("UPDATE events SET available_seats = available_seats - ? WHERE id = ? AND available_seats >= ?");
                $updateEvent->bind_param("iii", $quantity, $event_id, $quantity);
                $updateEvent->execute();
            }
        }
        

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}
?>
