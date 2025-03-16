<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php"); // Redirect to login page after signup
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
    <title>Document</title>
    <style>
        *{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body{
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background-image: url(../../concert1.jpg);
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
}
.container{
    width: 350px;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 20px;
    background-color: rgba(0, 0, 0, 0.5);
}
h1{
    text-align: center;
    color: white;
}

.input{
    width: 270px;
    height: 30px;
    margin-bottom: 10px;
    border: 0px;
    border-radius: 5px;
}
.btn{
    width: 270px;
    height: 35px;
    border-radius: 10px;
    cursor: pointer;
    border: 0;
    background-color: rgb(133, 48, 183);
    color: white;
    font-size: 20px;
}
a{
    position: relative;
    left: 30px;
    text-decoration: none;
    color: white;
}
    </style>
</head>
<body>
    <div class="container">
    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form action="" method="post">
            <h1>Sign Up</h1><br><br>
            <input class="input" type="text" name="username" id="username" required maxlength="30" placeholder="   Username"><br><br>
            <input class="input" type="email" name="email" id="email" placeholder="   Email"><br><br>
            <input class="input" type="password" name="password" id="password" placeholder="   Password" minlength="8" maxlength="20"><br><br>
            <input class="btn" type="submit" value="Sign Up"><br><br>   
            <a href="login.php">Already have an account? Login</a>
        </form>
    </div>
</body>
</html>
