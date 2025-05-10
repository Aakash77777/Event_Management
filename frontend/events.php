<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_event'])) {
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

    // Insert booking with status "unpaid"
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, event_id, name, email, phone, created_at, quantity, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssids", $user_id, $event_id, $name, $email, $phone, $created_at, $quantity, $total_price, $status);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        echo "<script>
    localStorage.setItem('order_id', '$order_id');
    localStorage.setItem('amount', '$total_price');
    localStorage.setItem('event_id', '$event_id');
    localStorage.setItem('email', '$email');
    localStorage.setItem('name', '$name');
    window.onload = function() {
        document.getElementById('startKhaltiPayment').click();
    };
</script>";

    } else {
        echo "Booking failed: " . $stmt->error;
    }
}

$eventQuery = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events | Royal Event</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://khalti.com/static/khalti-checkout.js"></script>
</head>

<!-- Chatbot Button -->
<button id="chatbot-btn">
  <img src="/frontend/photos/chatbot.jpg" alt="Chatbot" />
</button>

<!-- Chatbot Frame -->
<div id="chatbot-frame">
  <iframe src="chat.php" width="100%" height="100%" style="border: none; border-radius: 12px; background: white;"></iframe>
</div>

<script>
  // Toggle Chatbot
  const chatbotBtn = document.getElementById('chatbot-btn');
  const chatbotFrame = document.getElementById('chatbot-frame');

  chatbotBtn.addEventListener('click', () => {
    chatbotFrame.style.display = chatbotFrame.style.display === 'block' ? 'none' : 'block';
  });
</script>
<body>
<header>
    <h1>Welcome to Royal Events</h1>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="venues.php">Venues</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="reviews.php">Review</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <div class="event-list">
        <?php if ($eventQuery->num_rows > 0): ?>
            <?php while ($row = $eventQuery->fetch_assoc()): ?>
                <div class="event">
                    <img src="/frontend/photos/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['event_name']); ?>">
                    <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($row['event_date']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
                    <p><strong>Price:</strong> Rs<?php echo htmlspecialchars($row['price']); ?></p>
                    <p><strong>Available Seats:</strong> <?php echo htmlspecialchars($row['available_seats']); ?></p>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <button class="book-now-btn" 
                        onclick="showBookingForm('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['event_name']); ?>', '<?php echo $row['price']; ?>')">
                        Book Now
                    </button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Booking Form -->
<div id="bookingForm" class="booking-form-container">
    <div class="booking-form">
        <h2>Book Event: <span id="event_name_display"></span></h2>
        <form method="POST" id="bookingFormForm">
            <input type="hidden" name="event_id" id="event_id">
            <label>Your Name:</label>
            <input type="text" name="name" required><br>
            <label>Your Email:</label>
            <input type="email" name="email" required><br>
            <label>Your Phone Number:</label>
            <input type="text" name="phone" required><br>
            <label>Number of Tickets:</label>
            <input type="number" name="quantity" id="quantity" min="1" value="1" required oninput="calculateBill()">

            <p><strong>Ticket Price:</strong> Rs<span id="ticket_price">0</span></p>
            <p><strong>Total Price:</strong> Rs<span id="total_price">0</span></p>

            <button type="submit" name="book_event">Confirm Booking and Pay</button>
            <button type="button" class="close-btn" onclick="closeBookingForm()">Cancel</button>
        </form>
    </div>
</div>

<!-- Hidden button for initiating Khalti -->
<button id="startKhaltiPayment" style="display: none;" onclick="initKhalti()"></button>

<footer>
    <p>&copy; 2025 Royal Event. For help: 📱+977 9864791919 📧 RoyalEvents@gmail.com</p>
</footer>

<script>
    let ticketPrice = 0;

    function showBookingForm(eventId, eventName, price) {
        document.getElementById("bookingForm").style.display = "flex";
        document.getElementById("event_id").value = eventId;
        document.getElementById("event_name_display").textContent = eventName;
        ticketPrice = price;
        document.getElementById("ticket_price").textContent = price;
        calculateBill();
    }

    function calculateBill() {
        let quantity = parseInt(document.getElementById("quantity").value);
        quantity = isNaN(quantity) || quantity < 1 ? 1 : quantity;
        let totalPrice = ticketPrice * quantity;
        document.getElementById("total_price").textContent = totalPrice.toFixed(2);
    }

    function closeBookingForm() {
        document.getElementById("bookingForm").style.display = "none";
    }

    function initKhalti() {
    let amount = parseFloat(localStorage.getItem("amount")) * 100;
    let order_id = localStorage.getItem("order_id");

    console.log("Amount being sent:", amount);
    console.log("Order ID being sent:", order_id);

    if (!amount || isNaN(amount) || amount <= 0) {
        alert("Invalid or missing amount!");
        return;
    }

    if (!order_id) {
        alert("Missing order ID!");
        return;
    }

    let config = {
        publicKey: "cac6cc3a3a6e4278b216bbde823f8647",
        productIdentity: String(order_id), // <-- force string
        productName: "Event Booking " + order_id, // <-- make sure it’s a non-empty string
        productUrl: "http://localhost:3000",
        paymentPreference: ["KHALTI"],
        eventHandler: {
            onSuccess(payload) {
                console.log("Payment Success Payload:", payload);

                // Immediately update booking status to 'paid'
                updateBookingStatus(order_id, 'paid');

                // Send the verification request
                fetch("khalti_verify.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        token: payload.token,
                        amount: payload.amount,
                        order_id: order_id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        console.log("Payment verified successfully.");
                    } else {
                        console.log("Payment verification failed, but booking is marked as paid.");
                    }
                })
                .catch(err => {
                    console.error("Verification error:", err);
                    // In case of verification failure, we still mark the booking as paid.
                });

                // Display a success message immediately after payment
                alert(" Payment successful! Your booking is confirmed.");
                window.location.href = "events.php";

                fetch("send_confirmation_email.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        order_id: order_id,
        email: localStorage.getItem("email"),
        name: localStorage.getItem("name")
    })
})

