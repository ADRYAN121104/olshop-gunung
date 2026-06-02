<?php
// koneksi.php
$host = "localhost";
$user = "root";     // Default XAMPP biasanya root
$pass = "";         // Default XAMPP biasanya kosong
$db   = "OUTDOOR_STORE"; // Harus sama persis dengan di phpMyAdmin

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>