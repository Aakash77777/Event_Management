<?php
include 'db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer via Composer

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

$order_id = $data['order_id'] ?? '';
$email = $data['email'] ?? '';
$name = $data['name'] ?? '';

if (!$order_id || !$email || !$name) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$subject = "Royal Events - Booking Confirmation #$order_id";
$body = "Dear $name,\n\nThank you for your booking!\nYour order ID is $order_id.\n\nWe look forward to seeing you at the event!\n\nRegards,\nRoyal Events Team";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hattigauda11@gmail.com';
    $mail->Password = 'ozii hjae ytpw ljis'; // Must be a valid App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('hattigauda11@gmail.com', 'Royal Events');
    $mail->addAddress($email, $name);

    $mail->Subject = $subject;
    $mail->isHTML(false); // This is important if you're sending plain text
    $mail->Body = $body;

    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Mailer Error: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => $mail->ErrorInfo]);
}
