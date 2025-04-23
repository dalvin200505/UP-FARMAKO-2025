<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // kosongkan jika tidak ada password
$db   = 'tiket'; // ganti dengan nama database kamu

$con = mysqli_connect($host, $user, $pass, $db);

if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
