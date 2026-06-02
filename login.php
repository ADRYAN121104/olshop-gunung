<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = mysqli_query($koneksi,
    "SELECT * FROM users
    WHERE username='$username'
    AND password='$password'");

    if(mysqli_num_rows($query) == 1){

        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;

        header("Location: index.php");
        exit;

    }else{
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>

body{
    background:#f5f5f5;
    font-family:Arial;
}

.login-box{
    width:350px;
    background:white;
    padding:30px;
    margin:100px auto;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.2);
}

input{
    width:100%;
    padding:12px;
    margin-top:10px;
    box-sizing:border-box;
}

button{
    width:100%;
    padding:12px;
    margin-top:15px;
    background:#1b4332;
    color:white;
    border:none;
    cursor:pointer;
}

.error{
    color:red;
    margin-bottom:10px;
}

</style>

</head>
<body>

<div class="login-box">

<h2>Login Admin</h2>

<?php
if(isset($error)){
    echo "<p class='error'>$error</p>";
}
?>

<form method="POST">

<input
type="text"
name="username"
placeholder="Username"
required>

<input
type="password"
name="password"
placeholder="Password"
required>

<button
type="submit"
name="login">
Login
</button>

</form>

</div>

</body>
</html>