<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemesan' || !isset($_GET['id_pemesanan'])) {
    header("Location: login.php");
    exit();
}

$id_pemesanan = $_GET['id_pemesanan'];
$username = $_SESSION['username'];

$query = "SELECT pm.id_pemesanan, pm.id_rute, r.asal, r.tujuan, r.harga, pm.tanggal_pemesanan, pm.status_pembayaran, pm.username 
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
    <title>Cetak Bukti Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .ticket-container {
            max-width: 700px;
            margin: 40px auto;
        }

        .ticket-card {
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            background-color: white;
            overflow: hidden;
        }

        .ticket-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 1.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .ticket-body {
            padding: 2rem;
        }

        .ticket-body p {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .ticket-body .label {
            font-weight: 600;
            width: 180px;
            display: inline-block;
        }

        .ticket-footer {
            background-color: #f1f3f5;
            padding: 1.2rem;
            text-align: center;
        }

        .btn {
            border-radius: 30px;
        }

        .badge-success {
            background-color: #28a745;
            font-size: 1rem;
            padding: 0.4rem 0.7rem;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .ticket-container {
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="ticket-container">
    <div class="ticket-card">
        <div class="ticket-header">
            Bukti Pembayaran Tiket Kereta
        </div>
        <div class="ticket-body">
            <p><span class="label">ID Pemesanan:</span> <?= $pemesanan['id_pemesanan']; ?></p>
            <p><span class="label">Username:</span> <?= htmlspecialchars($pemesanan['username']); ?></p>
            <p><span class="label">Rute:</span> <?= htmlspecialchars($pemesanan['asal']) . " → " . htmlspecialchars($pemesanan['tujuan']); ?></p>
            <p><span class="label">Tanggal Pemesanan:</span> <?= date('d-m-Y', strtotime($pemesanan['tanggal_pemesanan'])); ?></p>
            <p><span class="label">Harga:</span> Rp <?= number_format($pemesanan['harga'], 0, ',', '.'); ?></p>
            <p><span class="label">Status Pembayaran:</span> <span class="badge badge-success"><?= htmlspecialchars($pemesanan['status_pembayaran']); ?></span></p>
        </div>
        <div class="ticket-footer no-print">
            <button class="btn btn-primary me-2" onclick="window.print()">Cetak Bukti</button>
            <a href="dashboard_user.php" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
