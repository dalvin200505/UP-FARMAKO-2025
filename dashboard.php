<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            height: 100vh;
            background: #1f2d3d;
            padding-top: 20px;
            position: fixed;
            width: 240px;
        }
        .sidebar .nav-link {
            color: #ced4da;
            margin-bottom: 10px;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.2s ease-in-out;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
        }
        .main-content {
            margin-left: 240px;
            padding: 2rem;
        }
        .navbar {
            margin-left: 240px;
            background-color: #0d6efd;
        }
        .navbar .navbar-brand, .navbar .text-white {
            color: #fff !important;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            .main-content, .navbar {
                margin-left: 0;
            }
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column text-white position-fixed">
    <h4 class="text-center text-light mb-4">🚆 Tiket Admin</h4>
    <a href="admin/dashboard.php" class="nav-link active"><i class="bi bi-house-door"></i> Dashboard</a>
    <a href="kelola_transportasi.php" class="nav-link"><i class="bi bi-bus-front"></i> Transportasi</a>
    <a href="kelola_rute.php" class="nav-link"><i class="bi bi-geo-alt"></i> Rute</a>
    <a href="kelola_pemesanan.php" class="nav-link"><i class="bi bi-card-checklist"></i> Pemesanan</a>
    <a href="kelola_pengguna.php" class="nav-link"><i class="bi bi-people"></i> Pengguna</a>
    <a href="konfirmasi_pembayaran.php" class="nav-link"><i class="bi bi-credit-card-2-front"></i> Konfirmasi</a>
    <a href="riwayat_pembayaran.php" class="nav-link"><i class="bi bi-clock-history"></i> Riwayat</a>
    <a href="jumlah_pendapatan.php" class="nav-link"><i class="bi bi-bar-chart-line"></i> Pendapatan</a>
    <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>

</div>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand">Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?></span>
        <div class="ms-auto text-white">
            Role: <strong><?= $_SESSION['role']; ?></strong>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="bg-white p-4 rounded shadow-sm mb-4">
            <h2 class="mb-1">👋 Halo, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Anda login sebagai <strong><?= $_SESSION['role']; ?></strong>. Silakan gunakan menu di samping untuk mengelola aplikasi.</p>
        </div>

        <!-- Dashboard Cards -->
        <div class="row g-4">
            <?php
            $features = [
                ["🚌", "Data Transportasi", "Tambah, ubah, atau hapus transportasi", "kelola_transportasi.php"],
                ["🗺️", "Data Rute", "Atur rute dan tujuan transportasi", "kelola_rute.php"],
                ["📋", "Data Pemesanan", "Lihat dan kelola data pemesanan", "kelola_pemesanan.php"],
                ["👥", "Data Pengguna", "Kelola admin, petugas, dan penumpang", "kelola_pengguna.php"],
                ["💳", "Konfirmasi Pembayaran", "Verifikasi bukti pembayaran", "konfirmasi_pembayaran.php"],
                ["📂", "Riwayat Pembayaran", "Lihat seluruh riwayat pembayaran", "riwayat_pembayaran.php"],
                ["📊", "Laporan Pendapatan", "Lihat total pendapatan aplikasi", "jumlah_pendapatan.php"]
            ];
            foreach ($features as [$icon, $title, $desc, $link]) {
                echo '
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">'.$icon.' '.$title.'</h5>
                            <p class="card-text">'.$desc.'</p>
                            <a href="'.$link.'" class="btn btn-outline-primary btn-sm">Buka</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
