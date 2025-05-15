<?php
include 'db_connect.php';
// Load Composer autoloader (adjust path if needed)
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get JSON input from POST body
$data = json_decode(file_get_contents("php://input"), true);

// Extract variables with fallback
$order_id = $data['order_id'] ?? '';
$email = $data['email'] ?? '';
$name = $data['name'] ?? '';

// Validate input
if (!$order_id || !$email || !$name) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'hattigauda11@gmail.com';    // Your Gmail
    $mail->Password   = 'ozii hjae ytpw ljis';       // Your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption
    $mail->Port       = 587;

    // Sender & recipient
    $mail->setFrom('hattigauda11@gmail.com', 'Royal Events');
    $mail->addAddress($email, $name);

    // Email subject & body (plain text)
    $mail->isHTML(false);
    $mail->Subject = "Royal Events - Booking Confirmation #$order_id";
    $mail->Body = "Dear $name,\n\nYour booking is confirmed.\nYou can download your ticket from Profile → My Event Bookings.\n\nOrder ID: $order_id\n\nWe look forward to seeing you at the event!\n\nRegards,\nRoyal Events Team";

    $mail->send();

    // Success response
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Log error and return failure
    error_log("Mailer Error: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => $mail->ErrorInfo]);
}
