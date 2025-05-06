<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["message"])) {
    $message = strtolower(trim($_POST["message"]));

    $responses = [
        "hello" => "Hi there! How can I help you today?",
        "hi" => "Hello! Need help with something?",
        "book" => "You can browse events and venues right from this page and book them easily.",
        "bye" => "Goodbye! Let us know if you need anything else.",
        "help" => "I can help you with bookings, events info, and more.",
        "event" => "Click on 'Events' and then 'Book' to use the booking form.",
        "venue" => "click on 'Venues' and then 'book' to use the booking form."
    ];

    $response = "Sorry, I didn't understand that.";

    foreach ($responses as $key => $val) {
        if (strpos($message, $key) !== false) {
            $response = $val;
            break;
        }
    }

    echo $response;
}
?>
