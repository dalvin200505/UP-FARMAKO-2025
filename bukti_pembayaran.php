<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemesan' || !isset($_GET['id_pemesanan'])) {
    header("Location: login.php");
    exit();
}

$id_pemesanan = $_GET['id_pemesanan'];
$username = $_SESSION['username'];

$query = "SELECT pm.id_pemesanan, pm.id_rute, r.asal, r.tujuan, r.harga, r.kelas, r.tanggal_keberangkatan, r.waktu_keberangkatan,
                 pm.tanggal_pemesanan, pm.status_pembayaran, pm.username 
          FROM pemesanan pm
          JOIN rute_transportasi r ON pm.id_rute = r.id_rute
          WHERE pm.id_pemesanan = '$id_pemesanan' AND pm.username = '$username'";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) == 0) {
    die("Bukti pembayaran tidak ditemukan atau akses tidak valid.");
}

$pemesanan = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .card, .ticket {
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background-color: white;
        }
        .card-header, .ticket-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            text-align: center;
            padding: 1.5rem;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            font-size: 1.6rem;
            font-weight: bold;
        }
        .card-body, .ticket-body {
            padding: 2rem;
            font-size: 1.1rem;
        }
        .ticket-body p {
            margin: 0.4rem 0;
        }
        .barcode, .qrcode {
            text-align: center;
            margin-top: 1.5rem;
        }
        .btn {
            border-radius: 30px;
            padding: 0.6rem 1.5rem;
        }
        .ticket {
            display: none;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .ticket {
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 2rem;
            }
            .ticket * {
                visibility: visible;
            }
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9 col-sm-11">
            <div class="card">
                <div class="card-header">
                    Bukti Pembayaran Tiket
                </div>
                <div class="card-body">
                    <p><strong>ID Pemesanan:</strong> <?= htmlspecialchars($pemesanan['id_pemesanan'] ?? 'Tidak ada ID Pemesanan'); ?></p>
                    <p><strong>Username:</strong> <?= htmlspecialchars($pemesanan['username'] ?? 'Tidak ada username'); ?></p>
                    <p><strong>Rute:</strong> <?= htmlspecialchars($pemesanan['asal'] ?? 'Tidak ada asal') . " - " . htmlspecialchars($pemesanan['tujuan'] ?? 'Tidak ada tujuan'); ?></p>
                    <p><strong>Tanggal Keberangkatan:</strong> <?= date('d-m-Y', strtotime($pemesanan['tanggal_keberangkatan'] ?? '')); ?></p>
                    <p><strong>Waktu Keberangkatan:</strong> <?= htmlspecialchars($pemesanan['waktu_keberangkatan'] ?? 'Tidak ada waktu keberangkatan'); ?></p>
                    <p><strong>Kelas:</strong> <?= strtoupper(htmlspecialchars($pemesanan['kelas'] ?? 'Tidak ada kelas')); ?></p>
                    <p><strong>Harga:</strong> Rp <?= number_format($pemesanan['harga'] ?? 0, 0, ',', '.'); ?></p>
                    <p><strong>Status Pembayaran:</strong> 
                        <span class="badge bg-success"><?= htmlspecialchars($pemesanan['status_pembayaran'] ?? 'Tidak ada status'); ?></span>
                    </p>
                </div>
                <div class="card-footer text-center">
                    <a href="dashboard_user.php" class="btn btn-primary me-2">Kembali ke Dashboard</a>
                    <button onclick="window.print()" class="btn btn-outline-secondary">Cetak Tiket</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tiket versi cetak -->
<div class="ticket container mt-5">
    <div class="ticket-header">
        Kartu Tiket Kereta
    </div>
    <div class="ticket-body">
        <p><strong>ID Pemesanan:</strong> <?= htmlspecialchars($pemesanan['id_pemesanan'] ?? 'Tidak ada ID Pemesanan'); ?></p>
        <p><strong>Nama Pemesan:</strong> <?= htmlspecialchars($pemesanan['username'] ?? 'Tidak ada username'); ?></p>
        <p><strong>Rute:</strong> <?= htmlspecialchars($pemesanan['asal'] ?? 'Tidak ada asal') . " - " . htmlspecialchars($pemesanan['tujuan'] ?? 'Tidak ada tujuan'); ?></p>
        <p><strong>Tanggal Keberangkatan:</strong> <?= date('d-m-Y', strtotime($pemesanan['tanggal_keberangkatan'] ?? '')); ?></p>
        <p><strong>Waktu Keberangkatan:</strong> <?= htmlspecialchars($pemesanan['waktu_keberangkatan'] ?? 'Tidak ada waktu keberangkatan'); ?></p>
        <p><strong>Kelas:</strong> <?= strtoupper(htmlspecialchars($pemesanan['kelas'] ?? 'Tidak ada kelas')); ?></p>
        <p><strong>Harga Tiket:</strong> Rp <?= number_format($pemesanan['harga'] ?? 0, 0, ',', '.'); ?></p>
        <div class="barcode">
            <svg id="barcode"></svg>
        </div>
        <div class="qrcode mt-3" id="qrcode"></div>
    </div>
</div>

<script>
    // Barcode
    JsBarcode("#barcode", "<?= htmlspecialchars($pemesanan['id_pemesanan'] ?? ''); ?>", {
        format: "CODE128",
        lineColor: "#000",
        width: 2,
        height: 60,
        displayValue: true
    });

    // QR Code
    new QRCode(document.getElementById("qrcode"), {
        text: "<?= htmlspecialchars($pemesanan['id_pemesanan'] ?? ''); ?>",
        width: 100,
        height: 100
    });
</script>

</body>
</html>
