<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login dan memiliki peran pemesan
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemesan') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id_pemesanan'])) {
    $id_pemesanan = $_GET['id_pemesanan'];

    // Periksa apakah pemesanan dengan ID ini milik user yang sedang login
    $id_user = $_SESSION['id_user'];
    $query_check = "SELECT * FROM pemesanan WHERE id_pemesanan = '$id_pemesanan' AND id_user = '$id_user'";
    $result_check = mysqli_query($con, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Proses pembatalan pemesanan
        $query_batal = "UPDATE pemesanan SET status = 'batal' WHERE id_pemesanan = '$id_pemesanan'";
        $result_batal = mysqli_query($con, $query_batal);

        if ($result_batal) {
            header("Location: tiket_saya.php?message=Batal pemesanan berhasil.");
        } else {
            header("Location: tiket_saya.php?message=Terjadi kesalahan saat membatalkan pemesanan.");
        }
    } else {
        header("Location: tiket_saya.php?message=Pemesan tidak ditemukan.");
    }
} else {
    header("Location: tiket_saya.php?message=ID pemesanan tidak valid.");
}
?>
