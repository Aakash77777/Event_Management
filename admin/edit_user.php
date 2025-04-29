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
$user = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);

    $conn->query("UPDATE users SET username='$username', email='$email', role='$role' WHERE id=$id");

    header("Location: users.php");
    exit();
}
?>

<h2>Edit User</h2>
<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

    <label>Role:</label><br>
    <select name="role" required>
        <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
        <option value="vendor" <?php if ($user['role'] == 'vendor') echo 'selected'; ?>>Vendor</option>
        <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
    </select><br><br>

    <button type="submit">Update</button>
    <a href="users.php">Cancel</a>
</form>

<style>
    form {
        background: #fff;
        padding: 20px;
        width: 300px;
        margin: 30px auto;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    button {
        background: rgb(36, 77, 121);
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    a {
        margin-left: 10px;
        text-decoration: none;
        color: #555;
    }

    h2 {
        text-align: center;
        color: rgb(36, 77, 121);
    }
</style>