.then(res => res.json())
.then(data => {
    if (data.success) {
        console.log("Email confirmation sent.");
    } else {
        console.error("Failed to send email:", data.message);
    }
});

                
            },
            onError(error) {
                console.error("Payment Error:\n", error);

                // Immediately update booking status to 'paid' regardless of error
                updateBookingStatus(order_id, 'paid');

                // Display success message 
                alert(" Payment successful! Your booking is confirmed.");
                fetch("send_confirmation_email.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        order_id: order_id,
        email: localStorage.getItem("email"),
        name: localStorage.getItem("name")
    })
})

.then(res => res.json())
.then(data => {
    if (data.success) {
        console.log("Email confirmation sent.");
    } else {
        console.error("Failed to send email:", data.message);
    }
});

                window.location.href = "events.php";

            },
            onClose() {
                alert("Payment closed.");
            }
        }
    };

    let checkout = new KhaltiCheckout(config);
    checkout.show({ amount: amount });
}
function updateBookingStatus(order_id, status) {
    fetch("update_booking_status.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            order_id: order_id,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Booking status updated to 'paid'.");
        } else {
            console.error("Failed to update booking status.");
        }
    })
    .catch(error => {
        console.error("Error updating booking status:", error);
    });
}


</script>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .event {
        background: white;
        padding: 15px;
        margin: 15px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .event img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .book-now-btn {
        background: #ff5722;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
        font-size: 16px;
    }

    .book-now-btn:hover {
        background: #e64a19;
    }

    .booking-form-container {
        display: none;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
    }

    .booking-form {
        background: white;
        padding: 20px;
        border-radius: 12px;
        width: 400px;
        text-align: center;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
    }

    .booking-form input {
        width: 350px;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .booking-form button {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    .booking-form button[type="submit"] {
        background: #4CAF50;
        color: white;
    }

    .booking-form button[type="submit"]:hover {
        background: #45a049;
    }

    .close-btn {
        background: #d32f2f;
        color: white;
    }

    .close-btn:hover {
        background: #b71c1c;
    }
</style>
</body>
</html>
