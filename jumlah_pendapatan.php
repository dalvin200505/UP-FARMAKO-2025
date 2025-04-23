<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit();
}

// Query untuk menghitung total pendapatan
$queryPendapatan = "SELECT SUM(r.harga) AS total_pendapatan
                    FROM pemesanan p 
                    JOIN rute_transportasi r ON p.id_rute = r.id_rute
                    WHERE p.status_pembayaran = 'Terkonfirmasi'";

$result = mysqli_query($con, $queryPendapatan);
$totalPendapatan = 0;
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalPendapatan = $row['total_pendapatan'] ?? 0;
} else {
    $totalPendapatan = 0;
}

// Fitur Filter
$filter = "";
if (isset($_POST['filter'])) {
    $filter = mysqli_real_escape_string($con, $_POST['filter']);
}

// Query untuk menampilkan riwayat pembayaran dengan filter
$queryRiwayat = "SELECT rp.*, p.id_pemesanan, u.username, r.asal, r.tujuan 
                 FROM riwayat_pembayaran rp
                 JOIN pemesanan p ON rp.id_pemesanan = p.id_pemesanan
                 JOIN users u ON p.id_user = u.id_user
                 JOIN rute_transportasi r ON p.id_rute = r.id_rute
                 WHERE u.username LIKE '%$filter%' OR r.asal LIKE '%$filter%' OR r.tujuan LIKE '%$filter%'
                 ORDER BY rp.tanggal_update DESC";
$resultRiwayat = mysqli_query($con, $queryRiwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jumlah Pendapatan - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            border-bottom: 2px solid #ddd;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .navbar-nav .nav-link {
            font-weight: 500;
        }
        .card-custom {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 10px 10px 0 0;
        }
        .card-body {
            padding: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="#">Tiket Kereta Indonesia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="konfirmasi_pembayaran.php">Konfirmasi Pembayaran</a></li>
                <li class="nav-item"><a class="nav-link" href="kelola_pemesanan.php">Kelola Pemesanan</a></li>
                <li class="nav-item"><a class="nav-link btn-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container my-5">
    <div class="mb-4">
        <h3 class="fw-semibold text-primary">Jumlah Pendapatan</h3>
        <p class="text-muted">Total pendapatan dari pembayaran yang telah dikonfirmasi.</p>
    </div>

    <!-- Tampilkan total pendapatan -->
    <div class="alert alert-info">
        <h4>Total Pendapatan: Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h4>
    </div>
    <!-- Tombol Cetak -->
<a href="cetak_pendapatan.php" class="btn btn-outline-primary mb-4" target="_blank">
    <i class="bi bi-printer"></i> Cetak Pendapatan
</a>


    <!-- Filter Form -->
    <form method="POST" class="mb-4 d-flex">
        <input type="text" name="filter" class="form-control" placeholder="Cari berdasarkan nama pemesan atau rute..." value="<?= htmlspecialchars($filter) ?>">
        <button type="submit" class="btn btn-primary ms-2">Filter</button>
    </form>

    <!-- Riwayat Pembayaran -->
    <div class="mb-4">
        <h3 class="fw-semibold text-primary">Riwayat Pembayaran</h3>
    </div>
    <div class="row g-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    Riwayat Pembayaran
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pemesan</th>
                                    <th>Rute</th>
                                    <th>Status Pembayaran</th>
                                    <th>Tanggal Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($resultRiwayat) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($resultRiwayat)) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $row['asal'] . " - " . $row['tujuan'] . "</td>";
                                        echo "<td>" . $row['status_pembayaran'] . "</td>";
                                        echo "<td>" . $row['tanggal_update'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>Tidak ada riwayat pembayaran.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
