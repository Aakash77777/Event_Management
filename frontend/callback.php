<?php
session_start();
include 'db_connect.php';

define('KHALTI_API_URL', 'https://dev.khalti.com/api/v2/epayment/lookup/');
define('KHALTI_SECRET_KEY', '729a74fefc2f44eaa1aefd38530fc9e4'); // Use test key for development

function lookupKhaltiPayment($pidx) {
    $data = ["pidx" => $pidx];

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

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['pidx'])) {
    $pidx = $_GET['pidx'];
    $result = lookupKhaltiPayment($pidx);

    if (isset($result['error'])) {
        echo "Error verifying payment: " . $result['error'];
        exit;
    }

    if ($result['status'] == 'Completed') {
        $order_id = $result['purchase_order']['id']; // Get the order ID used earlier

        // Update booking status to 'paid' in the database
        $stmt = $conn->prepare("UPDATE bookings SET status = 'paid' WHERE id = ?");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<h2>Payment successful! Your booking is confirmed.</h2>";
            echo "<p>Thank you for using Khalti. You can go back to the <a href='profile.php'>Profile</a> page.</p>";
        } else {
            echo "<h2>Payment succeeded, but booking not found or already updated.</h2>";
        }
    } else {
        echo "<h2>❌ Payment was not completed. Please try again.</h2>";
    }
} else {
    echo "Invalid access.";
}
?>
