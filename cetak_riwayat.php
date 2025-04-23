<?php
// Koneksi ke database
include 'koneksi.php';

if (isset($_GET['id_pemesanan'])) {
    $id_pemesanan = $_GET['id_pemesanan'];

    // Ambil data pemesanan berdasarkan ID
    $query = "SELECT p.*, u.username, r.asal, r.tujuan, r.harga 
              FROM pemesanan p
              JOIN users u ON p.id_user = u.id_user
              JOIN rute_transportasi r ON p.id_rute = r.id_rute
              WHERE p.id_pemesanan = '$id_pemesanan'";
    $result = mysqli_query($con, $query);
    $data = mysqli_fetch_assoc($result);
} else {
    echo "ID Pemesanan tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tiket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .ticket {
            padding: 20px;
            border: 1px solid #000;
            width: 60%;
            margin: 0 auto;
            background-color: #f9f9f9;
        }
        .ticket h3 {
            text-align: center;
        }
        .ticket-details {
            margin-top: 20px;
        }
        .ticket-details p {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h3>Tiket Pemesanan</h3>
        <div class="ticket-details">
            <p><strong>Nama Pemesan:</strong> <?php echo htmlspecialchars($data['username']); ?></p>
            <p><strong>Rute:</strong> <?php echo htmlspecialchars($data['asal'] . " - " . $data['tujuan']); ?></p>
            <p><strong>Status Pembayaran:</strong> <?php echo htmlspecialchars($data['status_pembayaran']); ?></p>
            <p><strong>Tanggal Pemesanan:</strong> <?php echo $data['tanggal_pemesanan']; ?></p>
            <p><strong>Harga Tiket:</strong> <?php echo isset($data['harga']) && $data['harga'] !== NULL ? "Rp " . number_format($data['harga']) : "Harga tidak tersedia"; ?></p>
        </div>
    </div>

    <script>
        window.print();  // Memanggil fungsi print untuk mencetak halaman
    </script>
</body>
</html>
