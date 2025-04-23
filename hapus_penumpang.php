<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_penumpang = $_GET['id'];

    // Cek apakah penumpang terkait dengan pemesanan yang valid
    $check_sql = "SELECT id_pemesanan FROM penumpang WHERE id_penumpang = '$id_penumpang'";
    $check_result = mysqli_query($con, $check_sql);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row) {
        // Menghapus data penumpang
        $sql = "DELETE FROM penumpang WHERE id_penumpang = '$id_penumpang'";
        if (mysqli_query($con, $sql)) {
            echo "<script>alert('Penumpang berhasil dihapus!'); window.location.href = 'kelola_pemesanan.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat menghapus penumpang!'); window.location.href = 'kelola_pemesanan.php';</script>";
        }
    } else {
        echo "<script>alert('Penumpang tidak ditemukan!'); window.location.href = 'kelola_pemesanan.php';</script>";
    }
}
?>
