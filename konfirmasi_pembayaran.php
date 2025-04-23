<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit();
}
$id_admin = $_SESSION['id_user'];

// Proses konfirmasi pembayaran
if (isset($_POST['konfirmasi'])) {
    $id_pemesanan = $_POST['id_pemesanan'];
    $status = $_POST['konfirmasi'];

    // Ambil data pemesanan dan email
    $getData = mysqli_query($con, "SELECT p.*, u.email, u.username, r.asal, r.tujuan 
                                   FROM pemesanan p 
                                   JOIN users u ON p.id_user = u.id_user 
                                   JOIN rute_transportasi r ON p.id_rute = r.id_rute 
                                   WHERE p.id_pemesanan = '$id_pemesanan'");
    $data = mysqli_fetch_assoc($getData);
    $email = $data['email'];
    $asal = $data['asal'];
    $tujuan = $data['tujuan'];
    $username = $data['username'];
    $old_status = $data['status_pembayaran']; // Menyimpan status pembayaran lama

    // Update status pembayaran dan simpan status lama
    $query = "UPDATE pemesanan 
              SET status_pembayaran = '$status', status_pembayaran_lama = '$old_status' 
              WHERE id_pemesanan = '$id_pemesanan'";

    if (mysqli_query($con, $query)) {
        $success_msg = "Status pembayaran berhasil diperbarui!";

        // Simpan riwayat pembayaran ke dalam tabel riwayat_pembayaran
        $insertHistory = "INSERT INTO riwayat_pembayaran (id_pemesanan, status_pembayaran) 
                          VALUES ('$id_pemesanan', '$status')";
        mysqli_query($con, $insertHistory);

        // Kirim email notifikasi
        $subject = "Notifikasi Pembayaran Tiket";
        if ($status == "Terkonfirmasi") {
            $message = "Halo $username,\n\nPembayaran tiket Anda untuk rute $asal - $tujuan telah dikonfirmasi.\n\nTerima kasih telah menggunakan layanan kami.";
        } else {
            $message = "Halo $username,\n\nMohon maaf, pembayaran Anda untuk rute $asal - $tujuan ditolak.\nSilakan unggah ulang bukti pembayaran atau hubungi kami untuk bantuan lebih lanjut.";
        }

        $headers = "From: noreply@tiketkereta.com\r\n";
        $headers .= "Reply-To: support@tiketkereta.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        mail($email, $subject, $message, $headers);
    } else {
        $error_msg = "Gagal memperbarui status pembayaran.";
    }
}

// Fitur Filter
$filter = "";
if (isset($_POST['filter'])) {
    $filter = mysqli_real_escape_string($con, $_POST['filter']);
}

// Query untuk menampilkan riwayat pembayaran
$queryRiwayat = "SELECT rp.*, p.id_pemesanan, u.username, r.asal, r.tujuan 
                 FROM riwayat_pembayaran rp
                 JOIN pemesanan p ON rp.id_pemesanan = p.id_pemesanan
                 JOIN users u ON p.id_user = u.id_user
                 JOIN rute_transportasi r ON p.id_rute = r.id_rute
                 ORDER BY rp.tanggal_update DESC";
$resultRiwayat = mysqli_query($con, $queryRiwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran - Admin</title>
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
    <?php if (isset($success_msg)) : ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php elseif (isset($error_msg)) : ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
    <?php endif; ?>

    <div class="mb-4">
        <h3 class="fw-semibold text-primary">Konfirmasi Pembayaran</h3>
        <p class="text-muted">Tinjau dan konfirmasi status pembayaran pemesanan tiket.</p>
    </div>

    <!-- Filter Form -->
    <form method="POST" class="mb-4 d-flex">
        <input type="text" name="filter" class="form-control" placeholder="Cari berdasarkan nama pemesan atau rute..." value="<?= htmlspecialchars($filter) ?>">
        <button type="submit" class="btn btn-primary ms-2">Filter</button>
    </form>

    <!-- Daftar Pembayaran Menunggu Konfirmasi -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header">
                    Pembayaran Menunggu Konfirmasi
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
                                    <th>Bukti Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT p.*, u.username, u.email, r.asal, r.tujuan 
                                          FROM pemesanan p 
                                          JOIN users u ON p.id_user = u.id_user 
                                          JOIN rute_transportasi r ON p.id_rute = r.id_rute 
                                          WHERE p.status_pembayaran = 'Menunggu Konfirmasi' 
                                          AND (u.username LIKE '%$filter%' OR r.asal LIKE '%$filter%' OR r.tujuan LIKE '%$filter%')
                                          ORDER BY p.id_pemesanan DESC";
                                $result = mysqli_query($con, $query);
                                $no = 1;
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $row['asal'] . " - " . $row['tujuan'] . "</td>";
                                        echo "<td>" . $row['status_pembayaran'] . "</td>";
                                        echo "<td>";
if (!empty($row['bukti_pembayaran'])) {
    echo "<a href='bukti_pembayaran/{$row['bukti_pembayaran']}' target='_blank' class='btn btn-sm btn-info'>Lihat</a>";
} else {
    echo "Belum Upload";
}
echo "</td>";

                                        echo "<td>
                                                <form method='POST'>
                                                    <input type='hidden' name='id_pemesanan' value='" . $row['id_pemesanan'] . "'>
                                                    <button type='submit' name='konfirmasi' value='Terkonfirmasi' class='btn btn-success'>Konfirmasi</button>
                                                    <button type='submit' name='konfirmasi' value='Ditolak' class='btn btn-danger'>Tolak</button>
                                                </form>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Tidak ada pemesanan menunggu konfirmasi.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="mb-4">
        <h3 class="fw-semibold text-primary">Riwayat Pembayaran</h3>
    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
