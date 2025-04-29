<?php
session_start();
include 'db_connect.php';

define('KHALTI_API_URL', 'https://dev.khalti.com/api/v2/epayment/initiate/');
define('KHALTI_SECRET_KEY', 'test_secret_key_729a74fefc2f44eaa1aefd38530fc9e4'); // Use the test secret key for now

function initiateKhaltiPayment($amount, $purchase_order_id, $purchase_order_name, $return_url, $website_url, $customer_info) {
    $data = [
        "return_url" => $return_url,
        "website_url" => $website_url,
        "amount" => $amount, // Amount in paisa
        "purchase_order_id" => $purchase_order_id,
        "purchase_order_name" => $purchase_order_name,
        "customer_info" => $customer_info
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, KHALTI_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Key ' . KHALTI_SECRET_KEY,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $error = curl_error($ch);

    curl_close($ch);

    if ($error) {
        return ['error' => $error];
    } else {
        return json_decode($response, true);
    }
}

// Handle booking and payment initiation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'];
    $quantity = $_POST['quantity'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $created_at = date("Y-m-d H:i:s");
    $status = 'unpaid';

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    $stmt = $conn->prepare("SELECT price, available_seats FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();

    if (!$event) {
        die("Invalid event selected.");
    }

    $event_price = $event['price'];
    $available_seats = $event['available_seats'];

    if ($quantity > $available_seats) {
        die("Not enough seats available. Only $available_seats left.");
    }

    $total_price = $event_price * $quantity;

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, name, email, phone, created_at, quantity, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssids", $user_id, $event_id, $name, $email, $phone, $created_at, $quantity, $total_price, $status);

    if ($stmt->execute()) {
        // Initiate Khalti payment
        $purchase_order_id = "order_" . time();
        $purchase_order_name = "Event Booking: " . $event['event_name'];

        // Insert booking and include order_id
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, name, email, phone, created_at, quantity, total_price, status, order_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssidss", $user_id, $event_id, $name, $email, $phone, $created_at, $quantity, $total_price, $status, $purchase_order_id);

        $return_url = "http://localhost/payment.php"; // Use localhost if testing locally
        // Redirect URL after payment
        $website_url = "http://localhost/index.php";

        $customer_info = [
            "name" => $name,
            "email" => $email,
            "phone" => $phone
        ];

        $result = initiateKhaltiPayment($total_price * 100, $purchase_order_id, $purchase_order_name, $return_url, $website_url, $customer_info);

        if (isset($result['error'])) {
            echo "Error initiating payment: " . $result['error'];
        } else {
            // Redirect user to Khalti payment page
            header('Location: ' . $result['payment_url']);
            exit();
        }
    } else {
        echo "Booking failed: " . $stmt->error;
    }
}
?>
