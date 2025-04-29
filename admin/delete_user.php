<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "No user ID provided.";
    exit();
}

$id = intval($_GET['id']);

$conn->query("DELETE FROM users WHERE id = $id");

header("Location: users.php");
exit();
?>
