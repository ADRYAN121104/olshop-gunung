<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi,
    "SELECT * FROM users
    WHERE username='$username'
    AND password='$password'");

    if(mysqli_num_rows($query) > 0){

        $_SESSION['login'] = true;

        header("Location: index.php");
        exit;

    }else{
        echo "Login gagal";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <style>

        body{
            font-family: Arial;
            background: #f5f5f5;
        }

        .login-box{
            width: 350px;
            background: white;
            padding: 30px;
            margin: 100px auto;
            border-radius: 10px;
        }

        input{
            width: 100%;
            padding: 12px;
            margin-top: 10px;
        }

        button{
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: green;
            color: white;
            border: none;
        }

    </style>

</head>
<body>

<div class="login-box">

<h2>Login Admin</h2>

<form method="POST">

<input type="text"
name="username"
placeholder="Username"
required>

<input type="password"
name="password"
placeholder="Password"
required>

<button type="submit"
name="login">
Login
</button>

</form>

</div>

</body>
</html>