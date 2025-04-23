<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit();
}

// Proses update status
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_pemesanan = $_GET['id'];
    $status = $_GET['status'];

    if (in_array($status, ['pending', 'berhasil', 'batal'])) {
        $sql = "UPDATE pemesanan SET status = '$status' WHERE id_pemesanan = '$id_pemesanan'";
        if (mysqli_query($con, $sql)) {
            echo "<script>alert('Status pemesanan berhasil diperbarui!'); window.location.href = 'kelola_pemesanan.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat memperbarui status!');</script>";
        }
    } else {
        echo "<script>alert('Status tidak valid!');</script>";
    }
}

// Proses hapus pemesanan
if (isset($_GET['id']) && isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($con, "DELETE FROM penumpang WHERE id_pemesanan = '$id'");
    if (mysqli_query($con, "DELETE FROM pemesanan WHERE id_pemesanan = '$id'")) {
        echo "<script>alert('Data pemesanan berhasil dihapus!'); window.location.href = 'kelola_pemesanan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data pemesanan!');</script>";
    }
}

$search = '';
$filter_status = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}
if (isset($_POST['filter_status'])) {
    $filter_status = $_POST['filter_status'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pemesanan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Kelola Pemesanan</h2>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#pemesanan" data-bs-toggle="tab">📋 Daftar Pemesanan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#penumpang" data-bs-toggle="tab">🧍‍♂️ Data Penumpang</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#laporan" data-bs-toggle="tab">📈 Laporan</a>
        </li>
    </ul>
    <div class="tab-content">
        <!-- Tab Pemesanan -->
        <div class="tab-pane fade show active" id="pemesanan">
            <form method="POST" class="mb-3 d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama pemesan..." value="<?= htmlspecialchars($search) ?>">
                <select name="filter_status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($filter_status == 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="berhasil" <?= ($filter_status == 'berhasil') ? 'selected' : '' ?>>Berhasil</option>
                    <option value="batal" <?= ($filter_status == 'batal') ? 'selected' : '' ?>>Batal</option>
                </select>
            </form>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Pemesan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT pemesanan.*, users.nama AS nama_pemesan 
                              FROM pemesanan 
                              JOIN users ON pemesanan.id_user = users.id_user
                              WHERE users.nama LIKE '%$search%'";
                    if (!empty($filter_status)) {
                        $query .= " AND pemesanan.status = '$filter_status'";
                    }
                    $query .= " ORDER BY id_pemesanan DESC";
                    $result = mysqli_query($con, $query);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>{$row['id_pemesanan']}</td>";
                            echo "<td>{$row['nama_pemesan']}</td>";
                            echo "<td>{$row['tanggal_pemesanan']}</td>";
                            echo "<td>{$row['status']}</td>";
                            echo "<td>
                                <a href='kelola_pemesanan.php?id={$row['id_pemesanan']}&status=berhasil' class='btn btn-sm btn-success'>Setujui</a>
                                <a href='kelola_pemesanan.php?id={$row['id_pemesanan']}&status=batal' class='btn btn-sm btn-warning'>Tolak</a>
                                <a href='kelola_pemesanan.php?id={$row['id_pemesanan']}&aksi=hapus' class='btn btn-sm btn-danger' onclick='return confirm(\"Hapus pemesanan ini?\")'>Hapus</a>
                              </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Tidak ada data ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <button class="btn btn-outline-primary" onclick="window.print()">🖨️ Cetak</button>
        </div>

        <!-- Tab Penumpang -->
        <div class="tab-pane fade" id="penumpang">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID Penumpang</th>
                        <th>ID Pemesan</th>
                        <th>Nama Penumpang</th>
                        <th>No Identitas</th>
                        <th>Kursi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $penumpang = mysqli_query($con, "SELECT * FROM penumpang");
                    while ($row = mysqli_fetch_assoc($penumpang)) {
                        echo "<tr>
                                <td>{$row['id_penumpang']}</td>
                                <td>{$row['id_pemesanan']}</td>
                                <td>{$row['nama_penumpang']}</td>
                                <td>{$row['no_identitas']}</td>
                                <td>{$row['kursi']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <button class="btn btn-outline-primary" onclick="window.print()">🖨️ Cetak</button>
        </div>

        <!-- Tab Laporan -->
        <div class="tab-pane fade" id="laporan">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Status</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $statuses = ['pending', 'berhasil', 'batal'];
                    foreach ($statuses as $status) {
                        $res = mysqli_query($con, "SELECT COUNT(*) as total FROM pemesanan WHERE status='$status'");
                        $data = mysqli_fetch_assoc($res);
                        echo "<tr><td>$status</td><td>{$data['total']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <button class="btn btn-outline-primary" onclick="window.print()">🖨️ Cetak</button>
        </div>
    </div>
    <a href="dashboard.php" class="btn btn-outline-secondary mt-3">← Kembali ke Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
