<?php
include 'koneksi.php';
session_start();

// Cek apakah pemesan sudah login dan memiliki ID pemesanan yang valid
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemesan' || !isset($_GET['id_pemesanan'])) {
    header("Location: login.php");
    exit();
}

$id_pemesanan = $_GET['id_pemesanan'];
$username = $_SESSION['username'];

// Ambil data pemesanan
$query = "SELECT pm.id_pemesanan, pm.id_rute, r.asal, r.tujuan, r.harga, pm.tanggal_pemesanan 
          FROM pemesanan pm
          JOIN rute_transportasi r ON pm.id_rute = r.id_rute
          WHERE pm.id_pemesanan = '$id_pemesanan' AND pm.username = '$username'";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) == 0) {
    die("Pemesanan tidak ditemukan atau akses tidak valid.");
}

$pemesanan = mysqli_fetch_assoc($result);

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simulasikan proses pembayaran
    $status_pembayaran = 'Lunas'; // Misalnya, pembayaran berhasil
    $update_query = "UPDATE pemesanan SET status_pembayaran = '$status_pembayaran' 
                     WHERE id_pemesanan = '$id_pemesanan'";
    $update_result = mysqli_query($con, $update_query);

    if ($update_result) {
        $message = "Pembayaran berhasil! Tiket Anda sudah dikonfirmasi.";
        // Redirect ke halaman konfirmasi atau bukti pembayaran
        header("Location: bukti_pembayaran.php?id_pemesanan=$id_pemesanan");
        exit();
    } else {
        $message = "Terjadi kesalahan saat memproses pembayaran.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Tiket Kereta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Roboto', sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand {
            font-weight: bold;
        }
        footer {
            background-color: #f8f9fa;
            padding: 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Pemesanan Tiket</a>
            <div class="d-flex">
                <a href="dashboard_user.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-light text-primary">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-12">
                <div class="card p-4 shadow-lg">
                    <h3 class="text-center mb-4 fw-bold text-primary">Pembayaran Tiket Kereta</h3>

                    <!-- Message -->
                    <?php if (isset($message)) { ?>
                        <div class="alert <?= strpos($message, 'berhasil') !== false ? 'alert-success' : 'alert-danger' ?> text-center" role="alert">
                            <?= $message; ?>
                        </div>
                    <?php } ?>

                    <!-- Detail Pemesanan -->
                    <h5>Detail Pemesanan</h5>
                    <p><strong>Rute:</strong> <?= htmlspecialchars($pemesanan['asal']) . " - " . htmlspecialchars($pemesanan['tujuan']); ?></p>
                    <p><strong>Tanggal Pemesanan:</strong> <?= date('d-m-Y', strtotime($pemesanan['tanggal_pemesanan'])); ?></p>
                    <p><strong>Harga:</strong> Rp <?= number_format($pemesanan['harga'], 0, ',', '.'); ?></p>

                    <!-- Form Pembayaran -->
                    <form method="POST">
                        <h5 class="mt-4">Metode Pembayaran</h5>
                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label">Pilih Metode Pembayaran</label>
                            <select id="metode_pembayaran" name="metode_pembayaran" class="form-select" required>
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="Kartu Kredit">Kartu Kredit</option>
                                <option value="E-wallet">E-wallet</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Bayar Sekarang</button>
                    </form>

                    <!-- After Successful Payment -->
                    <?php if (isset($message) && strpos($message, 'berhasil') !== false) { ?>
                        <div class="alert alert-success mt-4 text-center" role="alert">
                            Pembayaran Anda berhasil! Anda bisa melihat <a href="bukti_pembayaran.php?id_pemesanan=<?= $id_pemesanan; ?>" class="alert-link">bukti pembayaran di sini.</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center">
        <p class="mb-0">&copy; <?= date("Y"); ?> Sistem Pemesanan Transportasi. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
