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
        <th>Role</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $users->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $row['id']; ?>" title="Edit">
                    ✏️
                </a>
                <a href="delete_user.php?id=<?php echo $row['id']; ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this user?');">
                    🗑️
                </a>
            </td>
        </tr>
    <?php } ?>
</table>

<style>  
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

th {
    background: rgb(36, 77, 121);
    color: white;
    padding: 15px;
    text-align: left;
    font-size: 16px;
}

td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #e3f2fd;
}

h2 {
    text-align: center;
    color: rgb(36, 77, 121);
    font-size: 24px;
    margin-bottom: 20px;
}

a {
    margin-right: 10px;
    text-decoration: none;
    font-size: 18px;
}

@media (max-width: 768px) {
    table {
        font-size: 14px;
    }

    th, td {
        padding: 10px;
    }
}
</style>
