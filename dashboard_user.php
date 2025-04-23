<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemesan') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$query_user = mysqli_query($con, "SELECT * FROM users WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($query_user);

$search = '';
if (isset($_POST['search'])) {
    $search = mysqli_real_escape_string($con, $_POST['search']);
}

// Proses upload bukti pembayaran
if (isset($_POST['upload_bukti'])) {
    $id_pemesanan = $_POST['id_pemesanan'];
    $file = $_FILES['bukti'];

    if ($file['error'] === 0 && $file['size'] <= 2 * 1024 * 1024) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $namaFile = 'bukti_' . time() . '.' . $ext;
        $uploadPath = 'bukti_pembayaran/' . $namaFile;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            mysqli_query($con, "UPDATE pemesanan SET bukti_pembayaran = '$namaFile', status_pembayaran = 'Menunggu Konfirmasi' WHERE id_pemesanan = '$id_pemesanan' AND id_user = '$id_user'");
            $success_msg = "Bukti pembayaran berhasil diunggah!";
        } else {
            $error_msg = "Gagal mengunggah file.";
        }
    } else {
        $error_msg = "File terlalu besar atau tidak valid (maks 2MB).";
    }
}

// Ambil data pembayaran untuk ditampilkan
$query_pembayaran = mysqli_query($con, "
    SELECT p.*, r.asal, r.tujuan, r.tanggal_keberangkatan, r.waktu_keberangkatan 
    FROM pemesanan p 
    JOIN rute_transportasi r ON p.id_rute = r.id_rute 
    WHERE p.id_user = '$id_user' 
    ORDER BY p.id_pemesanan DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pemesan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
        }
        .navbar { border-bottom: 2px solid #ddd; }
        .navbar-brand { font-weight: bold; }
        .navbar-nav .nav-link { font-weight: 500; }
        .card-custom {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 10px 10px 0 0;
        }
        .btn-custom { background-color: #28a745; color: white; }
        .btn-custom:hover { background-color: #218838; }
        .btn-logout { background-color: #dc3545; color: white; }
        .btn-logout:hover { background-color: #c82333; }
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
                <li class="nav-item"><a class="nav-link active" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="dashboard_user.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="tiket_saya.php">Tiket Saya</a></li>
                <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link btn-logout" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <?php if (isset($success_msg)) : ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php elseif (isset($error_msg)) : ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
    <?php endif; ?>

    <div class="mb-4">
        <h3 class="fw-semibold text-primary">Halo, <?= htmlspecialchars($user['username'] ?? '-') ?> 👋</h3>
        <p class="text-muted">Selamat datang di dashboard pemesanan tiket Anda.</p>
    </div>

    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Cari transportasi, asal, tujuan..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <!-- Jadwal Transportasi -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">Jadwal Transportasi Tersedia</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Transportasi</th>
                                    <th>Asal</th>
                                    <th>Tujuan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Harga</th>
                                    <th>Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT r.id_rute, t.jenis_transportasi, r.asal, r.tujuan, r.tanggal_keberangkatan, r.waktu_keberangkatan, r.harga, r.kelas 
                                          FROM rute_transportasi r 
                                          JOIN transportasi t ON r.id_transportasi = t.id_transportasi 
                                          WHERE r.asal LIKE '%$search%' OR r.tujuan LIKE '%$search%' OR t.jenis_transportasi LIKE '%$search%' 
                                          ORDER BY r.tanggal_keberangkatan ASC";
                                $result = mysqli_query($con, $query);
                                $no = 1;
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['jenis_transportasi']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['asal']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['tujuan']) . "</td>";
                                        echo "<td>" . date('d-m-Y', strtotime($row['tanggal_keberangkatan'])) . "</td>";
                                        echo "<td>" . date('H:i', strtotime($row['waktu_keberangkatan'])) . "</td>";
                                        echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                                        echo "<td><a href='pesan_tiket.php?id=" . $row['id_rute'] . "' class='btn btn-custom btn-sm'>Pesan</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9' class='text-center text-danger'>Tidak ada jadwal transportasi tersedia.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Pembayaran -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">Status Pembayaran Tiket Anda</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Rute</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                mysqli_data_seek($query_pembayaran, 0);
                                while ($data = mysqli_fetch_assoc($query_pembayaran)) {
                                    echo "<tr>";
                                    echo "<td>{$no}</td>";
                                    echo "<td>{$data['asal']} → {$data['tujuan']}</td>";
                                    echo "<td>" . date('d-m-Y', strtotime($data['tanggal_keberangkatan'])) . "</td>";
                                    echo "<td>" . date('H:i', strtotime($data['waktu_keberangkatan'])) . "</td>";
                                    echo "<td>{$data['status_pembayaran']}</td>";
                                    echo "<td>";
                                    if ($data['bukti_pembayaran']) {
                                        echo "<a href='bukti_pembayaran/{$data['bukti_pembayaran']}' class='btn btn-outline-success btn-sm' target='_blank'>Lihat Bukti</a> ";
                                    } else {
                                        echo "<form method='POST' enctype='multipart/form-data' class='d-inline-block'>";
                                        echo "<input type='hidden' name='id_pemesanan' value='{$data['id_pemesanan']}'>";
                                        echo "<input type='file' name='bukti' required class='form-control form-control-sm mb-2'>";
                                        echo "<button type='submit' name='upload_bukti' class='btn btn-sm btn-primary'>Upload</button>";
                                        echo "</form>";
                                    }
                                                                        // Menampilkan konten tiket tersembunyi untuk dicetak
                                    if (strtolower($data['status_pembayaran']) === 'terkonfirmasi') {
                                        echo "
                                        <div id='tiket_{$data['id_pemesanan']}' class='d-none'>
                                            <div style='padding:20px; font-family:Arial;'>
                                                <h3>Tiket Kereta Indonesia</h3>
                                                <hr>
                                                <p><strong>Nama Pemesan:</strong> " . htmlspecialchars($user['username']) . "</p>
                                                <p><strong>Rute:</strong> {$data['asal']} → {$data['tujuan']}</p>
                                                <p><strong>Tanggal:</strong> " . date('d-m-Y', strtotime($data['tanggal_keberangkatan'])) . "</p>
                                                <p><strong>Waktu:</strong> " . date('H:i', strtotime($data['waktu_keberangkatan'])) . "</p>
                                                <p><strong>Status:</strong> {$data['status_pembayaran']}</p>
                                                <hr>
                                                <p>Terima kasih telah memesan tiket bersama kami.</p>
                                            </div>
                                        </div>
                                        ";
                                    }
                                    $no++;
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

<!-- JS dan Script Cetak -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function printTiket(id) {
        var content = document.getElementById(id).innerHTML;
        var printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Cetak Tiket</title></head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }
</script>

</body>
</html>
