<?php
session_start();
require 'koneksi.php';

// Cek apakah user sudah login dan memiliki role pemesan
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemesan') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id_pemesanan'])) {
    header("Location: tiket_saya.php?message=ID pemesanan tidak ditemukan.");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_pemesanan = $_GET['id_pemesanan'];

// Ambil data detail pemesanan yang sesuai dengan user
$query = "SELECT p.*, r.asal, r.tujuan, r.tanggal_keberangkatan, r.waktu_keberangkatan, r.kelas, r.harga
          FROM pemesanan p
          JOIN rute_transportasi r ON p.id_rute = r.id_rute
          WHERE p.id_pemesanan = '$id_pemesanan' AND p.id_user = '$id_user'";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: tiket_saya.php?message=Data tidak ditemukan atau bukan milik Anda.");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Fungsi format waktu keberangkatan
function formatTanggalIndonesia($tanggal, $waktu) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $timestamp = strtotime("$tanggal $waktu");
    $namaHari = $hari[date('l', $timestamp)];
    $tgl = date('j', $timestamp);
    $namaBulan = $bulan[(int)date('n', $timestamp)];
    $tahun = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);

    return "$namaHari, $tgl $namaBulan $tahun, $jam WIB";
}

$jadwal = formatTanggalIndonesia($data['tanggal_keberangkatan'], $data['waktu_keberangkatan']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Tiket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">TiketKu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="dashboard_user.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="tiket_saya.php">Tiket Saya</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container my-5">
    <div class="card shadow rounded">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Detail Tiket</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">ID Pemesanan</dt>
                <dd class="col-sm-8"><?= $data['id_pemesanan']; ?></dd>

                <dt class="col-sm-4">Rute</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($data['asal']) . " → " . htmlspecialchars($data['tujuan']); ?></dd>

                <dt class="col-sm-4">Waktu Keberangkatan</dt>
                <dd class="col-sm-8"><?= $jadwal; ?></dd>

                <dt class="col-sm-4">Kelas</dt>
                <dd class="col-sm-8"><?= ucfirst($data['kelas']); ?></dd>

                <dt class="col-sm-4">Harga</dt>
                <dd class="col-sm-8">Rp<?= number_format($data['harga'], 0, ',', '.'); ?></dd>

                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">
                    <span class="badge bg-<?= 
                        $data['status'] == 'berhasil' ? 'success' :
                        ($data['status'] == 'pending' ? 'warning text-dark' : 'danger') ?>">
                        <?= ucfirst($data['status']); ?>
                    </span>
                </dd>
            </dl>
            <a href="tiket_saya.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
