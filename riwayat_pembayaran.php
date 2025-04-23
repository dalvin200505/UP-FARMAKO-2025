<?php
// Koneksi ke database
include 'koneksi.php';

if (isset($_GET['id_pemesanan']) && isset($_GET['status'])) {
    $id_pemesanan = $_GET['id_pemesanan'];
    $status = $_GET['status'];

    // Update status pembayaran di tabel pemesanan
    $updateStatus = "UPDATE pemesanan SET status_pembayaran = '$status' WHERE id_pemesanan = '$id_pemesanan'";
    if (mysqli_query($con, $updateStatus)) {
        // Simpan riwayat pembayaran
        $insertHistory = "INSERT INTO riwayat_pembayaran (id_pemesanan, status_pembayaran) 
                          VALUES ('$id_pemesanan', '$status')";
        mysqli_query($con, $insertHistory);
        echo "Status pembayaran berhasil diperbarui dan riwayat disimpan!";
    } else {
        echo "Error updating status: " . mysqli_error($con);
    }
}

// Proses penghapusan riwayat pembayaran
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Hapus riwayat pembayaran berdasarkan ID
    $deleteHistory = "DELETE FROM riwayat_pembayaran WHERE id_pemesanan = '$delete_id'";
    if (mysqli_query($con, $deleteHistory)) {
        // Set status pembayaran menjadi "pending" atau sesuai kebutuhan
        $updateStatus = "UPDATE pemesanan SET status_pembayaran = 'pending' WHERE id_pemesanan = '$delete_id'";
        mysqli_query($con, $updateStatus);
        echo "Riwayat pembayaran berhasil dihapus dan status pemesanan dikembalikan.";
    } else {
        echo "Error deleting record: " . mysqli_error($con);
    }
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran dan Konfirmasi Pembayaran</title>
    <!-- Menggunakan Bootstrap 4.5.2 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Menambahkan Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .btn-info, .btn-danger, .btn-primary {
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-info:hover, .btn-danger:hover, .btn-primary:hover {
            transform: translateY(-2px);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h4 class="mb-4 text-center text-primary">Riwayat Pembayaran</h4>

    <!-- Form Pencarian -->
    <form method="GET" class="form-inline mb-4 justify-content-end">
        <input type="text" name="search" class="form-control mr-2" placeholder="Cari nama pemesan..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Nama Pemesan</th>
                <th>Rute</th>
                <th>Status Pembayaran</th>
                <th>Tanggal Update</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Tambahkan filter jika ada pencarian
            $searchKeyword = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

            $queryRiwayat = "SELECT rp.*, p.id_pemesanan, u.username, r.asal, r.tujuan 
                             FROM riwayat_pembayaran rp
                             JOIN pemesanan p ON rp.id_pemesanan = p.id_pemesanan
                             JOIN users u ON p.id_user = u.id_user
                             JOIN rute_transportasi r ON p.id_rute = r.id_rute";

            if (!empty($searchKeyword)) {
                $queryRiwayat .= " WHERE u.username LIKE '%$searchKeyword%'";
            }

            $queryRiwayat .= " ORDER BY rp.tanggal_update DESC";

            $resultRiwayat = mysqli_query($con, $queryRiwayat);

            if (mysqli_num_rows($resultRiwayat) > 0) {
                while ($rowRiwayat = mysqli_fetch_assoc($resultRiwayat)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($rowRiwayat['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowRiwayat['asal'] . " - " . $rowRiwayat['tujuan']) . "</td>";
                    echo "<td>" . htmlspecialchars($rowRiwayat['status_pembayaran']) . "</td>";
                    echo "<td>" . $rowRiwayat['tanggal_update'] . "</td>";
                    echo "<td>
                            <button class='btn btn-info' data-toggle='modal' data-target='#detailModal" . $rowRiwayat['id_pemesanan'] . "'><i class='fas fa-info-circle'></i> Detail</button>
                            <button class='btn btn-primary' onclick='cetakTiket(" . $rowRiwayat['id_pemesanan'] . ")'><i class='fas fa-print'></i> Cetak</button>
                            <a href='?delete_id=" . $rowRiwayat['id_pemesanan'] . "' class='btn btn-danger' onclick='return confirm(\"Apakah Anda yakin ingin menghapus riwayat ini?\")'><i class='fas fa-trash-alt'></i> Hapus</a>
                          </td>";
                    echo "</tr>";

                    // Modal Detail
                    echo "
                    <div class='modal fade' id='detailModal" . $rowRiwayat['id_pemesanan'] . "' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                        <div class='modal-dialog' role='document'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='exampleModalLabel'>Detail Pemesanan</h5>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>
                                <div class='modal-body'>
                                    <p><strong>Nama Pemesan:</strong> " . htmlspecialchars($rowRiwayat['username']) . "</p>
                                    <p><strong>Rute:</strong> " . htmlspecialchars($rowRiwayat['asal'] . " - " . $rowRiwayat['tujuan']) . "</p>
                                    <p><strong>Status Pembayaran:</strong> " . htmlspecialchars($rowRiwayat['status_pembayaran']) . "</p>
                                    <p><strong>Tanggal Update:</strong> " . $rowRiwayat['tanggal_update'] . "</p>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Tidak ada riwayat status pembayaran.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


<a href="dashboard.php" class="btn btn-outline-secondary mt-3">← Kembali ke Dashboard</a>

<!-- JS untuk Cetak -->
<script>
    function cetakTiket(id_pemesanan) {
        // Membuka jendela baru untuk mencetak tiket
        window.open("cetak_riwayat.php?id_pemesanan=" + id_pemesanan, "_blank");
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
