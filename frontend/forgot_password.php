<?php
include 'db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        
        // Set timezone to Kathmandu
        date_default_timezone_set("Asia/Kathmandu");
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
    

        // Store token in DB
        $update = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE email=?");
        $update->bind_param("sss", $token, $expiry, $email);

        if ($update->execute()) {
            // Debug: Check if token is saved
            $debugTokenMsg = "Token generated and stored successfully. Token: $token";

            // Make sure this path is correct!
            $resetLink = "http://localhost/tryPHP/frontend/reset_password.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'hattigauda11@gmail.com';
                $mail->Password = 'ozii hjae ytpw ljis';  // Use App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('RoyalEvents@gmail.com', 'Event Management');
                $mail->addAddress($email);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Hello,\n\nClick the link below to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.";

                $mail->send();
                $success = "Password reset link has been sent to your email.<br><small>$debugTokenMsg</small>";
            } catch (Exception $e) {
                $error = "Email sending failed. Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Failed to store reset token.";
        }
    } else {
        $error = "Email not found in the system.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f0f0;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background-image: url(../../concert.jpg);
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 350px;
            height: 220px;
        }

        h2 {
            margin-bottom: 20px;
        }

        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #6c5ce7;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .message {
            margin-top: 10px;
            font-size: 14px;
        }
    
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <form method="POST">
        <label for="email">Enter your registered email</label>
        <input class="email" type="email" name="email" id="email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <div class="message">
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    </div>
</div>
</body>
</html>
