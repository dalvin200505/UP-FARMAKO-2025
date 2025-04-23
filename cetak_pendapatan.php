<?php
require 'koneksi.php';

// Hitung total pendapatan
$queryPendapatan = "SELECT SUM(r.harga) AS total_pendapatan
                    FROM pemesanan p 
                    JOIN rute_transportasi r ON p.id_rute = r.id_rute
                    WHERE p.status_pembayaran = 'Terkonfirmasi'";
$result = mysqli_query($con, $queryPendapatan);
$totalPendapatan = 0;
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalPendapatan = $row['total_pendapatan'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Pendapatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            text-align: center;
        }
        h2 {
            margin-bottom: 10px;
        }
        .pendapatan {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
        }
        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>
<body>

<h2>Laporan Total Pendapatan</h2>
<p class="pendapatan">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>

<p>Tanggal Cetak: <?= date('d-m-Y H:i') ?></p>

<button onclick="window.print()">🖨️ Cetak</button>


</body>
</html>
