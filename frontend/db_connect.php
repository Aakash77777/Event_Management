<?php
$servername = "localhost"; // Change this if your database is hosted elsewhere
$username = "root"; // Change this if you have a different MySQL user
$password = ""; // Add your MySQL password if you set one
$dbname = "event_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
