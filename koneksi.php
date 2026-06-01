<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "outdoor_store";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
// JANGAN MENULISKAN ECHO APAPUN DI SINI BIAR NAVBARE TETAP RAPI
?>