<?php
session_start();
include '../frontend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.php");
    exit();
}


$users = $conn->query("SELECT * FROM users");
?>

<h2>Manage Users</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
    </tr>
    <?php while ($row = $users->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['email']; ?></td>
        </tr>
    <?php } ?>
</table>
