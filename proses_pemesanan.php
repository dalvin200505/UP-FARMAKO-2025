<?php
include 'koneksi.php';
session_start();

$id_user = $_SESSION['id_user'];
$id_rute = $_POST['id_rute'];
$jumlah_tiket = $_POST['jumlah_tiket'];
$tanggal_pemesanan = date("Y-m-d H:i:s");

// Hitung total harga (misal: harga dari rute dikali jumlah tiket)
$query_rute = mysqli_query($conn, "SELECT harga FROM rute_transportasi WHERE id_rute = '$id_rute'");
$data_rute = mysqli_fetch_assoc($query_rute);
$total_harga = $data_rute['harga'] * $jumlah_tiket;

// Simpan pemesanan dengan status belum dibayar
mysqli_query($conn, "INSERT INTO pemesanan (id_user, id_rute, jumlah_tiket, total_harga, tanggal_pemesanan, status_pemesanan) VALUES ('$id_user', '$id_rute', '$jumlah_tiket', '$total_harga', '$tanggal_pemesanan', 'belum_bayar')");

// Ambil id pemesanan terakhir
$id_pemesanan = mysqli_insert_id($conn);

// Redirect ke halaman pembayaran
header("Location: pembayaran.php?id_pemesanan=$id_pemesanan");
exit();
?>
