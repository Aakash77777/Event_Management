<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php"); // Redirect to index.php after login
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "User not found!";
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
.error-message {
    color: red;
    text-align: center;
    margin-bottom: 10px;
}

body{
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background-image: url(../../concert.jpg);
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
}
.container{
    width: 350px;
    height: 370px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 20px;
    background-color: rgba(0, 0, 0, 0.5);
}
h1{
    text-align: center;
    padding: 20px;
    color: white;
}
.label{
    margin-bottom: 20px;
    color: white;
}
.input{
    width: 270px;
    height: 30px;
    margin-top: 10px;
    margin-bottom: 25px;
    border: 0px;
    border-radius: 5px;
}
.button{
    width: 270px;
    height: 35px;
    border-radius: 10px;
    cursor: pointer;
    border: 0;
    background-color: rgb(109, 49, 187);
    color: white;
    font-size: 20px;
    margin-bottom: 15px;
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
                <h1>Login</h1>
                <label class="label" for="email">Email</label><br>
                <input class="input" type="email" name="email" id="email" required maxlength="30"><br>
                <label class="label" for="password">Password</label><br>
                <input class="input" type="password" name="password" id="password" required maxlength="30"><br>
                <input class="button" type="submit" value="Login"><br>
                <a href="signup.php">Don't have an account? Sign Up</a>
            </form>
    </div>
</body>
</html>
