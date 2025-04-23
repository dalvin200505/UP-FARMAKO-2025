<?php
include 'koneksi.php';
$error = '';
$success = '';
$keyword = $_GET['search'] ?? '';

// Handle Tambah / Update Rute
if (isset($_POST['submit_rute'])) {
    $id_transportasi = $_POST['id_transportasi'];
    $asal = $_POST['asal'];
    $tujuan = $_POST['tujuan'];
    $tanggal_keberangkatan = $_POST['tanggal_keberangkatan'];
    $waktu_keberangkatan = $_POST['waktu_keberangkatan'];
    $harga = $_POST['harga'];
    $kelas = $_POST['kelas'];

    if ($id_transportasi && $asal && $tujuan && $tanggal_keberangkatan && $waktu_keberangkatan && $harga && $kelas) {
        if (isset($_POST['id_rute']) && $_POST['id_rute'] != '') {
            $id_rute = $_POST['id_rute'];
            $sql = "UPDATE rute_transportasi SET 
                        id_transportasi='$id_transportasi',
                        asal='$asal',
                        tujuan='$tujuan',
                        tanggal_keberangkatan='$tanggal_keberangkatan',
                        waktu_keberangkatan='$waktu_keberangkatan',
                        harga='$harga',
                        kelas='$kelas' 
                    WHERE id_rute='$id_rute'";
            $success = "Rute berhasil diperbarui!";
        } else {
            $sql = "INSERT INTO rute_transportasi (id_transportasi, asal, tujuan, tanggal_keberangkatan, waktu_keberangkatan, harga, kelas) 
                    VALUES ('$id_transportasi', '$asal', '$tujuan', '$tanggal_keberangkatan', '$waktu_keberangkatan', '$harga', '$kelas')";
            $success = "Rute berhasil ditambahkan!";
        }

        if (!mysqli_query($con, $sql)) {
            $error = "Terjadi kesalahan pada database.";
        }
    } else {
        $error = "Semua field wajib diisi.";
    }
}

// Hapus Rute
if (isset($_GET['delete'])) {
    $id_rute = $_GET['delete'];
    $cek_pemesanan = mysqli_query($con, "SELECT COUNT(*) as total FROM pemesanan WHERE id_rute='$id_rute'");
    $cek_result = mysqli_fetch_assoc($cek_pemesanan);

    if ($cek_result['total'] > 0) {
        $error = "Rute tidak dapat dihapus karena sudah digunakan dalam pemesanan.";
    } else {
        if (mysqli_query($con, "DELETE FROM rute_transportasi WHERE id_rute='$id_rute'")) {
            $success = "Rute berhasil dihapus.";
        } else {
            $error = "Gagal menghapus rute. Terjadi kesalahan pada database.";
        }
    }
}

// Ambil data untuk edit
$edit_rute = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $edit_query = mysqli_query($con, "SELECT * FROM rute_transportasi WHERE id_rute='$id_edit'");
    $edit_rute = mysqli_fetch_assoc($edit_query);
}

// Daftar transportasi
$transportasi_list = mysqli_query($con, "SELECT * FROM transportasi ORDER BY nama ASC");

// Query data rute (dengan pencarian jika ada)
$rute_query = "SELECT r.*, t.nama 
               FROM rute_transportasi r 
               JOIN transportasi t ON r.id_transportasi = t.id_transportasi";

