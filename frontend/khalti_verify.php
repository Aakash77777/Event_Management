<?php
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$token = $data['token'];
$amount = $data['amount'];
$booking_id = $data['order_id'];

$secret_key = "729a74fefc2f44eaa1aefd38530fc9e4";

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://khalti.com/api/v2/payment/verify/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => http_build_query([
        'token' => $token,
        'amount' => $amount
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Key $secret_key"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(['success' => false, 'message' => 'Curl Error: ' . $err]);
    exit;
}

$res_data = json_decode($response, true);

if (isset($res_data['idx'])) {
    // Payment verified successfully, now update booking
    $stmt = $conn->prepare("UPDATE bookings SET status = 'paid' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    // Reduce available seats in events table
    $stmt2 = $conn->prepare("SELECT event_id, quantity FROM bookings WHERE id = ?");
    $stmt2->bind_param("i", $booking_id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $booking = $result->fetch_assoc();

    if ($booking) {
        $event_id = $booking['event_id'];
        $quantity = $booking['quantity'];

        $stmt3 = $conn->prepare("UPDATE events SET available_seats = available_seats - ? WHERE id = ?");
        $stmt3->bind_param("ii", $quantity, $event_id);
        $stmt3->execute();
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
}
?>
