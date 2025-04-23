<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemesan') {
    header("Location: login.php");
    exit();
}

// Proses pemesanan tiket
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_rute = $_POST['id_rute']; // ID rute yang dipilih
    $id_user = $_SESSION['id_user']; // ID user yang login
    
    // Query untuk memasukkan data pemesanan
    $query = "INSERT INTO pemesanan (id_user, id_rute, status) VALUES ('$id_user', '$id_rute', 'pending')";
    $result = mysqli_query($con, $query);
    
    if ($result) {
        // Jika pemesanan berhasil, arahkan ke halaman pembayaran
        $id_pemesanan = mysqli_insert_id($con); // Mendapatkan ID pemesanan yang baru dimasukkan
        header("Location: pembayaran.php?id_pemesanan=$id_pemesanan"); // Mengarahkan ke halaman pembayaran dengan ID pemesanan
        exit();
    } else {
        // Menangani kesalahan jika pemesanan gagal
        echo "Pemesanan gagal! Silakan coba lagi.";
    }
}

// Ambil data pemesanan user
$id_user = $_SESSION['id_user'];
$pemesanan_query = mysqli_query($con, "SELECT * FROM pemesanan 
    JOIN rute_transportasi ON pemesanan.id_rute = rute_transportasi.id_rute 
    WHERE pemesanan.id_user = '$id_user' 
    ORDER BY id_pemesanan DESC");

$pemesanan = [];
while ($row = mysqli_fetch_assoc($pemesanan_query)) {
    $pemesanan[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Saya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card-custom {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .table-custom th, .table-custom td {
            vertical-align: middle;
        }
    </style>
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
                <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container my-5">
    <div class="mb-4">
        <h3 class="fw-semibold">Tiket Saya</h3>
        <p class="text-muted">Berikut adalah daftar tiket yang telah Anda pesan.</p>
    </div>

    <!-- Daftar Pemesanan -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    Riwayat Pemesanan Tiket
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 table-custom">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Rute</th>
                                    <th>Waktu Keberangkatan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($pemesanan) > 0): ?>
                                    <?php foreach ($pemesanan as $i => $row): ?>
                                        <tr>
                                            <td><?= $i + 1; ?></td>
                                            <td><?= htmlspecialchars($row['asal'] ?? '-') . " - " . htmlspecialchars($row['tujuan'] ?? '-') ?></td>
                                            <td>
                                                <?php 
                                                    $waktu_keberangkatan = $row['waktu_keberangkatan'] ?? null;
                                                    echo $waktu_keberangkatan ? date('d M Y, H:i', strtotime($waktu_keberangkatan)) : '-';
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    ($row['status'] ?? '') == 'berhasil' ? 'success' :
                                                    (($row['status'] ?? '') == 'pending' ? 'warning text-dark' : 'danger')
                                                ?>">
                                                    <?= ucfirst($row['status'] ?? 'tidak diketahui'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="detail_tiket.php?id_pemesanan=<?= $row['id_pemesanan'] ?>" class="btn btn-info btn-sm">Detail</a>
                                                <?php if ($row['status'] == 'pending'): ?>
                                                    <a href="batal_pemesanan.php?id_pemesanan=<?= $row['id_pemesanan'] ?>" class="btn btn-danger btn-sm">Batal</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-3">Belum ada pemesanan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