if (!empty($keyword)) {
    $rute_query .= " WHERE t.nama LIKE '%$keyword%' 
                     OR r.asal LIKE '%$keyword%' 
                     OR r.tujuan LIKE '%$keyword%' 
                     OR r.kelas LIKE '%$keyword%' 
                     OR r.tanggal_keberangkatan LIKE '%$keyword%'";
}
$rute_query .= " ORDER BY r.tanggal_keberangkatan DESC";
$rute_result = mysqli_query($con, $rute_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Rute Transportasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

    <h2 class="text-center mb-4">Kelola Rute Transportasi</h2>

    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <!-- Form pencarian -->
    <form class="mb-3 d-flex" method="GET">
        <input type="text" name="search" class="form-control me-2" placeholder="Cari asal, tujuan, kelas, tanggal..." value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit" class="btn btn-outline-primary">Cari</button>
        <?php if ($keyword): ?>
            <a href="kelola_rute.php" class="btn btn-outline-secondary ms-2">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Form Tambah/Edit -->
    <form method="POST" class="card p-4 shadow mb-4">
        <h5 class="mb-3"><?= $edit_rute ? 'Edit Rute' : 'Tambah Rute' ?></h5>

        <?php if ($edit_rute): ?>
            <input type="hidden" name="id_rute" value="<?= $edit_rute['id_rute'] ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 mb-2">
                <select name="id_transportasi" class="form-control" required>
                    <option value="">-- Pilih Transportasi --</option>
                    <?php mysqli_data_seek($transportasi_list, 0); while ($t = mysqli_fetch_assoc($transportasi_list)) : ?>
                        <option value="<?= $t['id_transportasi'] ?>" <?= ($edit_rute && $edit_rute['id_transportasi'] == $t['id_transportasi']) ? 'selected' : '' ?>>
                            <?= $t['nama'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <input type="text" name="asal" class="form-control" placeholder="Asal" value="<?= $edit_rute['asal'] ?? '' ?>" required>
            </div>
            <div class="col-md-4 mb-2">
                <input type="text" name="tujuan" class="form-control" placeholder="Tujuan" value="<?= $edit_rute['tujuan'] ?? '' ?>" required>
            </div>
            <div class="col-md-3 mb-2">
                <input type="date" name="tanggal_keberangkatan" class="form-control" value="<?= $edit_rute['tanggal_keberangkatan'] ?? '' ?>" required>
            </div>
            <div class="col-md-3 mb-2">
                <input type="time" name="waktu_keberangkatan" class="form-control" value="<?= $edit_rute['waktu_keberangkatan'] ?? '' ?>" required>
            </div>
            <div class="col-md-3 mb-2">
                <input type="number" name="harga" class="form-control" placeholder="Harga" value="<?= $edit_rute['harga'] ?? '' ?>" required>
            </div>
            <div class="col-md-3 mb-2">
                <select name="kelas" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    <option value="ekonomi" <?= ($edit_rute && $edit_rute['kelas'] == 'ekonomi') ? 'selected' : '' ?>>Ekonomi</option>
                    <option value="bisnis" <?= ($edit_rute && $edit_rute['kelas'] == 'bisnis') ? 'selected' : '' ?>>Bisnis</option>
                    <option value="vip" <?= ($edit_rute && $edit_rute['kelas'] == 'vip') ? 'selected' : '' ?>>VIP</option>
                </select>
            </div>
        </div>
        <button type="submit" name="submit_rute" class="btn btn-primary mt-3"><?= $edit_rute ? 'Update Rute' : 'Simpan Rute' ?></button>
        <?php if ($edit_rute): ?>
            <a href="kelola_rute.php" class="btn btn-secondary mt-3 ms-2">Batal Edit</a>
        <?php endif; ?>
    </form>

    <!-- Tabel Data -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
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
        <?php if (mysqli_num_rows($rute_result) > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($rute_result)) : ?>
                <tr>
                    <td><?= $r['id_rute'] ?></td>
                    <td><?= $r['nama'] ?></td>
                    <td><?= $r['asal'] ?></td>
                    <td><?= $r['tujuan'] ?></td>
                    <td><?= $r['tanggal_keberangkatan'] ?></td>
                    <td><?= $r['waktu_keberangkatan'] ?></td>
                    <td>Rp<?= number_format($r['harga'], 0, ',', '.') ?></td>
                    <td><?= ucfirst($r['kelas']) ?></td>
                    <td>
                        <a href="?edit=<?= $r['id_rute'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete=<?= $r['id_rute'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">Tidak ada data ditemukan.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-outline-secondary mt-3">← Kembali ke Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
