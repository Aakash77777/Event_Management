<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // Get selected role (User or Vendor)

    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        header("Location: login.php"); // Redirect to login after successful signup
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-image: url(../../concert1.jpg);
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }
        .container {
            width: 350px;
            height: auto;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            flex-direction: column;
        }
        h1 {
            text-align: center;
            color: white;
        }
        .input {
            width: 270px;
            height: 35px;
            margin-bottom: 15px;
            border: 0px;
            border-radius: 5px;
            padding-left: 10px;
        }
        .btn {
            width: 270px;
            height: 35px;
            border-radius: 10px;
            cursor: pointer;
            border: 0;
            background-color: rgb(133, 48, 183);
            color: white;
            font-size: 20px;
        }
        a {
            position: relative;
            left: 30px;
            text-decoration: none;
            color: white;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form action="" method="post">
            <h1>Sign Up</h1><br>
            <input class="input" type="text" name="username" id="username" required maxlength="30" placeholder="   Username"><br>
            <input class="input" type="email" name="email" id="email" required placeholder="   Email"><br>
            <input class="input" type="password" name="password" id="password" required placeholder="   Password" minlength="8" maxlength="20"><br>

            <select class="input" name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="User">User</option>
                <option value="Vendor">Vendor</option>
            </select><br>

            <input class="btn" type="submit" value="Sign Up"><br><br>   
            <a href="login.php">Already have an account? Login</a>
        </form>
    </div>
</body>
</html>
