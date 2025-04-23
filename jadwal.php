<?php
include 'koneksi.php';
session_start();

// Cek jika user belum login atau bukan role 'pemesan'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pemesan') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Transportasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #fff);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .table thead {
            background-color: #0d6efd;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        footer {
            background-color: #f8f9fa;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Sistem Pemesanan</a>
            <div class="d-flex">
                <a href="dashboard_user.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-light text-primary">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="card p-4">
            <h3 class="text-center mb-4 fw-bold text-primary">Jadwal Transportasi</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead>
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
                        // Perbaikan query menggunakan jenis_transportasi
                        $query = "SELECT r.id_rute, t.jenis_transportasi, r.asal, r.tujuan, r.tanggal_keberangkatan, r.waktu_keberangkatan, r.harga, r.kelas 
                                  FROM rute_transportasi r 
                                  JOIN transportasi t ON r.id_transportasi = t.id_transportasi 
                                  ORDER BY r.tanggal_keberangkatan ASC";
                        $result = mysqli_query($con, $query);
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['jenis_transportasi']) . "</td>"; // Gunakan jenis_transportasi
                            echo "<td>" . htmlspecialchars($row['asal']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tujuan']) . "</td>";
                            echo "<td>" . date('d-m-Y', strtotime($row['tanggal_keberangkatan'])) . "</td>";
                            echo "<td>" . date('H:i', strtotime($row['waktu_keberangkatan'])) . "</td>";
                            echo "<td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>";
                            echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                            echo "<td><a href='pesan_tiket.php?id=" . $row['id_rute'] . "' class='btn btn-sm btn-success'>Pesan</a></td>";
                            echo "</tr>";
                        }
                        if (mysqli_num_rows($result) == 0) {
                            echo "<tr><td colspan='9' class='text-danger'>Tidak ada jadwal tersedia saat ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3">
        <p class="mb-0">&copy; <?= date("Y"); ?> Sistem Pemesanan Transportasi. All rights reserved.</p>
    </footer>

</body>
</html>
